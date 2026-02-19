<?php
/**
 * Mavenbird
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mavenbird.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mavenbird.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mavenbird
 * @package     Mavenbird_Blog
 * @copyright   Copyright (c) Mavenbird (https://www.mavenbird.com/)
 * @license     https://www.mavenbird.com/LICENSE.txt
 */

namespace Mavenbird\Blog\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Mavenbird\Blog\Model\ResourceModel\Category\CollectionFactory;

/**
 * Class WidgetCategory
 * @package Mavenbird\Blog\Model\Config\Source\Widget
 */
class WidgetCategory implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    private $category;

    /**
     * WidgetCategory constructor.
     *
     * @param CollectionFactory $category
     */
    public function __construct(
        CollectionFactory $category
    ) {
        $this->category = $category;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->category->create();
        $ar = [];
        foreach ($collection->getItems() as $item) {
            if ($item->getId() === '1') {
                continue;
            }
            $ar[] = ['value' => $item->getId(), 'label' => $item->getName()];
        }

        return $ar;
    }
}
