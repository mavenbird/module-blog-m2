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
use Mavenbird\Blog\Api\Data\MonthlyArchiveInterface;

/**
 * Class MonthlyArchive
 * @package Mavenbird\Blog\Model
 */
class MonthlyArchive extends DataObject implements MonthlyArchiveInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($value)
    {
        $this->setData(self::LABEL, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPostCount()
    {
        return $this->getData(self::POST_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setPostCount($value)
    {
        $this->setData(self::POST_COUNT, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLink()
    {
        return $this->getData(self::LINK);
    }

    /**
     * {@inheritdoc}
     */
    public function setLink($value)
    {
        $this->setData(self::LINK, $value);

        return $this;
    }
}
