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


require([
    'jquery'
], function ($) {
    'use strict';

    var cmtBox = $('.mbblog-comment-textarea'),
        submitCmt = $('.mbblog-comment-submitbtn'),
        defaultCmt = $('ul.mbblog-comment-details:first'),
        likeBtn = defaultCmt.find('.btn-like'),
        replyBtn = defaultCmt.find('.btn-reply');

    submitComment();
    likeComment(likeBtn);
    showReply(replyBtn);

    $('li.mbblog-comment-details-list:first').css({'border-top': 'none'});
    $('.default-cmt__cmt-login__btn-login').click(function () {
        var socialPopup = $("[href$='social-login-popup']");

        if (socialPopup.length) {
            socialPopup.first().trigger("click");
        } else {
            window.location.href = loginUrl;
        }
    });

    /**
     * Check the guest name and email input is valid
     *
     * @returns {boolean}
     */
    function checkGuestFormValidate() {
        if (isLogged == 'No') {
            return $("#mbblog-comment-form").valid();
        }
        return true;
    }

    /**
     * The comment submit button action
     */
    function submitComment() {
        submitCmt.click(function () {
            $(".mbblog-comment-fields").find('.messages').hide();
            if (checkGuestFormValidate()) {
                var cmtText = cmtBox.val();

                if (cmtText.trim().length) {
                    $('.mbblog-comment-loading').show();
                    $(this).prop('disabled', true);
                    var ajaxRequest = ajaxCommentActions(cmtText, submitCmt);
                    ajaxRequest.done(function () {
                        cmtBox.val('');
                        $('.mbblog-comment-loading').hide();
                        $(this).prop('disabled', false);
                    }.bind(this));
                } else {
                    $('.mbblog-comment-textarea').parent().append(messengerBox.cmt_warning);
                }
            }
        });
    }

    //like action
    function likeComment(btn) {
        btn.each(function () {
            var likeEl = $(this);

            likeEl.click(function () {
                var cmtId = $(this).attr('data-cmt-id'),
                    cmtRowContainer = $(this).closest('.mbblog-comment-details-list');
                if (isLogged === 'Yes') {
                    var likeCount = $(this).find('span').text();
                    if ($(this).attr('click') === '1') {
                        if ($(this).hasClass('mbblog-liked')) {
                            $(this).css('color', '#333333');
                            likeCount--;
                            $(this).find('span').text((likeCount === 0) ? "" : likeCount);
                            $(this).removeClass('mbblog-liked')

                        } else {
                            likeCount++;
                            $(this).find('span').text(likeCount);
                            $(this).css('color', likedColor);
                            $(this).addClass('mbblog-liked')
                        }
                        $.ajax({
                            type: "POST",
                            url: window.location.href,
                            data: {cmtId: cmtId},
                            success: function (response) {
                                if (response.status === 'ok') {
                                    $(likeEl).attr('click', '1');
                                } else if (response.status === 'error' && response.hasOwnProperty(error)) {
                                    defaultCmt.append(response.error);
                                }
                            }
                        });
                    }
                    $(this).attr('click', '0');

                } else {
                    cmtRowContainer.append(messengerBox.login_warning);
                    jQuery.fn.fadeOutAndRemove = function (speed) {
                        $(this).fadeOut(speed, function () {
                            $(this).remove();
                        })
                    };
                    var removeNotification = function () {
                        $('.message.error.message-error').fadeOutAndRemove('normal');
                    };
                    setTimeout(removeNotification, 3000);
                }

            });
        });
    }

    //show reply
    function showReply(btn) {
        btn.each(function () {

            $(this).click(function () {
                var cmtId = (typeof $(this).closest('.mbblog-comment-details-list').parent().parent().parent().parent().attr('data-cmt-id') !== 'undefined') ? $(this).closest('.mbblog-comment-details-list').parent().parent().attr('data-cmt-id') : $(this).attr('data-cmt-id'),
                    inputCmtID = $(this).attr('data-cmt-id'),
                    cmtRowCmt = $("div").find('#cmt-row');
                var cmtRowContainer = $(this).closest('.mbblog-comment-details-list');
                if ($("li.cmt-row-" + cmtId).find("ul").length) {
                    var cmtRowContainer = $("#cmt-id-" + cmtId + " ul:last-child");
                }
                var cmtRow = cmtRowContainer.find('.row__' + inputCmtID);
                var cmtName = $(".username__" + inputCmtID).text();

                if (isLogged === 'Yes') {
                    if (cmtRowCmt.length) {
                        cmtRowCmt.toggle();
                        $("#cmt-row").remove();
                    }
                    if (cmtRow.length) {
                        cmtRow.toggle();
                        $("#cmt-row").remove();
                    } else {
                        cmtRowContainer.append('<div id="cmt-row" class="cmt-row__reply-row row row__' + inputCmtID + ' col-md-12">' +
                            '<div class="reply-form__form-input form-group col-xs-8 col-md-6">' +
                            '<label for="reply_cmt' + inputCmtID + '"></label>' +
                            '<input type="text" id="reply_cmt' + inputCmtID + '" class="form-group__input form-control" placeholder="Press enter to submit reply" value="' + cmtName + ' " autofocus onfocus="this.setSelectionRange(1000,1001);"/>' +
                            '</div>' +
                            '</div>');
                        var input = $('#reply_cmt' + inputCmtID);
                        input.closest('.form-group').append(
                            $('.mbblog-comment-save .mbblog-comment-loading').clone()
                        );
                        input.focus();
                        submitReply(input, cmtId, cmtRowContainer);
                    }
                } else {
                    cmtRowContainer.append(messengerBox.login_warning);
                    jQuery.fn.fadeOutAndRemove = function (speed) {
                        $(this).fadeOut(speed, function () {
                            $(this).remove();
                        })
                    };
                    var removeNotification = function () {
                        $('.message.error.message-error').fadeOutAndRemove('normal');
                    };
                    setTimeout(removeNotification, 3000);
                }
            });
        });
    }

    //submit reply
    function submitReply(input, replyId, parentComment) {
        input.keypress(function (e) {
            var text = input.val();
            if (text !== '') {
                if (e.keyCode === 13) {
                    input.siblings('.mbblog-comment-loading').show();
                    input.prop('disabled', true);
                    var ajaxRequest = ajaxCommentActions(text, input, true, replyId, parentComment);
                    ajaxRequest.done(function () {
                        input.closest('.cmt-row__reply-row').hide();
                        input.siblings('.mbblog-comment-loading').hide();
                        input.prop('disabled', false);
                        $("#cmt-row").remove();
                    });
                }
            }
        });
    }

    //submit comment actions
    function ajaxCommentActions(cmtText, inputEl, checkReply, cmtId, parentComment) {
        var isReply = (typeof checkReply !== 'undefined') ? 1 : 0,
            replyId = (typeof cmtId !== 'undefined') ? cmtId : 0,
            displayReply = (typeof checkReply !== 'undefined');
        var guestName = $('#mbblog-comment-guest-name').val();
        var guestEmail = $('#mbblog-comment-guest-email').val();
        return $.ajax({
            type: 'POST',
            url: window.location.href,
            // async: false,
            data: {cmt_text: cmtText, isReply: isReply, replyId: replyId, guestName: guestName, guestEmail: guestEmail},
            success: function (response) {
                switch (response.status) {
                    case 'duplicated':
                        $('.mbblog-comment-textarea').parent().append(messengerBox.exist_email_warning);
                        break;
                    case 3:
                        $('.mbblog-comment-section').prepend(messengerBox.comment_approve);
                        break;
                    case 1:
                        displayComment(response, displayReply);
                        var cmtCount = defaultCmt.find('li').length;
                        $('.mb-cmt-count').text(cmtCount);
                        inputEl.val('');
                        break;
                    case 'error':
                        if (checkReply !== 'undefined') {
                            parentComment.append(response.error);
                        } else {
                            defaultCmt.append(response.error);
                        }
                        break;
                }
            }
        });
    }

    // display comment
    function displayComment(cmt, isReply) {
        function htmlComment(text) {
            var html = '';
            var sub = text.split("\n");
            for (var i = 0; i < sub.length; i++) {
                html += '<p>' + sub[i] + '</p>';
            }
            return html;
        }
        var cmtRow = '<li style="width: 100%" id="cmt-id-' + cmt.cmt_id + '" class="mbblog-comment-details-list cmt-row-' + cmt.cmt_id + ' cmt-row col-m-12 '
            + (isReply ? ('reply-row') : '') + '" data-cmt-id="' + cmt.cmt_id + '"' + (isReply ? ('data-reply-id="' + cmt.reply_cmt + '"') : '')
            + '> <div class="mbblog-comment-username"> <span class="mbblog-comment-username username username__' + cmt.cmt_id + '">' + cmt.user_cmt
            + '</span> </div> <div class="mbblog-comment-details"> <p>' + htmlComment(cmt.cmt_text)
            + '</p> </div> <div class="mbblog-comment-review interactions"> <div class="mbblog-comment-action-btn"> <a class="mbblog-comment-action-btn action btn-like mbblog-like" data-cmt-id="'
            + cmt.cmt_id + '" click="1"><i class="fa fa-thumbs-up" aria-hidden="true" style="margin-right: 3px"></i><span class="count-like__like-text"></span></a> <a class="mbblog-comment-action-btn action btn-reply" data-cmt-id="'
            + cmt.cmt_id + '">' + reply + '</a>  </div> <div class="mbblog-comment-createdate"> <span>' + cmt.created_at + '</span> </div> </div> </li>';

        if (isReply) {
            var replyCmtId = cmt.reply_cmt;
            var replyCmt = defaultCmt.find('.mbblog-comment-details-list');

            replyCmt.each(function () {
                var cmtEl = $(this);
                if (cmtEl.attr('data-cmt-id') === replyCmtId) {
                    var replyList = cmtEl.find('ul.mbblog-comment-details:first');

                    if (!replyList.length) {
                        cmtRow = $('<ul class="mbblog-comment-details row">' + cmtRow + '</ul>');
                        cmtEl.append(cmtRow);

                        likeComment(cmtRow.find('.btn-like'));
                        showReply(cmtRow.find('.btn-reply'));
                    } else {
                        cmtRow = $(cmtRow);
                        replyList.append(cmtRow);

                        likeComment(cmtRow.find('.btn-like'));
                        showReply(cmtRow.find('.btn-reply'));

                    }

                    return false;
                }
            });
        } else {
            cmtRow = $(cmtRow);
            defaultCmt.append(cmtRow);

            likeComment(cmtRow.find('.btn-like'));
            showReply(cmtRow.find('.btn-reply'));
        }
    }
});
