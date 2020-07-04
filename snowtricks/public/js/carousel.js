jQuery(function() {
    let beginLoad = 0;
    let endLoad = 3;
    let nbItems = 4;
    let nbMedias = $('.my-carousel-item').length;
    let w = $(window).width();

    $('.prev-items').hide();
    $('.my-carousel-item').hide();
    breackpoint();
    $(window).resize(function() {

        w = $(window).width();console.log(w);
        breackpoint();
    });
    function breackpoint() {
        if( w < 975 ){
            $('#carousel-area').hide();

        }else {
            $('#carousel-area').show();
            $('.carousel-nav').show();
            loadItems(beginLoad,beginLoad+3,0);
        }
    }

    $('.call-modal').click(function (e) {
        e.preventDefault();
        $('.modal-img').css('background-image','url('+$(this).attr('href')+')');


        $('.modal-title').text($(this).attr('id'));

    });

    $('.load-all-items').click(function (e) {

        e.preventDefault();
        if(!$(this).hasClass('active')){
            loadItems(0,$('.my-carousel-item').length,0);
            $('#carousel-area').slideDown();
            $('.carousel-load').text('Cacher les médias');
            $('.carousel-nav').hide();
            $(this).addClass('active');
        }else{
            $(this).removeClass('active');
            $('#carousel-area').slideUp();
            $('.carousel-load').text('Afficher les médias');
        }


    });

    function indicator() {
        $('.indicator').removeClass('active');
        let index = Math.floor(beginLoad/nbItems)+1;
        console.log(index);
        $('#index-'+index).addClass('active');

    }

    function loadItems(beginLoad, endLoad, speed = 400) {
        $('.my-carousel-item').each(function (index) {
            if (index > endLoad || index < beginLoad) {
                $(this).stop().slideUp(speed);
            }
            if (index <= endLoad && index >= beginLoad) {
                $(this).stop().slideDown(speed);
            }

        });
    }

    $('.prev-items').click(function (e) {
        e.preventDefault();
        $('.next-items').show();
        beginLoad -= nbItems;
        endLoad -= nbItems;
        if(beginLoad <= 0){
            beginLoad = 0;
            endLoad = nbItems-1;
            $('.prev-items').hide(400);
        }
        loadItems(beginLoad, endLoad,300);
        indicator();
    });
    $('.next-items').click(function (e) {
        $('.prev-items').show();
        e.preventDefault();
        beginLoad += nbItems;
        endLoad += nbItems;
        if(endLoad >= nbMedias)
            $('.next-items').hide(400);
        if (beginLoad > nbMedias){
            beginLoad -= nbItems;
            endLoad -= nbItems;
        }
        loadItems(beginLoad, endLoad,300);
        indicator();
    });
    loadItems(0, 3, 0);
});