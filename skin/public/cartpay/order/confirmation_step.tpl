{extends file="cartpay/step.tpl"}

{block name="step:formclass"}{/block}
{block name="step:name"}{#confirmation#}{/block}
{block name="step:content"}
    <div class="row">
        <div class="col-2 col-xs-3 col-sm-4 col-md-5 col-lg-9 order-resume">
            <h2>{#resume#}</h2>
            <div class="row">
                <div class="col-4 col-xs-6 col-sm-8 col-md-10 col-lg-4">
                    <h3>{#personal_informations#}</h3>
                    <p>{$buyer.firstname}&nbsp;{$buyer.firstname}</p>
                    <p>{$buyer.email}</p>
                    {if $buyer.phone}<p>{$buyer.phone}</p>{/if}
                    {if $buyer.company}<p>{$buyer.company}</p>{/if}
                    {if $buyer.vat}<p>{$buyer.vat}</p>{/if}
                </div>
                <div class="col-4 col-xs-6 col-sm-8 col-md-10 col-lg-4">
                    <h3>{#billing_information#}</h3>
                    <p>{$buyer.address}</p>
                    <p>{$buyer.postcode}&nbsp;{$buyer.city}</p>
                    <p>{$buyer.country}</p>
                </div>
                <div class="col-4 col-xs-6 col-sm-8 col-md-10 col-lg-4">
                    <h3>{#payment_mehtod#}</h3>
                    <p>{$pma[$order.payment_order]['name']}</p>
                </div>
                {if is_array($additionnalResume) && !empty($additionnalResume)}
                {foreach $additionnalResume as $resume}
                    <div class="col-4 col-xs-6 col-sm-8 col-md-10 col-lg-4">
                        <h3>{$resume.name}</h3>
                        {$resume.desc}
                    </div>
                {/foreach}
                {/if}
            </div>
        </div>
        <div class="col-2 col-xs-3 col-sm-4 col-md-5 col-lg-3 mini-cart">
            <header>
                <h2>{#my_cart#}</h2><div><i class="material-icons cart-icon ico ico-shopping_cart"></i><span class="badge cart-total-items">{$cart.nb_items}</span></div>
            </header>
            <ul class="shopping-cart-items">
                {include file="cartpay/loop/float-cart-item.tpl" data=$cart.items}
            </ul>
            <ul class="shopping-cart-fees">
                {include file="cartpay/loop/float-cart-fee.tpl" data=$cart.fees}
            </ul>
            <div class="shopping-cart-total">
                <span class="lighter-text">{#total#}&thinsp;:</span>&nbsp;<span class="main-color-text"><span class="total_cart">{if $setting.price_display.value === 'tinc'}{$cart.total.inc}{else}{$cart.total.exc}{/if}</span>&nbsp;<span class=currency">€</span></span>
            </div>
        </div>
    </div>
{/block}
{block name="step:submit"}
    <div class="cart-submit">
        <input id="amount" type="hidden" name="purchase[amount]" class="form-control required" value="{$cart.total.inc|number_format:2:'.':'&thinsp;'}" />
        <input type="hidden" name="custom[order]" value=""/>
        <input id="amount" type="hidden" name="custom[email]" class="form-control required" value="{$buyer.email}" />
        {*<input id="shipping" type="hidden" name="custom[shipping]" class="form-control required" value="" />*}
        <button type="submit" class="btn btn-box btn-main"><span class="material-icons ico ico-check"></span>&nbsp;{#go_to_payment#|ucfirst}</button>
    </div>
{/block}