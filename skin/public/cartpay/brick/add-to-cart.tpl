<form id="cartpay-form" class="add-to-cart form-inline" method="post" action="{$url}/{$lang}/cartpay/?action=add">
    <div class="form-group">
        <label for="quantity">{#quantity#}</label>
        <input type="number" min="1" step="1" name="quantity" id="quantity" class="form-control required" value="1" required/>
    </div>
    <input type="hidden" name="id_product" value="{$product.id}" />
    <button type="submit" class="btn btn-box btn-invert btn-main">{#add_cart#|ucfirst}</button>
</form>