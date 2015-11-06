jQuery(document).ready( function($) {
    //Prepare la box de popin et celle de la video
    jQuery(document).foundation();
    
    // initialise homepage scroll vers form Inscription
    $('.login a.login__inscription').on('click', function(event){
        event.preventDefault();
        var target = $(this.hash);
        if (target.length) {
            $('html,body').animate({
              scrollTop: target.offset().top
            }, 1000);
        }
    });
    // -------- fin
    
    // initialise popin de confirmation retour mail
    $('#formRegisterConfirmed').foundation('reveal', 'open');
    $("#formRegisterConfirmed").on('close.fndtn.reveal', function () {
        if($(this).data('reload')){
            location.replace($(this).data('reload'));
        }else{
            location.reload();
        }
    });
    
    // activation des événements sur formulaire inscription
    activateRegisterForm();    
    
    //Soumission des forms en ajax à transformer en function AjaxThisForm(form, callback)
    $("#formIdea").on("submit","form", function(e) {
            e.preventDefault();
            $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize()
        })
        .done(function (data) {
            if (typeof data.message !== 'undefined') {
                $("#suggest_idea_front").replaceWith(data.succespopup);
                $("#formIdea").on('close.fndtn.reveal', function () {
                    location.reload();
                });
                $('html,body').animate({
                      scrollTop: $("#formIdea").offset().top
                    }, 1000);
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            if (typeof jqXHR.responseJSON !== 'undefined') {
                if (jqXHR.responseJSON.hasOwnProperty('form')) {
                    $("#suggest_idea_front").replaceWith(jqXHR.responseJSON.form);
                    console.log(jqXHR.responseJSON.message);
                }
            } 
            else {
                console.log(errorThrown);
            } 
        });
    });
    
    // gestion longueur des textarea
    // pour les 200 caractères
    //gestionLimiteTextareaCaracteres();
    // pour les 200 mots
    gestionLimiteTextareaMots();
    
    if($('#js-video').length>0){
        var v = document.getElementById('js-video'); // /!\ A généré une erreur sur une page.
        v.onended = function() { //event de fin de lecture de la vidéo
            //le formulaire est présent, le formulaire n'est présent que quand la personne n'a pas vu la vidéo en entier.
            if($('#js-videoform').length == 1) {
                $("#form_videoseenon").val(true);
                $.ajax({
                    type: $('#js-videoform').attr('method'),
                    url: $('#js-videoform').attr('action'),
                    data: $('#js-videoform').serialize()
                })
                .done(function (data) {
                if (typeof data.message !== 'undefined') {
                    location.reload();
                }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    if (typeof jqXHR.responseJSON !== 'undefined') {
                        if (jqXHR.responseJSON.hasOwnProperty('message')) {
                            console.log(jqXHR.responseJSON.message);
                        }
                    } 
                    else {
                        console.log(errorThrown);
                    }
                });
            }
            else {
                console.log("La videoest finie et pas de requete ajax.");
            }
            $('#videoModal').foundation('reveal', 'close');
        };
    }
    
    $('.js-launchvideo').on('click', function() {
        $('#videoModal').foundation('reveal', 'open');
        $('#videoModal').css('top','0px'); // Test d'ajustement du dialog modal
        v.play();
    });
});



function gestionLimiteTextareaCaracteres(){
    $(document).on('load change keyup', '#form_description', function () {
        var len = $(this).val().length;
        var targetCompteur = $('.compteurDesc');
        targetCompteur.html(targetCompteur.data('maxchar') - len);
    });        
    $('#form_description').trigger('change');
    
    $(document).on('load change keyup', '#form_customerprofit', function () {
        var len = $(this).val().length;
        var targetCompteur = $('.compteurProfit');
        targetCompteur.html(targetCompteur.data('maxchar') - len);
    });        
    $('#form_customerprofit').trigger('change');
    
    $(document).on('load change keyup', '#form_partnerprofit', function () {
        var len = $(this).val().length;
        var targetCompteur = $('.compteurPartner');
        targetCompteur.html(targetCompteur.data('maxchar') - len);
    });        
    $('#form_partnerprofit').trigger('change');
    
    $(document).on('load change keyup', '#form_bonuscontent', function () {
        var len = $(this).val().length;
        var targetCompteur = $('.compteurBonus');
        targetCompteur.html(targetCompteur.data('maxchar') - len);
    });        
    $('#form_bonuscontent').trigger('change');
}

