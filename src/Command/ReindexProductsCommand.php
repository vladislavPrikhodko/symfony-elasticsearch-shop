<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\ProductRepository;
use App\Service\ProductIndexer;

#[AsCommand(
    name: 'app:reindex-products',
    description: 'Reindex all products into Elasticsearch',
)]
class ReindexProductsCommand extends Command
{
    public function __construct(
        private ProductRepository $productRepository,
        private ProductIndexer $indexer
    ) {
        parent::__construct();
    }

    
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {

        $products = $this->productRepository->findAll();

        foreach ($products as $product) {

            $this->indexer->index($product);

            $output->writeln(
                'Indexed product #' . $product->getId()
            );
        }

        return Command::SUCCESS;
    }
}
