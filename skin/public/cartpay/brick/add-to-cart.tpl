<form id="cartpay-form" class="add-to-cart nice-form" method="post" action="{$url}/{$lang}/cartpay/?action=add">
    <div class="row">
        <div class="col-12 col-md">
    <div class="form-group">
        <input type="number" min="1" step="1" name="quantity" id="quantity" class="form-control required" value="1" required/>
        <label for="quantity">{#quantity#|ucfirst}</label>
    </div>
        </div>
    {if is_array($cartpay_params) && !empty($cartpay_params)}
        {foreach $cartpay_params as $param}
            {if $param@first}
                <div class="col-12 col-md">
                    {$param}
                </div>
                </div>
                {else}
                {$param}
                {/if}

        {/foreach}
    {/if}
    <div class="submit text-center">
        <input type="hidden" name="id_product" value="{$product.id}" />
        <button type="submit" class="btn btn-main">{#add_cart#|ucfirst}</button>
        {*<a href="#desc" data-toggle="tab" class="btn btn-sd">annuler</a>*}
    </div>
    <div class="mc-message mc-message-cartpay"></div>
</form>