   $( document ).ready(function() 
   {
      $id =  $(document).find('.search-by__selected').attr('id');
      searchByVal($id);
   });
  
  function changeSearchBy(id)
  {
     $('*').removeClass('search-by__selected');
     $('#' + id).addClass('search-by__selected');
     $('#search-by').val(id);
  }

  function searchByVal(id)
  {
     $('#search-by').val(id);
  }