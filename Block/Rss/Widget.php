<?php

namespace Mavenbird\Blog\Block\Rss;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mavenbird\Blog\Helper\Data as HelperData;

class Widget extends Template
{
    /**
     * @var HelperData
     */
    protected $helper;

    public function __construct(
        Context $context,
        HelperData $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Get RSS URL
     */
    public function getRssUrl($type = 'post/rss')
    {
        $url = $this->helper->getUrl(
            $this->helper->getRoute() . '/' . $type
        );

        return rtrim($url, '/') . '.xml';
    }
}