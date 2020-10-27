<?php

namespace Assessment\SimpleQueue\Model\Product\Queue;

use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

/**
 * The purpose of this class is to attend the messages posted to the Queue simple_queue_log_product_sku.
 *
 * Class Consumer
 * @package Assessment\SimpleQueue\Model\Product\Queue
 */
class Consumer
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Consumer constructor.
     * @param LoggerInterface $logger
     * @param EntityManager $entityManager
     * @param SerializerInterface $serializer
     */
    public function __construct(
        LoggerInterface $logger,
        EntityManager $entityManager,
        SerializerInterface $serializer
    ) {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    /**
     * Process the queue list for log product sku.
     *
     * @param OperationInterface $operation
     * @throws \Exception In case the process is not able to work on the Operation.
     * @return void
     */
    public function process(OperationInterface $operation): void
    {
        try {
            $serializedData = $operation->getSerializedData();
            $data = $this->serializer->unserialize($serializedData);
            // This is where the actual logic of the Consumer gets executed.
            $this->execute($data);
        } catch (\Exception $e) {
            $status = OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED;
            $errorCode = $e->getCode();
            $message = __('Sorry, something went wrong during product sku logging.');
        }

        // Set the status of the operation and save it.
        // If there was an exception the status should be Failed, Complete otherwise.
        $operation->setStatus($status ?? OperationInterface::STATUS_TYPE_COMPLETE)
            ->setErrorCode($errorCode ?? null)
            ->setResultMessage($message ?? null);

        $this->entityManager->save($operation);
    }

    /**
     * Execute the logging
     *
     * @param array $data
     * @return void
     */
    private function execute(array $data): void
    {
        // logs into /var/log/consumer.log file the sku.
        $this->logger->log(\Monolog\Logger::INFO, __('Product Sku: ') . $data['sku']);
    }
}
