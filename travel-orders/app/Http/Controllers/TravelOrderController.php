<?php

namespace App\Http\Controllers;

use App\Models\TravelOrder;
use App\Http\Requests\StoreTravelOrderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\TravelOrderStatusUpdated;
use Illuminate\Support\Facades\Auth;

class TravelOrderController extends Controller
{
    public function store(StoreTravelOrderRequest $request): JsonResponse
    {
        $order = TravelOrder::create([
            'user_id' => Auth::id(),
            'requester_name' => $request->requester_name,
            'destination' => $request->destination,
            'departure_date' => $request->departure_date,
            'return_date' => $request->return_date,
            'status' => TravelOrder::STATUS_REQUESTED
        ]);

        return response()->json($order, 201);
    }

    public function show($id)
    {
        $order = TravelOrder::where('user_id', Auth::id())->findOrFail($id);

        return response()->json($order);
    }

    public function index(Request $request)
    {
        if (Auth::user()->is_admin) {
            $query = TravelOrder::query();
        } else {
            $query = TravelOrder::where('user_id', Auth::id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('destination')) {
            $query->where('destination', 'like', '%' . $request->destination . '%');
        }

        if ($request->filled('departure_date')) {
            $query->whereDate('departure_date', '>=', $request->departure_date);
        }

        if ($request->filled('return_date')) {
            $query->whereDate('return_date', '<=', $request->return_date);
        }

        return response()->json(
            $query->paginate(10)
        );
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:requested,approved,canceled'
        ]);

        // somente admin pode alterar status
        if (!Auth::user()->is_admin) {
            return response()->json([
                'message' => 'Only administrators can update travel order status'
            ], 403);
        }

        $order = TravelOrder::findOrFail($id);

        // não pode cancelar ordem aprovada
        if ($order->status === TravelOrder::STATUS_APPROVED && $request->status === TravelOrder::STATUS_CANCELED) {
            return response()->json([
                'message' => 'Approved orders cannot be canceled'
            ], 400);
        }

        $order->update([
            'status' => $request->status
        ]);

        $user = User::find($order->user_id);
        $user->notify(new TravelOrderStatusUpdated($order));

        return response()->json($order);
    }

    public function destroy($id)
    {
        $order = TravelOrder::where('user_id', Auth::id())->findOrFail($id);

        if ($order->status === TravelOrder::STATUS_APPROVED) {
            return response()->json([
                'message' => 'Approved orders cannot be canceled'
            ], 400);
        }

        $order->delete();

        return response()->json([
            'message' => 'Travel order deleted successfully'
        ]);
    }
}