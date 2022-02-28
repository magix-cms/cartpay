function fade(elem, type, ms) {
    let isIn = type === 'in',
        opacity = isIn ? 0 : 1,
        interval = 50,
        duration = ms,
        gap = interval / duration;

    if(isIn) {
        elem.style.display = 'block';
        elem.style.opacity = opacity;
    }

    function func() {
        opacity = isIn ? opacity + gap : opacity - gap;
        elem.style.opacity = opacity;

        if(opacity <= 0) elem.style.display = 'none';
        if(opacity <= 0 || opacity >= 1) window.clearInterval(fading);
    }

    let fading = window.setInterval(func, interval);
}

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

const floatCart = {
    container: null,
    elem: null,
    list: null,
    opened: function(){ return this.container.classList.contains('open'); },
    add: function(item){
        this.list.querySelector('li:last-child').insertAdjacentHTML('beforebegin',item);
        this.elem.classList.remove('empty-cart');
    },
    upd: function(id, nb, tot_nb, tot){
        let badges = document.querySelectorAll('.cart-total-items');
        badges.forEach(function(b){
            b.innerHTML = tot_nb
        });
        if(!tot_nb) this.elem.classList.add('empty-cart');
        let item = document.getElementById('item_'+id);
        if(item !== undefined && nb > 0) {
            item.querySelector('.quantity').innerHTML = nb;
        }
        else if(nb === 0) {
            item.remove();
        }
        this.elem.querySelector('.total_cart').innerHTML = tot;
    },
    open: function(e){
        if(!this.opened()) {
            fade(this.elem,'in',200);
            this.container.classList.add('open');
            e.stopPropagation();
            document.addEventListener('click', this.outsideClickListener);
        }
    },
    close: function(){
        if(this.opened()) {
            fade(this.elem,'out',200);
            this.container.classList.remove('open');
            document.removeEventListener('click', this.outsideClickListener);
        }
    },
    toggle: function(){
        fade(this.elem,this.opened() ? 'out': 'in',200);
        this.container.classList.toggle('open',!this.opened());
        if(this.opened()) {
            document.addEventListener('click', this.outsideClickListener);
        }
        else {
            document.removeEventListener('click', this.outsideClickListener);
        }
    },
    outsideClickListener: function(e) {
        if (!floatCart.elem.contains(e.target) && floatCart.opened()) {
            floatCart.close();
        }
    }
};

const Cart = {
    container: null,
    elem: null,
    list: null,
    upd: function(cart){
        let item = document.getElementById('product_'+cart.id_item);
        if(item !== undefined && cart.nb > 0) {
            //item.querySelector('.quantity').innerHTML = cart.nb;
            item.querySelector('.product-total').innerHTML = cart.product_tot.toFixed(2);
        }
        else if(cart.nb === 0) {
            item.remove();
        }

        this.container.querySelector('.tot_products').innerHTML = cart.total.tot;
        this.container.querySelector('.tot_exc').innerHTML = cart.total.exc;
        for (let [rate, value] of Object.entries(cart.total.vat)) {
            this.container.querySelector('.tot_vat_'+rate).innerHTML = value;
        }
        this.container.querySelector('.tot_inc').innerHTML = cart.total.tot;

        if(!cart.nb_items) this.container.classList.add('empty-cart');
    }
};

window.addEventListener('load',function(){
    let cartButton = document.getElementById('shopping-cart-btn');

    if(cartButton !== undefined && cartButton !== null) {
        floatCart.container = document.getElementById('shopping-float-cart');
        floatCart.elem = floatCart.container.querySelector('.float-cart');
        floatCart.list = floatCart.elem.querySelector('.shopping-cart-items');

        cartButton.addEventListener('click',function(e){
            e.preventDefault();
            floatCart.open(e);
            return false;
        });
    }

    let addToCart = document.querySelectorAll('.add-to-cart');
    let productPrice = document.querySelector('.product-price');
    if(productPrice !== null && productPrice !== undefined) {
        let displayMode = productPrice.dataset.display;
        let productVat = parseFloat(productPrice.dataset.vat);
        let price = parseFloat(productPrice.dataset.price);
        addToCart.forEach((atc) => {
            let priceReplacer = atc.querySelectorAll('[data-price-replacer]');
            let priceImpacter = atc.querySelectorAll('[data-impact]');
            let priceAdditionnal = atc.querySelectorAll('[data-price-additionnal] [data-price]');
            let quantity = atc.querySelector('[name="quantity"]');
            let ofs = atc.querySelectorAll('.optional-field');
            function updatePrice() {
                let updatedPrice = price;
                let optionPrice = 0;
                let itemQ = quantity.value;
                priceReplacer.forEach((pr) => {
                    let selected = pr.options[pr.selectedIndex];
                    if(typeof selected !== "undefined") {
                        updatedPrice = (selected.dataset.price !== '' && selected.dataset.price !== undefined) ? parseFloat(selected.dataset.price) : updatedPrice;
                        if(selected.dataset.vat !== '' && selected.dataset.vat !== undefined) productVat = parseFloat(selected.dataset.vat);
                    }
                });
                priceImpacter.forEach((pi) => {
                    if(pi.checked || pi.selected) updatedPrice = updatedPrice + parseFloat(pi.dataset.impact);
                });
                priceAdditionnal.forEach((pa) => {
                    let vat = pa.dataset.vat !== undefined ? parseFloat(pa.dataset.vat) : 0;
                    let computedOptionPrice = displayMode === 'tinc' ? parseFloat(pa.dataset.price) * (1 + (vat/100)) : parseFloat(pa.dataset.price);
                    if(pa.checked || pa.selected) optionPrice = optionPrice + computedOptionPrice;
                });
                let computedPrice = displayMode === 'tinc' ? updatedPrice * (1 + productVat/100) : updatedPrice;
                productPrice.innerHTML = ((computedPrice * itemQ) + optionPrice).toLocaleString(undefined,{
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
                ofs.forEach((of) => {
                    let target = of.dataset.target;
                    if(target !== null && target !== undefined) document.querySelector(target).classList.remove('in');
                });
                ofs.forEach((of) => {
                    let target = of.dataset.target;
                    if(target !== null && target !== undefined) {
                        let targetElement = document.querySelector(target);
                        if(of.checked && !targetElement.classList.contains('in')) targetElement.classList.add('in');
                    }
                });
            }
            priceReplacer.forEach((pr) => {
                pr.addEventListener('change',updatePrice);
                pr.addEventListener('input',updatePrice);
            });
            priceImpacter.forEach((pi) => {
                pi.addEventListener('change',updatePrice);
                pi.addEventListener('input',updatePrice);
            });
            priceAdditionnal.forEach((pa) => {
                pa.addEventListener('change',updatePrice);
                pa.addEventListener('input',updatePrice);
            });
            quantity.addEventListener('change',updatePrice);
            quantity.addEventListener('input',updatePrice);
            ofs.forEach((of) => {
                of.addEventListener('change',updatePrice);
                of.addEventListener('input',updatePrice);
            });
            updatePrice();
        });
    }

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

    Cart.container = document.getElementById('shopping-cart');

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