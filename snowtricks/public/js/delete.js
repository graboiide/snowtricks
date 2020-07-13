jQuery(function() {

    $("#delete-figure").on('click',function (e) {
        e.preventDefault();
        $('#deleteModal').modal('show');
        $('#confirm-delete').attr('href','/admin/remove/'+$(this).data('id')+'/true')
    });

});