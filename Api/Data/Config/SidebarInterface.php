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

namespace Mavenbird\Blog\Api\Data\Config;

/**
 * Interface SidebarInterface
 * @package Mavenbird\Blog\Api\Data\Config
 */
interface SidebarInterface
{
    const NUMBER_RECENT    = 'number_recent';
    const NUMBER_MOST_VIEW = 'number_most_view';

    /**
     * @return string/null
     */
    public function getNumberRecent();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setNumberRecent($value);

    /**
     * @return string/null
     */
    public function getNumberMostView();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setNumberMostView($value);
}
