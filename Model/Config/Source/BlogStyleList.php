<?php

namespace Mavenbird\Blog\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class BlogStyleList implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Style 1')],
            ['value' => '2', 'label' => __('Style 2')]
        ];
    }
}