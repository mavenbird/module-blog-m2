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

namespace Mavenbird\Blog\Model\Config\Source\DateFormat;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class TypeMonth
 * @package Mavenbird\Blog\Model\Config\Source\DateFormat
 */
class TypeMonth implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $dateArray = [];
        $type = ['F , Y', 'Y - m', 'm / Y', 'M  Y'];
        foreach ($type as $item) {
            $dateArray [] = [
                'value' => $item,
                'label' => $item . ' (' . date($item) . ')'
            ];
        }

        return $dateArray;
    }
}
