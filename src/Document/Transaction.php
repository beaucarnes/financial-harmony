<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Encrypt;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EncryptQuery;

#[ODM\Document(collection: 'transactions')]
class Transaction
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    #[Encrypt(queryType: EncryptQuery::Equality)]
    private string $accountNumber;

    #[ODM\Field(type: 'float')]
    #[Encrypt(queryType: EncryptQuery::Range, min: 0.0, max: 1000000.0, precision: 2)]
    private float $amount;

    #[ODM\Field(type: 'string')]
    private string $transactionType; // 'deposit', 'withdrawal', 'transfer'

    #[ODM\Field(type: 'string')]
    private string $description;

    #[ODM\Field(type: 'string')]
    #[Encrypt]
    private string $cardNumber;

    #[ODM\Field(type: 'string')]
    #[Encrypt]
    private string $cvv;

    #[ODM\Field(type: 'string')]
    #[Encrypt]
    private string $expiryDate;

    #[ODM\Field(type: 'string')]
    private string $merchantName;



    #[ODM\Field(type: 'date')]
    private \DateTime $transactionDate;

    #[ODM\Field(type: 'string')]
    private string $status; // 'pending', 'completed', 'failed'

    public function __construct()
    {
        $this->transactionDate = new \DateTime();
        $this->status = 'pending';
    }

    // Getters
    public function getId(): ?string { return $this->id; }
    public function getAccountNumber(): string { return $this->accountNumber; }
    public function getAmount(): float { return $this->amount; }
    public function getTransactionType(): string { return $this->transactionType; }
    public function getDescription(): string { return $this->description; }
    public function getCardNumber(): string { return $this->cardNumber; }
    public function getCvv(): string { return $this->cvv; }
    public function getExpiryDate(): string { return $this->expiryDate; }
    public function getMerchantName(): string { return $this->merchantName; }
    public function getTransactionDate(): \DateTime { return $this->transactionDate; }
    public function getStatus(): string { return $this->status; }

    // Setters
    public function setAccountNumber(string $accountNumber): self { $this->accountNumber = $accountNumber; return $this; }
    public function setAmount(float $amount): self { $this->amount = $amount; return $this; }
    public function setTransactionType(string $transactionType): self { $this->transactionType = $transactionType; return $this; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }
    public function setCardNumber(string $cardNumber): self { $this->cardNumber = $cardNumber; return $this; }
    public function setCvv(string $cvv): self { $this->cvv = $cvv; return $this; }
    public function setExpiryDate(string $expiryDate): self { $this->expiryDate = $expiryDate; return $this; }
    public function setMerchantName(string $merchantName): self { $this->merchantName = $merchantName; return $this; }
    public function setTransactionDate(\DateTime $transactionDate): self { $this->transactionDate = $transactionDate; return $this; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
}
