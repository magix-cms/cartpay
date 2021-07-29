{strip}
    {if !isset($lazy)}
        {$lazy = false}
    {/if}
{/strip}
{if isset($data) && is_array($data)}
	{foreach $data as $item}
        <li id="item_{$item.id}" class="cart-item">
            {include file="img/img.tpl" img=$item.img lazy=$lazy}
            <div class="item-details">
                <span class="item-name">{$item.name}</span>
                <small>
                    <span class="item-price">{if $setting.price_display.value === 'tinc'}{$item.unit_price_inc}{else}{$item.unit_price}{/if}&nbsp;<span class=currency">€</span></span>
                    <span class="item-quantity">{#quantity#|ucfirst}&thinsp;:&nbsp;<span class="quantity">{$item.q}</span></span>
                </small>
            </div>
        </li>
	{/foreach}
{/if}