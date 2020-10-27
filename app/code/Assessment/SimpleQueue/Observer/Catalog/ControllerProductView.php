<?php
declare(strict_types=1);

namespace Assessment\SimpleQueue\Observer\Catalog;

use Assessment\SimpleQueue\Model\Product\Queue\Publisher;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * The purpose of this class is to catch the event of a product view and create a new message in the queue.
 * Class ControllerProductView
 * @package Assessment\SimpleQueue\Observer\Catalog
 */
class ControllerProductView implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var Publisher
     */
    private $publisher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ControllerProductView constructor.
     * @param LoggerInterface $logger
     * @param Publisher $publisher
     */
    public function __construct(
        LoggerInterface $logger,
        Publisher $publisher
    ) {
        $this->publisher = $publisher;
        $this->logger = $logger;
    }

    /**
     * This observer gets called during Product Detail Page view, triggering the publish for the message queue.
     * @param Observer $observer
     * @return void
     */
    public function execute(
        Observer $observer
    ) {
        $product = $observer->getData('product');
        try {
            $this->publisher->publish($product->getId());
        } catch (NoSuchEntityException $e) {
            $this->logger->warning('Assessment/SimpleQueue', ['exception' => $e]);
        }
    }
}
