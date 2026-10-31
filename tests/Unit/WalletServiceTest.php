<?php

namespace Tests\Unit;

use App\Exceptions\InsufficientWalletBalanceException;
use App\Models\Company;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class WalletServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WalletService $walletService;

    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->walletService = new WalletService;

        // Create a test company with zero initial wallet balance
        $this->company = Company::create([
            'name' => 'Test Agency Electrics',
            'code' => 'TEST-001',
            'contact_person' => 'Tester',
            'email' => 'test@example.com',
            'status' => 'active',
            'wallet_balance' => 0.00,
            'max_workers' => 20,
            'max_meters' => 50000,
        ]);
    }

    public function test_can_credit_wallet_and_create_immutable_ledger_record(): void
    {
        $user = User::factory()->create(['company_id' => $this->company->id]);

        $tx = $this->walletService->credit(
            $this->company,
            1250.50,
            'Welcome Bonus',
            'REF-BONUS-01',
            $user->id
        );

        $this->assertInstanceOf(WalletTransaction::class, $tx);
        $this->assertEquals(1250.50, $tx->amount);
        $this->assertEquals(1250.50, $tx->balance_after);
        $this->assertEquals('credit', $tx->type);
        $this->assertEquals('Welcome Bonus', $tx->reason);
        $this->assertEquals('REF-BONUS-01', $tx->reference_id);
        $this->assertEquals($user->id, $tx->created_by);

        // Verify company wallet balance was updated in database
        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'wallet_balance' => 1250.50,
        ]);

        // Verify in-memory model was also updated
        $this->assertEquals(1250.50, $this->company->wallet_balance);
    }

    public function test_can_debit_wallet_when_sufficient_balance_exists(): void
    {
        // First credit
        $this->walletService->credit($this->company, 2000.00, 'Initial deposit');

        // Then debit
        $tx = $this->walletService->debit(
            $this->company,
            750.25,
            'Subscription renewal deduction',
            'SUB-001'
        );

        $this->assertEquals(750.25, $tx->amount);
        $this->assertEquals(1249.75, $tx->balance_after);
        $this->assertEquals('debit', $tx->type);

        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'wallet_balance' => 1249.75,
        ]);
        $this->assertEquals(1249.75, $this->company->wallet_balance);
    }

    public function test_debit_throws_insufficient_wallet_balance_exception_when_balance_is_low(): void
    {
        $this->walletService->credit($this->company, 500.00, 'Small deposit');

        $this->expectException(InsufficientWalletBalanceException::class);
        $this->expectExceptionMessage('Insufficient wallet balance. Available: ₹500.00, Required: ₹1200.00');

        $this->walletService->debit($this->company, 1200.00, 'High subscription fee');

        // Verify balance was untouched
        $this->company->refresh();
        $this->assertEquals(500.00, $this->company->wallet_balance);
    }

    public function test_cannot_credit_or_debit_zero_or_negative_amounts(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->walletService->credit($this->company, 0.00, 'Zero credit');

        $this->expectException(InvalidArgumentException::class);
        $this->walletService->debit($this->company, -50.00, 'Negative debit');
    }

    public function test_multiple_sequential_transactions_maintain_exact_ledger_integrity(): void
    {
        $this->walletService->credit($this->company, 1000.00, 'Deposit 1');
        $this->walletService->credit($this->company, 500.50, 'Deposit 2');
        $this->walletService->debit($this->company, 300.25, 'Payment 1');
        $this->walletService->credit($this->company, 200.00, 'Referral bonus');
        $this->walletService->debit($this->company, 700.00, 'Payment 2');

        // Net: 1000 + 500.50 - 300.25 + 200 - 700 = 700.25
        $this->company->refresh();
        $this->assertEquals(700.25, $this->company->wallet_balance);

        $transactions = WalletTransaction::where('company_id', $this->company->id)->get();
        $this->assertCount(5, $transactions);
    }
}
