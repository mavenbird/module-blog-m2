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

namespace Mavenbird\Blog\Helper;

use DateTimeZone;
use Exception;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filter\TranslitUrl;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mavenbird\Blog\Model\Author;
use Mavenbird\Blog\Model\AuthorFactory;
use Mavenbird\Blog\Model\Category;
use Mavenbird\Blog\Model\CategoryFactory;
use Mavenbird\Blog\Model\Config\Source\SideBarLR;
use Mavenbird\Blog\Model\Post;
use Mavenbird\Blog\Model\PostFactory;
use Mavenbird\Blog\Model\PostHistoryFactory;
use Mavenbird\Blog\Model\ResourceModel\Author\Collection as AuthorCollection;
use Mavenbird\Blog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Mavenbird\Blog\Model\ResourceModel\Post\Collection as PostCollection;
use Mavenbird\Blog\Model\ResourceModel\Tag\Collection as TagCollection;
use Mavenbird\Blog\Model\ResourceModel\Topic\Collection;
use Mavenbird\Blog\Model\Tag;
use Mavenbird\Blog\Model\TagFactory;
use Mavenbird\Blog\Model\Topic;
use Mavenbird\Blog\Model\TopicFactory;
use Mavenbird\Blog\Helper\AbstractData as CoreHelper;

/**
 * Class Data
 * @package Mavenbird\Blog\Helper
 */
class Data extends CoreHelper
{
    const CONFIG_MODULE_PATH = 'blog';
    const TYPE_POST          = 'post';
    const TYPE_CATEGORY      = 'category';
    const TYPE_TAG           = 'tag';
    const TYPE_TOPIC         = 'topic';
    const TYPE_HISTORY       = 'history';
    const TYPE_AUTHOR        = 'author';
    const TYPE_MONTHLY       = 'month';

    const XML_PATH_ENABLED = 'blog/general/basic_settings/enabled';
    const XML_PATH_BLOG_NAME = 'blog/general/basic_settings/name';
    const XML_PATH_BLOG_SUBTITLE = 'blog/general/basic_settings/blog_subtitle';
    const XML_PATH_URL_PREFIX = 'blog/general/basic_settings/url_prefix';
    const XML_PATH_URL_SUFFIX = 'blog/general/basic_settings/url_suffix';
    const XML_PATH_TOPLINKS = 'blog/general/basic_settings/toplinks';
    const XML_PATH_FOOTER = 'blog/general/basic_settings/footer';
    const XML_PATH_FONT_COLOR = 'blog/general/basic_settings/font_color';

    const XML_PATH_CUSTOMER_APPROVE = 'blog/general/author_settings/customer_approve';
    const XML_PATH_AUTO_APPROVE = 'blog/general/author_settings/auto_approve';
    const XML_PATH_AUTO_POST = 'blog/general/author_settings/auto_post';
    const XML_PATH_HISTORY_LIMIT = 'blog/general/author_settings/history_limit';

    const XML_PATH_DISPLAY_STYLE = 'blog/blog_pages/blog_list/layout/display_style';
    const XML_PATH_BLOG_MODE_GRID_VIEW = 'blog/blog_pages/blog_list/layout/blog_mode_grid_view';
    const XML_PATH_BLOG_LIST_LAYOUT = 'blog/blog_pages/blog_list/layout/blog_list_layout';
    const XML_PATH_BLOG_STYLE_LAYOUT = 'blog/blog_pages/blog_list/layout/blog_style_layout';

    const XML_PATH_FEATURED_BLOG_CATEGORY = 'blog/blog_pages/blog_list/featured_content/featured_blog_category';
    const XML_PATH_FEATURED_BLOG_AUTHORS = 'blog/blog_pages/blog_list/featured_content/featured_blog_authors';
    const XML_PATH_BLOG_STYLE_RECOMMEND = 'blog/blog_pages/blog_list/featured_content/blog_style_recommend';

    const XML_PATH_SHORT_DESCRIPTION_LENGTH = 'blog/blog_pages/blog_list/content_display/short_description_length';
    const XML_PATH_BLOG_LIST_DISPLAY_SHORT_DESCRIPTION = 'blog/blog_pages/blog_list/content_display/blog_list_display_short_description';
    const XML_PATH_BLOG_LIST_DISPLAY_SHARE = 'blog/blog_pages/blog_list/content_display/blog_list_display_share';
    const XML_PATH_DATE_TYPE = 'blog/blog_pages/blog_list/content_display/date_type';

    const XML_PATH_PAGINATION = 'blog/blog_pages/blog_list/pagination_sub/pagination';

    const XML_PATH_BLOG_VIEW_LAYOUT = 'blog/blog_pages/blog_post/layout/blog_view_layout';
    const XML_PATH_DISPLAY_AUTHOR = 'blog/blog_pages/blog_post/post_inline_metadata/display_author';
    const XML_PATH_DISPLAY_EDITING_DATE = 'blog/blog_pages/blog_post/post_inline_metadata/display_editing_date';
    const XML_PATH_DISPLAY_NAVIGATION_BLOG = 'blog/blog_pages/blog_post/navigation/display_navigation_blog';
    const XML_PATH_RELATED_POST = 'blog/blog_pages/blog_post/related_posts/related_post';
    const XML_PATH_RELATED_MODE = 'blog/blog_pages/blog_post/related_posts/related_mode';
    const XML_PATH_IS_REVIEW = 'blog/blog_pages/blog_post/reviews/is_review';
    const XML_PATH_REVIEW_MODE = 'blog/blog_pages/blog_post/reviews/review_mode';
    const XML_PATH_ENABLE_TO_SAVE = 'blog/blog_pages/blog_post/post_view_page_settings/enable_to_save';
    const XML_PATH_SEO_URL_KEY = 'blog/seo/url_key';
    
