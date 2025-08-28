<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Encrypt;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EncryptQuery;

#[ODM\Document(collection: 'accounts')]
class Account
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $customerName;

    #[ODM\Field(type: 'string')]
    #[Encrypt(queryType: EncryptQuery::Equality)]
    private string $accountNumber;

    #[ODM\Field(type: 'float')]
    #[Encrypt(queryType: EncryptQuery::Range, min: 0.0, max: 10000000.0)]
    private float $balance;

    #[ODM\Field(type: 'string')]
    #[Encrypt(queryType: EncryptQuery::Equality)]
    private string $ssn;

    #[ODM\Field(type: 'string')]
    private string $email;

    #[ODM\Field(type: 'date')]
    private \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters
    public function getId(): ?string { return $this->id; }
    public function getCustomerName(): string { return $this->customerName; }
    public function getAccountNumber(): string { return $this->accountNumber; }
    public function getBalance(): float { return $this->balance; }
    public function getSsn(): string { return $this->ssn; }
    public function getEmail(): string { return $this->email; }
    public function getCreatedAt(): \DateTime { return $this->createdAt; }

    // Setters
    public function setCustomerName(string $customerName): self { $this->customerName = $customerName; return $this; }
    public function setAccountNumber(string $accountNumber): self { $this->accountNumber = $accountNumber; return $this; }
    public function setBalance(float $balance): self { $this->balance = $balance; return $this; }
    public function setSsn(string $ssn): self { $this->ssn = $ssn; return $this; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function setCreatedAt(\DateTime $createdAt): self { $this->createdAt = $createdAt; return $this; }
}
