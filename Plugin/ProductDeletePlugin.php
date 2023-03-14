<?php

namespace Logicrays\RabbitMQ\Plugin;

use Logicrays\RabbitMQ\Model\Product\DeletePublisher;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;

class ProductDeletePlugin
{
    /**
     * @var DeletePublisher
     */
    private $productDeletePublisher;

    /**
     * __construct function
     *
     * @param DeletePublisher $productDeletePublisher
     */
    public function __construct(
        DeletePublisher $productDeletePublisher
    ) {
        $this->productDeletePublisher = $productDeletePublisher;
    }

    /**
     * _afterDelete
     *
     * @param ProductResource $subject
     * @param ProductResource $result
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return ProductResource
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDelete(
        ProductResource $subject,
        ProductResource $result,
        \Magento\Catalog\Api\Data\ProductInterface $product
    ) {
        $this->productDeletePublisher->execute($product);
        return $result;
    }
}
