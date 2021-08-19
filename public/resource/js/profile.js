
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
        let phone = $('#phone_user').val();

        let data_send = {name, gender, town_user, date_user, about_user, id_user, phone};
       
        $.ajax(
            {
                url: '/change_profile_confirm/' + id_user,
                type: "PUT",
                data: {
                        data_send: data_send
                      },
                dataType: "JSON",
                headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
                success: function (data) 
                {
                  if(!data.data_user.original.status)
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
                    if(data.data_user.original.status == 1)
                        location.href = '/profile';
                  }
                },
                error: function(data) 
                {
                   //getError(data.responseText);
                    var errors = data.responseJSON;
                    console.log(data.responseText);
                }
            });
    }

    function c_confidentiality(id)
    {

        $('#modal_window_text').html("<div class='row mb-3'><div class='col-12'><b>" + id + "</b>g</div></div>" +
                                      "<div class='row mb-0 align-items-center d-flex  justify-content-start'>" +
                                      "<div class='col-1'><input class='form-control' type='radio' name=" + id + " /></div><div class='col-10 '>Открыть для всех</div>" +
                                       "</div>" +
                                       "<div class='row mb-2 align-items-center d-flex justify-content-start'>" +
                                      "<div class='col-1'><input class='form-control' type='radio' name=" + id + " /></div><div class='col-10'>Скрыть для всех</div>" +
                                       "</div>" +
                                      "<div class='row align-items-center'><button class='col-12 btn btn-primary'>Готово</button></div>"
                                      );
                       
        $('#modal_window').modal('show');
    }