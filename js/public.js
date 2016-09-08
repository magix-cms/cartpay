var cartProduct =(function($, undefined){
    function addProductCart(idcatalog,idform){
        var quantite_default = $('#product_quantity,#product_quantity_p').data('qty');

        $( "#"+idform ).validate({
            onsubmit: true,
            highlight: function(element, errorClass, validClass) {
                if($(element).parent().is("p")){
                    $(element).parent().addClass("error");
                }else if($(element).parent().is("div")){
                    $(element).parent().parent().addClass("error");
                }
            },
            unhighlight: function(element, errorClass, validClass) {
                if($(element).parent().is("p")){
                    $(element).parent().removeClass("error");
                }else if($(element).parent().is("div")){
                    $(element).parent().parent().removeClass("error");
                }
            },
            // the errorPlacement has to take the table layout into account
            errorPlacement: function(error, element) {
                $(".quantity-error").remove();
                error.insertAfter(element.parent());
            },
            errorClass: "quantity-error alert alert-warning col-xs-12",
            errorElement:"div",
            validClass: "success",
            // set this class to error-labels to indicate valid fields
            success: function(label) {
                // set &nbsp; as text for IE
                label.remove();
                $(".quantity-error").remove();

            },
            rules: {
                product_quantity: {
                    required: true,
                    min: quantite_default
                }
            },
            submitHandler: function (form) {
                $.nicenotify({
                    ntype: "submit",
                    uri: '/plugins.php?magixmod=cartpay&add_cart='+idcatalog,
                    typesend: 'post',
                    idforms: $(form),
                    resetform: false,
                    beforeParams:function(){},
                    successParams:function(e){
                        $.nicenotify.initbox(e,{
                            display:true
                        });
                        loadCartNbrItems('cart-resume-nbr-items');
                        loadCartPriceItems('cart-resume-price-items');
                        //$('#command,#personnalizeall').modal('hide');
                    }
                });
                return false;
            }
        });
    }

    /**
     * quick add product cart in catalog
     * @param idcatalog
     * @param form
     */
    function quickAddProductCart(idcatalog,form){
        form.validate({
            onsubmit: true,
            highlight: function(element, errorClass, validClass) {
                if($(element).parent().is("p")){
                    $(element).parent().addClass("error");
                }else if($(element).parent().is("div")){
                    $(element).parent().parent().addClass("error");
                }
            },
            unhighlight: function(element, errorClass, validClass) {
                if($(element).parent().is("p")){
                    $(element).parent().removeClass("error");
                }else if($(element).parent().is("div")){
                    $(element).parent().parent().removeClass("error");
                }
            },
            // the errorPlacement has to take the table layout into account
            errorPlacement: function(error, element) {
                $(".quantity-error").remove();
                error.insertAfter(element.parent());
            },
            errorClass: "quantity-error alert alert-warning col-xs-12",
            errorElement:"div",
            validClass: "success",
            // set this class to error-labels to indicate valid fields
            success: function(label) {
                // set &nbsp; as text for IE
                label.remove();
                $(".quantity-error").remove();

            },
            rules: {
                product_quantity: {
                    required: true,
                    min: 1,
                    max: 1
                }
            },
            submitHandler: function (form) {
                $.nicenotify({
                    ntype: "submit",
                    uri: '/plugins.php?magixmod=cartpay&add_cart='+idcatalog,
                    typesend: 'post',
                    idforms: $(form),
                    resetform: false,
                    beforeParams:function(){},
                    successParams:function(e){
                        $.nicenotify.initbox(e,{
                            display:true
                        });
                        loadCartNbrItems('cart-resume-nbr-items');
                        loadCartPriceItems('cart-resume-price-items');
                        //$('#command,#personnalizeall').modal('hide');
                    }
                });
                return false;
            }
        });
    }

    /**
     * Suppression d'un élément du tableaux
     * @param adminurl
     */
    function deleteCartItem(iso,id_cart,id_container,id_table){
        $(document).on("click",'.d-plugin-cart-item',function(event){
//        $(document).click('.d-plugin-cart-item',function(event){
            event.preventDefault();
            var item = $(this).attr("rel");
                $.nicenotify({
                    ntype: "ajax",
                    uri: '/plugins.php?magixmod=cartpay&delete_item=true',
                    typesend: 'post',
                    noticedata:'item_to_delete='+item,
                    beforeParams:function(){},
                    successParams:function(e){
                        $.nicenotify.initbox(e,{
                            display:false
                        });
                        loadCartTable(iso,id_cart,id_container,id_table);
                        loadCartAmountToPay(id_cart);
                    }
                });
                return false;
        });
    }

    /**
     * send devis
     * @param idform
     * @param iso
     */
    function sendDevis(idform,iso){
        $(document).on("click",'#sendDevis',function(event){
            event.preventDefault();
            $('#'+idform).on('submit',function(event){
                 event.preventDefault();
                 if ($('#'+idform).valid()) {
                     $.nicenotify({
                         ntype:"submit",
                         idforms: $(this),
                         resetform: false,
                         uri: '/plugins.php?magixmod=cartpay&strLangue='+iso+'&send_devis=true',
                         successParams:function(e) {
                             $.nicenotify.initbox(e,{
                                 display:true
                             });
                             /*$.redirect({
                                 lang: iso,
                                 url: '/',
                                 time:100
                             });*/
                         }
                     });
                     return false;
                 }
            });
            $('#'+idform).submit();
            $('#'+idform).off();
        });
    }

    /**
     * Envoi la demande de devis par mail et retourne le statut
     * @param idform
     * @param iso
     */
    function sendCartDevis(idform,iso){
        $(document).on("click",'#sendCartDevis',function(event){
            event.preventDefault();
            $('#'+idform).on('submit',function(event){
                event.preventDefault();
                if ($('#'+idform).valid()) {
                    $.nicenotify({
                        ntype:"submit",
                        idforms: $(this),
                        resetform: true,
                        uri: '/plugins.php?magixmod=cartpay&strLangue='+iso+'&payment=resume&statut=success',
                        successParams:function(e) {
                            $.nicenotify.initbox(e,{
                                display:true
                            });
                            $("#resume_cart").slideUp("slow");
                            loadCartNbrItems('cart-resume-nbr-items');
                            loadCartPriceItems('cart-resume-price-items');
                        }
                    });
                    return false;
                }
            });
            $('#'+idform).submit();
            $('#'+idform).off();
        });
    }
    //sendCart
    /**
     * Envoi la demande
     * @param idform
     * @param iso
     */
    function sendCart(idform,iso){

        $(document).on("click",'#sendCart',function(event){
            event.preventDefault();
            $('#'+idform).on('submit',function(event){
                if ($('#'+idform).valid()) {

                } else {
                    event.preventDefault();
                }
            });
            $('#'+idform).submit();
            $('#'+idform).off();
        });
    }
    /**
     *
     * @param id_cart
     * @param id_container
     * @param id_table
     */
    function loadCartTable(iso,id_cart,id_container,id_table){
        if(id_cart != null){
            $.nicenotify({
                ntype:"ajax",
                uri: '/plugins.php?magixmod=cartpay&strLangue='+iso+'&json_cart='+id_cart,
                typesend:'get',
                dataType:'html',
                beforeParams:function(){
                    var loader = $(document.createElement("span")).addClass("loader col-md-offset-5").append(
                        $(document.createElement("img"))
                            .attr('src','/plugins/cartpay/img/loader/small_loading.gif')
                            .attr('width','20px')
                            .attr('height','20px')
                    );
                    $('#'+id_container).html(loader);
                },
                successParams:function(j){
                    $('#'+id_container).empty();
                    $.nicenotify.initbox(j,{
                        display:false
                    });
					if(j) {
						$('#'+id_container).html(j);
						loadCartNbrItems('cart-resume-nbr-items');
						loadCartPriceItems('cart-resume-price-items');
						loadCartAmountToPay(id_cart);
					} else {
						window.location.href = '/'+iso+'/cartpay/';
					}
                }
            });
        }
    }

    /**
     * Chargement du nombre d'éléments
     * @param id_cible
     * @returns {boolean}
     */
    function loadCartNbrItems(id_cible){
        $.nicenotify({
            ntype:'ajax',
            uri:'/plugins.php?magixmod=cartpay&get_nbr_items=true',
            typesend:'get',
            datatype:'html',
            successParams:function(e){
                if(e > 0) {
                    $('.btn-cart').removeClass('hide');
                    $('.empty-cart').addClass('hide');
                    $('.'+id_cible).removeClass('hide');
                }else{
                    $('.btn-cart').addClass('hide');
                    $('.empty-cart').removeClass('hide');
                    $('.'+id_cible).addClass('hide');
                }
                $('.'+id_cible).html(e);
                $('.'+id_cible).parent().stop(true,true);
                $('.'+id_cible).parent().effect("pulsate", { times:1 }, 1000);
            }
        });
        return false;
    }

    /**
     * Chargement du prix des éléments
     * @param id_cible
     * @returns {boolean}
     */
    function loadCartPriceItems(id_cible){
        $.nicenotify({
            ntype:'ajax',
            uri:'/plugins.php?magixmod=cartpay&get_price_items=true',
            typesend:'get',
            datatype:'html',
            successParams:function(e){
                $('.'+id_cible).html(e);
                $('.'+id_cible).parent().stop(true,true);
                $('.'+id_cible).parent().effect("pulsate", { times:1 }, 1000);
            }
        });
        return false;
    }

    /**
     * Chargement du montant à payer
     * @param id_cart
     */
    function loadCartAmountToPay(id_cart){
        var id_cible = 'amount_to_pay_view';
        var id_cible_hidden = 'amount_to_pay_hidden';
        $.nicenotify({
            ntype:'ajax',
            uri:'/plugins.php?magixmod=cartpay&get_amount_to_pay='+id_cart,
            typesend:'get',
            datatype:'html',
            successParams:function(e){
                $('#'+id_cible).html(e);
                $('#'+id_cible_hidden).val(e);
            }
        });
    }

    /**
     * Validation du formulaire
     * @param idform
     * @param lang
     * @param id_container
     * @param id_table
     */
    function validate_form(idform,lang,id_container,id_table){
            $(function(){
                $('#adressliv').change(function(){
                    if(this.checked) {
                        $('#lastname_liv_cart').rules('remove');
                        $('#firstname_liv_cart').rules('remove');
                        $('#street_liv_cart').rules('remove');
                        $('#city_liv_cart').rules('remove');
                        $('#postal_liv_cart').rules('remove');
                        $('#country_liv_cart').rules('remove');
                    } else {
                        $('#lastname_liv_cart').rules('add',{required: true});
                        $('#firstname_liv_cart').rules('add',{required: true});
                        $('#street_liv_cart').rules('add',{required: true});
                        $('#city_liv_cart').rules('add',{required: true});
                        $('#postal_liv_cart').rules('add',{required: true});
                        $('#country_liv_cart').rules('add',{required: true});
                    }
                });
            });
            $('#'+idform).validate({
                ignore: [],
                onsubmit: true,
                highlight: function(element, errorClass, validClass) {
                    if($(element).parent().is("p")){
                        $(element).parent().addClass("error");
                    }else if($(element).parent().is("div")){
                        $(element).parent().parent().addClass("error");
                    }
                },
                unhighlight: function(element, errorClass, validClass) {
                    if($(element).parent().is("p")){
                        $(element).parent().removeClass("error");
                    }else if($(element).parent().is("div")){
                        $(element).parent().parent().removeClass("error");
                    }
                },
                // the errorPlacement has to take the table layout into account
                errorPlacement: function(error, element) {
                    if ( element.is(":radio") ){
                        error.insertAfter(element);
                    }else if ( element.is(":checkbox") ){
                        error.insertAfter(element.next());
                    }else if ( element.is("select")){
                        error.insertAfter(element);
                    }else if ( element.is(".checkMail") ){
                        error.insertAfter(element.next());
                    }else if ( element.is("#cryptpass") ){
                        error.insertAfter(element.next());
                        $("<br />").insertBefore(error);
                    }else{
                        if(element.next().is(":button") || element.next().is(":file")){
                            error.insertAfter(element);
                            $("<br />").insertBefore(error);
                        }else if ( element.next().is(":submit") ){
                            error.insertAfter(element.next());
                            $("<br />").insertBefore(error);
                        }else{
                            error.insertAfter(element);
                        }
                    }
                },
                errorClass: "alert alert-warning",
                errorElement:"div",
                validClass: "success",
                // set this class to error-labels to indicate valid fields
                success: function(label) {
                    // set &nbsp; as text for IE
                    label.remove();

                },
                event:'submit',
                rules: {
                    lastname_cart: {
                        required: true,
                        minlength: 2
                    },
                    firstname_cart: {
                        required: true,
                        minlength: 2
                    },
                    email_cart: {
                        required: true,
                        email: true
                    },
                    street_cart: {
                        required: true,
                        minlength: 2
                    },
                    city_cart: {
                        required: true,
                        minlength: 2
                    },
                    postal_cart: {
                        required: true,
                        minlength: 2
                    },
                    country_cart: {
                        required: true
                    },
                    lastname_liv_cart: {
                        required: true
                    },
                    firstname_liv_cart: {
                        required: true
                    },
                    street_liv_cart: {
                        required: true
                    },
                    city_liv_cart: {
                        required: true
                    },
                    postal_liv_cart: {
                        required: true
                    },
                    country_liv_cart: {
                        required: true
                    },
                    confidentiality_control:{
                        required: true
                    },
                    id_cart_to_send:{
                        required: true
                    }
                }
            });
    }

    /**
     * Afficher/cacher l'adresse de livraison
     */
    function delivery(){
        $('#adressliv').on('click',function(){
            //si la case a cocher est cochée
            if(this.checked){
                $('#delivery').hide();
            }else{
                $('#delivery').show();
            }
        });
    }

    /**
     * Affiche la modale de modifications de la quantité
     */
    function modalQuantity(){
        $(document).on('click','.edit-qty',function(){
            var quantity = $(this).data('qty');
            var id_item = $(this).data('item');
            $('#quantity_qty').val(quantity);
            $('#item_qty').val(id_item);
        });
    }

    /**
     * Mise a jour de la Quantité d'un article
     * @param iso
     * @param id_cart
     * @param id_container
     * @param id_table
     */
    function updateQuantity(iso,id_cart,id_container,id_table){
        $('#form_edit_qty').on('submit',function(event){
            event.preventDefault();
            $.nicenotify({
                ntype:"submit",
                idforms: $(this),
                resetform: false,
                uri: '/plugins.php?magixmod=cartpay&strLangue='+iso,
                successParams:function(e) {
                    $.nicenotify.initbox(e,{
                        display:false
                    });
                    $('#quantity-cart').modal('hide');
                    //
                   /* var $currentOption = $('select#period_ship option:selected').val();
                    if($currentOption != ''){
                        currentShipping(iso,id_cart,id_container,id_table);
                    }else{
                        loadCartTable(iso,id_cart,id_container,id_table);
                    }*/
                    selectTva(iso,id_cart,id_container,id_table);
                    loadCartAmountToPay(id_cart);
                }
            });
            return false;
        });
    }

    /**
     * Relance le calcul du total suivant la sélection de période de livraison
     * @param isolang
     * @param id_cart
     * @param id_container
     * @param id_table
     */
    function selectTva(iso,id_cart,id_container,id_table){
        if($('select#country_cart').length != 0){
            $('select#country_cart').on('change',function(){
                var $currentOption = $(this).find('option:selected').val();
                if($currentOption != ''){
                    if(id_cart != null){
                        $.nicenotify({
                            ntype:"ajax",
                            uri: '/plugins.php?magixmod=cartpay&strLangue='+iso+'&json_cart='+id_cart,
                            typesend:'post',
                            dataType:'html',
                            noticedata:'tva_country='+$currentOption,
                            beforeParams:function(){
                                var loader = $(document.createElement("span")).addClass("loader col-md-offset-5").append(
                                    $(document.createElement("img"))
                                        .attr('src','/plugins/cartpay/img/loader/small_loading.gif')
                                        .attr('width','20px')
                                        .attr('height','20px')
                                );
                                $('#'+id_container).html(loader);
                            },
                            successParams:function(j){
                                $('#'+id_container).empty();
                                $.nicenotify.initbox(j,{
                                    display:false
                                });
                                $('#'+id_container).html(j);
                            }
                        });

                        loadCartNbrItems('cart-resume-nbr-items');
                        loadCartPriceItems('cart-resume-price-items');
                    }
                }
            });
        }else{
            if(id_cart != null){
                $.nicenotify({
                    ntype:"ajax",
                    uri: '/plugins.php?magixmod=cartpay&strLangue='+iso+'&json_cart='+id_cart,
                    typesend:'post',
                    dataType:'html',
                    noticedata:'tva_country='+$('#country_cart').val(),
                    beforeParams:function(){
                        var loader = $(document.createElement("span")).addClass("loader col-md-offset-5").append(
                            $(document.createElement("img"))
                                .attr('src','/plugins/cartpay/img/loader/small_loading.gif')
                                .attr('width','20px')
                                .attr('height','20px')
                        );
                        $('#'+id_container).html(loader);
                    },
                    successParams:function(j){
                        $('#'+id_container).empty();
                        $.nicenotify.initbox(j,{
                            display:false
                        });
                        $('#'+id_container).html(j);
                    }
                });

                loadCartNbrItems('cart-resume-nbr-items');
                loadCartPriceItems('cart-resume-price-items');
            }
        }

    }

    /**
     * Récupère la période sélectionné et l'envoi par post pour le recalcul
     * @param iso
     * @param id_cart
     * @param id_container
     * @param id_table
     */
    function currentShipping(iso,id_cart,id_container,id_table){
        var $currentOption = $('select#period_ship option:selected').val();
        if($currentOption != '') {
            $.nicenotify({
                ntype: "ajax",
                uri: '/plugins.php?magixmod=cartpay&strLangue=' + iso + '&json_cart=' + id_cart,
                typesend: 'post',
                dataType: 'html',
                noticedata: 'period_ship=' + $currentOption,
                beforeParams: function () {
                    var loader = $(document.createElement("span")).addClass("loader col-md-offset-5").append(
                        $(document.createElement("img"))
                            .attr('src', '/plugins/cartpay/img/loader/small_loading.gif')
                            .attr('width', '20px')
                            .attr('height', '20px')
                    );
                    $('#' + id_container).html(loader);
                },
                successParams: function (j) {
                    $('#' + id_container).empty();
                    $.nicenotify.initbox(j, {
                        display: false
                    });
                    $('#' + id_container).html(j);
                }
            });
            loadCartNbrItems('cart-resume-nbr-items');
            loadCartPriceItems('cart-resume-price-items');
        }
    }

    function addBooking(idcatalog,idform){
        $( "#"+idform ).validate({
            onsubmit: true,
            highlight: function(element, errorClass, validClass) {
                if($(element).parent().is("p")){
                    $(element).parent().addClass("error");
                }else if($(element).parent().is("div")){
                    $(element).parent().parent().addClass("error");
                }
            },
            unhighlight: function(element, errorClass, validClass) {
                if($(element).parent().is("p")){
                    $(element).parent().removeClass("error");
                }else if($(element).parent().is("div")){
                    $(element).parent().parent().removeClass("error");
                }
            },
            // the errorPlacement has to take the table layout into account
            errorPlacement: function(error, element) {
                $(".quantity-error").remove();
                error.insertAfter(element.parent());
            },
            errorClass: "quantity-error alert alert-warning col-xs-12",
            errorElement:"div",
            validClass: "success",
            // set this class to error-labels to indicate valid fields
            success: function(label) {
                // set &nbsp; as text for IE
                label.remove();
                $(".quantity-error").remove();

            },
            rules: {
                booking_quantity: {
                    required: true
                }
            },
            submitHandler: function (form) {
                $.nicenotify({
                    ntype: "submit",
                    uri: '/plugins.php?magixmod=cartpay&booking='+idcatalog,
                    typesend: 'post',
                    idforms: $(form),
                    resetform: false,
                    beforeParams:function(){},
                    successParams:function(e){
                        $.nicenotify.initbox(e,{
                            display:false
                        });
                        $('#modal-booking').modal('hide');
                    }
                });
                return false;
            }
        });
    }

    /**
     * fonction public
     */
    return{
        run:function(idcatalog,idform){
            addProductCart(idcatalog,idform);
        },
        runFast:function(classForm){
            $(classForm).each(function(){
                var idcatalog = $('[name="idcatalog"]',$(this)).val();
                quickAddProductCart(idcatalog,$(this));
            });
        },
        runBooking:function(idcatalog,idform){
            addBooking(idcatalog,idform);
        },
        runCart:function(id_cart,id_container,id_table,idform,isolang){
            loadCartTable(isolang,id_cart,id_container,id_table);
            deleteCartItem(isolang,id_cart,id_container,id_table);
            validate_form(idform,isolang,id_container,id_table);
            delivery();
            modalQuantity();
            updateQuantity(isolang,id_cart,id_container,id_table);
            selectTva(isolang,id_cart,id_container,id_table);
        },
        runSend:function(id_cart,id_container,id_table,idform,isolang){
            sendDevis(idform,isolang);
        }
    };
})(jQuery);