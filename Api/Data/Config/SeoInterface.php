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
 * Interface SeoInterface
 * @package Mavenbird\Blog\Api\Data\Config
 */
interface SeoInterface
{
    const META_TITLE       = 'meta_title';
    const META_DESCRIPTION = 'meta_description';

    /**
     * @return string/null
     */
    public function getMetaTitle();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMetaTitle($value);

    /**
     * @return string/null
     */
    public function getMetaDescription();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMetaDescription($value);
}
