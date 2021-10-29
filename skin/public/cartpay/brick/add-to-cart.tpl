<form id="cartpay-form" class="add-to-cart form-inline" method="post" action="{$url}/{$lang}/cartpay/?action=add">
    <div class="form-group">
        {*<label for="quantity">{#quantity#|ucfirst}</label>*}
        <input type="number" min="1" step="1" name="quantity" id="quantity" class="form-control required" value="1" required/>
    </div>
    {if is_array($product.attributes) && !empty($product.attributes)}
        <div class="form-group">
            <label for="param[attribute]">{#ph_transport_city#|ucfirst}</label>
            <select name="param[attribute]" id="param[attribute]" class="form-control required" required>
                {*<option disabled selected>-- {#pn_transport_city#|ucfirst} --</option>*}
                {foreach $product.attributes as $item}
                    <option value="{$item.id}">{$item.type} : {$item.name}</option>
                {/foreach}
            </select>
        </div>
    {/if}
    <input type="hidden" name="id_product" value="{$product.id}" />
    <button type="submit" class="btn btn-main">{#add_cart#|ucfirst}</button>
</form>