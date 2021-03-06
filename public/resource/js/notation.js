    $('.content.clossable').hover(function()
    {
        $(this).find('.close').animate({opacity:1},100)},
            function()
            {
                $(this).find('.close').animate({opacity:0},100)
            }
    )
    
    $(document).ready(function() 
    { 
        $(".notation_carousel_photo").click(function()
        {	
            var img = $(this);
            var src = img.attr('src'); 
            $("body").append("<div class='popup'>"+ //Добавляем в тело документа разметку всплывающего окна
                             "<div class='popup_bg'></div>"+ // Блок, который будет служить фоном затемненным
                             "<img src='"+ src +"' class='popup_img' />"+ // Само увеличенное фото
                             "</div>"); 

            $(".popup").fadeIn(800); // Медленно выводим изображение

            $(".popup_bg").click(function()
            {	
                // Событие клика на затемненный фон	   
                $(".popup").fadeOut(800);	// Медленно убираем всплывающее окно

                setTimeout(function()
                {	// Выставляем таймер
                  $(".popup").remove(); // Удаляем разметку всплывающего окна
                }, 800);
            });
        });
        
    });

    function add_notation()
    {
        
        $.ajax(
        {
            url: '/notation',
            type: "POST",
            data: {
                    name_tema: $('#name_tema').val(), 
                    text_notation: $('#text_notation').val(),
                    method: 'add'
                  },
            headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) 
            {
                if(data.success == 1)
                {
                    location.href = '/home';
                }
                
               // console.log( data.success);
            },
            error: function(data)
            {
                var errors = data.responseJSON;

                console.log(errors);
                // blade page
                 errorsHtml = '<div class="alert alert-danger">' +
                                '<ul>';

                 $.each( errors.errors, function( key, value ) 
                 {
                      errorsHtml += '<li>'+ value + '</li>';
                 });
                 errorsHtml += '</ul></div>';

                 $( '#form-errors' ).html( errorsHtml ); //appending to a <div id="form-errors"></div> 
            }
            
            
        });
    }

    function change_rating(action = 1)
    {
        if(action != 0 && action != 1)
            return;

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
                    success: function (data) 
                    {
                       if(data.success == 1)
                       {
                            let rating = Number($('#rating_voted').text());

                            if(action == 1)
                            {
                                rating++;
                                url = '/img/icons/like.svg';
                                class_add = 'rating_like';
                            }  
                            else
                            {
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
                       //console.log( data);
                    },
                    error: function(data)
                    {
                        var errors = data.responseJSON;
                        console.log(errors);
                    }
                    
                    
                });
    }
    
    function edit_notation()
    {
        let notation_id = $('#id_notation').val();
        let name_tema = $('#name_tema').val();
        let text_notation = $('#text_notation').val();

        $.ajax(
            {
                url: '/notation/edit_upd/' + notation_id,
                type: "PUT",
                data: {
                        notation_id: notation_id, 
                        name_tema: name_tema,
                        text_notation, text_notation
                      },
                headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
                success: function (data) 
                {
                   if(data.success === true)
                   {
                        $('#modal_window_text').text('Новость успешно изменена!');
                       // $('#modal_window_close').attr('href', '/notation/view/' + notation_id);
                        $('#modal_window').modal('show');

                        $('#modal_window_close').on('click', function(){
                            window.location.href = '/notation/view/' + notation_id;
                        })
                   }
                   //console.log( data);
                },
                error: function(data)
                {
                    var errors = data.responseJSON;
                    console.log(errors);
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
                    data: {
                            notation_id: notation_id
                          },
                    headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
                    success: function (data) 
                    {
                        if(data.success.status == 1)
                        {
                            $('#modal_window').modal('hide');
                            window.location.href = '/';
                        }

                       console.log( data);
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
                   if(data.answer === 'success')
                   {
                       location.href = '';
                   }
                   //console.log( data);
                },
                error: function(data)
                {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
    }