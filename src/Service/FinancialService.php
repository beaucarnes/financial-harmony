<?php

namespace App\Service;

use App\Document\Account;
use App\Document\Transaction;
use Doctrine\ODM\MongoDB\DocumentManager;

class FinancialService
{
    public function __construct(
        private DocumentManager $documentManager
    ) {}

    public function createAccount(string $customerName, string $accountNumber, float $balance, string $ssn, string $email): Account
    {
        $account = new Account();
        $account->setCustomerName($customerName)
                ->setAccountNumber($accountNumber)
                ->setBalance($balance)
                ->setSsn($ssn)
                ->setEmail($email);

        $this->documentManager->persist($account);
        $this->documentManager->flush();

        return $account;
    }

    public function createTransaction(
        string $accountNumber,
        float $amount,
        string $transactionType,
        string $description,
        string $cardNumber,
        string $cvv,
        string $expiryDate,
        string $merchantName
    ): Transaction {
        $transaction = new Transaction();
        $transaction->setAccountNumber($accountNumber)
                   ->setAmount($amount)
                   ->setTransactionType($transactionType)
                   ->setDescription($description)
                   ->setCardNumber($cardNumber)
                   ->setCvv($cvv)
                   ->setExpiryDate($expiryDate)
                   ->setMerchantName($merchantName);

        $this->documentManager->persist($transaction);
        $this->documentManager->flush();

        return $transaction;
    }

    public function findAccountByNumber(string $accountNumber): ?Account
    {
        return $this->documentManager->getRepository(Account::class)
            ->findOneBy(['accountNumber' => $accountNumber]);
    }

    public function findAccountsByBalanceRange(float $minBalance, float $maxBalance): array
    {
        $qb = $this->documentManager->createQueryBuilder(Account::class);
        $qb->field('balance')->gte($minBalance)->lte($maxBalance);
        
        return $qb->getQuery()->execute()->toArray();
    }

    public function findTransactionsByAccountNumber(string $accountNumber): array
    {
        return $this->documentManager->getRepository(Transaction::class)
            ->findBy(['accountNumber' => $accountNumber]);
    }

    public function findTransactionsByAmountRange(float $minAmount, float $maxAmount): array
    {
        $qb = $this->documentManager->createQueryBuilder(Transaction::class);
        $qb->field('amount')->gte($minAmount)->lte($maxAmount);
        
        return $qb->getQuery()->execute()->toArray();
    }

    public function findAccountBySsn(string $ssn): ?Account
    {
        return $this->documentManager->getRepository(Account::class)
            ->findOneBy(['ssn' => $ssn]);
    }

    public function getAllAccounts(): array
    {
        return $this->documentManager->getRepository(Account::class)->findAll();
    }

    public function getAllTransactions(): array
    {
        return $this->documentManager->getRepository(Transaction::class)->findAll();
    }
}