function gestionLimiteTextareaMots(){
    $(document).on('load keydown', '#form_description', function (e) {
        var words = $.trim(this.value).length ? this.value.match(/\S+/g).length : 0;
        var targetCompteur = $('.compteurDesc');
        if (words <= targetCompteur.data('maxchar')) {
            targetCompteur.html(targetCompteur.data('maxchar') - words);
        }else{
            if (e.which !== 8) e.preventDefault();
        }
    });        
    $('#form_description').trigger('keydown');
    
    $(document).on('load keydown', '#form_customerprofit', function (e) {
        var words = $.trim(this.value).length ? this.value.match(/\S+/g).length : 0;
        var targetCompteur = $('.compteurProfit');
        if (words <= targetCompteur.data('maxchar')) {
            targetCompteur.html(targetCompteur.data('maxchar') - words);
        }else{
            if (e.which !== 8) e.preventDefault();
        }
    });        
    $('#form_customerprofit').trigger('keydown');
    
    $(document).on('load keydown', '#form_partnerprofit', function (e) {
        var words = $.trim(this.value).length ? this.value.match(/\S+/g).length : 0;
        var targetCompteur = $('.compteurPartner');
        if (words <= targetCompteur.data('maxchar')) {
            targetCompteur.html(targetCompteur.data('maxchar') - words);
        }else{
            if (e.which !== 8) e.preventDefault();
        }
    });        
    $('#form_partnerprofit').trigger('keydown');
    
    $(document).on('load keydown', '#form_bonuscontent', function (e) {
        var words = $.trim(this.value).length ? this.value.match(/\S+/g).length : 0;
        var targetCompteur = $('.compteurBonus');
        if (words <= targetCompteur.data('maxchar')) {
            targetCompteur.html(targetCompteur.data('maxchar') - words);
        }else{
            if (e.which !== 8) e.preventDefault();
        }
    });        
    $('#form_bonuscontent').trigger('keydown');
    
}


function activateRegisterForm(){
    //Trigger register form pour slide        /!\ Retour au step 1 à rajouter en cas d'erreur de validation par le client.
    $(".js-register-next").click( function(e) {
        e.preventDefault();
        /*
        // prevalidation du formulaire
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize()
        })
        .done(function (data) {
            
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            
        });
        */
       
       if($("#fos_user_registration_form_firstname").val() ==''){
           $("#fos_user_registration_form_firstname").focus();
           return false;
       }
       if($("#fos_user_registration_form_lastname").val() ==''){
           $("#fos_user_registration_form_lastname").focus();
           return false;
       }
       if($("#fos_user_registration_form_email").val() ==''){
           $("#fos_user_registration_form_email").focus();
           return false;
       }
       if($("#fos_user_registration_form_plainPassword_first").val() ==''){
           $("#fos_user_registration_form_plainPassword_first").focus();
           return false;
       }
       if($("#fos_user_registration_form_plainPassword_second").val() ==''){
           $("#fos_user_registration_form_plainPassword_second").focus();
           return false;
       }
        
        /*var $theForm = $(this).closest('form');
        $theForm[0].checkValidity();*/
        /*if (( typeof($theForm[0].checkValidity) == "function" ) && !$theForm[0].checkValidity()) {
            return;
        }*/
        
        /*if(!$("#fos_user_registration_form_lastname")[0].checkValidity()){
            $('form.fos_user_registration_register').find(':submit').click();
            //return false;
        }*/
       
       
        // retour Ajax positif = slide charte sinon affichage erreurs
        $(".register__slider__item:eq(0)").css('margin-left','-100%');
    });
    
    $('.register__submit').click( function(e) {
        e.preventDefault();
        if($("#fos_user_registration_form_cgvaccepted").is(':checked') == false){
           $("#fos_user_registration_form_cgvaccepted").focus();
           return false;
        }
        $(this).closest("form").submit();
    });
    
    
    $('form.fos_user_registration_register').on('submit', function(event){
       event.preventDefault();     
        $.ajax({
             type: $(this).attr('method'),
             url: $(this).attr('action'),
             data: $(this).serialize()
        })
        .done(function (data) {
            $('form.fos_user_registration_register').css('visibility','hidden');
            $('body').append(data.popin);
            $('#formRegisterConfirmed').foundation('reveal', 'open');
            $("#formRegisterConfirmed").on('close.fndtn.reveal', function () {
                location.reload();
            });
        })
        .fail(function (jqXHR, textStatus, errorThrown) { 
            $(".register__slider__item:eq(0)").css('margin-left','0%');            
            if (typeof jqXHR.responseJSON !== 'undefined') {                
                if (jqXHR.responseJSON.hasOwnProperty('form')) {                    
                    //console.log(jqXHR.responseJSON.message);
                    $('form.fos_user_registration_register').replaceWith(jqXHR.responseJSON.form);
                    $('form.fos_user_registration_register').find('.showErrors').each(function(){
                       if($(this).html() != '') {
                           $(this).removeClass('hidden');
                           $(this).show(0);
                       }
                    });
                    
                    activateRegisterForm();
                }
            } 
            else {
                console.log(errorThrown);
            } 
        });
    });
}