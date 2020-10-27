<?php
declare(strict_types=1);

namespace Assessment\SimpleQueue\Console\Command;

use Assessment\SimpleQueue\Model\Product\Queue\Publisher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SimpleQueue
 *
 * This command will receive an SKU as a parameter and will queue it for execution.
 *
 * @package Assessment\SimpleQueue\Console\Command
 */
class SimpleQueue extends Command
{
    const PROD_ID_ARGUMENT = "product_id";

    /**
     * @var Publisher
     */
    private $publisher;

    /**
     * SimpleQueue constructor.
     * @param Publisher $publisher
     */
    public function __construct(
        Publisher $publisher
    ) {
        parent::__construct();
        $this->publisher = $publisher;
    }

    /**
     * Configures the command.
     * @return void
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::PROD_ID_ARGUMENT,
                null,
                InputOption::VALUE_REQUIRED,
                'Product Id with which to dispatch the message'
            ),
        ];

        $this->setName("assessment:simplequeue:dispatch");
        $this->setDescription("dispatches simple queue message given a Product Id.");
        $this->setDefinition($options);
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer|void|null
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $productId = (int) $input->getOption(self::PROD_ID_ARGUMENT);
        try {
            $this->publisher->publish($productId);
            $output->writeln(__('Product id "%1" was added to the queue.', $productId));
        } catch (\Magento\Framework\Exception\NoSuchEntityException $noSuchEntityException) {
            $output->writeln(__('Product id "%1" does not exist, message was not added to the queue.', $productId));
        }
    }
}
