<?php

declare(strict_types=1);

namespace Logicrays\RabbitMQ\Model\Order\Queue;

use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Sales\Api\Data\OrderInterface;

class Publisher
{
    private const TOPIC_NAME = 'logicrays.order.details.topic';

    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @param PublisherInterface $publisher
     */
    public function __construct(
        PublisherInterface $publisher
    ) {
        $this->publisher = $publisher;
    }

    /**
     * Publish order details to queue
     *
     * @param OrderInterface $order
     * @return void
     */
    public function publish(OrderInterface $order): void
    {
        // Get all information related to order
        $orderData = $this->getOrderInformation($order);

        // Get customer data
        $customerData =  $this->getCustomerData($order);

        // Get order items
        $orderOrderItems = $this->getOrderItems($order);

        // Get order shipping address
        $orderShippingAddressData = $this->getOrderShippingAddress($order);

        // Get order billing address
        $orderBillingddressData = $this->getOrderBillingAddress($order);

        // Get order shipping information
        $orderShippingData = $this->getOrderShippingInformation($order);

        // Get order payment information
        $orderPaymentData = $this->getOrderPaymentInformation($order);

        // Create an array for order information
        $orderDataArray = $orderData;
        $orderDataArray["customer"] = $customerData;
        $orderDataArray["items"] = $orderOrderItems;
        $orderDataArray["shipping_address"] = $orderShippingAddressData;
        $orderDataArray["billing_address"] = $orderBillingddressData;
        $orderDataArray["shipping"] = $orderShippingData;
        $orderDataArray["payment"] = $orderPaymentData;

        // Prepare order data in json format
        $orderJson = json_encode($orderDataArray, JSON_PRETTY_PRINT);

        $this->publisher->publish(self::TOPIC_NAME, $orderJson);
    }

    /**
     * Get Order Information function
     *
     * @param OrderInterface $order
     * @return array
     */
    public function getOrderInformation($order)
    {
        $orderData = [];
        $orderData = [
            "order_id" => $order->getEntityId(),
            "increment_id" => $order->getIncrementId(),
            "customer_email" => $order->getCustomerEmail(),
            "order_state" => $order->getState(),
            "order_status" => $order->getStatus(),
            "order_total" => $order->getGrandTotal(),
            "order_subtotal" => $order->getSubtotal(),
            "total_qty_ordered" => $order->getTotalQtyOrdered(),
            "order_currency" => $order->getOrderCurrencyCode(),
            "store_id" => $order->getStoreId(),
            "remote_ip" => $order->getRemoteIp()
        ];
        return $orderData;
    }

    /**
     * Get Order Shipping Information function
     *
     * @param OrderInterface $order
     * @return array
     */
    public function getOrderShippingAddress($order)
    {
        $orderShippingAddress = [];
        /* check order is not virtual */
        if (!$order->getIsVirtual()) {
            $shippingAddress = $order->getShippingAddress();
            $street = $shippingAddress->getStreet();
            $orderShippingAddress = [
                "firstname" => $shippingAddress->getFirstname(),
                "lastname" => $shippingAddress->getLastname(),
                "street_1" => isset($street[0])? $street[0]:"",
                "street_2" => isset($street[1])? $street[1]:"",
                "city" => $shippingAddress->getCity(),
                "region_id" => $shippingAddress->getRegionId(),
                "region" => $shippingAddress->getRegion(),
                "postcode" => $shippingAddress->getPostcode(),
                "country_id" => $shippingAddress->getCountryId(),
            ];
        }
        return $orderShippingAddress;
    }

    /**
     * Get Order Billing Information function
     *
     * @param OrderInterface $order
     * @return array
     */
    public function getOrderBillingAddress($order)
    {
        $orderBillingAddress = [];
        $billingAddress = $order->getBillingAddress();
        $street = $billingAddress->getStreet();
        $orderBillingAddress = [
            "firstname" => $billingAddress->getFirstname(),
            "lastname" => $billingAddress->getLastname(),
            "street_1" => isset($street[0])? $street[0]:"",
            "street_2" => isset($street[1])? $street[1]:"",
            "city" => $billingAddress->getCity(),
            "region_id" => $billingAddress->getRegionId(),
            "region" => $billingAddress->getRegion(),
            "postcode" => $billingAddress->getPostcode(),
            "country_id" => $billingAddress->getCountryId(),
        ];
        return $orderBillingAddress;
    }

    /**
     * Get Customer Information function
     *
     * @param OrderInterface $order
     * @return array
     */
    public function getCustomerData($order)
    {
        $customerId= $order->getCustomerId();
        $customerEmail = $order->getCustomerEmail();

        $customerData = [
            "guestCustomer" => $order->getCustomerIsGuest(),
            "customer_id" => $customerId,
            "email" => $customerEmail,
            "groupId " => $order->getCustomerGroupId(),
            "firstname" => $order->getCustomerFirstname(),
            "middlename" => $order->getCustomerMiddlename(),
            "lastname" => $order->getCustomerLastname(),
            "prefix" => $order->getCustomerPrefix(),
            "suffix" => $order->getCustomerSuffix(),
            "dob" => $order->getCustomerDob(),
            "taxvat" => $order->getCustomerTaxvat(),
        ];
        return $customerData;
    }

    /**
     * Get Order Payment Information function
     *
     * @param OrderInterface $order
     * @return array
     */
    public function getOrderPaymentInformation($order)
    {
        // Get Order Payment
        $payment = $order->getPayment();
        $paymentData = [
            "method_code" => $payment->getMethod(),
            "method_title" => $payment->getAdditionalInformation()['method_title'],
            "amount_paid" => $payment->getAmountPaid(),
            "amount_ordered" => $payment->getAmountOrdered(),
        ];
        return $paymentData;
    }

    /**
     * Get Order Shipping Information function
     *
     * @param OrderInterface $order
     * @return array
     */
    public function getOrderShippingInformation($order)
    {
        $shippingData = [
            "method_code" => $order->getShippingMethod(),
            "method_title" => $order->getShippingDescription(),
            "shipping_amount" => $order->getShippingAmount(),
            "shipping_discount_amount" => $order->getShippingDiscountAmount(),
            "shipping_tax_amount" => $order->getShippingTaxAmount(),
        ];
        return $shippingData;
    }

    /**
     * Get Order Items function
     *
     * @param OrderInterface $order
     * @return array
     */
    public function getOrderItems($order)
    {
        $customerId = $order->getCustomerId();
        // Get Order Items
        $orderItems = $order->getAllItems();
        $items = [];
        foreach ($orderItems as $item) {
            $items [] = [
                "item_id" => $item->getItemId(),
                "order_id" => $item->getOrderId(),
                "parent_item_id" => $item->getParentItemId(),
                "quote_item_id" => $item->getQuoteItemId(),
                "store_id" => $item->getStoreId(),
                "product_id" => $item->getProductId(),
                "sku" => $item->getSku(),
                "name" => $item->getName(),
                "product_type" => $item->getProductType(),
                "is_virtual" => $item->getIsVirtual(),
                "weight" => $item->getWeight(),
                "qty_ordered" => $item->getQtyOrdered(),
                "item_price" => $item->getPrice()
            ];
        }
        return $items;
    }
}
