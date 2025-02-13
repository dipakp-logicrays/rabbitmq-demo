<?php
declare(strict_types=1);

namespace Logicrays\RabbitMQ\Model\Order\Queue;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Area;
use Exception;

class Consumer
{
    public const SENDER_EMAIL   = 'trans_email/ident_general/email';
    public const SENDER_NAME   = 'trans_email/ident_general/name';
    public const SEND_ORDER_DETAILS_EMAIL_TEMPLATE = 'send_order_details/general/email';
    public const SEND_ORDER_DETAILS__EMAIL_RECIPIENTS = 'send_order_details/general/recipients_emails';

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param TransportBuilder $transportBuilder
     * @param Json $json
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        Json $json,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->json = $json;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
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

            $this->logger->info('Order details: ' . $message);

            $storeId = $orderData['store_id'];

            $template = $this->scopeConfig->getValue(
                self::SEND_ORDER_DETAILS_EMAIL_TEMPLATE,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $senderEmail= $this->scopeConfig->getValue(self::SENDER_EMAIL, ScopeInterface::SCOPE_STORE, $storeId);
            $senderName = $this->scopeConfig->getValue(self::SENDER_NAME, ScopeInterface::SCOPE_STORE, $storeId);
            $emails = $this->scopeConfig->getValue(
                self::SEND_ORDER_DETAILS__EMAIL_RECIPIENTS,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );

            $vars = [
                'increment_id' => $orderData['increment_id'],
                'order_data'  => $message,
            ];

            $transport = $this->transportBuilder->setTemplateIdentifier(
                $template
            )->setTemplateOptions(
                [
                    'area'  => Area::AREA_FRONTEND,
                    'store' => $storeId,
                ]
            )->setTemplateVars(
                $vars
            )->setFrom(
                [
                    'email' => $senderEmail,
                    'name' => $senderName
                ]
            )->addTo(
                $emails
            )->getTransport();
            $transport->sendMessage();

        } catch (\Exception $e) {
            $this->logger->error('Error processing order details queue: ' . $e->getMessage());
        }
    }
}
