<?php declare(strict_types=1);

namespace Logicrays\RabbitMQ\Model\MessageQueues\Product;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;

class Consumer
{
    /**
     * @var \Zend\Log\Logger
     */
    private $logger;

    /**
     * @var string
     */
    private $logFileName = 'product-save-consumer.log';

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * Consumer constructor.
     *
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
     * @param string $_data
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
   
    public function processMessage(string $_data)
    {
        $this->logger->info($_data);
    }
}
