<?php

namespace Mavenbird\Blog\Model\Sitemap;

use Magento\Sitemap\Model\ItemProvider\ItemProviderInterface;
use Magento\Sitemap\Model\SitemapItemInterfaceFactory;
use Mavenbird\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Mavenbird\Blog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

class Blog implements ItemProviderInterface
{
    private $postCollectionFactory;
    private $categoryCollectionFactory;
    private $sitemapItemFactory;

    public function __construct(
        PostCollectionFactory $postCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        SitemapItemInterfaceFactory $sitemapItemFactory
    ) {
        $this->postCollectionFactory = $postCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->sitemapItemFactory = $sitemapItemFactory;
    }

    public function getItems($storeId): array
    {
        $items = [];

        /** BLOG POSTS */
        $posts = $this->postCollectionFactory->create();
        $posts->addFieldToFilter('enabled', 1);

        foreach ($posts as $post) {
            if (!$post->getUrlKey()) {
                continue;
            }

            $items[] = $this->sitemapItemFactory->create([
                'url' => 'blog/' . ltrim($post->getUrlKey(), '/'),
                'updatedAt' => $post->getUpdatedAt(),
                'changeFrequency' => 'daily',
                'priority' => 0.8
            ]);
        }

        /** BLOG CATEGORIES */
        $categories = $this->categoryCollectionFactory->create();
        $categories->addFieldToFilter('enabled', 1);

        foreach ($categories as $category) {
            if (!$category->getUrlKey()) {
                continue;
            }

            $items[] = $this->sitemapItemFactory->create([
                'url' => 'blog/category/' . ltrim($category->getUrlKey(), '/'),
                'updatedAt' => $category->getUpdatedAt(),
                'changeFrequency' => 'weekly',
                'priority' => 0.7
            ]);
        }

        return $items;
    }
}