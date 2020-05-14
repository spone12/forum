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
        $('.search-marker').hide(300);
    });
    $('#search').blur(function(){
        $('.search-marker').show(300);
    })

    $('body').on('click', '.password-control', function()
    {
        if ($('#password').attr('type') == 'password')
        {
            $(this).addClass('view');
            $('#password').attr('type', 'text');
        } 
        else
        {
            $(this).removeClass('view');
            $('#password').attr('type', 'password');
        }

        return false;
    });
});