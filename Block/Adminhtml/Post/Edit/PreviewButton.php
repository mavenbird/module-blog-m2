<?php
namespace Mavenbird\Blog\Block\Adminhtml\Post\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Store\Model\StoreManagerInterface;

class PreviewButton implements ButtonProviderInterface
{
    protected $context;
    protected $storeManager;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager
    ) {
        $this->context      = $context;
        $this->storeManager = $storeManager;
    }

    public function getButtonData()
    {
        $postId = $this->context->getRequest()->getParam('id');
        if (!$postId) {
            return [];
        }

        $store = $this->storeManager->getStore();
        $previewUrl = rtrim($store->getBaseUrl(UrlInterface::URL_TYPE_WEB), '/')
            . '/mbblog/post/preview?id=' . (int)$postId;

        return [
            'label'      => __('Preview'),
            'class'      => 'preview',
            'on_click'   => sprintf("window.open('%s', '_blank')", $previewUrl),
            'sort_order' => 70,
        ];
    }
}