{extends file="layout.tpl"}
{block name="title"}{seo_rewrite conf=['level'=>'root','type'=>'title','default'=>{#seo_cartpay_title#}]}{/block}
{block name="description"}{seo_rewrite conf=['level'=>'root','type'=>'description','default'=>{#seo_cartpay_desc#}]}{/block}
{block name='body:id'}cart-resume{/block}

{block name="article:content"}
    {*<pre>
        *}{*{$config_cart|print_r}*}{*
    {$product_cart|print_r}
    *}{*{$session_cart|print_r}
    {$account|print_r}*}{*
    </pre>*}
    {if $config_cart.type_order eq "quotation"}
        <header>
            <h1 class="text-center">{#cart_quotation_h1#|ucfirst}</h1>
        </header>
    <table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>
                {#product#|ucfirst}
            </th>
            <th>
                {#quantity#|ucfirst}
            </th>
            <th>
                {#price#|ucfirst}
            </th>
        </tr>
    </thead>
        <tbody>
        {foreach $product_cart as $key}
        <tr>
            <td>
                <a href="{$key.url}">{$key.name_p}</a>
            </td>
            <td>
                {$key.quantity}
            </td>
            <td>
                {$key.price_p|round:2|number_format:2:',':' '|decimal_trim:','} €
            </td>
        </tr>
        {/foreach}
        </tbody>
    </table>
        {if $account}
        <ul class="list-unstyled">
            <li>
                {#lastname_ac#|ucfirst} : {$account.lastname_ac}
            </li>
            <li>
                {#firstname_ac#|ucfirst} : {$account.firstname_ac}
            </li>
            <li>
                {#email_ac#|ucfirst} : {$account.email_ac}
            </li>
        </ul>
            {if $product_cart != null}
                <form id="cartpay-form" class="validate_form cartpay_refresh" method="post" action="{$url}/{$lang}/cartpay/?action=send">
                    {*<div class="row">
                        <fieldset class="col-ph-12 col-md-6">
                            <legend>{#particulars#|ucfirst}</legend>
                            <div class="row">
                                <div class="col-ph-12 col-sm-6">
                                    <div class="form-group">
                                        <label for="firstname_ac">{#firstname_ac#|ucfirst} :</label>
                                        <input id="firstname_ac" type="text" name="cart[firstname_ac]" value="{$account.firstname_ac}" placeholder="{#ph_firstname#|ucfirst}" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-ph-12 col-sm-6">
                                    <div class="form-group">
                                        <label for="lastname_ac">{#lastname_ac#|ucfirst} :</label>
                                        <input id="lastname_ac" type="text" name="cart[lastname_ac]" value="{$account.lastname_ac}" placeholder="{#ph_lastname#|ucfirst}" class="form-control"  />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email_ac">{#email_ac#|ucfirst}&nbsp;*</label>
                                <input id="email_ac" type="email" name="cart[email_ac]" value="{$account.email_ac}" placeholder="{#ph_email#}" class="form-control required" required/>
                            </div>
                        </fieldset>
                    </div>*}
                    <input type="hidden" name="cart_type" value="quotation">
                    <button type="submit" class="btn btn-box btn-invert btn-main-theme">{#pn_cartpay_send#|ucfirst}</button>
                </form>
            {/if}
            {else}
            {* TODO Ajouter ici buyer quand pas de profil*}
            {*{#connect_to_quotation#} {#connection#}*}
            <form id="cartpay-form" class="validate_form cartpay_refresh" method="post" action="{$url}/{$lang}/cartpay/?action=send">
                <div class="row">
                    <fieldset class="col-ph-12 col-md-6">
                        <legend>{#personal_informations#|ucfirst}</legend>
                        <div class="row">
                            <div class="col-ph-12 col-sm-6">
                                <div class="form-group">
                                    <label for="cart[firstname]">{#firstname#|ucfirst} :</label>
                                    <input id="cart[firstname]" type="text" name="cart[firstname]" value="" placeholder="{#ph_firstname#|ucfirst}" class="form-control" />
                                </div>
                            </div>
                            <div class="col-ph-12 col-sm-6">
                                <div class="form-group">
                                    <label for="cart[lastname]">{#lastname#|ucfirst} :</label>
                                    <input id="cart[lastname]" type="text" name="cart[lastname]" value="" placeholder="{#ph_lastname#|ucfirst}" class="form-control"  />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cart[email]">{#email#|ucfirst}&nbsp;*</label>
                            <input id="cart[email]" type="email" name="cart[email]" value="" placeholder="{#ph_email#|ucfirst}" class="form-control required" required/>
                        </div>
                        <div class="row">
                            <div class="col-ph-12 col-sm-6">
                                <div class="form-group">
                                    <label for="cart[company]">{#company#|ucfirst} :</label>
                                    <input id="cart[company]" type="text" name="cart[company]" value="" placeholder="{#ph_company#|ucfirst}" class="form-control" />
                                </div>
                            </div>
                            <div class="col-ph-12 col-sm-6">
                                <div class="form-group">
                                    <label for="cart[vat]">{#vat#|ucfirst} :</label>
                                    <input id="cart[vat]" type="text" name="cart[vat]" value="" placeholder="{#ph_vat#|ucfirst}" class="form-control"  />
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <input type="hidden" name="cart_type" value="quotation">
                <button type="submit" class="btn btn-box btn-invert btn-main-theme">{#cart_quotation_btn_send#|ucfirst}</button>
            </form>
        {/if}
    {/if}

    <h1>{#my_cart#}</h1>
    <div id="shopping-cart">
        <ul class="shopping-cart-items">
            {include file="cartpay/loop/cart-item.tpl" data=$cart.items}
        </ul>
        <div class="tot row">
            <div class="col-6 text-right">{#total_products#}</div>
            <div class="col-6 tot_products">{if $setting.price_display.value === 'tinc'}{$cart.total.inc|string_format:"%.2f"}{else}{$cart.total.exc|string_format:"%.2f"}{/if}</div>
        </div>
        <div class="tot row">
            <div class="col-6 text-right">{#total_exc#}</div>
            <div class="col-6 tot_exc">{$cart.total.exc|string_format:"%.2f"}</div>
        </div>
        <div class="tot row">
            <div class="col-6 text-right">{#total_vat#}</div>
            <div class="col-6 tot_vat">{$cart.total.vat|string_format:"%.2f"}</div>
        </div>
        <div class="tot row">
            <div class="col-6 text-right">{#total_inc#}</div>
            <div class="col-6 tot_inc">{if $setting.price_display.value === 'tinc'}{$cart.total.inc|string_format:"%.2f"}{else}{$cart.total.exc|string_format:"%.2f"}{/if}</div>
        </div>
        <div class="actions"></div>
        <p class="cart-empty">{#empty_cart#}</p>
    </div>
{/block}

{block name="foot"}

    {capture name="formVendors"}/min/?g=form{/capture}
    <script src="{if $setting.concat.value}{$smarty.capture.formVendors|concat_url:'js'}{else}{$smarty.capture.formVendors}{/if}"></script>
    {capture name="globalForm"}/min/?f=skin/{$theme}/js/form.min.js{if {$lang} !== "en"},libjs/vendor/localization/messages_{$lang}.js{/if}{/capture}
    <script src="{if $setting.concat.value}{$smarty.capture.globalForm|concat_url:'js'}{else}{$smarty.capture.globalForm}{/if}" async defer></script>
{/block}