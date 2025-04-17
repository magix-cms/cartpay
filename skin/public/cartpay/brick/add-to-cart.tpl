<form id="cartpay-form" class="add-to-cart" method="post" action="{$url}/{$lang}/cartpay/?action=add">
    <div class="row">
        <div class="col-12 col-sm-6">
            {strip}{if $product.price !== '0.00'}
                <div class="price">
                    {if $product.promo_price !== '0.00'}
                        {$promo_price = $product.promo_price * (1 + ($setting.vat_rate/100))}
                        <span class="crossed-price">{$price|round:1|number_format:2:',':' '|decimal_trim:','}&nbsp;€&nbsp;</span>
                    {else}
                        {$price = $price|round:1|number_format:2:',':' '|decimal_trim:','}
                    {/if}
                <span class="product-price" data-price="{if $product.promo_price !== '0.00'}{$promo_price}{else}{$price|round:2|number_format:2:',':' '|decimal_trim:','}{/if}" data-vat="{$setting.vat_rate}">{if $product.promo_price !== '0.00'}{$promo_price}{else}{$price}{/if}</span> €&nbsp;{if $setting.price_display === 'tinc'}<span class="price-tax">{#tax_included#}</span>{else}<span class="price-tax">{#tax_excluded#}</span>{/if}
                </div>
            {/if}{/strip}
        </div>
        <div class="col-12 col-sm-6">
            {*<div class="form-group">
                <label for="quantity">{#quantity#|ucfirst}</label>
                <input type="number" min="1" step="1" name="quantity" id="quantity" class="form-control required" value="1" required/>
            </div>*}
            <div class="input-group">
                <span class="input-group-btn">
                    <input type="button" id="decrement" value="-" class="button-minus btn btn-default" data-field="quantity">
                </span>
                <input type="number" min="1" step="1" value="1" id="quantity" name="quantity" class="quantity-field form-control required" required />
                <span class="input-group-btn">
                    <input type="button" id="increment" value="+" class="button-plus btn btn-default" data-field="quantity">
                </span>
            </div>

        </div>
    </div>
    {*{print_r($cartpay_params)}*}
    {if is_array($cartpay_params) && !empty($cartpay_params)}
        {foreach $cartpay_params as $param}
            {if $param@first}
                <div class="row">
                    <div class="col-12">
                        {$param}
                    </div>
                </div>
            {else}
                {$param}
            {/if}

        {/foreach}
    {/if}
    <div class="submit">
        <input type="hidden" name="id_product" value="{$product.id}" />
        <button type="submit" class="btn btn-main">{#add_cart#|ucfirst}</button>
        {*<a href="#desc" data-toggle="tab" class="btn btn-sd">annuler</a>*}
    </div>
    <div class="mc-message mc-message-cartpay"></div>
</form>