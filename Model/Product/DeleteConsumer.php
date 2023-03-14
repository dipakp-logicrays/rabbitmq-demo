<?php

namespace Logicrays\RabbitMQ\Model\Product;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;

class DeleteConsumer
{
    /**
     * @var \Zend\Log\Logger
     */
    private $logger;

    /**
     * @var string
     */
    private $logFileName = 'product-delete-consumer.log';

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * DeleteConsumer constructor.
     * @param DirectoryList $directoryList
     * @throws FileSystemException
     */
    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->directoryList = $directoryList;
        $logDir = $directoryList->getPath('log');
        $writer = new \Zend\Log\Writer\Stream($logDir . DIRECTORY_SEPARATOR . $this->logFileName);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $this->logger = $logger;
    }

    /**
     * _processMessage
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function processMessage(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $this->logger->info($product->getId() . ' ' . $product->getSku());
    }
}
