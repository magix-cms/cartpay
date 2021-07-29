{extends file="layout.tpl"}
{block name="title"}{seo_rewrite conf=['level'=>'root','type'=>'title','default'=>{#seo_cartpay_title#}]}{/block}
{block name="description"}{seo_rewrite conf=['level'=>'root','type'=>'description','default'=>{#seo_cartpay_desc#}]}{/block}
{block name='body:id'}cart-resume{/block}

{block name="article:content"}
    <h1 class="text-center">{#my_cart#}</h1>
    <div id="shopping-cart"{if !$cart.nb_items} class="empty-cart"{/if}>
        <ul class="shopping-cart-items">
            {include file="cartpay/loop/cart-item.tpl" data=$cart.items}
        </ul>
        <div class="cart-total">
            <div class="tot row">
                <div class="col-6 text-right">{#total_products#}</div>
                <div class="col-6"><span class="tot_products">{if $setting.price_display.value === 'tinc'}{$cart.total.inc|string_format:"%.2f"}{else}{$cart.total.exc|string_format:"%.2f"}{/if}</span>&nbsp;€</div>
            </div>
            <div class="tot row">
                <div class="col-6 text-right">{#total_exc#}</div>
                <div class="col-6"><span class="tot_exc">{$cart.total.exc|string_format:"%.2f"}</span>&nbsp;€</div>
            </div>
            {foreach $cart.total.vat as $rate => $vat}
                <div class="tot row">
                    <div class="col-6 text-right">{#total_vat#}&nbsp;<small>({$rate}%)</small></div>
                    <div class="col-6"><span class="tot_vat_{$rate}">{$vat|string_format:"%.2f"}</span>&nbsp;€</div>
                </div>
            {/foreach}
            <div class="tot row">
                <div class="col-6 text-right">{#total_inc#}</div>
                <div class="col-6"><span class="tot_inc">{if $setting.price_display.value === 'tinc'}{$cart.total.inc|string_format:"%.2f"}{else}{$cart.total.exc|string_format:"%.2f"}{/if}</span>&nbsp;€</div>
            </div>
        </div>
        <div class="actions">
            <div class="row row-center">
                {if $config_cart.quotation_enabled}
                    <div class="col-12 col-xs-6 col-lg-4">
                        <div class="action quotation">
                            <div class="icon">
                                <i class="material-icons">assignment</i>
                            </div>
                            <div class="text">
                                <p class="h3">{#title_quotation#}</p>
                                <p class="help-block">
                                    {#txt_quotation#}
                                </p>
                            </div>
                            <a href="{$quotationFirstStep}" title="{#continue_quotation#}"><span class="sr-only">{#continue_quotation#}</span><i class="material-icons">keyboard_arrow_right</i></a>
                        </div>
                    </div>
                {/if}
                {if $config_cart.order_enabled && !empty($available_payment_methods)}
                    <div class="col-12 col-xs-6 col-lg-4">
                        <div class="action order">
                            <div class="icon">
                                <i class="material-icons">credit_card</i>
                                <i class="material-icons">verified_user</i>
                            </div>
                            <div class="text">
                                <p class="h3">{#title_order#}</p>
                                <p class="help-block">
                                    {#txt_order#}
                                </p>
                            </div>
                            <a href="{$orderFirstStep}" title="{#continue_order#}"><span class="sr-only">{#continue_order#}</span><i class="material-icons">keyboard_arrow_right</i></a>
                        </div>
                    </div>
                {/if}
            </div>
        </div>
        <p class="cart-empty">{#empty_cart#}</p>
    </div>
{/block}

{block name="foot"}

    {capture name="formVendors"}/min/?g=form{/capture}
    <script src="{if $setting.concat.value}{$smarty.capture.formVendors|concat_url:'js'}{else}{$smarty.capture.formVendors}{/if}"></script>
    {capture name="globalForm"}/min/?f=skin/{$theme}/js/form.min.js{if {$lang} !== "en"},libjs/vendor/localization/messages_{$lang}.js{/if}{/capture}
    <script src="{if $setting.concat.value}{$smarty.capture.globalForm|concat_url:'js'}{else}{$smarty.capture.globalForm}{/if}" async defer></script>
{/block}