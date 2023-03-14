<?php

namespace Logicrays\RabbitMQ\Model\Product;

class DeletePublisher
{
    public const TOPIC_NAME = 'logicrays.product.delete';

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
    public function execute(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $this->publisher->publish(self::TOPIC_NAME, $product);
    }
}
