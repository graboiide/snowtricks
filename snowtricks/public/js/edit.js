jQuery(function() {

    let viewImg = null;
    let idTarget = null;
    let coverEdit = false;
    const coverInfo = $('.cover-info');
    let index = $('#tricks_medias .form_tricks').length;
    let addMedia = false;

    coverInfo.hide();
    $('#cover-edit').on('click',function (e) {
        e.preventDefault();
        if (coverEdit){
            coverInfo.hide(400);
            coverEdit = false;
            $(this).children().css('color','#476f9e');
            return;
        }
        if (!coverEdit){
            coverInfo.show(400);
            coverEdit = true;
            $(this).children().css('color','#ff864f');
            $('img').on('click',function (e) {
                if (coverEdit)
                    $('#cover').attr('src',$(this).attr('src'));
                $('#tricks_cover').val($(this).attr('src'));

            });
            return;
        }
    });
    $('#add-media').on('click',function (e) {
        e.preventDefault();
        addMedia = true;


        // ajout du formualire sdans la collection
        const formTricks = $('#tricks_medias');
        const template= formTricks.data('prototype').replace(/__name__/g,index);

        formTricks.append(template);
        idTarget = '#tricks_medias_'+index;
        autoUrl();
        const target = $(idTarget);
        target.removeClass('d-none');

        $('.modal-body').append(target);
        $('.modal-title').text('Ajouter un média');
        $(idTarget+'_type').on('change',function () {
            displayUploadButton();
        });
        displayUploadButton();
    });


    $('#cover-remove').on('click',function (e) {
        $('#cover').attr('src',$('#tricks_medias_0_view img').attr('src'));
        $('#tricks_cover').val('');
    });
    $('#tricks_name').on('keyup',function (e) {
        $('.title-tricks h1').text($('#tricks_name').val());
    });

    $('#loader-image').on('click','.edit',function (e) {
        e.preventDefault();
        idTarget = $(this).parent().data('target');
        viewImg = $($(this).parent().data('target')+'_view img');
        let target = $($(this).parent().data('target'));

        target.removeClass('d-none');

        $('.modal-body').append(target);
        $('.modal-title').text($(this).data('title'));
        $(idTarget+'_type').on('change',function () {
            displayUploadButton();
        });

        //modifie url des videos en direct
        autoUrl();
        displayUploadButton();

    });
    function replaceUploadfield() {
        $(idTarget+'_url').after($('.upload-file'));
    }
    function displayUploadButton() {
        replaceUploadfield();
       if($(idTarget+'_type').val() === '0')
           $('.upload-file').show();
       else
           $('.upload-file').hide();
    }
    function autoUrl(){ console.log(idTarget+'_url');
        $(idTarget+'_url').on('change',function (e) {

            console.log($(this).val().replace('/^https://youtu.be/g','https://youtube.com/embed'));
            $(idTarget+'_url').val($(this).val().replace(/^https:\/\/youtu.be/,'https://www.youtube.com/embed'));
            $(idTarget+'_url').val($(this).val().replace(/watch\?v=/,'embed/'));
            $(idTarget+'_url').val($(this).val().replaceAll(/&+[*]/g,''));
            $(idTarget+'_url').val($(this).val().replace(/^https:\/\/vimeo.com/,'https://player.vimeo.com/video'));
            $(idTarget+'_url').val($(this).val().replace(/^https:\/\/dai.ly/,'https://www.dailymotion.com/embed/video'));

        });
    }

    $('#loader-image').on('click','.remove',function (e) {
        e.preventDefault();
        let target = $($(this).parent().data('target'));
        target.remove();
        let view = $($(this).parent().data('target')+'_view');
        view.remove();

    });
    $('.save').on('click',function (e) {
        // si on ajoute un media on ajoute en premier la card
        let type = $(idTarget+'_type').val();
        let view = $(idTarget+'_view');
        let src = $(idTarget+'_url').val();
        if(addMedia){
            const id= idTarget.replace(/#/,"");
            //appel le prototype d'un media
            $.get('/generate/media/'+$(idTarget+'_type').val(), function( data ) {
                let tmpl;
                tmpl = data
                    .replace(/__id__/g,index)
                    .replace(/__url__/g,src);

                $('#add-media').before(tmpl);
                addMedia = false;
                index++;
            });
            return;
        }

        $.get('/generate/media/'+$(idTarget+'_type').val(), function( data ) {
            let tmpl;
            tmpl = data
                .replace(/__id__/g,view.data('id'))
                .replace(/__url__/g,src);

            view.before(tmpl);
            view.remove();

            index++;
        });



    });

    $('#exampleModal').on('hidden.bs.modal', function (e) {
        $('.form_tricks').addClass('d-none');
    });


    $('.progress').hide();
    let uploadActive= false;

    $('#upload').on('click',function (e) {
        if(!uploadActive){
            e.preventDefault();
            $('#file').click();
        }

    });
    $('#file').on('change',function (e) {
        let file = $('#file').get(0).files[0];
        $('.progress').show();
        uploadActive = true;

        if(file !== "undefined"){
            let mydata = new FormData();
            //on ajopute le primer fichier de la liste
            mydata.append('file', file);
            mydata.append('greg', 45);
            $('#upload').removeClass('btn-danger').addClass('btn-primary');

            $.ajax({
                xhr: function () { // xhr qui traite la barre de progression permet de redefinir httprequest
                    myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) { // vérifie si l'upload existe
                        myXhr.upload.addEventListener('progress', afficherAvancement, false);

                    }
                    return myXhr;
                },
                url: '/admin/upload',
                data: mydata,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                type: 'post',
                success: function (data,u) {
                    $('.progress').hide();
                    uploadActive = false;
                    $('#upload span').text('Upload');
                    $(idTarget+'_url').val('/uploads/'+data);
                    $('.progress-bar').css('width',0);


                },
                error: function(err) {
                    uploadActive = false;
                    $('#upload span').text('Une erreur est survenue');
                    $('#upload').removeClass('btn-primary').addClass('btn-danger');
                    $('.progress').hide();
                }

            });
        }



    })  ;




    function afficherAvancement(e) {
        let width = Math.round((e.loaded/e.total)*100);
        $('.progress-bar').css('width',width+'%');
        $('#upload span').text(width+'%');
    }

});