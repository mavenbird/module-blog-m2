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

namespace Mavenbird\Blog\Api\Data;

/**
 * Interface BlogConfigInterface
 * @package Mavenbird\Blog\Api\Data
 */
interface BlogConfigInterface
{
    const GENERAL = 'general';
    const SIDEBAR = 'sidebar';
    const SEO     = 'seo';

    /**
     * @return \Mavenbird\Blog\Api\Data\Config\GeneralInterface
     */
    public function getGeneral();

    /**
     * @param \Mavenbird\Blog\Api\Data\Config\GeneralInterface $value
     *
     * @return $this
     */
    public function setGeneral($value);

    /**
     * @return \Mavenbird\Blog\Api\Data\Config\SidebarInterface
     */
    public function getSidebar();

    /**
     * @param \Mavenbird\Blog\Api\Data\Config\SidebarInterface $value
     *
     * @return $this
     */
    public function setSidebar($value);

    /**
     * @return \Mavenbird\Blog\Api\Data\Config\SeoInterface
     */
    public function getSeo();

    /**
     * @param \Mavenbird\Blog\Api\Data\Config\SeoInterface $value
     *
     * @return $this
     */
    public function setSeo($value);
}
