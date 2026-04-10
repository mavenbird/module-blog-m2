<?php

namespace Mavenbird\Blog\Block\Adminhtml\Post\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveDraftButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => __('Save as Draft'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'mavenbird_blog_post_form.mavenbird_blog_post_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'action' => 'draft'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'sort_order' => 88,
        ];
    }
}

