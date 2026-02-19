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

namespace Mavenbird\Blog\Block\MonthlyArchive;

use Magento\Framework\Exception\LocalizedException;
use Mavenbird\Blog\Helper\Data;
use Mavenbird\Blog\Model\ResourceModel\Post\Collection;

/**
 * Class Listpost
 * @package Mavenbird\Blog\Block\MonthlyArchive
 */
class Listpost extends \Mavenbird\Blog\Block\Listpost
{
    /**
     * @return Collection|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCollection()
    {
        return $this->helperData->getPostCollection(Data::TYPE_MONTHLY, $this->getMonthKey());
    }

    /**
     * @return mixed
     */
    protected function getMonthKey()
    {
        return $this->getRequest()->getParam('month_key');
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    protected function getMonthLabel()
    {
        return $this->helperData->getDateFormat($this->getMonthKey() . '-10', true);
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb($this->getMonthKey(), [
                'label' => __('Monthy Archive'),
                'title' => __('Monthy Archive')
            ]);
        }
    }

    /**
     * @param bool $meta
     *
     * @return array|false|string
     */
    public function getBlogTitle($meta = false)
    {
        $blogTitle = parent::getBlogTitle($meta);

        if ($meta) {
            array_push($blogTitle, $this->getMonthLabel());

            return $blogTitle;
        }

        return $this->getMonthLabel();
    }
}
