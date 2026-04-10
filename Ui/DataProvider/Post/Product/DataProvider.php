<?php
namespace Mavenbird\Blog\Ui\DataProvider\Post\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    protected $collection;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->collection->addAttributeToSelect(['sku']);
        $this->collection->getSelect()->joinLeft(
            ['mb_p' => $this->collection->getTable('mavenbird_blog_post_product')],
            'e.entity_id = mb_p.entity_id',
            ['position']
        )->group('e.entity_id');
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getField() === 'post_id') {
            // filter is passed via params, not field filter
            return;
        }
        parent::addFilter($filter);
    }
}