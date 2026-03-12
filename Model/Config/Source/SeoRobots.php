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
 * Class Display
 * @package Mavenbird\Blog\Model\Config\Source\Blogview
 */
class SeoRobots implements ArrayInterface
{
    const INDEX_FOLLOW = 'INDEX,FOLLOW';
    const FOLLOW = 'FOLLOW';
    const NOINDEX_FOLLOW = 'NOINDEX,FOLLOW';
    const INDEX_NOFOLLOW = 'INDEX,NOFOLLOW';
    const NOINDEX_NOFOLLOW = 'NOINDEX,NOFOLLOW';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->toArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
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
        return [
            self::INDEX_FOLLOW => __('Index, Follow'),
            self::FOLLOW => __('Follow'),
            self::NOINDEX_FOLLOW => __('Noindex, Follow'),
            self::INDEX_NOFOLLOW => __('Index, Nofollow'),
            self::NOINDEX_NOFOLLOW => __('Noindex, Nofollow')
        ];
    }
}
