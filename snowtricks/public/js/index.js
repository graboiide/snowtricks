jQuery(function() {

    let clone;
    let parent;
    let idtarget = null;
    $('.tricks-area').on('click','.remove-tricks',function(e){
        console.log("test");
        idtarget = $(this).data('id');
        e.preventDefault();
        parent = $(this).parent().parent().parent().parent();
        clone = parent.clone().appendTo('.modal-body');
        clone.children().find('.remove-tricks').remove();
        console.log(clone);
        $('#deleteModal').modal('show');
    });

    $('#confirm-delete').on('click',function () {
        $.get( "/admin/remove/"+idtarget)
            .done(function( data ) {
                parent.find('.flex-nowrap').remove();
                parent.find('img').css('opacity','0.3');
                parent.find('h5').text('Supprimer').css('opacity','0.3');
                $('#deleteModal').modal('hide');
            });

    });

    $('#deleteModal').on('hidden.bs.modal', function (e) {
        if(clone){
            clone.remove();
        }
    })

});