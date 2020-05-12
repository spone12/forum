$(function() 
{
    $rotated = false;
    $('#navbarDropdown').hover(function()
    {
        if($rotated == false)
        {
            $('#navigation_arrow').css({
                'transform': 'rotate(' + 90 + 'deg)',
                'transition': 'all 0.5s ease 0s'
             });
             $rotated = true;
        }
        else
        {
            $('#navigation_arrow').css({
                'transform': 'rotate(' + 0 + 'deg)',
                'transition': 'all 0.5s ease 0s'
             });
             $rotated = false;
        }
    });
    

    $('#search').on('click', function(){
        $('.search-marker').hide();
    });
    $('#search').blur(function(){
        $('.search-marker').show();
    })
});