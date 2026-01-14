<?php

namespace App\Services;

use App\Exceptions\WalletException;
use App\Models\Transaction;
use App\Models\TransactionEntry;
use App\Models\Wallet;
use App\Repository\TransactionRepository;
use App\Repository\WalletRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Exceptions\TransactionException;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\TransactionNotFoundException;


class TransactionService
{
    public function __construct(
        protected TransactionRepository $transactionRepository,
        protected WalletRepository $walletRepository
    ) {}

    /**
     * Create a new transaction with debit and credit entries
     *
     * @param array $data
     * @return Transaction
     * @throws InsufficientFundsException
     * @throws TransactionException
     * @throws ValidationException
     * @throws WalletException
     */
    public function createTransaction(array $data): Transaction
    {
        // Prevent same wallet transactions
        if ($data['debit_wallet_id'] === $data['credit_wallet_id']) {
            throw new TransactionException(
                'Debit and credit wallets cannot be the same'
            );
        }

        try {
            DB::beginTransaction();

            // Get wallets with lock for update to prevent race conditions
            $debitWallet = $this->getWalletWithLock($data['debit_wallet_id']);
            $creditWallet = $this->getWalletWithLock($data['credit_wallet_id']);

            // Validate wallets
            $this->validateWallets($debitWallet, $creditWallet, $data['amount']);

            // Create transaction entries
            $debitEntry = $this->createDebitEntry($debitWallet, $data);
            $creditEntry = $this->createCreditEntry($creditWallet, $data);

            // Create transaction group
            $transaction = $this->transactionRepository->createTransaction([
                'debitEntry_id' => $debitEntry->id,
                'creditEntry_id' => $creditEntry->id,
                'reversible' => $data['reversible'] ?? false,
                'metadata' => $data['metadata'] ?? null,
            ]);

            // Update wallet balances
            $this->transactionRepository->updateWalletBalances($debitWallet, $data['amount'], 'debit');
            $this->transactionRepository->updateWalletBalances($creditWallet, $data['amount'], 'credit');

            DB::commit();

            return $transaction->load(['debitEntry.wallet', 'creditEntry.wallet']);

        } catch (ValidationException|InsufficientFundsException|WalletException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create transaction', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
            ]);

            throw new TransactionException(
                'Failed to create transaction: ' . $e->getMessage(),
                previous: $e
            );
        }
    }

    /**
     * Get wallet with lock for update
     *
     * @param string|int $walletId
     * @return Wallet
     * @throws TransactionException
     * @throws WalletException
     */
    private function getWalletWithLock(string|int $walletId): Wallet
    {
        $wallet = Wallet::where('id', $walletId)
            ->lockForUpdate()
            ->first();

        if (!$wallet) {
            throw new WalletException("Wallet not found: {$walletId}");
        }

        if ($wallet->status !== 'active') {
            throw new TransactionException("Wallet is not active: {$walletId}");
        }

        return $wallet;
    }

    /**
     * Validate wallets for transaction
     *
     * @param Wallet $debitWallet
     * @param Wallet $creditWallet
     * @param float $amount
     * @throws TransactionException
     * @throws InsufficientFundsException
     */
    private function validateWallets(Wallet $debitWallet, Wallet $creditWallet, float $amount): void
    {
        // Check if debit wallet has sufficient balance
        if (!$debitWallet->hasSufficientBalance($amount)) {
            throw new InsufficientFundsException(
                "Insufficient funds in wallet."
            );
        }

        // Check currency consistency (optional)
        if ($debitWallet->currency !== $creditWallet->currency) {
            throw new TransactionException(
                "Currency mismatch. Debit wallet: {$debitWallet->currency}, " .
                "Credit wallet: {$creditWallet->currency}"
            );
        }
    }

    /**
     * Create debit transaction entry
     *
     * @param Wallet $wallet
     * @param array $data
     * @return TransactionEntry
     * @throws \Exception
     */
    private function createDebitEntry(Wallet $wallet, array $data): TransactionEntry
    {
        return $this->transactionRepository->createTransactionEntry([
            'wallet_id' => $wallet->id,
            'type' => 'debit',
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? $wallet->currency,
            'metadata' => $data['metadata'] ?? null,
            'status' => 'success',
            'description' => $data['description'] ?? 'Debit transaction'
        ]);
    }

    /**
     * Create credit transaction entry
     *
     * @param Wallet $wallet
     * @param array $data
     * @return TransactionEntry
     * @throws \Exception
     */
    private function createCreditEntry(Wallet $wallet, array $data): TransactionEntry
    {
        return $this->transactionRepository->createTransactionEntry([
            'wallet_id' => $wallet->id,
            'type' => 'credit',
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? $wallet->currency,
            'metadata' => $data['metadata'] ?? null,
            'status' => 'success',
            'description' => $data['description'] ?? 'Credit transaction'
        ]);
    }


    /**
     * Get transaction by ID with related data
     *
     * @param string $transactionId
     * @return array
     * @throws TransactionNotFoundException
     */
    public function getTransaction(string $transactionId)
    {
        $transactionEntry = $this->transactionRepository->findTransactionEntryById($transactionId);

        if (!$transactionEntry) {
            throw new TransactionNotFoundException("Transaction not found.");
        }

        return [
            'transaction' => $transactionEntry->withoutRelations(),
            'debit_user' => $transactionEntry->transaction->debitEntry->wallet->user,
            'credit_user' => $transactionEntry->transaction->creditEntry->wallet->user,
        ];
    }

    /**
     * Get wallet transaction history
     *
     * @param string $walletId
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getWalletHistory(string $walletId, array $filters = [])
    {
        $query = TransactionEntry::query()
            ->where('wallet_id', $walletId)
            ->orderBy('created_at', 'desc');

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }

        return $query->paginate($filters['per_page'] ?? 20);
    }
}
