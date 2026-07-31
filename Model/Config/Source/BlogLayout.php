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

/**
 * Class BlogLayout
 * @package Mavenbird\Blog\Model\Config\Source
 */
class BlogLayout implements ArrayInterface
{
    const LEFT = '2columns-left';
    const RIGHT = '2columns-right';
    const ONECOLUMN = '1column';
    const THREECOLUMNS = '3columns';
    const FULLWIDTH = '1column-fullwidth';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::LEFT,
                'label' => __('Left')
            ],
            [
                'value' => self::RIGHT,
                'label' => __('Right')
            ],
            [
                'value' => self::ONECOLUMN,
                'label' => __('One Column')
            ],
            [
                'value' => self::THREECOLUMNS,
                'label' => __('3 Columns')
            ],
            [
                'value' => self::FULLWIDTH,
                'label' => __('Full Width')
            ]
        ];
    }
}
