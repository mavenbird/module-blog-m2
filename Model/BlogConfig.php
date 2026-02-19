<?php
/**
 * Mavenbird
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mavenbird.com license that is
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

namespace Mavenbird\Blog\Model;

use Magento\Framework\DataObject;
use Mavenbird\Blog\Api\Data\BlogConfigInterface;

/**
 * Class BlogConfig
 * @package Mavenbird\Blog\Model
 */
class BlogConfig extends DataObject implements BlogConfigInterface
{

    /**
     * {@inheritdoc}
     */
    public function getGeneral()
    {
        return $this->getData(self::GENERAL);
    }

    /**
     * {@inheritdoc}
     */
    public function setGeneral($value)
    {
        $this->setData(self::GENERAL, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSidebar()
    {
        return $this->getData(self::SIDEBAR);
    }

    /**
     * {@inheritdoc}
     */
    public function setSidebar($value)
    {
        $this->setData(self::SIDEBAR, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSeo()
    {
        return $this->getData(self::SEO);
    }

    /**
     * {@inheritdoc}
     */
    public function setSeo($value)
    {
        $this->setData(self::SEO, $value);

        return $this;
    }
}
