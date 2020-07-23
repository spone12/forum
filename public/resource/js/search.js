
  function change_search_by(id)
  {
     $('*').removeClass('search-by__selected');
     $('#' + id).addClass('search-by__selected');
     $('#search-by').val(id);
  }