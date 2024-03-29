/**
 * Initialise the display of notice message
 * @param {html} m - message to display.
 * @param {int|boolean} [timeout=false] - Time before hiding the message.
 * @param {string|boolean} [sub=false] - Sub-controller name to select the container for the message.
 * @param {string|boolean} [modal=false] - Modal id.
 */
function initAlert(m,timeout,sub,modal) {
    sub = typeof sub !== 'undefined' ? sub : false;
    timeout = typeof timeout !== 'undefined' ? timeout : false;
    modal = typeof modal !== 'undefined' ? modal : false;
    if(sub) $.jmRequest.notifier = { box:"", cssClass : '.mc-message-'+sub };
    $.jmRequest.initbox(m,{ display:true });
    if(timeout) window.setTimeout(function () {
        $('.mc-message .alert').removeClass('in').remove();
        if(modal) { $(modal).modal('hide'); }
    }, timeout);
}

window.addEventListener('load',function(){
    $('.add-to-cart').each(function(){
        $(this).removeData();
        $(this).off();
        $(this).validate({
            ignore: [],
            onsubmit: true,
            event: 'submit',
            submitHandler: function(f,e) {
                e.preventDefault();

                // --- Initialise the ajax request
                $.jmRequest({
                    handler: "submit",
                    url: $(f).attr('action'),
                    method: 'post',
                    form: $(f),
                    resetForm: true,
                    beforeSend: function () {
                        const loader = $(document.createElement("div")).addClass("loader form-group")
                            .append(
                                $(document.createElement("i")).addClass("fa fa-spinner fa-pulse fa-fw"),
                                $(document.createElement("span")).append("Chargement en cours...").addClass("sr-only")
                            );
                        $('input[type="submit"], button[type="submit"]').hide().after(loader);
                    },
                    success: function (d) {
                        $('.mc-message').removeClass('text-center');
                        $('.loader').remove();
                        $('input[type="submit"], button[type="submit"]').show();

                        niceForms.reset();

                        if(typeof d.debug === 'string' && d.debug !== '') {
                            initAlert(d.debug,false);
                        }
                        else if(typeof d.notify === 'string' && d.notify !== '') {
                            initAlert(d.notify,4000);
                        }
                        else if(typeof d === 'string' && d !== '') {
                            initAlert(d,4000);
                        }

                        if(typeof d.result === 'string' && d.result !== '') {
                            floatCart.add(d.result);
                        }

                        if(typeof d.extend === 'object') {
                            let cart = d.extend;
                            floatCart.upd(cart.id_item, cart.nb, cart.nb_items, cart.total);
                        }
                    }
                });

                return false;
            }
        });
    });

    $('.edit-product-quantity').each(function(){
        $(this).removeData();
        $(this).off();
        $(this).validate({
            ignore: [],
            onsubmit: true,
            event: 'submit',
            submitHandler: function(f,e) {
                e.preventDefault();

                // --- Initialise the ajax request
                $.jmRequest({
                    handler: "submit",
                    url: $(f).attr('action'),
                    method: 'post',
                    form: $(f),
                    beforeSend: function () {
                        const loader = $(document.createElement("div")).addClass("loader")
                            .append(
                                $(document.createElement("i")).addClass("fa fa-spinner fa-pulse fa-fw"),
                                $(document.createElement("span")).append("Chargement en cours...").addClass("sr-only")
                            );
                        $('input[type="submit"], button[type="submit"]').hide().after(loader);
                    },
                    success: function (d) {
                        $('.mc-message').removeClass('text-center');
                        $('.loader').remove();
                        $('input[type="submit"], button[type="submit"]').show();

                        niceForms.reset();

                        if(typeof d.debug === 'string' && d.debug !== '') {
                            initAlert(d.debug,false);
                        }
                        else if(typeof d.notify === 'string' && d.notify !== '') {
                            initAlert(d.notify,4000);
                        }
                        else if(typeof d === 'string' && d !== '') {
                            initAlert(d,4000);
                        }

                        if(typeof d.extend === 'object') {
                            let cart = d.extend;
                            floatCart.upd(cart.id_item, cart.nb, cart.nb_items, cart.total.tot);
                            Cart.upd(cart);
                        }
                    }
                });

                return false;
            }
        });
    });

    let quantityField = document.querySelectorAll('#shopping-cart [name="quantity"]');
    let wait = null;
    quantityField.forEach(function(i){
        function submitForm() {
            clearTimeout(wait);
            wait = setTimeout(function(){
                $(i.form).submit();
            },500);
        }

        i.addEventListener('change',submitForm);
    });
});