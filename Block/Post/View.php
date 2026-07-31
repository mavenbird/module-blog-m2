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

namespace Mavenbird\Blog\Block\Post;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Messages;
use Mavenbird\Blog\Helper\Data;
use Mavenbird\Blog\Model\Post;
use Mavenbird\Blog\Model\PostLike;

/**
 * Class View
 * @package Mavenbird\Blog\Block\Post
 * @method Post getPost()
 * @method void setPost($post)
 */
class View extends \Mavenbird\Blog\Block\Listpost
{
    /**
     * config logo blog path
     */
    const LOGO = 'mavenbird/blog/logo/';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();

        $post      = $this->postFactory->create();
        $id        = $this->getRequest()->getParam('id');
        $historyId = $this->getRequest()->getParam('historyId');

        if ($historyId) {
            $history = $this->helperData->getFactoryByType(Data::TYPE_HISTORY)->create()->load($historyId);
            $post    = $this->helperData->getFactoryByType(Data::TYPE_POST)->create()->load($history->getPostId());
            $data    = $history->getData();
            $post->addData($data);
        } elseif ($id) {
            $post->load($id);
        }
        $this->setPost($post);
    }

    /**
     * @return bool
     */
    public function getRelatedMode()
    {
        return (int) $this->helperData->getRelatedMode() === 1 ? true : false;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getDecrypt($value)
    {
        return $this->enc->decrypt($value);
    }

    /**
     * @return mixed
     */
    protected function getBlogObject()
    {
        return $this->getPost();
    }

    /**
     * check customer is logged in or not
     */
    public function isLoggedIn()
    {
        return $this->helperData->isLogin();
    }

    /**
     * @return string
     */
    public function checkRss()
    {
        return $this->helperData->getBlogUrl('post/rss');
    }

    /**
     * @param $topic
     *
     * @return string
     */
    public function getTopicUrl($topic)
    {
        return $this->helperData->getBlogUrl($topic, Data::TYPE_TOPIC);
    }

    /**
     * @param $tag
     *
     * @return string
     */
    public function getTagUrl($tag)
    {
        return $this->helperData->getBlogUrl($tag, Data::TYPE_TAG);
    }

    /**
     * @param $category
     *
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->helperData->getBlogUrl($category, Data::TYPE_CATEGORY);
    }

    /**
     * @param $code
     *
     * @return mixed
     */
    public function helperComment($code)
    {
        switch ($code) {
            case 'type':
                return $this->helperData->getCommentType();
            case 'need_approve':
                return $this->helperData->getCommentNeedApprove();
            case 'disqus':
                return $this->helperData->getCommentDisqus();
            case 'facebook_appid':
                return $this->helperData->getCommentFacebookAppid();
            case 'facebook_number_comment':
                return $this->helperData->getCommentFacebookNumberComment();
            case 'facebook_colorscheme':
                return $this->helperData->getCommentFacebookColorscheme();
            case 'facebook_order_by':
                return $this->helperData->getCommentFacebookOrderBy();
            default:
                return null;
        }
    }

    /**
     * get comments tree html
     *
     * @return mixed
     */
    public function getCommentsHtml()
    {
        return $this->commentTree;
    }

    /**
     * @param $userId
     *
     * @return CustomerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getUserComment($userId)
    {
        return $this->customerRepository->getById($userId);
    }

    /**
     * @param $cmtId
     *
     * @return int|string
     */
    public function getCommentLikes($cmtId)
    {
        $likes = $this->likeFactory->create()
            ->getCollection()
            ->addFieldToFilter('comment_id', $cmtId)
            ->getSize();

        return $likes ?: '';
    }

    /**
     * @param $cmtId
     *
     * @return bool
     */
    public function isLiked($cmtId)
    {
        if ($this->helperData->isLogin()) {
            $customerId = $this->helperData->getCustomerIdByContext();
            $likes      = $this->likeFactory->create()->getCollection();
            foreach ($likes as $like) {
                if ($like->getEntityId() == $customerId && $like->getCommentId() == $cmtId) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $postId
     *
     * @return array
     */
    public function getPostComments($postId)
    {
        $result   = [];
        $comments = $this->cmtFactory->create()->getCollection()
            ->addFieldToFilter('main_table.post_id', $postId);
        foreach ($comments as $comment) {
            $result[] = $comment->getData();
        }

        return $result;
    }

    /**
     * @param $postId
     * @param $action
     *
     * @return int
     */
    public function getPostLike($postId, $action)
    {
        /** @var PostLike $postLike */
        $postLike = $this->postLikeFactory->create();

        return $postLike->getCollection()->addFieldToFilter('post_id', $postId)
            ->addFieldToFilter('action', $action)->count();
    }

    /**
     * @param $comment
     *
     * @return string
     */
    public function commentHtml($comment)
    {
        $html = '';
        foreach (explode("\n", trim($comment)) as $value) {
            $html .= '<p>' . $this->escapeHtml($value) . '</p>';
        }

        return $html;
    }

    /**
     * @param $comments
     * @param $cmtId
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCommentsTree($comments, $cmtId)
    {
        $this->commentTree .= '<ul class="mbblog-comment-details row">';
        foreach ($comments as $comment) {
            if ($comment['reply_id'] == $cmtId && $comment['status'] == 1) {
                $isReply = (bool) $comment['is_reply'];
                $replyId = $isReply ? $comment['reply_id'] : '';
                if ($comment['entity_id'] == 0) {
                    $userName = $comment['user_name'];
                } else {
                    $userCmt  = $this->getUserComment($comment['entity_id']);
                    $userName = $userCmt->getFirstName() . ' '
                        . $userCmt->getLastName();
                }
                $countLikes        = $this->getCommentLikes($comment['comment_id']);
                $isLiked           = ($this->isLiked($comment['comment_id'])) ? "mbblog-liked" : "mbblog-like";
                $this->commentTree .= '<li id="cmt-id-' . $comment['comment_id']
                    . '" class="mbblog-comment-details-list cmt-row-'
                    . $comment['comment_id'] . ' cmt-row col-md-12'
                    . ($isReply ? ' reply-row' : '') . '" data-cmt-id="'
                    . $comment['comment_id'] . '" ' . ($replyId
                        ? 'data-reply-id="' . $replyId . '"' : '') . '>
                                <div class="mbblog-comment-username">
                                    <span class="mbblog-comment-username username username__'
                    . $comment['comment_id'] . '">'
                    . $userName . '</span>
                                </div>
                                <div class="mbblog-comment-details">
                                   ' . $this->commentHtml($comment['content']) . '
                                </div>
                                <div class="mbblog-comment-review interactions">
                                    <div class="mbblog-comment-action-btn">
                                        <a class="mbblog-comment-action-btn action btn-like '
                    . $isLiked . '" data-cmt-id="'
                    . $comment['comment_id'] . '" click="1">
                                        <i class="fa fa-thumbs-up" aria-hidden="true"></i>
                                        <span class="count-like__like-text">'
                    . $countLikes . '</span></a>
                                        <a class="mbblog-comment-action-btn action btn-reply" data-cmt-id="'
                    . $comment['comment_id'] . '">' . __('Reply') . '</a>
                                    </div>
                                    <div class="mbblog-comment-createdate">
                                        <span>' . $this->getDateFormat($comment['created_at']) . '</span>
                                    </div>
                                </div>';
                if ($comment['has_reply']) {
                    $this->commentTree .= $this->getCommentsTree(
                        $comments,
                        $comment['comment_id']
                    );
                }
                $this->commentTree .= '</li>';
            }
        }
        $this->commentTree .= '</ul>';
    }

    /**
     * get tag list
     *
     * @param Post $post
     *
     * @return string
     */
    public function getTagList($post)
    {
        $tagCollection = $post->getSelectedTagsCollection();
        $result        = '';
        if (!empty($tagCollection)) {
            $listTags = [];
            foreach ($tagCollection as $tag) {
                $listTags[] = '<a class="mb-info" href="' . $this->getTagUrl($tag) . '">' . $tag->getName() . '</a>';
            }
            $result = implode(', ', $listTags);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->customerUrl->getLoginUrl();
    }

    /**
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->customerUrl->getRegisterUrl();
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            if ($catId = $this->getRequest()->getParam('cat')) {
                $category = $this->categoryFactory->create()
                    ->load($catId);
                if ($category->getId()) {
                    $breadcrumbs->addCrumb($category->getUrlKey(), [
                        'label' => $category->getName(),
                        'title' => $category->getName(),
                        'link'  => $this->helperData->getBlogUrl($category, Data::TYPE_CATEGORY)
                    ]);
                }
            }

            $post = $this->getPost();
            $breadcrumbs->addCrumb($post->getUrlKey(), [
                'label' => $post->getName(),
                'title' => $post->getName()
            ]);
        }
    }

    /**
     * @param $meta
     *
     * @return array|Phrase|string
     * @throws NoSuchEntityException
     */
    public function getBlogTitle($meta = false)
    {
        $blogTitle = parent::getBlogTitle($meta);
        $post      = $this->getBlogObject();
        if (!$post) {
            return $blogTitle;
        }

        if ($meta) {
            if ($this->helperData->getMetaTitleByStoreId($post->getMetaTitle())) {
                $blogTitle[] = $this->helperData->getMetaTitleByStoreId($post->getMetaTitle());
            } else {
                $blogTitle[] = ucfirst($post->getName());
            }

            return $blogTitle;
        }

        return ucfirst($post->getName());
    }

    /**
     * @param $priority
     * @param $message
     *
     * @return string
     */
    public function getMessagesHtml($priority, $message)
    {
        /** @var $messagesBlock Messages */
        $messagesBlock = $this->_layout->createBlock(Messages::class);
        $messagesBlock->{$priority}(__($message));

        return $messagesBlock->toHtml();
    }

    public function getDisplayEditingDate()
    {
        return $this->helperData->getDisplayEditingDate();
    }

    public function getPreviousPost()
    {
        $currentPost = $this->getPost();

        return $this->postFactory->create()->getCollection()
            ->addFieldToFilter('created_at', ['lt' => $currentPost->getCreatedAt()])
            ->addFieldToFilter('enabled', 1)
            ->setOrder('publish_date', 'DESC')
            ->setPageSize(1)
            ->getFirstItem();
    }

    public function getNextPost()
    {
        $currentPost = $this->getPost();

        return $this->postFactory->create()->getCollection()
            ->addFieldToFilter('created_at', ['gt' => $currentPost->getCreatedAt()])
            ->addFieldToFilter('enabled', 1)
            ->setOrder('publish_date', 'ASC')
            ->setPageSize(1)
            ->getFirstItem();
    }

    public function getDisplayNavigationBlog()
    {
        return $this->helperData->getDisplayNavigationBlog();
    }

    /**
 * Get enabled social share links based on admin configuration
 *
 * @param \Mavenbird\Blog\Model\Post|null $post
 * @return array
 */
public function getShareLinks(?Post $post = null): array
{
    $post = $post ?: $this->getPost();

    if (!$post || !$post->getId()) {
        return [];
    }

    $helper = $this->helperData;

    $url   = urlencode($post->getUrl());
    $title = urlencode($post->getName());

    $platforms = [
        'X' => [
            'enabled' => $helper->getShareTwitterEnabled(),
            'icon_class' => 'fa-brands',
            'icon' => 'fa-x-twitter',
            'url' => "https://twitter.com/intent/tweet?url={$url}&text={$title}"
        ],
        'Facebook' => [
            'enabled' => $helper->getShareFbEnabled(),
            'icon_class' => 'fa-brands',
            'icon' => 'fa-facebook-f',
            'url' => "https://www.facebook.com/sharer/sharer.php?u={$url}"
        ],
        'WhatsApp' => [
            'enabled' => $helper->getShareWhatsappEnabled(),
            'icon_class' => 'fa-brands',
            'icon' => 'fa-whatsapp',
            'url' => "https://wa.me/?text={$title}%20{$url}"
        ],
        'Telegram' => [
            'enabled' => $helper->getShareTelegramEnabled(),
            'icon_class' => 'fa-brands',
            'icon' => 'fa-telegram',
            'url' => "https://t.me/share/url?url={$url}&text={$title}"
        ],
        'LinkedIn' => [
            'enabled' => $helper->getShareLinkedinEnabled(),
            'icon_class' => 'fa-brands',
            'icon' => 'fa-linkedin-in',
            'url' => "https://www.linkedin.com/sharing/share-offsite/?url={$url}"
        ],
        'Reddit' => [
            'enabled' => $helper->getShareRedditEnabled(),
            'icon_class' => 'fa-brands',
            'icon' => 'fa-reddit-alien',
            'url' => "https://www.reddit.com/submit?url={$url}&title={$title}"
        ],
        'Email' => [
            'enabled' => $helper->getShareEmailEnabled(),
            'icon_class' => 'fa-solid',
            'icon' => 'fa-envelope',
            'url' => "mailto:?subject={$title}&body={$url}"
        ]
    ];

    $result = [];

    foreach ($platforms as $label => $data) {
        if (!empty($data['enabled'])) {
            $result[] = [
                'label' => __($label),
                'icon_class' => $data['icon_class'],
                'icon' => $data['icon'],
                'url' => $data['url']
            ];
        }
    }

    return $result;
}
}
