<?php
/**
 * Copyright © MageKey. All rights reserved.
 */
namespace MageKey\ConfigurableMatrixTable\Block\Product;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\ConfigurableProduct\Api\Data\OptionInterface;

class MatrixTable extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'MageKey_ConfigurableMatrixTable::product/matrixtable.phtml';

    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setXIndex($this->getReverse() ? 1 : 0);
        $this->setYIndex($this->getReverse() ? 0 : 1);
    }

    /**
     * Retrieve product instance
     *
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }

    /**
     * Check if attributes exists
     *
     * @return bool
     */
    public function hasMatrixData()
    {
        $attributes = $this->getMatrixAttributes();
        return !empty($attributes);
    }

    /**
     * Retrieve matrix value
     *
     * @return string
     */
    public function getMatrixValue($xValue, $yValue)
    {
        $productCollection = $this->getMatrixProductCollection();
        $xAttributeName = $this->getXAttribute()->getProductAttribute()->getName();
        $yAttributeName = $this->getYAttribute()->getProductAttribute()->getName();
        foreach ($productCollection as $product) {
            if ($product->getData($xAttributeName) == $xValue
                && $product->getData($yAttributeName) == $yValue) {
                return $product->getFinalPrice();
            }
        }
        return '';
    }

    /**
     * Retrieve matrix x attribute
     *
     * @return OptionInterface|null
     */
    public function getXAttribute()
    {
        $attributes = $this->getMatrixAttributes();
        return isset($attributes[$this->getXIndex()])
            ? $attributes[$this->getXIndex()]
            : null;
    }

    /**
     * Retrieve matrix x attribute
     *
     * @return OptionInterface|null
     */
    public function getYAttribute()
    {
        $attributes = $this->getMatrixAttributes();
        return isset($attributes[$this->getYIndex()])
            ? $attributes[$this->getYIndex()]
            : null;
    }

    /**
     * Retrieve matrix attributes
     *
     * @return array
     */
    protected function getMatrixAttributes()
    {
        if (!$this->hasMatrixAttributes()) {
            $attributes = [];
            if ($product = $this->getProduct()) {
                if ($product->getTypeId() == Configurable::TYPE_CODE) {
                    $collection = $product->getTypeInstance()->getConfigurableAttributes($product);
                    if ($collection->getSize() == 2) {
                        foreach ($collection as $attribute) {
                            $attributes[] = $attribute;
                        }
                    }
                }
            }
            $this->setData('matrix_attributes', $attributes);
        }
        return $this->getData('matrix_attributes');
    }

    /**
     * Retrieve matrix product collection
     *
     * @return \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\Collection
     */
    protected function getMatrixProductCollection()
    {
        if (!$this->hasMatrixProductCollection()) {
            $productCollection = $this->getProduct()
                ->getTypeInstance()
                ->getUsedProductCollection($this->getProduct());
            $attributes = $this->getMatrixAttributes();
            foreach ($attributes as $attribute) {
                $productCollection->addAttributeToSelect($attribute->getProductAttribute()->getName());
            }
            $productCollection->addFinalPrice();
            $this->setData('matrix_product_collection', $productCollection);
        }
        return $this->getData('matrix_product_collection');
    }
}
