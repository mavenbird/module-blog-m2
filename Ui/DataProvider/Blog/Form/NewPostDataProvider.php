<?php

namespace Mavenbird\Blog\Ui\DataProvider\Blog\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mavenbird\Blog\Model\ResourceModel\Post\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;

class NewPostDataProvider extends AbstractDataProvider
{
    protected $collection;
    protected $dataPersistor;
    protected $loadedData;
    protected $request;
    protected $storeManager;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection    = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->request       = $request;
        $this->storeManager    = $storeManager;
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $postId = (int) $this->request->getParam('id');

        foreach ($this->collection->getItems() as $post) {

            $data = $post->getData();

            if (isset($data['store_ids']) && !is_array($data['store_ids'])) {
                $data['store_ids'] = explode(',', $data['store_ids']);
            }

            // Prefill relations for UI multiselects
            try {
                $data['categories_ids'] = $post->getCategoryIds();
                $data['topics_ids']     = $post->getTopicIds();
                $data['tags_ids']       = $post->getTagIds();
            } catch (\Throwable $e) {
                $data['categories_ids'] = $data['categories_ids'] ?? [];
                $data['topics_ids']     = $data['topics_ids'] ?? [];
                $data['tags_ids']       = $data['tags_ids'] ?? [];
            }

            if ($post->getImage()) {
                $imageName = $post->getImage();
                $data['image'] = [
                    [
                        'name' => $imageName,
                        'url'  => $this->getMediaUrl($imageName)
                    ]
                ];
            }


            $this->loadedData[$post->getId()]['post'] = $data;
        }

        // NEW POST
        if (!$postId) {
            $this->loadedData[0]['post']['store_ids'] = ['1'];
        }

        return $this->loadedData;
    }

    protected function getMediaUrl($imageName)
{
    return $this->storeManager->getStore()->getBaseUrl(
        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
    ) . 'mavenbird/blog/post/' . $imageName;
}
}
