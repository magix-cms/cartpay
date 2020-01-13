<form id="cartpay-form" class="validate_form span_feedback" method="post" action="{geturl}/{getlang}/cartpay/?action=add">
    <input type="hidden" name="cart[id_product]" value="{$product.id}" />
    <input type="number" min="1" name="cart[quantity]" value="1" />
    <button type="submit" class="btn btn-box btn-invert btn-main-theme">{#add_cart#|ucfirst}</button>
</form>