    // List Inline Metadata
    const XML_PATH_LIST_DISPLAY_DATE = 'blog/blog_pages/blog_list/list_inline_metadata/display_date';
    const XML_PATH_LIST_DISPLAY_CATEGORY = 'blog/blog_pages/blog_list/list_inline_metadata/display_category';
    const XML_PATH_LIST_DISPLAY_AUTHOR = 'blog/blog_pages/blog_list/list_inline_metadata/display_author';
    const XML_PATH_LIST_DISPLAY_COMMENTS = 'blog/blog_pages/blog_list/list_inline_metadata/display_comments';
    const XML_PATH_LIST_DISPLAY_VIEWS = 'blog/blog_pages/blog_list/list_inline_metadata/display_views';
    const XML_PATH_LIST_DISPLAY_LIKES = 'blog/blog_pages/blog_list/list_inline_metadata/display_likes';

    const XML_PATH_SIDEBAR_LEFT_RIGHT = 'blog/sidebar/general/sidebar_left_right';
    const XML_PATH_SHOW_SIDEBAR_STATIC_BLOCK = 'blog/sidebar/general/show_sidebar_static_block';
    const XML_PATH_LIST_OF_STATIC_BLOCK = 'blog/sidebar/general/list_of_static_block';

    const XML_PATH_SHOW_TABLE_OF_CONTENT = 'blog/sidebar/table_of_contents/show_table_of_content';
    const XML_PATH_STICKY_TABLE_OF_CONTENT = 'blog/sidebar/table_of_contents/sticky_table_of_content';

    const XML_PATH_SIDEBAR_CATEGORY_SHOW = 'blog/sidebar/categories/sidebar_category_show';
    const XML_PATH_CATEGORY_ACCORDION = 'blog/sidebar/categories/category_accordion';

    const XML_PATH_SIDEBAR_TOPIC_SHOW = 'blog/sidebar/topics/sidebar_topic_show';
    const XML_PATH_SIDEBAR_TAG_SHOW = 'blog/sidebar/tags/sidebar_tag_show';
    const XML_PATH_SIDEBAR_RSS_SHOW = 'blog/sidebar/rss/sidebar_rss_show';

    const XML_PATH_ENABLE_SEARCH = 'blog/sidebar/search/enable_search';
    const XML_PATH_SEARCH_LIMIT = 'blog/sidebar/search/search_limit';
    const XML_PATH_MIN_CHARS = 'blog/sidebar/search/min_chars';
    const XML_PATH_SHOW_IMAGE = 'blog/sidebar/search/show_image';
    const XML_PATH_SEARCH_DESCRIPTION = 'blog/sidebar/search/description';

    const XML_PATH_ENABLE_WIDGET = 'blog/sidebar/recent_post/enable_widget';
    const XML_PATH_NUMBER_RECENT_POSTS = 'blog/sidebar/recent_post/number_recent_posts';
    const XML_PATH_NUMBER_MOSTVIEW_POSTS = 'blog/sidebar/recent_post/number_mostview_posts';

    const XML_PATH_ENABLE_MONTHLY = 'blog/sidebar/monthly_archive/enable_monthly';
    const XML_PATH_NUMBER_RECORDS = 'blog/sidebar/monthly_archive/number_records';
    const XML_PATH_DATE_TYPE_MONTHLY = 'blog/sidebar/monthly_archive/date_type_monthly';

    const XML_PATH_PRODUCT_PAGE_ENABLE_POST = 'blog/product_integration/product_page/enable_post';
    const XML_PATH_PRODUCT_PAGE_POST_LIMIT = 'blog/product_integration/product_page/post_limit';
    const XML_PATH_POST_DETAIL_ENABLE_PRODUCT = 'blog/product_integration/post_detail/enable_product';
    const XML_PATH_POST_DETAIL_RELATED_MODE = 'blog/product_integration/post_detail/related_mode';
    const XML_PATH_POST_DETAIL_PRODUCT_LIMIT = 'blog/product_integration/post_detail/product_limit';
    const XML_PATH_POST_DETAIL_TITLE = 'blog/product_integration/post_detail/title';

    const XML_PATH_COMMENT_TYPE = 'blog/comments/general/type';
    const XML_PATH_COMMENT_NEED_APPROVE = 'blog/comments/general/need_approve';
    const XML_PATH_COMMENT_DISQUS = 'blog/comments/disqus_sub/disqus';
    const XML_PATH_COMMENT_FACEBOOK_APPID = 'blog/comments/facebook/facebook_appid';
    const XML_PATH_COMMENT_FACEBOOK_NUMBER_COMMENT = 'blog/comments/facebook/facebook_number_comment';
    const XML_PATH_COMMENT_FACEBOOK_COLORSCHEME = 'blog/comments/facebook/facebook_colorscheme';
    const XML_PATH_COMMENT_FACEBOOK_ORDER_BY = 'blog/comments/facebook/facebook_order_by';

