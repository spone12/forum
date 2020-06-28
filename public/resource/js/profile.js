
    jQuery(function()
    {
      $('#page_avatar_edit').on('click', function()
      {
        $('#user_avatar').click();
      });

      $("#user_avatar").change(function()
      { 
        // событие выбора файла
        $("#form_change_avatar").submit(); // отправка формы

        /*$("#form_change_avatar").submit(function( event ) {
          //alert( "Handler for .submit() called." );
         // event.preventDefault();
         alert(event);
        });*/

      });
    });


    function edit_profile()
    {
        let name = $('#name_user').val();
        let gender = $('#gender').val();
        let town_user = $('#town_user').val();
        let date_user = $('#date_user').val();
        let about_user = $('#about_user').val();
        let id_user = $('#id_user').val();

        let data_send = {name,gender,town_user,date_user,about_user,id_user};
       
        //console.log(data_send)
        $.ajax(
            {
                url: '/change_profile_confirm/' + id_user,
                type: "POST",
                data: {
                        data_send: data_send
                      },
                headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
                success: function (data) 
                {
                  //console.log(data);
                 // console.log(data.data_user.status);
                  if(!data.data_user)
                  {

                    let errorsHtml = '<div class="alert alert-primary">' +
                                        '<ul>' +
                                        '<li>Вы не изменили ни одного поля</li>' +
                                        '</ul>' +
                                    '</div>';
                    $('#form-errors').html( errorsHtml );
                  }
                  else
                  {
                    if(data.data_user.status == 1)
                        location.href = '/profile';
                  }
                },
                error: function(data)
                {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
    }