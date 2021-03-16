   $( document ).ready(function() 
   {
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