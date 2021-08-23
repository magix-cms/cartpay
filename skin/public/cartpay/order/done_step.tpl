{extends file="cartpay/step.tpl"}

{block name="step:formclass"}{/block}
{block name="step:name"}{$done.title}{/block}
{block name="step:form"}
    <div class="alert alert-{if $done.status === 'error'}danger{else}{$done.status}{/if}">
        <p class="text-center"><span class="material-icons ico ico-{$done.icon}"></span>&nbsp;{$done.msg}</p>
    </div>
    <p class="text-center">
        <a href="{$url}/{$lang}/" title=""><span class="material-icons ico ico-keyboard_backspace"></span>&nbsp;{#back_to_home#}</a>
    </p>
{/block}