{extends file="layout.tpl"}
{block name="title"}{seo_rewrite conf=['level'=>'root','type'=>'title','default'=>{#seo_cartpay_title#}]}{/block}
{block name="description"}{seo_rewrite conf=['level'=>'root','type'=>'description','default'=>{#seo_cartpay_desc#}]}{/block}
{block name='body:id'}{$type}{/block}

{block name="article:content"}
    <h1 class="text-center">{$type_title}</h1>
    {include file="cartpay/steps.tpl"}
    {block name="step:h2"}<h2 class="text-center">{block name="step:name"}{/block}</h2>{/block}
    {block name="step:form"}
    <form id="order-form" class="validate_form classic_form{block name="step:formclass"}{/block}" method="post" action="{$next_step_url}">
        {block name="step:content"}{/block}
        <div class="mc-message"></div>
        {block name="step:submit"}
        <div class="cart-next-step">
            <button type="submit" class="btn btn-main-invert">{#order_next_step#}&nbsp;<span class="material-icons ico ico-keyboard_arrow_right"></span></button>
            {block name="step:submit:hidden"}{/block}
        </div>
        {/block}
    </form>
    {/block}
{/block}

{block name="foot"}
    {capture name="formVendors"}/min/?g=form{/capture}
    <script src="{if $setting.concat.value}{$smarty.capture.formVendors|concat_url:'js'}{else}{$smarty.capture.formVendors}{/if}"></script>
    {capture name="globalForm"}/min/?f=skin/{$theme}/js/form.min.js{if {$lang} !== "en"},libjs/vendor/localization/messages_{$lang}.js{/if}{/capture}
    <script src="{if $setting.concat.value}{$smarty.capture.globalForm|concat_url:'js'}{else}{$smarty.capture.globalForm}{/if}" async defer></script>
{/block}