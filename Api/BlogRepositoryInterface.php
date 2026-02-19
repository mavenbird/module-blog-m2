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

namespace Mavenbird\Blog\Api;

use Exception;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class PostInterface
 * @package Mavenbird\Blog\Api
 */
interface BlogRepositoryInterface
{
    /**
     * @return \Mavenbird\Blog\Api\Data\PostInterface[]
     */
    public function getAllPost();

    /**
     * @return \Mavenbird\Blog\Api\Data\MonthlyArchiveInterface[]
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function getMonthlyArchive();

    /**
     * @param string $monthly
     * @param string $year
     *
     * @return \Mavenbird\Blog\Api\Data\PostInterface[]
     */
    public function getPostMonthlyArchive($monthly, $year);

    /**
     * @param string $postId
     *
     * @return \Mavenbird\Blog\Api\Data\PostInterface
     * @throws Exception
     */
    public function getPostView($postId);

    /**
     * @param string $authorName
     *
     * @return \Mavenbird\Blog\Api\Data\PostInterface[]
     */
    public function getPostViewByAuthorName($authorName);

    /**
     * @param string $authorId
     *
     * @return \Mavenbird\Blog\Api\Data\PostInterface[]
     */
    public function getPostViewByAuthorId($authorId);

    /**
     * @param string $postId
     *
     * @return \Mavenbird\Blog\Api\Data\CommentInterface[]
     */
    public function getPostComment($postId);

    /**
     * Get All Comment
     *
     * @return \Mavenbird\Blog\Api\Data\CommentInterface[]
     */
    public function getAllComment();

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Mavenbird\Blog\Api\Data\SearchResult\CommentSearchResultInterface
     */
    public function getCommentList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param string $commentId
     *
     * @return \Mavenbird\Blog\Api\Data\CommentInterface
     */
    public function getCommentView($commentId);

    /**
     * @param string $postId
     *
     * @return string
     */
    public function getPostLike($postId);

    /**
     * @param string $tagName
     *
     * @return \Mavenbird\Blog\Api\Data\PostInterface[]
     */
    public function getPostByTagName($tagName);

    /**
     * @param string $postId
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProductByPost($postId);

    /**
     * @param string $postId
     *
     * @return \Mavenbird\Blog\Api\Data\PostInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPostRelated($postId);

    /**
     * @param string $postId
     * @param \Mavenbird\Blog\Api\Data\CommentInterface $commentData
     *
     * @return \Mavenbird\Blog\Api\Data\CommentInterface
     * @throws Exception
     */
    public function addCommentInPost($postId, $commentData);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Mavenbird\Blog\Api\Data\SearchResult\PostSearchResultInterface
     * @throws NoSuchEntityException
     */
    public function getPostList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Create Post
     *
     * @param \Mavenbird\Blog\Api\Data\PostInterface $post
     *
     * @return \Mavenbird\Blog\Api\Data\PostInterface
     * @throws Exception
     */
    public function createPost($post);

    /**
     * Delete Post
     *
     * @param string $postId
     *
     * @return string
     */
    public function deletePost($postId);

    /**
     * @param string $postId
     * @param \Mavenbird\Blog\Api\Data\PostInterface $post
     *
     * @return \Mavenbird\Blog\Api\Data\PostInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updatePost($postId, $post);

    /**
     * Get All Tag
     *
     * @return \Mavenbird\Blog\Api\Data\TagInterface[]
     */
    public function getAllTag();

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Mavenbird\Blog\Api\Data\SearchResult\TagSearchResultInterface
     */
    public function getTagList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Create Post
     *
     * @param \Mavenbird\Blog\Api\Data\TagInterface $tag
     *
     * @return \Mavenbird\Blog\Api\Data\TagInterface
     * @throws Exception
     */
    public function createTag($tag);

    /**
     * Delete Tag
     *
     * @param string $tagId
     *
     * @return string
     */
    public function deleteTag($tagId);

    /**
     * @param string $tagId
     *
     * @return \Mavenbird\Blog\Api\Data\TagInterface
     */
    public function getTagView($tagId);

