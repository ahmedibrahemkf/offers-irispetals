<?php

namespace Tests\Feature;

use App\Models\Collector;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderUpdateValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_amount_paid_cannot_exceed_order_total(): void
    {
        $user = User::query()->create([
            'name' => 'Owner',
            'username' => 'owner',
            'role' => 'owner',
            'is_active' => true,
            'password' => Hash::make('Owner@123456'),
        ]);

        $order = Order::query()->create([
            'order_number' => 'ORD-2026-00001',
            'customer_name_snapshot' => 'عميل',
            'customer_phone_snapshot' => '01000000000',
            'source' => 'walk_in',
            'status' => 'new',
            'payment_status' => 'unpaid',
            'amount_total' => 5000,
            'amount_paid' => 0,
            'amount_remaining' => 5000,
            'created_by' => $user->id,
        ]);

        $response = $this
            ->withSession(['auth_user_id' => $user->id])
            ->from(route('admin.orders.edit', $order))
            ->put(route('admin.orders.update', $order), [
                'status' => 'confirmed',
                'payment_status' => 'partial',
                'amount_paid' => 6000,
            ]);

        $response->assertRedirect(route('admin.orders.edit', $order));
        $response->assertSessionHasErrors('amount_paid');
    }

    public function test_collection_split_cannot_exceed_order_total(): void
    {
        $user = User::query()->create([
            'name' => 'Owner',
            'username' => 'owner2',
            'role' => 'owner',
            'is_active' => true,
            'password' => Hash::make('Owner@123456'),
        ]);

        $collectorA = Collector::query()->create(['name' => 'Collector A', 'is_active' => true]);
        $collectorB = Collector::query()->create(['name' => 'Collector B', 'is_active' => true]);

        $order = Order::query()->create([
            'order_number' => 'ORD-2026-00002',
            'customer_name_snapshot' => 'عميل',
            'customer_phone_snapshot' => '01000000001',
            'source' => 'walk_in',
            'status' => 'new',
            'payment_status' => 'unpaid',
            'amount_total' => 5000,
            'amount_paid' => 0,
            'amount_remaining' => 5000,
            'created_by' => $user->id,
        ]);

        $response = $this
            ->withSession(['auth_user_id' => $user->id])
            ->from(route('admin.orders.edit', $order))
            ->put(route('admin.orders.update', $order), [
                'status' => 'confirmed',
                'payment_status' => 'partial',
                'amount_paid' => 0,
                'collector_ids' => [$collectorA->id, $collectorB->id],
                'collector_amounts' => [3000, 2500],
                'collector_notes' => ['part 1', 'part 2'],
            ]);

        $response->assertRedirect(route('admin.orders.edit', $order));
        $response->assertSessionHasErrors('amount_paid');
    }

    public function test_collection_split_updates_paid_amount_with_valid_total(): void
    {
        $user = User::query()->create([
            'name' => 'Owner',
            'username' => 'owner3',
            'role' => 'owner',
            'is_active' => true,
            'password' => Hash::make('Owner@123456'),
        ]);

        $collectorA = Collector::query()->create(['name' => 'Collector A', 'is_active' => true]);
        $collectorB = Collector::query()->create(['name' => 'Collector B', 'is_active' => true]);

        $order = Order::query()->create([
            'order_number' => 'ORD-2026-00003',
            'customer_name_snapshot' => 'عميل',
            'customer_phone_snapshot' => '01000000002',
            'source' => 'walk_in',
            'status' => 'new',
            'payment_status' => 'unpaid',
            'amount_total' => 5000,
            'amount_paid' => 0,
            'amount_remaining' => 5000,
            'created_by' => $user->id,
        ]);

        $response = $this
            ->withSession(['auth_user_id' => $user->id])
            ->put(route('admin.orders.update', $order), [
                'status' => 'confirmed',
                'payment_status' => 'partial',
                'amount_paid' => 0,
                'collector_ids' => [$collectorA->id, $collectorB->id],
                'collector_amounts' => [3000, 2000],
                'collector_notes' => ['part 1', 'part 2'],
            ]);

        $response->assertRedirect(route('admin.orders.show', $order));
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'amount_paid' => 5000,
            'amount_remaining' => 0,
            'payment_status' => 'paid',
        ]);
        $this->assertDatabaseCount('order_collections', 2);
    }
}

