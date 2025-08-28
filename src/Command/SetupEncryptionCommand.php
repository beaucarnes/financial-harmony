<?php

namespace App\Command;

use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:setup-encryption', description: 'Set up MongoDB Queryable Encryption schema')]
class SetupEncryptionCommand extends Command
{
    public function __construct(private DocumentManager $documentManager) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Setting up MongoDB Queryable Encryption');
        
        try {
            $client = $this->documentManager->getClient();
            
            // For Atlas, we'll just create the collections normally
            // The encryption will be handled by the client configuration
            $this->createCollection($client, 'financial_symphony', 'accounts', $io);
            $this->createCollection($client, 'financial_symphony', 'transactions', $io);
            
            $io->success('Collections created successfully!');
            $io->text('Note: For MongoDB Atlas, encryption is handled by the client configuration.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Error setting up collections: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function createCollection(Client $client, string $databaseName, string $collectionName, SymfonyStyle $io): void
    {
        $io->section("Creating collection: {$databaseName}.{$collectionName}");
        
        $database = $client->selectDatabase($databaseName);
        
        try {
            $database->createCollection($collectionName);
            $io->text("✓ Created collection: {$collectionName}");
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'already exists') !== false) {
                $io->text("✓ Collection {$collectionName} already exists");
            } else {
                throw $e;
            }
        }
    }
}
