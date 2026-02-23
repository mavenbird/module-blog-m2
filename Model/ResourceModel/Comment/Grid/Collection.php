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

namespace Mavenbird\Blog\Model\ResourceModel\Comment\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Mavenbird\Blog\Model\ResourceModel\Comment;
use Psr\Log\LoggerInterface as Logger;
use Zend_Db_Expr;

/**
 * Class Collection
 * @package Mavenbird\Blog\Model\ResourceModel\Comment\Grid
 */
class Collection extends SearchResult
{
    /**
     * Collection constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     *
     * @throws LocalizedException
     */
    // phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'mavenbird_blog_comment',
        $resourceModel = Comment::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->addPostName();
        $this->addCustomerName();

        $this->addExpressionFieldToSelect(
            'mb_created_at',
            'main_table.created_at',
            ['mb_created_at' => 'created_at']
        );

        return $this;
    }

    /**
     * @param string $field
     * @param string $direction
     *
     * @return SearchResult
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        switch ($field) {
            case 'customer_name':
                return parent::setOrder('user_name', $direction);
            case 'mb_created_at':
                return parent::setOrder('main_table.created_at', $direction);
        }

        return parent::setOrder($field, $direction);
    }

    /**
     * @param array|string $field
     * @param null $condition
     *
     * @return SearchResult
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'customer_name') {
            return parent::addFieldToFilter('user_name', $condition);
        }

        if ($field === 'post_name') {
            $field = 'mb.name';
        } elseif ($field === 'mb_created_at') {
            $field = 'main_table.created_at';
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @return $this
     */
    public function addPostName()
    {
        $this->getSelect()->joinLeft(
            ['mb' => $this->getTable('mavenbird_blog_post')],
            'main_table.post_id = mb.post_id',
            ['post_name' => 'name']
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function addCustomerName()
    {
        $this->getSelect()->joinLeft(
            ['ce' => $this->getTable('customer_entity')],
            'main_table.entity_id = ce.entity_id',
            ['firstname', 'lastname']
        )->columns([
            'customer_name' => new Zend_Db_Expr("CONCAT(`ce`.`firstname`,' ',`ce`.`lastname`)")
        ]);

        return $this;
    }
}
