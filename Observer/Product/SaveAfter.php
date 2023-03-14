<?php declare(strict_types=1);

namespace Logicrays\RabbitMQ\Observer\Product;

use Logicrays\RabbitMQ\Model\MessageQueues\Product\Publisher as Publisher;

class SaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var Publisher
     */
    private $_publisher;

    /**
     * __construct function
     *
     * @param Publisher $publisher
     */
    public function __construct(
        Publisher $publisher
    ) {
        $this->_publisher = $publisher;
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
            'sku' => $_product->getSku(),
            'comment' => 'saved'
        ];

        $this->_publisher->execute(
            json_encode($_data)
        );
    }
}
