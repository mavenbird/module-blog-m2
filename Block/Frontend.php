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

namespace Mavenbird\Blog\Block;

use Exception;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Url;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Mavenbird\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Category as CategoryOptions;
use Mavenbird\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Tag as TagOptions;
use Mavenbird\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Topic as TopicOptions;
use Mavenbird\Blog\Helper\Data as HelperData;
use Mavenbird\Blog\Helper\Image;
use Mavenbird\Blog\Model\CategoryFactory;
use Mavenbird\Blog\Model\CommentFactory;
use Mavenbird\Blog\Model\Config\Source\AuthorStatus;
use Mavenbird\Blog\Model\LikeFactory;
use Mavenbird\Blog\Model\Post;
use Mavenbird\Blog\Model\PostFactory;
use Mavenbird\Blog\Model\PostLikeFactory;

/**
 * Class Frontend
 * @package Mavenbird\Blog\Block
 */
class Frontend extends Template
{
    /**
     * @var FilterProvider
     */
    public $filterProvider;

    /**
     * @var HelperData
     */
    public $helperData;

    /**
     * @var StoreManagerInterface
     */
    public $store;

    /**
     * @var CommentFactory
     */
    public $cmtFactory;

    /**
     * @var LikeFactory
     */
    public $likeFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * @var
     */
    public $commentTree;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var DateTime
     */
    public $dateTime;

    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Url
     */
    protected $customerUrl;

    /**
     * @var CategoryOptions
     */
    protected $categoryOptions;

    /**
     * @var TopicOptions
     */
    protected $topicOptions;

    /**
     * @var TagOptions
     */
    protected $tagOptions;

    /**
     * @var PostLikeFactory
     */
    protected $postLikeFactory;

    /**
     * @var AuthorStatus
     */
    protected $authorStatusType;

    /**
     * @var ThemeProviderInterface
     */
    protected $themeProvider;

    /**
     * @var EncryptorInterface
     */
    public $enc;

    /**
     * Frontend constructor.
     *
     * @param Context $context
     * @param FilterProvider $filterProvider
     * @param CommentFactory $commentFactory
     * @param LikeFactory $likeFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param Registry $coreRegistry
     * @param HelperData $helperData
     * @param Url $customerUrl
     * @param CategoryFactory $categoryFactory
     * @param PostFactory $postFactory
     * @param DateTime $dateTime
     * @param PostLikeFactory $postLikeFactory
     * @param CategoryOptions $category
     * @param TopicOptions $topic
     * @param TagOptions $tag
     * @param ThemeProviderInterface $themeProvider
     * @param EncryptorInterface $enc
     * @param AuthorStatus $authorStatus
     * @param array $data
     */
    public function __construct(
        Context $context,
        FilterProvider $filterProvider,
        CommentFactory $commentFactory,
        LikeFactory $likeFactory,
        CustomerRepositoryInterface $customerRepository,
        Registry $coreRegistry,
        HelperData $helperData,
        Url $customerUrl,
        CategoryFactory $categoryFactory,
        PostFactory $postFactory,
        DateTime $dateTime,
        PostLikeFactory $postLikeFactory,
        CategoryOptions $category,
        TopicOptions $topic,
        TagOptions $tag,
        ThemeProviderInterface $themeProvider,
        EncryptorInterface $enc,
        AuthorStatus $authorStatus,
        array $data = []
    ) {
        $this->filterProvider     = $filterProvider;
        $this->cmtFactory         = $commentFactory;
        $this->likeFactory        = $likeFactory;
        $this->customerRepository = $customerRepository;
        $this->helperData         = $helperData;
        $this->coreRegistry       = $coreRegistry;
        $this->dateTime           = $dateTime;
        $this->categoryFactory    = $categoryFactory;
        $this->postFactory        = $postFactory;
        $this->customerUrl        = $customerUrl;
        $this->postLikeFactory    = $postLikeFactory;
        $this->categoryOptions    = $category;
        $this->topicOptions       = $topic;
        $this->tagOptions         = $tag;
        $this->authorStatusType   = $authorStatus;
        $this->themeProvider      = $themeProvider;
        $this->store              = $context->getStoreManager();
        $this->enc                = $enc;

        parent::__construct($context, $data);
    }

