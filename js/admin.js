/**
 * Created by augeri on 18/03/14.
 */
var MC_cartPay = (function($, window, document, undefined){
    // private function
    function trHidden(elem){
        $(elem).jmShowIt({
            open: 'open',
            contenerClass : 'tr.collapse-block',
            activeClass : 'on active',
            debug : false
        });
    }

    /**
     * set ajax load data
     * @param baseadmin
     * @param tab
     * @param action
     * @returns {string}
     */
    function setAjaxUrlLoad(baseadmin,tab,action){
        if(action != null){
            return '/'+baseadmin+'/plugins.php?name=cartpay&tab='+tab+'&action='+action;
        }else{
            return '/'+baseadmin+'/plugins.php?name=cartpay&tab='+tab;
        }
    }

    /**
     * Enregistre les changements ou ajoute le contenu en base de données
     * @param baseadmin
     * @param tab
     * @param action
     * @param id
     */
    function save(baseadmin,tab,action,id){
        if(id === '#cartpay_tva'){
            $.nicenotify.notifier = {
                box:"",
                elemclass : '.mc-message-tva'
            };
            $(id).validate({
                ignore: [],
                onsubmit: true,
                event: 'submit',
                rules: {
                    country: {
                        required: true,
                        minlength: 1
                    },
                    iso: {
                        required: true
                    }
                    ,
                    idtvac: {
                        required: true
                    }
                },
                submitHandler: function(form) {
                    $.nicenotify({
                        ntype: "submit",
                        uri: setAjaxUrlLoad(baseadmin,tab,action),
                        typesend: 'post',
                        idforms: $(form),
                        resetform: true,
                        successParams:function(data){
                            $.nicenotify.initbox(data,{
                                display:true
                            });
                            //$('#add-tva').modal('hide');
                            window.setTimeout(function() {
                                $(".alert-success").alert('close');
                                $('#add-tva').modal('hide');
                            }, 4000);
                            getHTMLFormat(baseadmin,tab,'html');
                        }
                    });
                    return false;
                }
            });
        }else{
            $(id).on('submit',function(){
                $.nicenotify.notifier = {
                    box:"",
                    elemclass : '.mc-message'
                };
                $.nicenotify({
                    ntype: "submit",
                    uri: setAjaxUrlLoad(baseadmin,tab, action),
                    typesend: 'post',
                    idforms: $(this),
                    beforeParams:function(){},
                    successParams:function(e){
                        $.nicenotify.initbox(e,{
                            display:true
                        });
                    }
                });
                return false;
            });
        }
    }
    /**
     *
     * @param baseadmin
     * @param action
     * @param edit
     * @param level
     * @param productype
     */
    function getHTMLFormat(baseadmin,tab,action){
        $.nicenotify({
            ntype: "ajax",
            uri: setAjaxUrlLoad(baseadmin,tab,action),
            typesend: 'get',
            datatype: 'html',
            beforeParams:function(){
                var loader = $(document.createElement("span")).addClass("loader offset5").append(
                    $(document.createElement("img"))
                        .attr('src','/'+baseadmin+'/template/img/loader/small_loading.gif')
                        .attr('width','20px')
                        .attr('height','20px')
                );
                $('#list-tva').html(loader);
            },
            successParams:function(data){
                $('#list-tva').empty();
                $.nicenotify.initbox(data,{
                    display:false
                });
                $('#list-tva').html(data);
            }
        });
    }

    /**
     * Suppression de la TVA d'un pays
     * @param baseadmin
     * @param tab
     * @param action
     * @param id
     * @param modal
     */
    function remove(baseadmin,tab,action,id,modal){
        $(document).on('click','.remove-tva',function(){
            var target = $(this).data('remove');
            $('#remove_tva').val(target);
        });
        $(id).validate({
            onsubmit: true,
            event: 'submit',
            rules: {
                remove_tva: {
                    required: true,
                    number: true,
                    minlength: 1
                }
            },
            submitHandler: function(form) {
                $.nicenotify({
                    ntype: "submit",
                    uri: setAjaxUrlLoad(baseadmin,tab,action),
                    typesend: 'post',
                    idforms: $(form),
                    resetform: true,
                    successParams:function(data){
                        $(modal).modal('hide');
                        window.setTimeout(function() { $(".alert-success").alert('close'); }, 4000);
                        $.nicenotify.initbox(data,{
                            display:true
                        });
                        //$('#item_'+$('#delete').val()).remove();
                        getHTMLFormat(baseadmin,tab,'html');
                    }
                });
                return false;
            }
        });
    }

    /**
     * Add country name in input hidden
     */
    function selectCountry(){
        $('select#iso').on('change',function(){
            var $currentOption = $(this).find('option:selected').data("country").toLowerCase();
            if($currentOption != ''){
                $('#country').val($currentOption);
            }
        });
    }
    return {
        //Fonction public
        run : function(){
            trHidden(".view-block");
        },
        runConfig: function(baseadmin,tab){
            save(baseadmin,tab,'update','#cartpay_config');
        },
        runTva: function(baseadmin,tab){
            selectCountry();
            save(baseadmin,tab,null,'#cartpay_tva_conf');
            save(baseadmin,tab,'add','#cartpay_tva');
            getHTMLFormat(baseadmin,tab,'html');
            remove(baseadmin,tab,'remove','#del_tva','#deleteModal');
        }
    }
})(jQuery, window, document);
