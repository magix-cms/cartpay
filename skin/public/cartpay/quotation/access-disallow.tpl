{extends file="layout.tpl"}
{block name="title"}{seo_rewrite conf=['level'=>'root','type'=>'title','default'=>{#seo_cartpay_title#}]}{/block}
{block name="description"}{seo_rewrite conf=['level'=>'root','type'=>'description','default'=>{#seo_cartpay_desc#}]}{/block}
{block name='body:id'}quotation-access-disallow{/block}

{block name="article:content"}
    <h1 class="text-center">{#quotation#}</h1>
    <div class="alert alert-danger">
        <p class="text-center"><span class="material-icons">warning</span>&nbsp;{#quotation_access_disallow#}</p>
    </div>
    <p class="text-center">
        <a href="{$url}/{$lang}/cartpay/" title=""><span class="material-icons">keyboard_backspace</span>&nbsp;{#back_to_cart#}</a>
    </p>
{/block}

{block name="foot"}
    {capture name="formVendors"}/min/?g=form{/capture}
    <script src="{if $setting.concat.value}{$smarty.capture.formVendors|concat_url:'js'}{else}{$smarty.capture.formVendors}{/if}"></script>
    {capture name="globalForm"}/min/?f=skin/{$theme}/js/form.min.js{if {$lang} !== "en"},libjs/vendor/localization/messages_{$lang}.js{/if}{/capture}
    <script src="{if $setting.concat.value}{$smarty.capture.globalForm|concat_url:'js'}{else}{$smarty.capture.globalForm}{/if}" async defer></script>
{/block}