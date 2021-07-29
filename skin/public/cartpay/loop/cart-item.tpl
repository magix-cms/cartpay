{strip}
    {if !isset($lazy)}
        {$lazy = true}
    {/if}
{/strip}
{if isset($data) && is_array($data)}
	{foreach $data as $item}
        <li id="product_{$item.id}" class="cart-item">
            <div class="item-details">
                {include file="img/img.tpl" img=$item.img lazy=$lazy}
                <div class="item-info">
                    {if $item.reference}<span class="item-ref">#&nbsp;{$item.reference}</span>{/if}
                    <span class="item-name">{$item.name}</span>
                    <span class="item-price">{if $setting.price_display.value === 'tinc'}{$item.unit_price_inc|string_format:"%.2f"}{else}{$item.unit_price|string_format:"%.2f"}{/if}&nbsp;<span class=currency">€</span></span>
                </div>
            </div>
            <div class="item-quantity">
                <form action="{$url}/{$lang}/cartpay/?action=edit" class="edit-product-quantity form-inline">
                    <div class="form-group">
                        <label for="quantity_{$item.id_items}" class="control-label">{#quantity#|ucfirst}&thinsp;:</label>
                        <input type="number" id="quantity_{$item.id_items}" name="quantity" min="0" step="1" value="{$item.q}" />
                    </div>
                    <input type="hidden" name="id_product" value="{$item.id}" />
                </form>
            </div>
            <div class="item-total">
                <span class="product-total">{if $setting.price_display.value === 'tinc'}{$item.total_inc|string_format:"%.2f"}{else}{$item.total|string_format:"%.2f"}{/if}</span>&nbsp;€
            </div>
            <div class="item-remove">
                <form action="{$url}/{$lang}/cartpay/?action=edit" class="edit-product-quantity">
                    <button type="submit" class="btn btn-box btn-invert btn-main-theme" title="{#remove_product_cart#}"><i class="material-icons">close</i></button>
                    <input type="hidden" name="id_product" value="{$item.id}" />
                    <input type="hidden" name="quantity" value="0" />
                </form>
            </div>
        </li>
	{/foreach}
{/if}