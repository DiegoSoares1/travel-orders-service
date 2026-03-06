<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TravelOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class TravelOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_travel_order()
    {
        $user = User::factory()->create([
            'password' => Hash::make('123456')
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/travel-orders', [
                'requester_name' => 'Diego',
                'destination' => 'São Paulo',
                'departure_date' => '2026-04-10',
                'return_date' => '2026-04-15'
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('travel_orders', [
            'destination' => 'São Paulo'
        ]);
    }

    public function test_admin_can_approve_travel_order()
    {
        $admin = User::factory()->create([
            'is_admin' => true
        ]);

        $token = $admin->createToken('admin-token')->plainTextToken;

        $order = TravelOrder::factory()->create([
            'status' => 'requested'
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->patchJson("/api/travel-orders/{$order->id}/status", [
                'status' => 'approved'
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('travel_orders', [
            'id' => $order->id,
            'status' => 'approved'
        ]);
    }

    public function test_normal_user_cannot_update_travel_order_status()
    {
        $user = User::factory()->create([
            'is_admin' => false
        ]);

        $token = $user->createToken('user-token')->plainTextToken;

        $order = TravelOrder::factory()->create([
            'status' => 'requested'
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->patchJson("/api/travel-orders/{$order->id}/status", [
                'status' => 'approved'
            ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_view_other_users_travel_order()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $token = $userA->createToken('user-token')->plainTextToken;

        $order = TravelOrder::factory()->create([
            'user_id' => $userB->id
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson("/api/travel-orders/{$order->id}");

        $response->assertStatus(404);
    }
}