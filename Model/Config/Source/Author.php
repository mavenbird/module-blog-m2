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
 * @package     Mavenbird_Faqs
 * @copyright   Copyright (c) Mavenbird (https://www.mavenbird.com/)
 * @license     https://www.mavenbird.com/LICENSE.txt
 */

namespace Mavenbird\Blog\Model\Config\Source;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Option\ArrayInterface;
use Mavenbird\Blog\Model\AuthorFactory;

/**
 * Class Author
 * @package Mavenbird\Faqs\Model\Config\Source
 */
class Author implements ArrayInterface
{
    /**
     * @var AuthorFactory
     */
    public $_authorFactory;

    public function __construct(
        AuthorFactory $authorFactory
    ) {
        $this->_authorFactory = $authorFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getAuthors() as $value => $author) {
            $options[] = [
                'value' => $value,
                'label' => $author->getName()
            ];
        }

        return $options;
    }

    /**
     * @return AbstractCollection
     */
    public function getAuthors()
    {
        return $this->_authorFactory->create()->getCollection()->addFieldToFilter('status', '1');
    }
}
