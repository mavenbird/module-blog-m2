<?php

namespace Mavenbird\Blog\Block\Adminhtml\Post\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveAndAddHistoryButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => __('Save & Add History'),
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
                                        'action' => 'add'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'sort_order' => 89,
        ];
    }
}

