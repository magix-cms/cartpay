{autoload_i18n}
{$dataCart = $getItemCartData}
{*$getItemPriceData|var_dump*}
<table class="table table-bordered table-condensed table-hover" id="cart_table">
    <thead>
    <tr>
        <th id="cart_product">{#products_cart#}</th>
        <th id="cart_desc">{#desc_cart#}</th>
        <th id="cart_price">{#price#}</th>
        <th id="cart_quantity">{#quantity_cart#}</th>
        <th id="cart_sub_total">{#sub_total_cart#}</th>
        {if $setParamsData.remove eq 'true'}
        <th id="cart_delete"><span data-toggle="tooltip" data-placement="top" title="{#delete_cart#}" class="fa fa-trash-o"></span></th>
        {/if}
    </tr>
    </thead>

{if is_array($dataCart) && !empty($dataCart)}
    <tfoot>
    {$block_size = 2}
    {if $smarty.post.tva_country OR $getItemPriceData.amount_vat != null}
        {$block_size = $block_size + 2}
    {/if}
    {if $getDataConfig.shipping eq '1'}
        {$block_size = $block_size + 1}
    {/if}
    {if $smarty.post.v_code}
        {$block_size = $block_size + 1}
    {/if}
    <tr>
        <td colspan="2" rowspan="{$block_size}">&nbsp;</td>
        <td colspan="2" class="text-right">
            {#total_product_ttc#}
        </td>
        <td colspan="2">
            {$getItemPriceData.amount_products} €
        </td>
    </tr>
    <tr class="total-line">
        {if $smarty.session.idprofil && $smarty.session.keyuniqid_pr}
        <td colspan="2" class="text-right">
            {#total_product_htva#}
        </td>
        <td colspan="2">
            {$getItemPriceData.amount_hvat} €
        </td>
        {/if}
    </tr>
    {if $getDataConfig.shipping eq '1'}
        <tr>
            <td colspan="2" class="text-right">
                {#shipping#}
            </td>
            <td colspan="2">
                {if $getItemPriceData.shipping != 0}
                    {$getItemPriceData.shipping} €
                {else}
                    {#free_shipping#}
                {/if}
            </td>
        </tr>
    {/if}
    {if $smarty.post.tva_country OR $getItemPriceData.amount_vat != null}
        {*<tr>
            <td colspan="4" class="text-right">
                {#vat#}
            </td>
            <td colspan="2">
                {$getItemPriceData.amount_vat} %
            </td>
        </tr>*}
        <tr>
            <td colspan="2" class="text-right">
                {#vat#}
            </td>
            <td colspan="2">
                {$getItemPriceData.amount_vat} €
            </td>
        </tr>
    {/if}
    {if $smarty.post.v_code}
        <tr>
            <td colspan="2" class="text-right">
                Promo
            </td>
            <td colspan="2">
                {$getItemPriceData.amount_promo} €
            </td>
        </tr>
    {/if}
    {if $smarty.post.tva_country OR $getItemPriceData.amount_vat != null}
        <tr class="total-line total">
            <td colspan="2" class="text-right">
                {#total_to_pay#}
            </td>
            <td colspan="2">
                <span id="amount_order">{$getItemPriceData.amount_to_pay}</span> €
            </td>
        </tr>
    {/if}
    </tfoot>
    <tbody>
    {foreach $dataCart as $key => $value nocache}
        <tr>
            <td>
                {if $value.imgSrc.small}
                <a href="{$value.imgSrc.large}" class="img-zoom" title="{$value.titlecatalog}">
                    <img src="{$value.imgSrc.small}" alt="{$value.titlecatalog}">
                </a>
                {else}
                    <img src="{$value.imgSrc.default}" alt="{$value.titlecatalog}">
                {/if}
            </td>
            <td>
                <p><a href="{$value.urlproduct}" title="{$value.titlecatalog}">{$value.titlecatalog}</a></p>
            </td>
            <td>
                {$value.price_products} €
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
    </tbody>
{/if}
</table>
