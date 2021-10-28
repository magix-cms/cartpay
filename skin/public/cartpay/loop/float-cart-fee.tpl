{strip}
    {if !isset($lazy)}
        {$lazy = false}
    {/if}
{/strip}
{if isset($data) && is_array($data)}
	{foreach $data as $name => $fee}
        <li id="fee_{$fee.id}" class="cart-fee">
            <span class="lighter-text">{$name}&thinsp;:</span>&nbsp;<span class="main-color-text"><span class="total_cart">{if $setting.price_display.value === 'tinc'}{$fee.price_inc}{else}{$fee.price}{/if}</span>&nbsp;<span class=currency">€</span></span>
        </li>
	{/foreach}
{/if}