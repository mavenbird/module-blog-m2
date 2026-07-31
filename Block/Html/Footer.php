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

namespace Mavenbird\Blog\Block\Html;

use Magento\Framework\View\Element\Html\Link;
use Magento\Framework\View\Element\Template\Context;
use Mavenbird\Blog\Helper\Data;

/**
 * Class Footer
 * @package Mavenbird\Blog\Block\Html
 */
class Footer extends Link
{
    /**
     * @var Data
     */
    public $helper;

    /**
     * @var string
     */
    protected $_template = 'Mavenbird_Blog::html\footer.phtml';

    /**
     * Footer constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->helper->getBlogUrl('');
    }

    /**
     * @return \Magento\Framework\Phrase|mixed|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLabel()
    {
        return $this->helper->getBlogName($this->helper->getCurrentStoreId()) ? : __('Blog');
    }

    /**
     * @return Data
     */
    public function getHelperData()
    {
        return $this->helper;
    }
}
