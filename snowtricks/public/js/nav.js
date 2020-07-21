
jQuery(function() {
    let w;

        w = $(window).width();
        if (w < 975) {
            $('nav').removeClass('fixed-top').addClass('fixed-bottom').addClass("bg-dark");
            $('#nav-screen-lg').hide();
            $('#nav-screen-sm').show();
        } else {
            $('nav').removeClass('fixed-bottom').addClass('fixed-top');
            $('#nav-screen-lg').show();
            $('#nav-screen-sm').hide();
        }


    $(window).scroll(function() {
        let scroll = $(window).scrollTop();
        if (scroll >= 90) {
            if(!$('nav').hasClass('fixed-bottom'))
                $(".my-nav").addClass("bg-dark");

        } else {
            if(!$('nav').hasClass('fixed-bottom'))
                $(".my-nav").removeClass("bg-dark");

        }
    });

    //Navigation topbottom
    $(".to-tricks-top").hide();
    $(".to-tricks").hide();


    $(document).ready(function() {
        $('.smooth-scroll').on('click', function() { // Au clic sur un élément
            var page = $(this).attr('href'); // Page cible
            var speed = 400; // Durée de l'animation (en ms)
            $('html, body').animate( { scrollTop: $(page).offset().top }, speed ); // Go
            return false;
        });
    });
});