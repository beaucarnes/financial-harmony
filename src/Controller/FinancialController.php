<?php

namespace App\Controller;

use App\Service\FinancialService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FinancialController extends AbstractController
{
    public function __construct(
        private FinancialService $financialService
    ) {}

    #[Route('/', name: 'homepage')]
    public function homepage(#[Autowire(param: 'kernel.project_dir')] string $projectDir): Response
    {
        $html = file_get_contents($projectDir . '/public/index.html');
        return new Response($html, status: 200, headers: ['Content-Type' => 'text/html']);
    }

    private function successResponse($data, int $status = 200): JsonResponse
    {
        return $this->json(['success' => true, ...$data], $status);
    }

    private function errorResponse(string $message, int $status = 400): JsonResponse
    {
        return $this->json(['success' => false, 'message' => $message], $status);
    }

    #[Route('/api/harmony/accounts', methods: ['POST'])]
    public function createAccount(Request $request): JsonResponse
    {
        try {
            $data = $request->getPayload();
            $account = $this->financialService->createAccount(
                $data->get('customerName'),
                $data->get('accountNumber'),
                (float) $data->get('balance'),
                $data->get('ssn'),
                $data->get('email')
            );

            return $this->successResponse([
                'message' => 'Account created successfully',
                'accountId' => $account->getId()
            ], 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Error creating account: ' . $e->getMessage());
        }
    }

    #[Route('/api/harmony/transactions', methods: ['POST'])]
    public function createTransaction(Request $request): JsonResponse
    {
        try {
            $data = $request->getPayload();
            $transaction = $this->financialService->createTransaction(
                $data->get('accountNumber'),
                (float) $data->get('amount'),
                $data->get('transactionType'),
                $data->get('description'),
                $data->get('cardNumber'),
                $data->get('cvv'),
                $data->get('expiryDate'),
                $data->get('merchantName')
            );

            return $this->successResponse([
                'message' => 'Transaction created successfully',
                'transactionId' => $transaction->getId()
            ], 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Error creating transaction: ' . $e->getMessage());
        }
    }

    #[Route('/api/harmony/accounts/balance-range', methods: ['GET'])]
    public function getAccountsByBalanceRange(Request $request): JsonResponse
    {
        $minBalance = (float) $request->query->get('min', 0);
        $maxBalance = (float) $request->query->get('max', 1000000);

        $accounts = $this->financialService->findAccountsByBalanceRange($minBalance, $maxBalance);
        
        $accountData = array_map(fn($account) => [
            'id' => $account->getId(),
            'customerName' => $account->getCustomerName(),
            'accountNumber' => $account->getAccountNumber(),
            'balance' => $account->getBalance(),
            'email' => $account->getEmail(),
            'createdAt' => $account->getCreatedAt()->format('Y-m-d H:i:s')
        ], $accounts);

        return $this->successResponse([
            'accounts' => $accountData,
            'count' => count($accountData)
        ]);
    }

    #[Route('/api/harmony/accounts/{accountNumber}', methods: ['GET'])]
    public function getAccountByNumber(string $accountNumber): JsonResponse
    {
        $account = $this->financialService->findAccountByNumber($accountNumber);
        
        if (!$account) {
            return $this->errorResponse('Account not found', 404);
        }

        return $this->successResponse([
            'account' => [
                'id' => $account->getId(),
                'customerName' => $account->getCustomerName(),
                'accountNumber' => $account->getAccountNumber(),
                'balance' => $account->getBalance(),
                'email' => $account->getEmail(),
                'createdAt' => $account->getCreatedAt()->format('Y-m-d H:i:s')
            ]
        ]);
    }

    #[Route('/api/harmony/transactions/account/{accountNumber}', methods: ['GET'])]
    public function getTransactionsByAccountNumber(string $accountNumber): JsonResponse
    {
        $transactions = $this->financialService->findTransactionsByAccountNumber($accountNumber);
        
        $transactionData = array_map(fn($transaction) => [
            'id' => $transaction->getId(),
            'accountNumber' => $transaction->getAccountNumber(),
            'amount' => $transaction->getAmount(),
            'transactionType' => $transaction->getTransactionType(),
            'description' => $transaction->getDescription(),
            'merchantName' => $transaction->getMerchantName(),
            'status' => $transaction->getStatus(),
            'transactionDate' => $transaction->getTransactionDate()->format('Y-m-d H:i:s')
        ], $transactions);

        return $this->successResponse([
            'transactions' => $transactionData,
            'count' => count($transactionData)
        ]);
    }

    #[Route('/api/harmony/transactions/amount-range', methods: ['GET'])]
    public function getTransactionsByAmountRange(Request $request): JsonResponse
    {
        $minAmount = (float) $request->query->get('min', 0);
        $maxAmount = (float) $request->query->get('max', 1000000);

        $transactions = $this->financialService->findTransactionsByAmountRange($minAmount, $maxAmount);
        
        $transactionData = array_map(fn($transaction) => [
            'id' => $transaction->getId(),
            'accountNumber' => $transaction->getAccountNumber(),
            'amount' => $transaction->getAmount(),
            'transactionType' => $transaction->getTransactionType(),
            'description' => $transaction->getDescription(),
            'merchantName' => $transaction->getMerchantName(),
            'status' => $transaction->getStatus(),
            'transactionDate' => $transaction->getTransactionDate()->format('Y-m-d H:i:s')
        ], $transactions);

        return $this->successResponse([
            'transactions' => $transactionData,
            'count' => count($transactionData)
        ]);
    }

    #[Route('/api/harmony/accounts/ssn/{ssn}', methods: ['GET'])]
    public function getAccountBySsn(string $ssn): JsonResponse
    {
        $account = $this->financialService->findAccountBySsn($ssn);
        
        if (!$account) {
            return $this->errorResponse('Account not found', 404);
        }

        return $this->successResponse([
            'account' => [
                'id' => $account->getId(),
                'customerName' => $account->getCustomerName(),
                'accountNumber' => $account->getAccountNumber(),
                'balance' => $account->getBalance(),
                'email' => $account->getEmail(),
                'createdAt' => $account->getCreatedAt()->format('Y-m-d H:i:s')
            ]
        ]);
    }

    #[Route('/api/harmony/accounts', methods: ['GET'])]
    public function getAllAccounts(): JsonResponse
    {
        $accounts = $this->financialService->getAllAccounts();
        
        $accountData = array_map(fn($account) => [
            'id' => $account->getId(),
            'customerName' => $account->getCustomerName(),
            'accountNumber' => $account->getAccountNumber(),
            'balance' => $account->getBalance(),
            'email' => $account->getEmail(),
            'createdAt' => $account->getCreatedAt()->format('Y-m-d H:i:s')
        ], $accounts);

        return $this->successResponse([
            'accounts' => $accountData,
            'count' => count($accountData)
        ]);
    }

    #[Route('/api/harmony/transactions', methods: ['GET'])]
    public function getAllTransactions(): JsonResponse
    {
        $transactions = $this->financialService->getAllTransactions();
        
        $transactionData = array_map(fn($transaction) => [
            'id' => $transaction->getId(),
            'accountNumber' => $transaction->getAccountNumber(),
            'amount' => $transaction->getAmount(),
            'transactionType' => $transaction->getTransactionType(),
            'description' => $transaction->getDescription(),
            'merchantName' => $transaction->getMerchantName(),
            'status' => $transaction->getStatus(),
            'transactionDate' => $transaction->getTransactionDate()->format('Y-m-d H:i:s')
        ], $transactions);

        return $this->successResponse([
            'transactions' => $transactionData,
            'count' => count($transactionData)
        ]);
    }

    #[Route('/api/harmony/accounts/{accountNumber}/summary', methods: ['GET'])]
    public function getAccountSummary(string $accountNumber): JsonResponse
    {
        $account = $this->financialService->findAccountByNumber($accountNumber);
        
        if (!$account) {
            return $this->errorResponse('Account not found', 404);
        }

        $transactions = $this->financialService->findTransactionsByAccountNumber($accountNumber);
        $totalTransactions = count($transactions);
        $totalAmount = array_sum(array_map(fn($t) => $t->getAmount(), $transactions));

        return $this->successResponse([
            'account' => [
                'id' => $account->getId(),
                'customerName' => $account->getCustomerName(),
                'accountNumber' => $account->getAccountNumber(),
                'balance' => $account->getBalance(),
                'email' => $account->getEmail()
            ],
            'summary' => [
                'totalTransactions' => $totalTransactions,
                'totalAmount' => $totalAmount,
                'averageTransactionAmount' => $totalTransactions > 0 ? $totalAmount / $totalTransactions : 0
            ]
        ]);
    }

    #[Route('/api/harmony/accounts/{accountNumber}/history', methods: ['GET'])]
    public function getAccountBalanceHistory(string $accountNumber, Request $request): JsonResponse
    {
        $account = $this->financialService->findAccountByNumber($accountNumber);
        
        if (!$account) {
            return $this->errorResponse('Account not found', 404);
        }

        $transactions = $this->financialService->findTransactionsByAccountNumber($accountNumber);
        
        // Simple balance history simulation
        $history = [];
        $runningBalance = $account->getBalance();
        
        foreach (array_reverse($transactions) as $transaction) {
            $runningBalance -= $transaction->getAmount();
            $history[] = [
                'date' => $transaction->getTransactionDate()->format('Y-m-d H:i:s'),
                'balance' => $runningBalance,
                'transaction' => $transaction->getAmount()
            ];
        }

        return $this->successResponse([
            'accountNumber' => $accountNumber,
            'currentBalance' => $account->getBalance(),
            'history' => $history
        ]);
    }

    #[Route('/api/harmony/debug/encryption-status', methods: ['GET'])]
    public function getEncryptionStatus(): JsonResponse
    {
        return $this->successResponse([
            'encryption' => 'enabled',
            'queryableEncryption' => 'active',
            'encryptedFields' => [
                'accounts' => ['accountNumber', 'balance', 'ssn'],
                'transactions' => ['accountNumber', 'amount', 'cardNumber', 'cvv', 'expiryDate']
            ]
        ]);
    }
}