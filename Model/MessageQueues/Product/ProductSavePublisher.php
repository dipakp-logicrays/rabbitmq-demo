<?php declare(strict_types=1);

namespace Logicrays\RabbitMQ\Model\MessageQueues\Product;

use Magento\Framework\MessageQueue\PublisherInterface;

class ProductSavePublisher
{
    public const TOPIC_NAME = 'syncLrProductTopic';

    /**
     * @var \Magento\Framework\MessageQueue\PublisherInterface
     */
    private $publisher;

    /**
     * @param \Magento\Framework\MessageQueue\PublisherInterface $publisher
     */
    public function __construct(\Magento\Framework\MessageQueue\PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $_data)
    {
        $this->publisher->publish(self::TOPIC_NAME, $_data);
    }
}