    /**
     * @return HelperData
     */
    public function getBlogHelper()
    {
        return $this->helperData;
    }

    /**
     * @return bool
     */
    public function isBlogEnabled()
    {
        return $this->helperData->isEnabled();
    }

    /**
     * Check if current theme is Hyva
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isHyvaTheme()
    {
        $currentTheme = $this->themeProvider->getThemeById($this->helperData->getCurrentThemeId());
        
        // Check current theme and all parent themes recursively
        while ($currentTheme) {
            if (str_contains(strtolower($currentTheme->getCode()), 'hyva')) {
                return true;
            }
            $parentTheme = $currentTheme->getParentTheme();
            if (!$parentTheme) {
                break;
            }
            $currentTheme = $parentTheme;
        }
        
        return false;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    public function getPageFilter($content)
    {
        try {
            return $this->filterProvider->getPageFilter()->filter((string) $content);
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * @param string $image
     * @param string $type
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getImageUrl($image, $type = Image::TEMPLATE_MEDIA_TYPE_POST)
    {
        $imageHelper = $this->helperData->getImageHelper();
        if ($image) {
            $imageFile = $imageHelper->getMediaPath($image, $type);
        }

        return $image ? $this->helperData->getImageHelper()->getMediaUrl($imageFile) : '';
    }

    /**
     * @param string|Object $urlKey
     * @param null $type
     *
     * @return string
     */
    public function getRssUrl($urlKey, $type = null)
    {
        if (is_object($urlKey)) {
            $urlKey = $urlKey->getUrlKey();
        }

        $urlKey = ($type ? $type . '/' : '') . $urlKey;
        $url    = $this->helperData->getUrl($this->helperData->getRoute() . '/' . $urlKey);

        return rtrim($url, '/') . '.xml';
    }

    /**
     * @param Post $post
     *
     * @return Phrase|string
     */
    public function getPostInfo($post)
    {
        try {
            $likeCollection = $this->postLikeFactory->create()->getCollection();
            $couldLike      = $likeCollection->addFieldToFilter('post_id', $post->getId())
                ->addFieldToFilter('action', '1')->count();
            $html           = __(
                '<i class="fa-regular fa-calendar-days"></i> %1',
                $this->getDateFormat($post->getPublishDate())
            );

            if ($categoryPost = $this->getPostCategoryHtml($post)) {
                $html .= __(' | Posted in %1', $categoryPost);
            }

            $author = $this->helperData->getAuthorByPost($post);
            if ($author && $author->getName() && $this->helperData->showAuthorInfo()) {
                $aTag = '<a class="mb-info" href="' . $author->getUrl() . '">'
                    . $this->escapeHtml($author->getName()) . '</a>';
                $html .= __(' | <i class="fa-solid fa-user"></i> %1', $aTag);
            }

            if ($this->getCommentinPost($post)) {
                $html .= __(
                    ' | <i class="fa-regular fa-comments" aria-hidden="true"></i> %1',
                    $this->getCommentinPost($post)
                );
            }

            if ($post->getViewTraffic()) {
                $html .= __(
                    ' | <i class="fa-regular fa-eye" title="Views" aria-hidden="true"></i> %1',
                    $post->getViewTraffic()
                );
            }

            if ($couldLike > 0) {
                $html .= __(' | <i class="fa-regular fa-thumbs-up" aria-hidden="true"></i> %1', $couldLike);
            }
        } catch (Exception $e) {
            $html = '';
        }

        return $html;
    }

    /**
     * @param Post $post
     *
     * @return int
     */
    public function getCommentinPost($post)
    {
        $cmt = $this->cmtFactory->create()->getCollection()->addFieldToFilter('post_id', $post->getId());

        return $cmt->count();
    }

