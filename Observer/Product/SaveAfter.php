<?php declare(strict_types=1);

namespace Logicrays\RabbitMQ\Observer\Product;

use Logicrays\RabbitMQ\Model\MessageQueues\Product\Publisher as Publisher;
use Logicrays\RabbitMQ\Model\MessageQueues\Product\ProductSavePublisher as ProductSavePublisher;

class SaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var Publisher
     */
    private $_publisher;

    /**
     * @var ProductSavePublisher
     */
    private $productSavePublisher;

    /**
     * __construct function
     *
     * @param Publisher $publisher
     * @param ProductSavePublisher $productSavePublisher
     */
    public function __construct(
        Publisher $publisher,
        ProductSavePublisher $productSavePublisher
    ) {
        $this->_publisher = $publisher;
        $this->productSavePublisher = $productSavePublisher;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $_product = $observer->getProduct();

        $_data=[
            'id' => $_product->getId(),
            'sku' => $_product->getSku(),
            'name' => $_product->getName(),
            'comment' => 'Product Saved!!'
        ];

        $this->_publisher->execute(
            json_encode($_data)
        );

        $this->productSavePublisher->execute(json_encode($_data));
    }
}
