jQuery(function() {
    const flashes = $(".flash");
    const nbFlash = flashes.length;

    if(nbFlash > 0){

        let top = 0;
        //flashes.css('left','0').css('opacity',1);
        flashes.each(function (i) {
            let flash = $(this);
            $(this).removeClass('d-none');
            let width = $(this).width()+top;
            top += 83;
            $(this).css('opacity',0);
            $(this).css('top',-83);

            $(this).animate({
                opacity: 1,
                top: top,

            }, 400, function() {
               setTimeout(function () {
                flash.animate({
                    opacity: 1,
                    top: -85,

                },500,function () {
                    flash.remove();
                });
               },3000)
            });
        });
    }

});