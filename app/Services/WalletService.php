<?php

namespace App\Services;

use App\Models\Wallet;
use App\Exceptions\WalletException;
use App\Repository\WalletRepository;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function __construct(
        protected WalletRepository $walletRepository,
        protected TransactionService $transactionService
    ) {}
    /**
     * @throws WalletException
     */
    public function createWallet(array $data): Wallet
    {
        $existingWallet = $this->walletRepository->findByUserId($data['user_id']);

        if ($existingWallet) {
            throw new WalletException(
                'User already has a wallet',
                442
            );
        }

        //add wallet number
        $data['wallet_number'] = 'WAL' . str_pad(random_int(1, 9999999), 7, '0', STR_PAD_LEFT);

        return $this->walletRepository->create($data);
    }

    /**
     * @throws WalletException
     */
    public function fundWallet(array $data)
    {
        if ($data['amount'] <= 0) {
            throw new WalletException(
                'Amount must be greater than 0',
                442
            );
        }

        //find ledger wallet
        $ledgerWallet = $this->walletRepository->findByWalletNumber(config('wallets.ledger_wallet_number'));

        $creditWallet = $this->walletRepository->findByWalletNumber($data['wallet_number']);

        return DB::transaction(function () use ($creditWallet, $ledgerWallet, $data) {
            $transaction = $this->transactionService->createTransaction([
                'debit_wallet_id' => $ledgerWallet->id,
                'credit_wallet_id' => $creditWallet->id,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? $ledgerWallet->currency,
                'description' => $data['description'] ?? 'Credit transaction',
                'metadata' => [

                ]
            ]);
            return [
                'wallet' => $creditWallet->refresh(),
                'transaction' => $transaction->creditEntry->withoutRelations()
            ];
        });
    }

    /**
     * @throws WalletException
     */
    public function withdrawFromWallet(array $data): array
    {
        if ($data['amount'] <= 0) {
            throw new WalletException(
                'Amount must be greater than 0',
                442
            );
        }

        //find admin wallet
        $ledgerWallet = $this->walletRepository->findByWalletNumber(config('wallets.ledger_wallet_number'));

        $debitWallet = $this->walletRepository->findByWalletNumber($data['wallet_number']);

        return DB::transaction(function () use ($debitWallet, $ledgerWallet, $data) {
            $transaction = $this->transactionService->createTransaction([
                'debit_wallet_id' => $debitWallet->id,
                'credit_wallet_id' => $ledgerWallet->id,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? $ledgerWallet->currency,
                'description' => $data['description '] ?? 'Debit transaction',
            ]);

            return [
                'wallet' => $debitWallet->refresh(),
                'transaction' => $transaction->debitEntry->withoutRelations()
            ];
        });
    }

    public function getWalletDetail(int $walletId): ?Wallet
    {
        $wallet = $this->walletRepository->findById($walletId);

        if (!$wallet) {
            return null;
        }

        $summary = [
            'total_credits' => $wallet->transactions()
                ->where('type', 'credit')
                ->sum('amount'),

            'total_debits' => $wallet->transactions()
                ->where('type', 'debit')
                ->sum('amount'),
        ];

        $wallet->transaction_summary = $summary;

        return $wallet->load('transactions');
    }

    /**
     * @throws WalletException
     */
    public function deleteWallet($walletId): bool
    {
        $wallet = $this->walletRepository->findById($walletId);

        if (!$wallet) {
            throw new WalletException(
                'Wallet not found',
                404
            );
        }

        // check if wallet balance is zero
        if ($wallet->balance > 0) {
            throw new WalletException(
                'Cannot delete wallet with non-zero balance',
                442
            );
        }

        return $this->walletRepository->delete($wallet);
    }
}