    /**
     * Get list category html of post
     *
     * @param Post $post
     *
     * @return string|null
     */
    public function getPostCategoryHtml($post)
    {
        $categoryHtml = [];

        try {
            if (!$post->getCategoryIds()) {
                return null;
            }

            $categories = $this->helperData->getCategoryCollection($post->getCategoryIds());
            $count      = 0;
            foreach ($categories as $_cat) {
                $count++;
                $maximum = $this->helperData->getCategoriesMaximum();
                if ($maximum && $count > $maximum) {
                    continue;
                }
                $categoryHtml[] = '<a class="mb-info" href="'
                    . $this->helperData->getBlogUrl(
                        $_cat,
                        HelperData::TYPE_CATEGORY
                    )
                    . '">' . $_cat->getName() . '</a>';
            }
        } catch (Exception $e) {
            return null;
        }

        return implode(', ', $categoryHtml);
    }

    /**
     * @param string $date
     * @param bool $monthly
     *
     * @return false|string|null
     */
    public function getDateFormat($date, $monthly = false)
    {
        try {
            $date = $this->helperData->getDateFormat($date, $monthly);
        } catch (Exception $e) {
            $date = null;
        }

        return $date;
    }

    /**
     * @param string $image
     * @param null $size
     * @param string $type
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function resizeImage($image, $size = null, $type = Image::TEMPLATE_MEDIA_TYPE_POST)
    {
        if (!$image) {
            return $this->getDefaultImageUrl();
        }

        return $this->helperData->getImageHelper()->resizeImage($image, $size, $type);
    }

    /**
     * get default image url
     */
    public function getDefaultImageUrl()
    {
        return $this->getViewFileUrl('Mavenbird_Blog::media/images/mavenbird-logo-default.png');
    }

    /**
     * @return string
     */
    public function getDefaultAuthorImage()
    {
        return $this->getViewFileUrl('Mavenbird_Blog::media/images/no-artist-image.jpg');
    }

    /**
     * Get social share links for a post
     *
     * @param \Mavenbird\Blog\Model\Post $post
     * @return array
     */
    public function getShareLinks(Post $post): array
    {
        $url   = urlencode($post->getUrl());
        $title = urlencode($post->getName());

        return [
            [
                'enabled' => $this->helperData->getShareTwitterEnabled(),
                'label' => __('X'),
                'icon_class' => 'fa-brands',
                'icon' => 'fa-x-twitter',
                'url' => "https://twitter.com/intent/tweet?url={$url}&text={$title}"
            ],
            [
                'enabled' => $this->helperData->getShareFbEnabled(),
                'label' => __('Facebook'),
                'icon_class' => 'fa-brands',
                'icon' => 'fa-facebook-f',
                'url' => "https://www.facebook.com/sharer/sharer.php?u={$url}"
            ],
            [
                'enabled' => $this->helperData->getShareWhatsappEnabled(),
                'label' => __('WhatsApp'),
                'icon_class' => 'fa-brands',
                'icon' => 'fa-whatsapp',
                'url' => "https://wa.me/?text={$title}%20{$url}"
            ],
            [
                'enabled' => $this->helperData->getShareTelegramEnabled(),
                'label' => __('Telegram'),
                'icon_class' => 'fa-brands',
                'icon' => 'fa-telegram',
                'url' => "https://t.me/share/url?url={$url}&text={$title}"
            ],
            [
                'enabled' => $this->helperData->getShareLinkedinEnabled(),
                'label' => __('LinkedIn'),
                'icon_class' => 'fa-brands',
                'icon' => 'fa-linkedin-in',
                'url' => "https://www.linkedin.com/sharing/share-offsite/?url={$url}"
            ],
            [
                'enabled' => $this->helperData->getShareRedditEnabled(),
                'label' => __('Reddit'),
                'icon_class' => 'fa-brands',
                'icon' => 'fa-reddit-alien',
                'url' => "https://www.reddit.com/submit?url={$url}&title={$title}"
            ],
            [
                'enabled' => $this->helperData->getShareEmailEnabled(),
                'label' => __('Email'),
                'icon_class' => 'fa-solid',
                'icon' => 'fa-envelope',
                'url' => "mailto:?subject={$title}&body={$url}"
            ]
        ];
    }

