<div class="float-cart{if $cart.nb_items eq 0} empty-cart{/if}">
    <div class="shopping-cart-header">
        <i class="material-icons cart-icon ico ico-bag2"></i><span class="badge cart-total-items">{$cart.nb_items}</span>
        <div class="shopping-cart-total">
            <span class="lighter-text">{#total#}&thinsp;:</span>&nbsp;<span class="main-color-text"><span class="total_cart">{if $setting.price_display.value === 'tinc'}{$cart.total.inc}{else}{$cart.total.exc}{/if}</span>&nbsp;<span class=currency">€</span></span>
        </div>
    </div>
    <ul class="shopping-cart-items">
        {include file="cartpay/loop/float-cart-item.tpl" data=$cart.items}
        <li>{#empty_cart#}</li>
    </ul>
    <a href="{$url}/{$lang}/cartpay/" class="btn btn-main btn-box btn-block">{#go_to_cart#}</a>
</div>