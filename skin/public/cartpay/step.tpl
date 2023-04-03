{extends file="layout.tpl"}
{block name="title"}{seo_rewrite conf=['level'=>'root','type'=>'title','default'=>{#seo_cartpay_title#}]}{/block}
{block name="description"}{seo_rewrite conf=['level'=>'root','type'=>'description','default'=>{#seo_cartpay_desc#}]}{/block}
{block name='body:id'}{$type}{/block}
{block name="styleSheet"}
    {*{$css_files = [
    "/skin/{$theme}/css/form{if $setting.mode.value !== 'dev'}.min{/if}.css"
    ]}*}
    {$css_files = ["form"]}
{/block}
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

{block name="scripts"}
    {$jquery = true}
    {$js_files = [
    'group' => [
    'form'
    ],
    'normal' => [
    ],
    'defer' => [
    "/skin/{$theme}/js/{if $setting.mode === 'dev'}src/{/if}form{if $setting.mode !== 'dev'}.min{/if}.js",
    "/skin/{$theme}/js/vendor/localization/messages_{$lang}.js"
    ]
    ]}
    {if {$lang} !== "en"}{$js_files['defer'][] = "/libjs/vendor/localization/messages_{$lang}.js"}{/if}
{/block}