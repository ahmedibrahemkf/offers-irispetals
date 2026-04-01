<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InvoicePaymentValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_cannot_exceed_remaining_amount(): void
    {
        $user = User::query()->create([
            'name' => 'Manager',
            'username' => 'manager',
            'role' => 'manager',
            'is_active' => true,
            'password' => Hash::make('Password@123'),
        ]);

        $invoice = Invoice::query()->create([
            'invoice_number' => 'INV-2026-00001',
            'type' => 'direct',
            'payment_status' => 'partial',
            'sub_total' => 100,
            'total_amount' => 100,
            'paid_amount' => 0,
            'remaining_amount' => 100,
            'issued_at' => now(),
            'created_by' => $user->id,
        ]);

        $response = $this
            ->withSession(['auth_user_id' => $user->id])
            ->from(route('admin.invoices.show', $invoice))
            ->post(route('admin.invoices.payments.store', $invoice), [
                'amount' => 150,
                'method' => 'cash',
            ]);

        $response->assertRedirect(route('admin.invoices.show', $invoice));
        $response->assertSessionHasErrors('amount');
    }
}