    const XML_PATH_SHARE_FB = 'blog/social_sharing/facebook/fb_share';
    const XML_PATH_SHARE_TWITTER = 'blog/social_sharing/twitter/x_share';
    const XML_PATH_SHARE_WHATSAPP = 'blog/social_sharing/whatsapp/whatsapp_share';
    const XML_PATH_SHARE_TELEGRAM = 'blog/social_sharing/telegram/telegram_share';
    const XML_PATH_SHARE_LINKEDIN = 'blog/social_sharing/linkedin/linkedin_share';
    const XML_PATH_SHARE_REDDIT = 'blog/social_sharing/reddit/reddit_share';
    const XML_PATH_SHARE_EMAIL = 'blog/social_sharing/email/email_share';

    const XML_PATH_SEO_META_TITLE = 'blog/seo/blog_seo_meta_title';
    const XML_PATH_SEO_META_DESCRIPTION = 'blog/seo/blog_seo_meta_description';
    const XML_PATH_SEO_META_KEYWORDS = 'blog/seo/blog_seo_meta_keywords';
    const XML_PATH_SEO_META_ROBOTS = 'blog/seo/blog_seo_meta_robots';

    /**
     * @var PostFactory
     */
    public $postFactory;

    /**
     * @var CategoryFactory
     */
    public $categoryFactory;

    /**
     * @var TagFactory
     */
    public $tagFactory;

    /**
     * @var TopicFactory
     */
    public $topicFactory;

    /**
     * @var AuthorFactory
     */
    public $authorFactory;

    /**
     * @var TranslitUrl
     */
    public $translitUrl;

    /**
     * @var DateTime
     */
    public $dateTime;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var HttpContext
     */
    protected $_httpContext;

    /**
     * @var PostHistoryFactory
     */
    protected $postHistoryFactory;

