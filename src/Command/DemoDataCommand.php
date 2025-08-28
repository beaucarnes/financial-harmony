<?php

namespace App\Command;

use App\Service\FinancialService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:demo-data', description: 'Populate database with demo financial data')]
class DemoDataCommand extends Command
{
    public function __construct(private FinancialService $financialService) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Creating Demo Financial Data');
        
        try {
            $this->createAccounts($io);
            $this->createTransactions($io);
            
            $io->success('Demo data created successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Error creating demo data: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function createAccounts(SymfonyStyle $io): void
    {
        $io->section('Creating demo accounts...');
        
        $accounts = [
            ['John Doe', '1234567890', 50000.00, '123-45-6789', 'john.doe@example.com'],
            ['Jane Smith', '0987654321', 75000.00, '987-65-4321', 'jane.smith@example.com'],
            ['Bob Johnson', '1122334455', 25000.00, '111-22-3333', 'bob.johnson@example.com'],
            ['Alice Brown', '5566778899', 100000.00, '555-66-7777', 'alice.brown@example.com']
        ];
        
        foreach ($accounts as [$name, $number, $balance, $ssn, $email]) {
            $account = $this->financialService->createAccount($name, $number, $balance, $ssn, $email);
            $io->text("✓ Created account for {$name} (ID: {$account->getId()})");
        }
    }

    private function createTransactions(SymfonyStyle $io): void
    {
        $io->section('Creating demo transactions...');
        
        $transactions = [
            ['1234567890', 1500.00, 'withdrawal', 'ATM withdrawal', '4111111111111111', '123', '12/25', 'Local ATM'],
            ['1234567890', 2500.00, 'deposit', 'Salary deposit', '', '', '', 'ABC Company'],
            ['0987654321', 3000.00, 'withdrawal', 'Online purchase', '5555555555554444', '456', '06/24', 'Online Store'],
            ['0987654321', 5000.00, 'deposit', 'Investment return', '', '', '', 'Investment Fund'],
            ['1122334455', 800.00, 'withdrawal', 'Restaurant payment', '378282246310005', '789', '09/26', 'Restaurant'],
            ['5566778899', 12000.00, 'deposit', 'Business payment', '', '', '', 'Business Client'],
            ['5566778899', 3500.00, 'withdrawal', 'Hotel booking', '6011111111111117', '012', '03/25', 'Hotel Chain']
        ];
        
        foreach ($transactions as [$account, $amount, $type, $desc, $card, $cvv, $expiry, $merchant]) {
            $transaction = $this->financialService->createTransaction($account, $amount, $type, $desc, $card, $cvv, $expiry, $merchant);
            $io->text("✓ Created {$type} transaction for account {$account} (ID: {$transaction->getId()})");
        }
    }
}