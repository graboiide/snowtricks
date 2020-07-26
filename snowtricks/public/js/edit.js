jQuery(function() {

    let viewImg = null;
    let idTarget = null;
    let coverEdit = false;
    const coverInfo = $('.cover-info');
    let index = $('#tricks_medias .form_tricks').length;
    let addMedia = false;

    coverInfo.hide();


    /**
     * EDIT COVER
     */
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

        }
    });



    /**
     * REMOVE COVER
     */
    $('#cover-remove').on('click',function (e) {
        $('#cover').attr('src',$('#tricks_medias_0_view img').attr('src'));
        $('#tricks_cover').val('');
    });
    $('#tricks_name').on('keyup',function (e) {
        $('.title-tricks h1').text($('#tricks_name').val());
    });

    /**
     * ADD MEDIA
     */
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

    /**
     * EDIT MEDIA
     */
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
        $('.upload-file').insertAfter($(idTarget+'_url'));
        //corrige un bug de multiplication  j'ai pas trouver mieux pour le moment
        $('.upload-file').each(function (i) {
            if(i > 0)
                $(this).remove();
        });

    }
    function displayUploadButton() {
        replaceUploadfield();
       if($(idTarget+'_type').val() === '0')
           $('.upload-file').show();
       else
           $('.upload-file').hide();
    }
    function autoUrl(){
        $(idTarget+'_url').on('change',function (e) {


            $(idTarget+'_url').val($(this).val().replace(/^https:\/\/youtu.be/,'https://www.youtube.com/embed'));
            $(idTarget+'_url').val($(this).val().replace(/watch\?v=/,'embed/'));
            $(idTarget+'_url').val($(this).val().replaceAll(/&+[*]/g,''));
            $(idTarget+'_url').val($(this).val().replace(/^https:\/\/vimeo.com/,'https://player.vimeo.com/video'));
            $(idTarget+'_url').val($(this).val().replace(/^https:\/\/dai.ly/,'https://www.dailymotion.com/embed/video'));

        });
    }

    /**
     * REMOVE MEDIA
     */
    $('#loader-image').on('click','.remove',function (e) {
        e.preventDefault();
        let target = $($(this).parent().data('target'));
        target.remove();
        let view = $($(this).parent().data('target')+'_view');
        view.remove();

    });

    /**
     * SAVE
     */

    function editMedia(){
        let type = $(idTarget+'_type').val();
        let view = $(idTarget+'_view');
        let src = $(idTarget+'_url').val();
        $('.upload-file').hide();
        $('.upload-file').appendTo($('#loader-image'));

        if(addMedia){
                        //appel le prototype d'un media
            $.get('/generate/media/'+$(idTarget+'_type').val(), function( data ) {
                let tmpl;
                tmpl = data
                    .replace(/__id__/g,index)
                    .replace(/__url__/g,src)
                    .replace(/__caption__/g,$(idTarget+'_caption').val());

                $('#add-media').before(tmpl);
                addMedia = false;
                index++;
                emptyMedias();
            });
            return;
        }

        $.get('/generate/media/'+$(idTarget+'_type').val(), function( data ) {
            let tmpl;
            tmpl = data
                .replace(/__id__/g,view.data('id'))
                .replace(/__url__/g,src)
                .replace(/__caption__/g,$(idTarget+'_caption').val());
            view.before(tmpl);
            view.remove();

            emptyMedias();
        });


    }
    function emptyMedias(){

        if($(idTarget+'_url').val() === '' && $(idTarget+'_caption').val() === ''){
            $('.modal-body').append($('.upload-file'));
            $(idTarget).remove();
            $(idTarget+'_view').remove();

            console.log(idTarget+'_view');
        }
        if($(idTarget+'_url').val() === '' || $(idTarget+'_caption').val() === ''){
            $(idTarget+'_view').css('border','3px solid red');
        }

    }

    /**
     * CLOSE MODAL
     */
    $('#exampleModal').on('hidden.bs.modal', function (e) {
        $('.form_tricks').addClass('d-none');
            editMedia();

    });

    /**
     * UPLOAD
     */
    $('.progress').hide();
    let uploadActive = false;

    $('#upload').on('click', function (e) {
        e.preventDefault();
        if (!uploadActive) {

            $('#file').click();
        }

    });
    $('#file').on('change', function (e) {
        let file = $('#file').get(0).files[0];
        $('.progress').show();
        uploadActive = true;


        let mydata = new FormData();
        //on ajopute le primer fichier de la liste
        mydata.append('file', file);
        $('#upload').removeClass('btn-danger').addClass('btn-primary');

        $.ajax({
            xhr: function () { // xhr qui traite la barre de progression permet de redefinir httprequest
                myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) { // vérifie si l'upload existe
                    console.log(myXhr.upload);
                    myXhr.upload.addEventListener('progress', afficherAvancement, false);

                }
                return myXhr;
            },
            url: '/admin/upload',
            data: mydata,
            cache: false,
            contentType: false,
            processData: false,
            type: 'post',
            success: function (data, u) {
                $('.progress').hide();
                uploadActive = false;
                $('#upload span').text('Upload');
                $(idTarget + '_url').val('/uploads/' + data);
                $('.progress-bar').css('width', 0);
            },
            error: function (err) {
                uploadActive = false;
                $('#upload span').text('Une erreur est survenue');
                $('#upload').removeClass('btn-primary').addClass('btn-danger');
                $('.progress').hide();
            }

        });

        function afficherAvancement(e) {
            console.log('total:' + e.total);
            console.log('progress:' + e.loaded);
            let width = Math.floor((e.loaded / e.total) * 100);
            $('.progress-bar').css('width', width + '%');
            $('#upload span').text(width + '%');


        }

    });


});