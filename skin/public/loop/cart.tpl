{autoload_i18n}
{$dataCart = $getItemCartData}
<table class="table table-bordered table-condensed table-hover" id="cart_table">
    <thead>
    <tr>
        <th id="cart_product">{#products_cart#}</th>
        <th id="cart_quantity">{#quantity_cart#}</th>
        <th id="cart_price">{#price#}</th>
        <th id="cart_sub_total">{#sub_total_cart#}</th>
        {if $setParamsData.remove eq 'true'}
        <th id="cart_delete"><span data-toggle="tooltip" data-placement="top" title="{#delete_cart#}" class="fa fa-trash-o"></span></th>
        {/if}
    </tr>
    </thead>
{if is_array($dataCart) && !empty($dataCart)}
    <tbody>
    {foreach $dataCart as $key => $value nocache}
        <tr>
            <td>
                <a href="{$value.urlproduct}">{$value.titlecatalog}</a>
            </td>
            <td>
                {if $setParamsData.editQuantity eq 'true'}
                <a data-target="#quantity-cart" data-toggle="modal" data-qty="{$value.quantity}" data-item="{$value.id_item}" class="edit-qty" href="#">
                    {$value.quantity} <span class="fa fa-edit"></span>
                </a>
                {else}
                {$value.quantity}
                {/if}
            </td>
            <td>
                {$value.price_products} €
            </td>
            <td>
                {$value.sub_amount} €
            </td>
            {if $setParamsData.remove eq 'true'}
            <td>
                <a href="#" class="d-plugin-cart-item" rel="{$value.id_item}">
                    <span class="fa fa-trash"></span>
                </a>
            </td>
            {/if}
        </tr>
    {/foreach}
    <tr>
        <td colspan="6">
            {#to_pay#}
        </td>
        <td colspan="2">
            {$getItemPriceData.ammount_products} €
        </td>
    </tr>
    {if $getItemPriceData.shipping != 0}
    <tr>
        <td colspan="6">
            {#shipping#}
        </td>
        <td colspan="2">
           {$getItemPriceData.shipping} €
        </td>
    </tr>
    {/if}
    {if $smarty.post.v_code}
    <tr>
        <td colspan="6">
            Promo
        </td>
        <td colspan="2">
            {$getItemPriceData.amount_promo} €
        </td>
    </tr>
    {/if}
    {*
    {if $smarty.post.profil_cashback}
        <tr>
            <td colspan="6">
                Cashback
            </td>
            <td colspan="2">
                {$getItemPriceData.amount_profil} €
            </td>
        </tr>
    {/if}
    *}
    {*<tr>
        <td colspan="6">
            {#tax#} (21%)
        </td>
        <td colspan="2">
            {$getItemPriceData.amount_tax} €
        </td>
    </tr>*}
    <tr>
        <td colspan="6">
            {#to_pay_ttc#}
        </td>
        <td colspan="2">
            <span id="amount_order">{$getItemPriceData.amount_to_pay}</span> €
        </td>
    </tr>
    </tbody>
{/if}
</table>