    /**
     * Get short description for a post
     *
     * @return string
     */
    public function getShortDescription()
    {
        $storeId = $this->store->getStore()->getId();

        return $this->helperData->getBlogListDisplayShortDescription($storeId);
    }

    /**
     * Get show share buttons for a post
     *
     * @return string
     */
    public function getShowShareButtons()
    {
        $storeId = $this->store->getStore()->getId();

        return $this->helperData->getBlogListDisplayShare($storeId);
    }

    /** blog style 2 listing */

    public function getBlogStyle()
    {
        return $this->helperData->getBlogStyle();
    }

    public function getFeaturedCategories()
    {
        return $this->helperData->getFeaturedCategories();
    }

    /**
     * Get posts by category IDs
     * 
     * @param array $categoryIds
     * @return \Mavenbird\Blog\Model\ResourceModel\Post\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPostsByCategoryIds($categoryIds)
    {
        $collection = $this->helperData->getPostList();
        
        // Join with post_category table to filter by multiple category IDs
        $collection->getSelect()->join(
            ['post_category' => $collection->getTable('mavenbird_blog_post_category')],
            'main_table.post_id = post_category.post_id',
            []
        )->where('post_category.category_id IN (?)', $categoryIds)
        ->group('main_table.post_id')
        ->order('main_table.created_at DESC')
        ->limit(4);
        
        $this->helperData->addStoreFilter($collection);
        
        return $collection;
    }

    /**
     * Get category names by IDs
     * 
     * @param array $categoryIds
     * @return array
     */
    public function getCategoryNamesByIds($categoryIds)
    {
        $categories = $this->helperData->getCategoryCollection($categoryIds);
        
        $categoryNames = [];
        foreach ($categories as $category) {
            $categoryNames[] = $category->getName();
        }
        
        return $categoryNames;
    }

    public function getRecommendCount()
    {
        return $this->helperData->getRecommendCount();
    }

    /**
     * @param Post $post
     *
     * @return Phrase|string
     */
    public function getstyle2PostInfo($post)
    {
        try {
            // Calculate read time using the same logic as readtime.phtml
            $postContent = strip_tags($post->getPostContent());
            $wordCount = str_word_count($postContent);
            $wordsPerMinute = 200; // Average reading speed
            $readTime = max(1, ceil($wordCount / $wordsPerMinute));
            
            // Format date as d-m-Y (20-05-2026) specifically for style 2
            $dateTime = new \DateTime($post->getPublishDate(), new \DateTimeZone('UTC'));
            $dateTime->setTimezone(new \DateTimeZone($this->helperData->getTimezone()));
            $formattedDate = $dateTime->format('d-m-Y');
            
            // Return HTML with separate divs for read time and publish date
            $html = '<div class="post-read-time">' . __('%1 min read', $readTime) . '</div>';
            $html .= '<div class="post-publish-date">' . $formattedDate . '</div>';
        } catch (Exception $e) {
            $html = '';
        }

        return $html;
    }

    public function getAuthorIds(): array
    {
        $authorIds = $this->helperData->getAuthorId();

        if (is_string($authorIds)) {
            $authorIds = json_decode($authorIds, true) ?: [];
        }

        return $authorIds;
    }

    /**
     * Get authors by their IDs
     * 
     * @param array $authorIds
     * @return array
     */
    public function getAuthorsByIds($authorIds)
    {
        $authors = [];
        foreach ($authorIds as $authorId) {
            $author = $this->helperData->getObjectByParam($authorId, null, \Mavenbird\Blog\Helper\Data::TYPE_AUTHOR);
            if ($author && $author->getId()) {
                $authors[] = $author;
            }
        }
        return $authors;
    }

    /** blog style 2 listing end*/
}