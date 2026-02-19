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

namespace Mavenbird\Blog\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mavenbird\Blog\Model\AuthorFactory;

/**
 * Class Author
 * @package Mavenbird\Blog\Controller\Adminhtml
 */
abstract class Author extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Mavenbird_Blog::author';

    /**
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @var AuthorFactory
     */
    public $authorFactory;

    /**
     * Author constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param AuthorFactory $authorFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        AuthorFactory $authorFactory
    ) {
        $this->authorFactory = $authorFactory;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     *
     * @return bool|\Mavenbird\Blog\Model\Author
     */
    public function initAuthor($register = false)
    {
        $authorId = (int)$this->getRequest()->getParam('id');

        /** @var \Mavenbird\Blog\Model\Author $author */
        $author = $this->authorFactory->create();
        if ($authorId) {
            $author->load($authorId);
            if (!$author->getId()) {
                $this->messageManager->addErrorMessage(__('This author no longer exists.'));

                return false;
            }
        }

        if ($register) {
            $this->coreRegistry->register('mavenbird_blog_author', $author);
        }

        return $author;
    }
}
