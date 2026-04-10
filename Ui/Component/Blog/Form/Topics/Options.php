<?php
declare(strict_types=1);

namespace Mavenbird\Blog\Ui\Component\Blog\Form\Topics;

use Magento\Framework\Data\OptionSourceInterface;
use Mavenbird\Blog\Model\ResourceModel\Topic\CollectionFactory;

class Options implements OptionSourceInterface
{
    public function __construct(
        private readonly CollectionFactory $collectionFactory
    ) {
    }

    public function toOptionArray(): array
    {
        $collection = $this->collectionFactory->create();

        $options = [];
        foreach ($collection as $topic) {
            $options[] = [
                'value' => (int) $topic->getId(),
                'label' => (string) $topic->getName(),
            ];
        }

        return $options;
    }
}

