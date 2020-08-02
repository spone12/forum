   $( document ).ready(function() 
   {
      $id =  $(document).find('.search-by__selected').attr('id');
      search_by_val($id);
   });
  
  function change_search_by(id)
  {
     $('*').removeClass('search-by__selected');
     $('#' + id).addClass('search-by__selected');
     $('#search-by').val(id);
  }

  function search_by_val(id)
  {
     $('#search-by').val(id);
  }