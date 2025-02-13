<?php
declare(strict_types=1);

namespace Logicrays\RabbitMQ\Model\Order\Queue;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Consumer
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param TransportBuilder $transportBuilder
     * @param Json $json
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        Json $json,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->json = $json;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * Process message from queue
     *
     * @param string $message
     * @return void
     */
    public function processMessage(string $message): void
    {
        try {
            $orderData = $this->json->unserialize($message);
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/send_order_details.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info('Order details: ' . $message);
            // $transport = $this->transportBuilder
            //     ->setTemplateIdentifier('order_details_email_template')
            //     ->setTemplateOptions([
            //         'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            //         'store' => $this->storeManager->getStore()->getId()
            //     ])
            //     ->setTemplateVars([
            //         'order_data' => $message // Sending raw JSON
            //     ])
            //     ->setFrom([
            //         'email' => 'sales@example.com',
            //         'name' => 'Sales'
            //     ])
            //     ->addTo($orderData['customer_email'])
            //     ->getTransport();

            // $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->error('Error processing order details queue: ' . $e->getMessage());
        }
    }
}