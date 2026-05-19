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
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;

/**
 * Class ListOfStaticBlock
 * @package Mavenbird\Blog\Model\Config\Source
 */
class ListOfStaticBlock implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    protected $blockCollectionFactory;

    /**
     * ListOfStaticBlock constructor.
     * @param CollectionFactory $blockCollectionFactory
     */
    public function __construct(CollectionFactory $blockCollectionFactory)
    {
        $this->blockCollectionFactory = $blockCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = [
            'value' => '',
            'label' => __('--Please Select--')
        ];
        $collection = $this->blockCollectionFactory->create();
        foreach ($collection as $block) {
            $options[] = [
                'value' => $block->getIdentifier(),
                'label' => $block->getTitle()
            ];
        }
        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = [];
        $options[''] = __('--Please Select--');
        $collection = $this->blockCollectionFactory->create();
        foreach ($collection as $block) {
            $options[$block->getIdentifier()] = $block->getTitle();
        }
        return $options;
    }
}