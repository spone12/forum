$('.content.clossable').hover(function()
{
    $(this).find('.close').animate({opacity:1}, 100)},
    function() {
        $(this).find('.close').animate({opacity:0}, 100)
    }
)

$(document).ready(function()
{
    $(".notation_carousel_photo").click(function()
    {
        var img = $(this);
        var src = img.attr('src');
        $("body").append("<div class='popup'>"+
                         "<div class='popup_bg'></div>"+
                         "<img src='"+ src +"' class='popup_img' />"+
                         "</div>");

        $(".popup").fadeIn(800);
        $(".popup_bg").click(function()
        {
            $(".popup").fadeOut(800);
            setTimeout(function() {
               $(".popup").remove();
            }, 800);
        });
    });
});

function addNotation()
{
    $.ajax(
    {
        url: '/notation',
        type: "POST",
        data: {
            notationName: $('#name_tema').val(),
            notationText: $('#text_notation').val()
        },
        headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
        success: function (data)
        {
            if (typeof(data.notationData.notationId) == 'number') {
                successMsg(
                    data.notationData.expAdded,
                    'Новость успешно создана',
                    2000,
                    '/notation/view/' + data.notationData.notationId
                )
            }
        },
        error: function(data) {
            errorMsgResponse(data);
        }
    });
}

function change_rating(action = 1)
{
    if (action != 0 && action != 1) {
        return;
    }

    let notation_id = $('#id_notation').val();
    $.ajax(
    {
        url: '/notation/rating/' + notation_id,
        type: "POST",
        data: {
            notation_id: notation_id,
            action: action,
        },
        headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
        success: function (data) {
           if (data.success == 1) {

                let rating = Number($('#rating_voted').text());
                if (action == 1) {
                    rating++;
                    url = '/img/icons/like.svg';
                    class_add = 'rating_like';
                } else {
                    rating--;
                    url = '/img/icons/dislike.svg';
                    class_add = 'rating_dislike';
                }

                $('#rating_voted').html(rating);
                $('#rating').attr({
                    src: url,
                    class: class_add
                });
           }
        },
        error: function(data) {
            var errors = data.responseJSON;
            console.log(errors);
        }
    });
}

function editNotation()
{
    let notationId = $('#id_notation').val();
    let notationName = $('#name_tema').val();
    let notationText = $('#text_notation').val();

    $.ajax(
    {
        url: '/notation/edit_upd/' + notationId,
        type: "PUT",
        data: {
            notationId: notationId,
            notationName: notationName,
            notationText: notationText
        },
        headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
        success: function (data) {

           if (data.success === true) {
               successMsg(
                   'Новость успешно изменена',
                   '', 2000,
                   '/notation/view/' + notationId
               );
           }
        },
        error: function(data)
        {
            errorMsgResponse(data);
        }
    });
}

function notation_delete()
{
    let notation_id = $('#id_notation').val();
    $('#modal_window_text').text('Вы действительно хотите удалить новость?');
    $('#modal_window_button').attr('type', 'button');
    $('#modal_window').modal('show');

     $('#modal_window_button').on('click', function()
     {
        $.ajax(
        {
            url: '/notation/delete/' + notation_id,
            type: "DELETE",
            data: { notation_id: notation_id },
            headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
            success: function (data)
            {
                $('#modal_window').modal('hide');
                if (data.success.status == 1) {
                    tata.text('', 'Новость успешно удалена', {
                        duration: 2000,
                        animate: 'slide',
                        position: 'tr',
                        onClose: function(){
                            window.location.href = '/';
                        }
                    });
                }
            },
            error: function(data)
            {
                var errors = data.responseJSON;
                console.log(errors);
            }
        });
     })
}

function del_photo(photo_id, notation_id)
{
    $.ajax(
    {
        url: '/notation/delete_photo/' + notation_id,
        type: "DELETE",
        data: {
            photo_id: photo_id,
            notation_id: notation_id
        },
        headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
        success: function (data)
        {
           if(data.success === 'success') {
               window.location.href = '';
           }
        },
        error: function(data)
        {
            var errors = data.responseJSON;
            console.log(errors);
        }
    });
}
