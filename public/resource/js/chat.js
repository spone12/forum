function stopEditMessage() {
    $('.edit_msg_stop').hide();
    $('#dialog__message').val('').attr('isEdit', false);
    $('.dialog__send')
        .attr('src', '/img/chat/send_message.png')
        .attr('onclick', 'sendMessage();');
}

function sendMessage() {
  let message = $.trim($('.dialog__message').val());
  let dialogWithId = $('#dialogWithId').val();
  let dialogId = $('#dialogId').val();

  if (jQuery.isEmptyObject(message)) {
    return;
  }

  $.ajax(
  {
        url: '/chat/send_message',
        type: "POST",
        headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
        async: false,
        data: {
           message: message,
           dialogWithId: dialogWithId,
           dialogId: dialogId
        }, success: function (data) {

            $('#dialog__message').val('');

            var newMessage = $('.chatLs__chat:eq(0)').clone();
            newMessage.find('.chatLs__text').text(message);
            newMessage.find('.chatLs__photo').attr('src', data.message.avatar);
            newMessage.find('.chatLs__name').text(data.message.name);
            newMessage.find('.chatLs__link').attr('href', '/profile/' + data.message.userId);
            newMessage.find('.chatLs__message-time').text(data.message.created_at);

            newMessage.appendTo('.chatLs');
            $('.chatLs').scrollTop($('.chatLs').prop('scrollHeight'));
        },
        error: function(data) {
            errorMsgResponse(data);
        }
     });
}

/**
 *
 * @param messageId
 */
function editMessage(messageId) {
    let message = $.trim($('.dialog__message').val());
    let dialogId = $('#dialogId').val();

    if (jQuery.isEmptyObject(message)) {
        return;
    }

    $.ajax({
        url: '/chat/edit_message',
        type: "PUT",
        headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
        async: false,
        data: {
            message: message,
            messageId: messageId,
            dialogId: dialogId
        }, success: function (data) {

            $('#dialog__message').val('');
            $('#chatLs__chat-' + messageId).find('.chatLs__text').text(message);
            stopEditMessage();
        },
        error: function(data) {
            errorMsgResponse(data);
        }
    });
}

$( document ).ready(function()
{
    $('.chatLs').scrollTop($('.chatLs').prop('scrollHeight'));
    /**
     * Message edit view function
     */
    $('img[class^=chatLs__message-edit-]').on('click', function()
    {
        let messageId = $(this).attr('class').split('edit-')[1];
        let message = $(this).closest('.chatLs__chat').find('.chatLs__text').text();
        $('#dialog__message').val(message).attr('isEdit', true);
        $('.dialog__send')
            .attr('src', '/img/icons/edit.png')
            .attr('onclick', 'editMessage(' + messageId + ');');
        $('.edit_msg_stop').show();
        $('html, body').animate({scrollTop: $(document).height() - $(window).height()}, 300);
    });

    /**
     * Message delete view function
     */
    $('img[class^=chatLs__message-delete-]').on('click', function()
    {

        let messageId = $(this).attr('class').split('delete-')[1];
        let dialogId = $('#dialogId').val();
        let mainBlock = $(this).closest('.chatLs__chat');
        mainBlock
            .addClass('delete_message')
            .find('.chatLs__move-edit, .chatLs__move-delete').addClass('hide_message_btn');
        mainBlock.find('.chatLs__move-recover').removeClass('hide_message_btn');

        $.ajax({
            url: '/chat/delete_message',
            type: "DELETE",
            headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
            async: false,
            data: {
                messageId: messageId,
                dialogId: dialogId
            }, success: function (data) {
            },
            error: function(data) {
                errorMsgResponse(data);
            }
        });
    });

    /**
     * Message recover view function
     */
    $('img[class^=chatLs__message-recover-]').on('click', function()
    {
        let messageId = $(this).attr('class').split('recover-')[1];
        let dialogId = $('#dialogId').val();
        let mainBlock = $(this).closest('.chatLs__chat');
        mainBlock
            .removeClass('delete_message')
            .find('.chatLs__move-edit, .chatLs__move-delete').removeClass('hide_message_btn');
        mainBlock.find('.chatLs__move-recover').addClass('hide_message_btn');

        $.ajax({
            url: '/chat/recover_message',
            type: "PUT",
            headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
            async: false,
            data: {
                messageId: messageId,
                dialogId: dialogId
            }, success: function (data) {
            },
            error: function(data) {
                errorMsgResponse(data);
            }
        });
    });

    /**
     * Stop edit message
     */
    $('.edit_msg_stop').on('click', function () {
        stopEditMessage();
    });

    $('.chatLs__chat').hover(
       function () {
           $(this).find('.chatLs__move-edit').show();
           $(this).find('.chatLs__move-delete').show();
       },
       function () {
           $(this).find('.chatLs__move-edit').hide();
           $(this).find('.chatLs__move-delete').hide();
       }
    );

    $(".search_chat").on("click", function()
    {
     if($(this).attr('isQuery') == 0)
        return;

     $(this).attr('isQuery', 0).text('/');
     $('#chatSearch').val("");

     $('.Chat-search__item').not('.Chat-search__item:first').remove();
     $('.Chat-search').hide();
     $('.mainData').show();
    });

    $('#chatSearch').on('change', function()
    {
         var searchWord = $.trim($(this).val());

         if (jQuery.isEmptyObject(searchWord)) {
             $('.Chat-search__item').not('.Chat-search__item:first').remove();
             $('.Chat-search').hide();
             $('.mainData').show();
             return;
         }

         $.ajax(
         {
            url: '/chat/search',
            type: "POST",
            headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
            data: {word: searchWord},
            success: function (data)
            {
               $('.mainData').hide();

               if (jQuery.isEmptyObject(data.searched[0])) {
                  $('.Chat-search__item:eq(0)').find('.Chat-search_body').html('Рузультатов поиска нет');
                  $('.Chat-search__item').not('.Chat-search__item:first').remove();
                  return;
               }

               $('.search_chat').attr('isQuery', 1).text('x');

               var chat = $('.Chat-search__item:eq(0)').clone();

               $('.Chat-search__item:eq(0)').find('.Chat-search_body')
                   .html('Результат поиска: '+ data.searched.length + ' элемент');
               $('.Chat-search__item').not('.Chat-search__item:first').remove();

               $.each(data.searched, function(key, searchItem) {
                  var elements = chat.clone();
                  elements.find('.Chat-search_body').text(searchItem.text).attr('href', '/chat/dialog/' + searchItem.dialog_id);
                  elements.find('.Chat-search__photo').attr('src', searchItem.avatar);
                  elements.find('.Chat-search__link').text(searchItem.name).attr('href', '/profile/' + searchItem.id);

                  elements.appendTo('.Chat-search');
               });

               $('.Chat-search').show();
            },
            error: function(data) {
               var errors = data.responseJSON;
               console.log(errors);
            }
         });
    });

    // If press Enter -> run send or edit message function
    $(document).keypress(function (e)
    {
        if (e.which === 13) {
           if ($('input').hasClass("dialog__message")) {
              $('.dialog__send').click();
           }
        }
    });
});
