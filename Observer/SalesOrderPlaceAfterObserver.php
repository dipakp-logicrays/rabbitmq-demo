<?php

declare(strict_types=1);

namespace Logicrays\RabbitMQ\Observer;

use Logicrays\RabbitMQ\Model\Order\Queue\Publisher;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SalesOrderPlaceAfterObserver implements ObserverInterface
{
    /**
     * @var Publisher
     */
    private $publisher;

    /**
     * @param Publisher $publisher
     */
    public function __construct(
        Publisher $publisher
    ) {
        $this->publisher = $publisher;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $order = $observer->getEvent()->getOrder();
        $this->publisher->publish($order);
    }
}