    /**
     * @param string $tagId
     * @param \Mavenbird\Blog\Api\Data\TagInterface $tag
     *
     * @return \Mavenbird\Blog\Api\Data\TagInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updateTag($tagId, $tag);

    /**
     * Get Topic List
     *
     * @return \Mavenbird\Blog\Api\Data\TopicInterface[]
     */
    public function getAllTopic();

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Mavenbird\Blog\Api\Data\SearchResult\TopicSearchResultInterface
     */
    public function getTopicList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param string $topicId
     *
     * @return \Mavenbird\Blog\Api\Data\TagInterface
     */
    public function getTopicView($topicId);

    /**
     * @param string $topicId
     *
     * @return \Mavenbird\Blog\Api\Data\PostInterface[]
     */
    public function getPostsByTopic($topicId);

    /**
     * Create Topic
     *
     * @param \Mavenbird\Blog\Api\Data\TopicInterface $topic
     *
     * @return \Mavenbird\Blog\Api\Data\TopicInterface
     * @throws Exception
     */
    public function createTopic($topic);

    /**
     * Delete Topic
     *
     * @param string $topicId
     *
     * @return string
     */
    public function deleteTopic($topicId);

    /**
     * @param string $topicId
     * @param \Mavenbird\Blog\Api\Data\TopicInterface $topic
     *
     * @return \Mavenbird\Blog\Api\Data\TopicInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updateTopic($topicId, $topic);

    /**
     * Get All Category
     *
     * @return \Mavenbird\Blog\Api\Data\CategoryInterface[]
     */
    public function getAllCategory();

    /**
     * Get Category List
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Mavenbird\Blog\Api\Data\SearchResult\CategorySearchResultInterface
     */
    public function getCategoryList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param string $categoryId
     *
     * @return \Mavenbird\Blog\Api\Data\CategoryInterface
     */
    public function getCategoryView($categoryId);

    /**
     * @param string $categoryId
     *
     * @return \Mavenbird\Blog\Api\Data\PostInterface[]
     */
    public function getPostsByCategoryId($categoryId);

    /**
     * @param string $categoryKey
     *
     * @return \Mavenbird\Blog\Api\Data\PostInterface[]
     */
    public function getPostsByCategory($categoryKey);

    /**
     * @param string $postId
     *
     * @return \Mavenbird\Blog\Api\Data\CategoryInterface[]
     */
    public function getCategoriesByPostId($postId);

    /**
     * Create Category
     *
     * @param \Mavenbird\Blog\Api\Data\CategoryInterface $category
     *
     * @return \Mavenbird\Blog\Api\Data\CategoryInterface
     * @throws Exception
     */
    public function createCategory($category);

    /**
     * Delete Category
     *
     * @param string $categoryId
     *
     * @return string
     */
    public function deleteCategory($categoryId);

    /**
     * @param string $categoryId
     * @param \Mavenbird\Blog\Api\Data\CategoryInterface $category
     *
     * @return \Mavenbird\Blog\Api\Data\CategoryInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updateCategory($categoryId, $category);

    /**
     * Get Author List
     *
     * @return \Mavenbird\Blog\Api\Data\AuthorInterface[]
     */
    public function getAuthorList();

    /**
     * Create Author
     *
     * @param \Mavenbird\Blog\Api\Data\AuthorInterface $author
     *
     * @return \Mavenbird\Blog\Api\Data\AuthorInterface
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createAuthor($author);

    /**
     * Delete Author
     *
     * @param string $authorId
     *
     * @return string
     */
    public function deleteAuthor($authorId);

    /**
     * @param string $authorId
     * @param \Mavenbird\Blog\Api\Data\AuthorInterface $author
     *
     * @return \Mavenbird\Blog\Api\Data\AuthorInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updateAuthor($authorId, $author);

    /**
     * @return \Mavenbird\Blog\Api\Data\BlogConfigInterface
     *
     * @throws InputException
     */
    public function getConfig();
}
