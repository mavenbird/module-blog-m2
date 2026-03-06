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
define([
  "jquery",
  "mage/translate",
  "underscore",
  "Magento_Ui/js/modal/modal",
  "Magento_Ui/js/modal/confirm",
  "uiRegistry",
  "moment",
  "Mavenbird_Blog/js/get-editor",
], function ($, $t, _, modal, confirm, registry, moment, editor) {
  "use strict";

  $.widget("mavenbird.mbBlogManagePost", {
    options: {
      deleteUrl: "",
      basePubUrl: "",
      postDatas: {},
      editorVersion: "",
      magentoVersion: "",
    },
    _create: function () {
      var self = this,
        htmlPopup = $("#mb-blog-new-post-popup");

      $(".mb-blog-new-post button").on("click", function () {
        self._AddNewPost(self, htmlPopup);
      });

      $(".mbblog-post-edit").on("click", function () {
        self._EditPost(self, this, htmlPopup);
      });

      $(".mbblog-post-duplicate").on("click", function () {
        self._DuplicatePost(self, this, htmlPopup);
      });

      $(".mbblog-post-delete").on("click", function () {
        self._DeletePost(self, this);
      });
    },
    _AddNewPost: function (self, htmlPopup) {
      var options = {
        type: "popup",
        title: $t("Add New Post"),
        responsive: true,
        innerScroll: true,
        buttons: [],
      };

      self._resetForm("");
      self._openPopup(options, htmlPopup, self);
    },
    _resetForm: function (postContent) {
      var iframe = document.getElementById("post_content_ifr");

      $("#mb_blog_post_form").trigger("reset");
      $("#short_description").empty();
      $("#post_content").empty();
      $("#post_id").removeAttr("value");

      if (iframe) {
        iframe.contentWindow.document.open();
        iframe.contentWindow.document.write(postContent);
        iframe.contentWindow.document.close();
      }

      $(".mb-field .mb-image-link").remove();
      registry.get("customCategory").value("");
      registry.get("customTag").value("");
      registry.get("customTopic").value("");
    },
    _EditPost: function (self, click, htmlPopup) {
      var postId = $(click).parent().data("postid"),
        postData = self.options.postDatas[postId],
        pubUrl = self.options.basePubUrl,
        options = {
          type: "popup",
          title: $t("Edit Post"),
          responsive: true,
          innerScroll: true,
          buttons: [],
        };
      if (htmlPopup.find('#mb_blog_post_form [name="name"]').length > 0) {
        self._resetForm(postData["post_content"]);
      }
      self._openPopup(options, htmlPopup, self);
      self._setPopupFormData(postData, pubUrl, htmlPopup);
    },
    _setPopupFormData: function (postData, pubUrl, htmlPopup) {
      _.each(postData, function (value, name) {
        var field = htmlPopup.find('#mb_blog_post_form [name="' + name + '"]'),
          imageEL,
          deleteEL,
          date;
        if (field.is('[type="file"]') && value) {
          imageEL =
            '<a class="mb-image-link" href="' +
            pubUrl +
            "mavenbird/blog/post/" +
            value +
            '" onclick="imagePreview(\'post_image_image\'); return false;" >' +
            '<img src="' +
            pubUrl +
            "mavenbird/blog/post/" +
            value +
            '" id="post_image_image"' +
            ' title="' +
            value +
            '" alt="' +
            value +
            '" height="22" width="22"' +
            ' class="small-image-preview v-middle">' +
            "</a>";
          field.parent().prepend(imageEL);
          deleteEL =
            '<span class="delete-image">' +
            '<input style="width: 8%" type="checkbox" name="image[delete]"' +
            ' value="1" class="checkbox" id="post_image_delete">' +
            '<label for="post_image_delete"> Delete Image</label>' +
            "</span>";
          if (!field.parent().find(".delete-image").lenth) {
            field.parent().append(deleteEL);
          }
        } else if (field.is('[type="datetime-local"]')) {
          date = moment(value).format("YYYY-MM-DDTkk:mm");
          field.val(date);
        } else if (field.is("input") || field.is("select")) {
          field.val(value);
        } else if (field.is("textarea")) {
          field.html(value);
        }
        if (name === "category_ids") {
          registry.get("customCategory").value(value);
        }
        if (name === "tag_ids") {
          registry.get("customTag").value(value);
        }
        if (name === "topic_ids") {
          registry.get("customTopic").value(value);
        }
      });
    },
    _DuplicatePost: function (self, click, htmlPopup) {
      var postId = $(click).parent().data("postid"),
        postData = self.options.postDatas[postId],
        pubUrl = self.options.basePubUrl,
        options = {
          type: "popup",
          title: $t("Duplicate Post"),
          responsive: true,
          innerScroll: true,
          buttons: [],
        };

      if (htmlPopup.find('#mb_blog_post_form [name="name"]').length > 0) {
        self._resetForm(postData["post_content"]);
      }
      self._openPopup(options, htmlPopup, self);
      self._setPopupFormData(postData, pubUrl, htmlPopup);
      $("#post_id").removeAttr("value");
      $("#image")
        .parent()
        .append(
          '<input type="hidden" name="image" value="' +
            postData["image"] +
            '" />',
        );
    },
    _DeletePost: function (self, widget) {
    var url = self.options.deleteUrl,
        parent = $(widget).closest(".post-info-action"),
        id = parent.data("postid") || parent.data("postId") || parent.attr("data-postid") || parent.attr("data-postId"),
        postData = self.options.postDatas[id] || {},
        postName = postData.name || "";

    confirm({
        title: $t("Delete Post"),
        content: $t('Are you sure you want to delete the post "%1" ?').replace('%1', postName),
        actions: {
            confirm: function () {
                $.ajax({
                    url: url,
                    type: "post",
                    data: {
                        post_id: id
                    },
                    showLoader: true,
                    success: function (result) {
                        if (result.status === 1) {
                            $('.post-list-item[data-post-id="' + result.post_id + '"]').remove();
                        }
                    }
                });
            }
        }
    });
},
    _openPopup: function (options, htmlPopup, self) {
      var popupModal,
        editorVersion = self.options.editorVersion,
        magentoVersion = self.options.magentoVersion;

      popupModal = modal(options, htmlPopup);
      popupModal.openModal();
      $("#mb_blog_post_form").trigger("contentUpdated");

      editor.config("post_content", editorVersion, magentoVersion);
    },
  });

  return $.mavenbird.mbBlogManagePost;
});
