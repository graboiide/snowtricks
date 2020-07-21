jQuery(function() {

    $('#send-comment').on('click',function (e) {
        e.preventDefault();
        let mydata = new FormData();
        //on ajopute le primer fichier de la liste
        mydata.append('idFigure', $(this).data("id"));
        mydata.append('message', $("#comment_message").val());

        $.ajax({
            url: '/addComment',
            data: mydata,
            cache: false,
            contentType: false,
            processData: false,
            type: 'post',
            success: function (data) {
                $('#comments-area').prepend(data);
            },
            error: function() {
                console.log('erreur');
            }

        });

    });

    $('#comments-area').on('click','#delete_comment',function (e) {
        e.preventDefault();
        let comment = $(this).parent().parent().parent();

        $.ajax({
            url : '/removeComment/'+$(this).data('id'),
            type : 'GET',
            dataType : 'html',
            success : function(data){
                comment.slideUp(400,function () {
                    $(this).remove();
                });
            },
            error : function(){
                console.log('error');
            }

        });

    });
    let comment;

    let id;
    const blockTextarea = $("#textarea-edit");
    let textarea = $("#edit-comment-textarea");
    $('#comments-area').on('click','#edit_comment',function (e) {
        e.preventDefault();
        comment = $('.comment-txt-'+$(this).data("id"));

        id =  $(this).data("id");

        comment.hide();
        textarea.val(comment.text());
        console.log('valuer comment text'+ comment.text());
        blockTextarea.removeClass('d-none');
        $('.edit-'+$(this).data("id")).append(blockTextarea);

    });
    $('.abort').on('click',function (e) {
        e.preventDefault();
        hideBlockEdit();

    });

    $('.valid-edit').on('click',function (e) {
        e.preventDefault();

        updateComment(comment,id);

    });
    function hideBlockEdit() {
        comment.show();
        blockTextarea.addClass('d-none');
    }
    function updateComment(comment,id){

        let mydata = new FormData();
        //on ajopute le primer fichier de la liste
        mydata.append('id', id);
        mydata.append('message',textarea.val());

        $.ajax({
            url: '/editComment',
            data: mydata,
            cache: false,
            contentType: false,
            processData: false,
            type: 'post',
            success: function (data) {
               comment.text(data);
                hideBlockEdit();
            },
            error: function() {
                console.log('error');
                hideBlockEdit();
            }

        });

    }

});


