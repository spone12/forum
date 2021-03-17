   $( document ).ready(function() 
   {
      $(".search_chat").on("click", function()
      { 
         if($(this).attr('isQuery') == 0)
            return;
         
         $(this).attr('isQuery', 0).text('/');
         $('#chatSearch').val("");  
      });

      $('#chatSearch').on('change', function()
      {
         $.ajax(
         {
            url: '/chat/search',
            type: "POST",
            headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
            data: {word: $(this).val()},
            success: function (data) 
            {
               $('.search_chat').attr('isQuery', 1).text('x');
               $('.mainData').text(data);
               console.log( data);
            },
            error: function(data)
            {
               var errors = data.responseJSON;
               console.log(errors);
            }
         });
      });
     
});