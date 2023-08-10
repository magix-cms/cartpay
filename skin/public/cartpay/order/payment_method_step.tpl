{extends file="cartpay/step.tpl"}

{block name="step:formclass"} actions{/block}
{block name="step:name"}{#choose_payment_mehtod#}{/block}
{block name="step:content"}
    <div class="row row-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6">
            {foreach $available_payment_methods as $key => $pma}
                <div class="action quotation">
                    <input type="radio" name="payment_method" id="payment_{$key}" value="{$key}" class="not-nice"{if $pma@first} required{/if}/>
                    <div class="icon">
                        {if isset($pma.img)}
                            <img src="{$pma.img}" class="img-responsive" />
                        {else}
                            {$pma.icon}
                        {/if}
                    </div>
                    <div class="text">
                        <p class="h3">{$pma.name}</p>
                        <p class="help-block">
                            {$pma.desc}
                        </p>
                    </div>
                    <label for="payment_{$key}" title="{#choose_this_payment_mehtod#}">
                        <span class="sr-only">{#choose_this_payment_mehtod#}</span>
{*                        <span class="material-icons ico ico-keyboard_arrow_right"></span>*}
                    </label>
                </div>
            {/foreach}
        </div>
    </div>
{/block}