    /**
     * @var ProductMetadataInterface
     */
    protected $_productMetadata;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param PostFactory $postFactory
     * @param CategoryFactory $categoryFactory
     * @param TagFactory $tagFactory
     * @param TopicFactory $topicFactory
     * @param AuthorFactory $authorFactory
     * @param PostHistoryFactory $postHistoryFactory
     * @param TranslitUrl $translitUrl
     * @param ProductMetadataInterface $productMetadata
     * @param Session $customerSession
     * @param HttpContext $httpContext
     * @param DateTime $dateTime
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        PostFactory $postFactory,
        CategoryFactory $categoryFactory,
        TagFactory $tagFactory,
        TopicFactory $topicFactory,
        AuthorFactory $authorFactory,
        PostHistoryFactory $postHistoryFactory,
        TranslitUrl $translitUrl,
        ProductMetadataInterface $productMetadata,
        Session $customerSession,
        HttpContext $httpContext,
        DateTime $dateTime
    ) {
        $this->postFactory        = $postFactory;
        $this->categoryFactory    = $categoryFactory;
        $this->tagFactory         = $tagFactory;
        $this->topicFactory       = $topicFactory;
        $this->authorFactory      = $authorFactory;
        $this->postHistoryFactory = $postHistoryFactory;
        $this->translitUrl        = $translitUrl;
        $this->dateTime           = $dateTime;
        $this->customerSession    = $customerSession;
        $this->_httpContext       = $httpContext;
        $this->_productMetadata   = $productMetadata;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @return bool
     */
    public function isEnabledReview()
    {
        $groupId = (string) $this->_httpContext->getValue(CustomerContext::CONTEXT_GROUP);

        if (
            $this->getIsReview()
            && in_array($groupId, explode(',', (string) $this->getReviewModeConfig()), true)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getReviewMode()
    {
        $login = $this->_httpContext->getValue(CustomerContext::CONTEXT_AUTH);

        if (
            !$login
            && in_array('0', explode(',', $this->getReviewModeConfig() ?? ''), true)
        ) {
            return '0';
        }

        return '1';
    }

    /**
     * @return string
     */
    public function getCurrentVersion()
    {
        return $this->_productMetadata->getVersion();
    }

    /**
     * @return int|null
     */
    public function getCurrentUser()
    {
        return $this->customerSession->getId();
    }

    /**
     * @return int|null
     */
    public function getCustomerIdByContext()
    {
        return $this->_httpContext->getValue('mb_customer_id') ?: $this->customerSession->getId();
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getCurrentStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return bool
     */
    public function isLogin()
    {
        return $this->_httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    /**
     * @return bool
     */
    public function isAuthor()
    {
        $collection = $this->getAuthorCollection();

        return empty($collection->getSize());
    }

    /**
     * @return mixed
     */
    public function isEnabledAuthor()
    {
        if (!$this->_httpContext->getValue(CustomerContext::CONTEXT_AUTH)) {
            return false;
        }

        return $this->getCurrentAuthor() ? true : false;
    }

    /**
     * Set Customer Id in Context
     */
    public function setCustomerContextId()
    {
        $customer = $this->customerSession->getCustomerData();
        if (!$this->_httpContext->getValue('mb_customer_id') && $customer) {
            $this->_httpContext->setValue('mb_customer_id', $customer->getId(), 0);
        }
    }

    /**
     * @return DataObject
     */
    public function getCurrentAuthor()
    {
        $collection = $this->getAuthorCollection();

        return $collection ? $collection->getFirstItem() : null;
    }

    /**
     * @return AbstractCollection
     */
    public function getAuthorCollection()
    {
        if ($customerId = $this->getCustomerIdByContext()) {
            return $this->getFactoryByType('author')->create()->getCollection()
                ->addFieldToFilter('customer_id', $customerId);
        }

        return null;
    }

    /**
     * @return Image
     */
    public function getImageHelper()
    {
        return $this->objectManager->get(Image::class);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_ENABLED, $storeId);
    }



    /**
     * @param null $storeId
     * @return array|mixed|string
     */
    public function getSidebarLayout($storeId = null)
    {
        $sideBarConfig = $this->getConfigValue(self::XML_PATH_SIDEBAR_LEFT_RIGHT, $storeId);
        if ($sideBarConfig == 0) {
            return SideBarLR::LEFT;
        }
        if ($sideBarConfig == 1) {
            return SideBarLR::RIGHT;
        }
        if ($sideBarConfig == 2) {
            return SideBarLR::ONECOLUMN;
        }
        return $sideBarConfig;
    }

    /**
     * @param null $storeId
     * @return array|mixed|string
     */
    public function getBlogListingLayout($storeId = null)
    {
        $sideBarConfig = $this->getConfigValue(self::XML_PATH_BLOG_LIST_LAYOUT, $storeId);
        if ($sideBarConfig == 0) {
            return SideBarLR::LEFT;
        }
        if ($sideBarConfig == 1) {
            return SideBarLR::RIGHT;
        }
        if ($sideBarConfig == 2) {
            return SideBarLR::ONECOLUMN;
        }
        return $sideBarConfig;
    }

    /**
     * @param null $storeId
     * @return array|mixed|string
     */
    public function getBlogViewLayout($storeId = null)
    {
        $sideBarConfig = $this->getConfigValue(self::XML_PATH_BLOG_VIEW_LAYOUT, $storeId);
        if ($sideBarConfig == 0) {
            return SideBarLR::LEFT;
        }
        if ($sideBarConfig == 1) {
            return SideBarLR::RIGHT;
        }
        if ($sideBarConfig == 2) {
            return SideBarLR::ONECOLUMN;
        }
        return $sideBarConfig;
    }

    public function applyBlogViewLayout($page)
    {
        $layout = $this->getBlogViewLayout();
        switch ($layout) {
            case \Mavenbird\Blog\Model\Config\Source\SideBarLR::LEFT:
                $page->getConfig()->setPageLayout('2columns-left');
                $page->addHandle('mbblog_layout_left');
                break;
            case \Mavenbird\Blog\Model\Config\Source\SideBarLR::RIGHT:
                $page->getConfig()->setPageLayout('2columns-right');
                $page->addHandle('mbblog_layout_right');
                break;
            case \Mavenbird\Blog\Model\Config\Source\SideBarLR::ONECOLUMN:
            default:
                $page->getConfig()->setPageLayout('1column');
                $page->addHandle('mbblog_layout_1column');
                break;
        }
        return $page;
    }

    public function applySidebarLayout($page)
    {
        $layout = $this->getSidebarLayout();

        switch ($layout) {
            case \Mavenbird\Blog\Model\Config\Source\SideBarLR::LEFT:
                $page->getConfig()->setPageLayout('2columns-left');
                $page->addHandle('mbblog_layout_left');
                break;
            case \Mavenbird\Blog\Model\Config\Source\SideBarLR::RIGHT:
                $page->getConfig()->setPageLayout('2columns-right');
                $page->addHandle('mbblog_layout_right');
                break;
            case \Mavenbird\Blog\Model\Config\Source\SideBarLR::ONECOLUMN:
            default:
                $page->getConfig()->setPageLayout('1column');
                $page->addHandle('mbblog_layout_1column');
                break;
        }

        return $page;
    }

    public function applyBlogListingLayout($page)
    {
        $layout = $this->getBlogListingLayout();

        switch ($layout) {
            case \Mavenbird\Blog\Model\Config\Source\SideBarLR::LEFT:
                $page->getConfig()->setPageLayout('2columns-left');
                $page->addHandle('mbblog_layout_left');
                break;
            case \Mavenbird\Blog\Model\Config\Source\SideBarLR::RIGHT:
                $page->getConfig()->setPageLayout('2columns-right');
                $page->addHandle('mbblog_layout_right');
                break;
            case \Mavenbird\Blog\Model\Config\Source\SideBarLR::ONECOLUMN:
            default:
                $page->getConfig()->setPageLayout('1column');
                $page->addHandle('mbblog_layout_1column');
                break;
        }

        return $page;
    }



    /**
     * @return mixed
     */
    public function showAuthorInfo($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DISPLAY_AUTHOR, $storeId);
    }
    
    /**
     * Check if publish date should be displayed in blog list
     * 
     * @param null $storeId
     * @return bool
     */
    public function showListDate($storeId = null)
    {
        return (bool)$this->getConfigValue(self::XML_PATH_LIST_DISPLAY_DATE, $storeId);
    }
    
    /**
     * Check if category should be displayed in blog list
     * 
     * @param null $storeId
     * @return bool
     */
    public function showListCategory($storeId = null)
    {
        return (bool)$this->getConfigValue(self::XML_PATH_LIST_DISPLAY_CATEGORY, $storeId);
    }
    
    /**
     * Check if author should be displayed in blog list
     * 
     * @param null $storeId
     * @return bool
     */
    public function showListAuthor($storeId = null)
    {
        return (bool)$this->getConfigValue(self::XML_PATH_LIST_DISPLAY_AUTHOR, $storeId);
    }
    
    /**
     * Check if comments count should be displayed in blog list
     * 
     * @param null $storeId
     * @return bool
     */
    public function showListComments($storeId = null)
    {
        return (bool)$this->getConfigValue(self::XML_PATH_LIST_DISPLAY_COMMENTS, $storeId);
    }
    
    /**
     * Check if views count should be displayed in blog list
     * 
     * @param null $storeId
     * @return bool
     */
    public function showListViews($storeId = null)
    {
        return (bool)$this->getConfigValue(self::XML_PATH_LIST_DISPLAY_VIEWS, $storeId);
    }
    
    /**
     * Check if likes count should be displayed in blog list
     * 
     * @param null $storeId
     * @return bool
     */
    public function showListLikes($storeId = null)
    {
        return (bool)$this->getConfigValue(self::XML_PATH_LIST_DISPLAY_LIKES, $storeId);
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getBlogName($store = null)
    {
        return $this->getConfigValue(self::XML_PATH_BLOG_NAME, $store) ?: __('Blog');
    }

    /**
     * Get blog subtitle
     *
     * @param null $store
     *
     * @return string
     */
    public function getBlogSubtitle($store = null)
    {
        return $this->getConfigValue(self::XML_PATH_BLOG_SUBTITLE, $store) ?: '';
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getRoute($store = null)
    {
        return $this->getConfigValue(self::XML_PATH_URL_PREFIX, $store) ?: 'blog';
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isTopLinksEnabled($storeId = null)
    {
        return (bool)$this->getConfigValue(self::XML_PATH_TOPLINKS, $storeId);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isFooterEnabled($storeId = null)
    {
        return (bool)$this->getConfigValue(self::XML_PATH_FOOTER, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getFontColor($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_FONT_COLOR, $storeId);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isFacebookShareEnabled($storeId = null)
    {
        return (bool)$this->getConfigValue(self::XML_PATH_SHARE_FB, $storeId);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isTwitterShareEnabled($storeId = null)
    {
        return (bool)$this->getConfigValue(self::XML_PATH_SHARE_TWITTER, $storeId);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isLinkedInShareEnabled($storeId = null)
    {
        return (bool)$this->getConfigValue(self::XML_PATH_SHARE_LINKEDIN, $storeId);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isEmailShareEnabled($storeId = null)
    {
        return (bool)$this->getConfigValue(self::XML_PATH_SHARE_EMAIL, $storeId);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isWhatsAppShareEnabled($storeId = null)
    {
        return (bool)$this->getConfigValue(self::XML_PATH_SHARE_WHATSAPP, $storeId);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isTelegramShareEnabled($storeId = null)
    {
        return (bool)$this->getConfigValue(self::XML_PATH_SHARE_TELEGRAM, $storeId);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isRedditShareEnabled($storeId = null)
    {
        return (bool)$this->getConfigValue(self::XML_PATH_SHARE_REDDIT, $storeId);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isPostDetailProductEnabled($storeId = null)
    {
        return (bool)$this->getConfigValue(self::XML_PATH_POST_DETAIL_ENABLE_PRODUCT, $storeId);
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getPostDetailProductTitle($store = null)
    {
        return $this->getConfigValue(self::XML_PATH_POST_DETAIL_TITLE, $store) ?: __('Related Products');
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getFacebookCommentsColorscheme($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_COMMENT_FACEBOOK_COLORSCHEME, $storeId);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getUrlSuffix($store = null)
    {
        $suffix = $this->getConfigValue(self::XML_PATH_URL_SUFFIX, $store);
        return $suffix ? '.' . $suffix : '';
    }

    public function getCategoriesMaximum($storeId = null)
    {
        return (int) $this->getConfigValue('blog/sidebar/categories/maximum', $storeId);
    }

    public function getPagination($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_PAGINATION, $storeId);
    }

    public function getDisplayStyle($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DISPLAY_STYLE, $storeId);
    }

    public function getSeoMetaDescription($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_SEO_META_DESCRIPTION, $storeId);
    }

    public function getSeoMetaKeywords($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_SEO_META_KEYWORDS, $storeId);
    }

    public function getSeoMetaRobots($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_SEO_META_ROBOTS, $storeId);
    }

    public function getSeoMetaTitle($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_SEO_META_TITLE, $storeId);
    }

    public function getBlogModeGridView($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_BLOG_MODE_GRID_VIEW, $storeId);
    }

    public function getShortDescriptionLength($storeId = null)
    {
        return (int) $this->getConfigValue(self::XML_PATH_SHORT_DESCRIPTION_LENGTH, $storeId);
    }

    public function getRelatedPost($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RELATED_POST, $storeId);
    }

    public function getShareTwitterEnabled($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_SHARE_TWITTER, $storeId);
    }

    public function getShareFbEnabled($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_SHARE_FB, $storeId);
    }

    public function getShareWhatsappEnabled($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_SHARE_WHATSAPP, $storeId);
    }

    public function getShareTelegramEnabled($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_SHARE_TELEGRAM, $storeId);
    }

    public function getShareLinkedinEnabled($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_SHARE_LINKEDIN, $storeId);
    }

    public function getShareRedditEnabled($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_SHARE_REDDIT, $storeId);
    }

    public function getShareEmailEnabled($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_SHARE_EMAIL, $storeId);
    }

    public function getEnableSearch($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_ENABLE_SEARCH, $storeId);
    }

    public function getSearchLimit($storeId = null)
    {
        return (int) $this->getConfigValue(self::XML_PATH_SEARCH_LIMIT, $storeId);
    }

    public function getMinChars($storeId = null)
    {
        return (int) $this->getConfigValue(self::XML_PATH_MIN_CHARS, $storeId);
    }

    public function getShowImage($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_SHOW_IMAGE, $storeId);
    }

    public function getSearchDescription($storeId = null)
    {
        return (int) $this->getConfigValue(self::XML_PATH_SEARCH_DESCRIPTION, $storeId);
    }

    public function getShowTableOfContent($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_SHOW_TABLE_OF_CONTENT, $storeId);
    }

    public function getStickyTableOfContent($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_STICKY_TABLE_OF_CONTENT, $storeId);
    }

    public function getShowSidebarStaticBlock($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_SHOW_SIDEBAR_STATIC_BLOCK, $storeId);
    }

    public function getListOfStaticBlock($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_LIST_OF_STATIC_BLOCK, $storeId);
    }

    public function getNumberRecentPosts($storeId = null)
    {
        return (int) $this->getConfigValue(self::XML_PATH_NUMBER_RECENT_POSTS, $storeId);
    }

    public function getNumberMostviewPosts($storeId = null)
    {
        return (int) $this->getConfigValue(self::XML_PATH_NUMBER_MOSTVIEW_POSTS, $storeId);
    }

    public function getEnableMonthly($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_ENABLE_MONTHLY, $storeId);
    }

    public function getNumberRecords($storeId = null)
    {
        return (int) $this->getConfigValue(self::XML_PATH_NUMBER_RECORDS, $storeId);
    }

    public function getPostDetailRelatedMode($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_POST_DETAIL_RELATED_MODE, $storeId);
    }

    public function getPostDetailProductLimit($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_POST_DETAIL_PRODUCT_LIMIT, $storeId);
    }

    public function getPostDetailTitle($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_POST_DETAIL_TITLE, $storeId);
    }

    public function getPostDetailEnableProduct($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_POST_DETAIL_ENABLE_PRODUCT, $storeId);
    }

    public function getProductPagePostLimit($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_PRODUCT_PAGE_POST_LIMIT, $storeId);
    }

    public function getRelatedMode($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RELATED_MODE, $storeId);
    }

    public function getProductPageEnablePost($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_PRODUCT_PAGE_ENABLE_POST, $storeId);
    }

    public function getCommentType($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_COMMENT_TYPE, $storeId);
    }

    public function getCommentNeedApprove($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_COMMENT_NEED_APPROVE, $storeId);
    }

    public function getCommentDisqus($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_COMMENT_DISQUS, $storeId);
    }

    public function getCommentFacebookAppid($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_COMMENT_FACEBOOK_APPID, $storeId);
    }

    public function getCommentFacebookNumberComment($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_COMMENT_FACEBOOK_NUMBER_COMMENT, $storeId);
    }

    public function getCommentFacebookColorscheme($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_COMMENT_FACEBOOK_COLORSCHEME, $storeId);
    }

    public function getCommentFacebookOrderBy($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_COMMENT_FACEBOOK_ORDER_BY, $storeId);
    }

    public function getDisplayEditingDate($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DISPLAY_EDITING_DATE, $storeId);
    }

    public function getDisplayNavigationBlog($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DISPLAY_NAVIGATION_BLOG, $storeId);
    }

    public function getCategoryAccordion($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_CATEGORY_ACCORDION, $storeId);
    }

    public function getBlogListLayout($storeId = null)
    {
        return $this->getBlogListingLayout($storeId);
    }

    public function getSidebarLeftRight($storeId = null)
    {
        return $this->getSidebarLayout($storeId);
    }

    public function getToplinks($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_TOPLINKS, $storeId);
    }

    public function getCustomerApprove($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_CUSTOMER_APPROVE, $storeId);
    }

    public function getEnableToSave($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_ENABLE_TO_SAVE, $storeId);
    }

    public function getSeoUrlKey($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_SEO_URL_KEY, $storeId);
    }

    public function getHistoryLimit($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_HISTORY_LIMIT, $storeId);
    }

    public function getAutoApprove($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_AUTO_APPROVE, $storeId);
    }

    public function getAutoPost($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_AUTO_POST, $storeId);
    }

    public function getIsReview($storeId = null)
    {
        return (bool) $this->getConfigValue(self::XML_PATH_IS_REVIEW, $storeId);
    }

    public function getReviewModeConfig($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_REVIEW_MODE, $storeId);
    }

    public function getDateType($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DATE_TYPE, $storeId);
    }

    public function getDateTypeMonthly($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DATE_TYPE_MONTHLY, $storeId);
    }

    public function getBlogListDisplayShortDescription($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_BLOG_LIST_DISPLAY_SHORT_DESCRIPTION, $storeId);
    }

    public function getBlogListDisplayShare($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_BLOG_LIST_DISPLAY_SHARE, $storeId);
    }

    /**
     * Get current theme id
     * @return mixed
     */
    public function getCurrentThemeId()
    {
        return $this->getConfigValue(DesignInterface::XML_PATH_THEME_ID);
    }

    /**
     * @param null $type
     * @param null $id
     * @param null $storeId
     *
     * @return PostCollection
     * @throws NoSuchEntityException
     */
    public function getPostCollection($type = null, $id = null, $storeId = null)
    {
        if ($id === null) {
            $id = $this->_request->getParam('id');
        }

        /** @var PostCollection $collection */
        $collection = $this->getPostList($storeId);

        switch ($type) {
            case self::TYPE_AUTHOR:
                $collection->addFieldToFilter('author_id', $id);
                break;
            case self::TYPE_CATEGORY:
                $collection->join(
                    ['category' => $collection->getTable('mavenbird_blog_post_category')],
                    'main_table.post_id=category.post_id AND category.category_id=' . $id,
                    ['position']
                );
                $collection->getSelect()->order('position asc');
                break;
            case self::TYPE_TAG:
                $collection->join(
                    ['tag' => $collection->getTable('mavenbird_blog_post_tag')],
                    'main_table.post_id=tag.post_id AND tag.tag_id=' . $id,
                    ['position']
                );
                $collection->getSelect()->order('position asc');
                break;
            case self::TYPE_TOPIC:
                $collection->join(
                    ['topic' => $collection->getTable('mavenbird_blog_post_topic')],
                    'main_table.post_id=topic.post_id AND topic.topic_id=' . $id,
                    ['position']
                );
                $collection->getSelect()->order('position asc');
                break;
            case self::TYPE_MONTHLY:
                $collection->addFieldToFilter('publish_date', ['like' => $id . '%']);
                break;
        }

        return $collection;
    }

    /**
     * @param null $storeId
     *
     * @return PostCollection
     * @throws NoSuchEntityException
     */
    public function getPostList($storeId = null)
    {
        /** @var PostCollection $collection */
        $collection = $this->getObjectList(self::TYPE_POST, $storeId)
            ->addFieldToFilter('publish_date', ['to' => $this->dateTime->date()])
            ->setOrder('publish_date', 'desc');

        return $collection;
    }

    /**
     * @param $array
     *
     * @return \Magento\Sales\Model\ResourceModel\Collection\AbstractCollection
     */
    public function getCategoryCollection($array)
    {
        try {
            $collection = $this->getObjectList(self::TYPE_CATEGORY)
                ->addFieldToFilter('category_id', ['in' => $array]);

            return $collection;
        } catch (Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }

        return null;
    }

    /**
     * Get object collection (Category, Tag, Post, Topic)
     *
     * @param null $type
     * @param null $storeId
     *
     * @return AuthorCollection|CategoryCollection|PostCollection|TagCollection|Collection
     * @throws NoSuchEntityException
     */
    public function getObjectList($type = null, $storeId = null)
    {
        /** @var AuthorCollection|CategoryCollection|PostCollection|TagCollection|Collection $collection */
        $collection = $this->getFactoryByType($type)
            ->create()
            ->getCollection()
            ->addFieldToFilter('enabled', 1);

        $this->addStoreFilter($collection, $storeId);

        return $collection;
    }

    /**
     * @param $collection
     * @param null $storeId
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function addStoreFilter($collection, $storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $collection->addFieldToFilter('store_ids', [
            ['finset' => Store::DEFAULT_STORE_ID],
            ['finset' => $storeId]
        ]);

        return $collection;
    }

    /**
     * @param $post
     * @param bool $modify
     *
     * @return Author
     */
    public function getAuthorByPost($post, $modify = false)
    {
        $author = $this->authorFactory->create();

        $authorId = $modify ? $post->getModifierId() : $post->getAuthorId();
        if ($authorId) {
            $author->load($authorId);
        }

        return $author;
    }

    /**
     * @param null $urlKey
     * @param null $type
     * @param null $store
     *
     * @return string
     */
    public function getBlogUrl($urlKey = null, $type = null, $store = null)
    {
        if (is_object($urlKey)) {
            $urlKey = $urlKey->getUrlKey();
        }

        $urlKey = ($type ? $type . '/' : '') . $urlKey;
        $url    = $this->getUrl($this->getRoute($store) . '/' . $urlKey);
        $url    = explode('?', $url ?? '');
        $url    = $url[0];

        return rtrim($url, '/') . $this->getUrlSuffix($store);
    }

    /**
     * @param $value
     * @param null $code
     * @param null $type
     *
     * @return Author|Category|Post|Tag|Topic
     */
    public function getObjectByParam($value, $code = null, $type = null)
    {
        $object = $this->getFactoryByType($type)
            ->create()
            ->load($value, $code);

        return $object;
    }

    /**
     * @param $type
     *
     * @return AuthorFactory|CategoryFactory|PostFactory|TagFactory|TopicFactory
     */
    public function getFactoryByType($type = null)
    {
        switch ($type) {
            case self::TYPE_CATEGORY:
                $object = $this->categoryFactory;
                break;
            case self::TYPE_TAG:
                $object = $this->tagFactory;
                break;
            case self::TYPE_AUTHOR:
                $object = $this->authorFactory;
                break;
            case self::TYPE_TOPIC:
                $object = $this->topicFactory;
                break;
            case self::TYPE_HISTORY:
                $object = $this->postHistoryFactory;
                break;
            default:
                $object = $this->postFactory;
        }

        return $object;
    }

    /**
     * Generate url_key for post, tag, topic, category, author
     *
     * @param $resource
     * @param $object
     * @param $name
     *
     * @return string
     * @throws LocalizedException
     */
    public function generateUrlKey($resource, $object, $name)
    {
        $attempt = -1;
        do {
            if ($attempt++ >= 10) {
                throw new LocalizedException(__('Unable to generate url key. Please check the setting and try again.'));
            }

            $urlKey = $this->translitUrl->filter($name);
            if ($urlKey) {
                $urlKey .= ($attempt ?: '');
            }
        } while ($this->checkUrlKey($resource, $object, $urlKey));

        return $urlKey;
    }

    /**
     * @param $resource
     * @param $object
     * @param $urlKey
     *
     * @return bool
     */
    public function checkUrlKey($resource, $object, $urlKey)
    {
        if (empty($urlKey)) {
            return true;
        }

        $adapter = $resource->getConnection();
        $select  = $adapter->select()
            ->from($resource->getMainTable(), '*')
            ->where('url_key = :url_key');

        $binds = ['url_key' => (string) $urlKey];

        if ($id = $object->getId()) {
            $select->where($resource->getIdFieldName() . ' != :object_id');
            $binds['object_id'] = (int) $id;
        }

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * get date formatted
     *
     * @param $date
     * @param bool $monthly
     *
     * @return false|string
     * @throws Exception
     */
    public function getDateFormat($date, $monthly = false)
    {
        $dateTime = new \DateTime($date, new DateTimeZone('UTC'));
        $dateTime->setTimezone(new DateTimeZone($this->getTimezone()));

        $dateType = $monthly ? $this->getDateTypeMonthly() : $this->getDateType();

        return $dateTime->format($dateType);
    }

    /**
     * @param $post
     *
     * @return mixed
     * @throws Exception
     */
    public function formatPublishDate($post)
    {
        $publicDateTime = new \DateTime($post->getData('publish_date'), new DateTimeZone('UTC'));
        $publicDateTime->setTimezone(new DateTimeZone($this->getTimezone()));
        $publicDateTime = $publicDateTime->format('m/d/Y H:i:s');
        $post->setData('publish_date', $publicDateTime);

        return $post;
    }

    /**
     * get configuration zone
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->getConfigValue('general/locale/timezone');
    }

    /**
     * @param $route
     * @param array $params
     *
     * @return string
     */
    public function getUrl($route, $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }

    /**
     * @param $object
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function checkStore($object)
    {
        $storeEnable = explode(',', $object->getStoreIds() ?? '');

        return in_array('0', $storeEnable, true)
            || in_array((string) $this->storeManager->getStore()->getId(), $storeEnable, true);
    }

    /**
     * @param $title
     *
     * @return mixed|null
     * @throws NoSuchEntityException
     */
    public function getMetaTitleByStoreId($title)
    {
        if (!$title) {
            return null;
        }

        $storeId = $this->storeManager->getStore()->getId();
        $decoded   = json_decode($title, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            $decoded = ['0' => $title];
        }

        return $decoded[$storeId] ?? $decoded[0] ?? null;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function handleSeoValueBeforeSave(array &$data)
    {
        $seoFields = ['meta_title', 'meta_description', 'meta_keywords', 'meta_robots'];

        foreach ($seoFields as $field) {
            if (!isset($data[$field])) {
                continue;
            }

            if (is_array($data[$field])) {
                $filtered = [];
                foreach ($data[$field] as $k => $v) {
                    if ($v !== null && !(is_string($v) && trim($v) === '')) {
                        $filtered[$k] = $v;
                    }
                }

                if (!empty($filtered)) {
                    $data[$field] = json_encode($filtered, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
                } else {
                    $data[$field] = null;
                }
            } else {
                if ($data[$field] === null || (is_string($data[$field]) && trim($data[$field]) === '')) {
                    $data[$field] = null;
                }
            }
        }

        return $data;
    }

    public function getBlogStyle($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_BLOG_STYLE_LAYOUT, $storeId);
    }

    public function getFeaturedCategories($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_FEATURED_BLOG_CATEGORY, $storeId);
    }

    public function getRecommendCount($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_BLOG_STYLE_RECOMMEND, $storeId);
    }

    public function getAuthorId($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_FEATURED_BLOG_AUTHORS, $storeId);
    }

    /**
     * Retrieve configuration value.
     *
     * @param string $field
     * @param null|int|string|\Magento\Store\Model\Store $scopeValue
     * @param string $scopeType
     * @return mixed
     */
    public function getConfigValue($field, $scopeValue = null, $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return parent::getConfigValue($field, $scopeValue, $scopeType);
    }
}