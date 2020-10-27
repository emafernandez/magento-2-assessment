<?php

namespace Assessment\SimpleQueue\Model\Product\Queue;

use Magento\AsynchronousOperations\Api\Data\OperationInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Publisher
 *
 * This class' purpose is to publish a message to the message queue 'simple_queue_log_product_sku'.
 *
 * @package Assessment\SimpleQueue\Model\Product\Queue
 */
class Publisher
{
    const QUEUE_NAME = 'simple.queue.log.product.sku';

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var PublisherInterface
     */
    private $publisher;
    /**
     * @var OperationInterfaceFactory
     */
    private $operationFactory;
    /**
     * @var IdentityGeneratorInterface
     */
    private $identityService;

    /**
     * Publisher constructor.
     * @param IdentityGeneratorInterface $identityService
     * @param OperationInterfaceFactory $operationFactory
     * @param PublisherInterface $publisher
     * @param ProductRepositoryInterface $productRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(
        IdentityGeneratorInterface $identityService,
        OperationInterfaceFactory $operationFactory,
        PublisherInterface $publisher,
        ProductRepositoryInterface $productRepository,
        SerializerInterface $serializer
    ) {
        $this->productRepository = $productRepository;
        $this->serializer = $serializer;
        $this->publisher = $publisher;
        $this->operationFactory = $operationFactory;
        $this->identityService = $identityService;
    }

    /**
     * This method will publish the message to the queue for processing.
     *
     * @param integer $productId
     * @throws NoSuchEntityException If product id doesn't map to any product in the catalog.
     * @return void
     */
    public function publish(int $productId)
    {
        $product = $this->productRepository->getById($productId);

        // Constructing the data array needed to create a new operation.
        // Bulk UUID is not needed right now since we are publishing 1 message at the time, but logic can be added
        // to create bulk operations.
        $data = [
            'data' => [
                'bulk_uuid' => $this->identityService->generateId(),
                'topic_name' => self::QUEUE_NAME,
                'serialized_data' => $this->serializer->serialize(['sku' => $product->getSku()]),
                'status' => \Magento\Framework\Bulk\OperationInterface::STATUS_TYPE_OPEN,
            ]
        ];

        // creation of the operation for the publisher.
        $operation = $this->operationFactory->create($data);
        $this->publisher->publish(self::QUEUE_NAME, $operation);
    }
}
