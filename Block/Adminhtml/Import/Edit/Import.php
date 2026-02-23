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

namespace Mavenbird\Blog\Block\Adminhtml\Import\Edit;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\Messages;
use Mavenbird\Blog\Helper\Data as BlogHelper;
use Mavenbird\Blog\Model\Config\Source\Import\Type;

/**
 * Class Import
 * @package Mavenbird\Blog\Block\Adminhtml\Import\Edit
 */
class Import extends Template
{
    /**
     * @var BlogHelper
     */
    public $blogHelper;

    /**
     * @var Type
     */
    public $importType;

    /**
     * Before constructor.
     *
     * @param Context $context
     * @param BlogHelper $blogHelper
     * @param Type $importType
     * @param array $data
     */
    public function __construct(
        Context $context,
        BlogHelper $blogHelper,
        Type $importType,
        array $data = []
    ) {
        $this->blogHelper = $blogHelper;
        $this->importType = $importType;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getTypeSelector()
    {
        $types = [];
        foreach ($this->importType->toOptionArray() as $item) {
            $types[] = $item['value'];
        }
        array_shift($types);

        return BlogHelper::jsonEncode($types);
    }

    /**
     * @param $priority
     * @param $message
     *
     * @return string
     */
    public function getMessagesHtml($priority, $message)
    {
        /** @var $messagesBlock Messages */
        $messagesBlock = $this->_layout->createBlock(Messages::class);
        $messagesBlock->{$priority}(__($message));

        return $messagesBlock->toHtml();
    }

    /**
     * @return string
     */
    public function getImportButtonHtml()
    {
        $importUrl = $this->getUrl('mavenbird_blog/import/import');
        $html = '&nbsp;&nbsp;<button id="word-press-import" href="' . $importUrl .
            '" class="" type="button" onclick="mbBlogImport.importAction();">' .
            '<span><span><span>Import</span></span></span></button>';

        return $html;
    }
}
