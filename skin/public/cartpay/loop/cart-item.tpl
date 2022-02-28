{strip}
    {if !isset($lazy)}
        {$lazy = true}
    {/if}
{/strip}
{if isset($data) && is_array($data)}
	{foreach $data as $item}
        <li id="product_{$item.id_items}" class="cart-item">
            <div class="item-details">
                {include file="img/img.tpl" img=$item.img lazy=$lazy}
                <div class="item-info">
                    {if $item.reference}<span class="item-ref">#&nbsp;{$item.reference}</span>{/if}
                    <span class="item-name">{$item.name}</span>
                    {if is_array($item.params) && !empty($item.params)}
                    {foreach $item.params as $param => $value}
                        {if is_array($value.value)}
                            {foreach $value.value as $v}
                                <span class="item-param">{*{$param}&nbsp;: *}{$v}</span>
                            {/foreach}
                        {else}
                            <span class="item-param">{*{$param}&nbsp;: *}{$value.value}{if !empty($value.price.price)}&thinsp;:&thinsp;<span class="item-price">{if $setting.price_display.value === 'tinc'}{math equation="price * (1 + (vat / 100))" price=$value.price.price vat=$value.price.vat format="%.2f"}{else}{$value.price.price|string_format:"%.2f"}{/if}&nbsp;<span class=currency">€</span>{if $setting.price_display.value === 'tinc'}{#tax_included#}{else}{#tax_excluded#}{/if}</span>{/if}</span>
                        {/if}
                        {*{if !empty($value.info) && is_array($value.info)}
                            {foreach $value.info as $info}
                            <span class="item-param">{$info.name}&nbsp;: {$info.value}</span>
                            {/foreach}
                        {/if}*}
                    {/foreach}
                    {/if}
                    <span class="item-price">{if $setting.price_display.value === 'tinc'}{$item.unit_price_inc|string_format:"%.2f"}{else}{$item.unit_price|string_format:"%.2f"}{/if}&nbsp;<span class=currency">€</span></span>
                </div>
            </div>
            <div class="item-quantity">
                <form action="{$url}/{$lang}/cartpay/?action=edit" class="edit-product-quantity form-inline">
                    <div class="form-group">
                        <label for="quantity_{$item.id_items}" class="control-label">{#quantity#|ucfirst}&thinsp;:</label>
                        <input type="number" id="quantity_{$item.id_items}" name="quantity" min="0" step="1" value="{$item.q}" />
                    </div>
                    {if is_array($item.params) && !empty($item.params)}
                    {foreach $item.params as $param => $value}
                        {if is_array($value.id)}
                            {foreach $value.id as $k => $v}
                                {if is_array($v)}{foreach $v as $sk => $sv}
                                    <input type="hidden" name="param[{$param}][{$k}][{$sk}]" value="{$sv}" />
                                {/foreach}{else}
                                    <input type="hidden" name="param[{$param}][{$k}]" value="{$v}" />
                                {/if}
                            {/foreach}
                        {else}
                            <input type="hidden" name="param[{$param}]" value="{$value.id}" />
                        {/if}
                    {/foreach}
                    {/if}
                    <input type="hidden" name="id_items" value="{$item.id_items}" />
                    <input type="hidden" name="id_product" value="{$item.item->id}" />
                </form>
            </div>
            <div class="item-total">
                <span class="product-total">{if $setting.price_display.value === 'tinc'}{$item.total_inc|string_format:"%.2f"}{else}{$item.total|string_format:"%.2f"}{/if}</span>&nbsp;€
            </div>
            <div class="item-remove">
                <form action="{$url}/{$lang}/cartpay/?action=edit" class="edit-product-quantity">
                    <button type="submit" class="btn btn-box btn-invert btn-main-theme" title="{#remove_product_cart#}"><i class="material-icons ico ico-close"></i></button>
                    {if is_array($item.params) && !empty($item.params)}
                    {foreach $item.params as $param => $value}
                        {if is_array($value.id)}
                            {foreach $value.id as $k => $v}
                                {if is_array($v)}{foreach $v as $sk => $sv}
                                    <input type="hidden" name="param[{$param}][{$k}][{$sk}]" value="{$sv}" />
                                {/foreach}{else}
                                    <input type="hidden" name="param[{$param}][{$k}]" value="{$v}" />
                                {/if}
                            {/foreach}
                        {else}
                            <input type="hidden" name="param[{$param}]" value="{$value.id}" />
                        {/if}
                    {/foreach}
                    {/if}
                    <input type="hidden" name="id_product" value="{$item.item->id}" />
                    <input type="hidden" name="id_items" value="{$item.id_items}" />
                    <input type="hidden" name="quantity" value="0" />
                </form>
            </div>
        </li>
	{/foreach}
{/if}