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

namespace Mavenbird\Blog\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\ValidatorException;

/**
 * Class FeaturedCategories
 * @package Mavenbird\Blog\Model\Config\Backend
 */
class FeaturedCategories extends Value
{
    /**
     * @return Value|void
     * @throws ValidatorException
     */
    public function beforeSave()
    {
        // Add debugging to check if this is being called
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/featured_categories.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('FeaturedCategories backend model called. Value before processing: ' . $this->getValue());
        
        if (!empty($this->getValue())) {
            // Ensure the value is a comma-separated string of integers
            $valueArray = explode(',', $this->getValue());
            $valueArray = array_map('intval', $valueArray);
            $valueArray = array_filter($valueArray, function($id) {
                return $id > 0;
            });
            $this->setValue(implode(',', $valueArray));
            $logger->info('Value after processing: ' . $this->getValue());
        } else {
            $logger->info('Value is empty, saving as empty string');
        }
        parent::beforeSave();
    }
}