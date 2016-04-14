<div id="form-cart-send-container">
{if !($config.profil eq '1' && $profilExist)}
{*    {if $smarty.session.idprofil && $smarty.session.keyuniqid_pr}
    <div class="alert alert-info">
        {capture name="loginRedirect"}<a class="btn btn-box btn-flat btn-main-theme" href="{$hashurl}" title="{#pn_upgrade_profil#}">{#pn_upgrade_profil#}</a>{/capture}
        {$smarty.config.form_legend_pr|sprintf:$smarty.capture.loginRedirect}
    </div>
    {/if}
{else}*}
    <div class="alert alert-info">
    {#form_legend#}
    </div>
{/if}
   {* {if $config.online_payment eq '1'}
        {capture name="formCart"}
            <form id="form-cart-send" action="{geturl}/{getlang}/cartpay/payment/" method="post" class="form">
        {/capture}
    {elseif $config.online_payment eq '0'}
        {capture name="formCart"}
            <form id="form-cart-devis" action="" method="post" class="form">
                <div class="mc-message"></div>
        {/capture}
    {/if}
    {$smarty.capture.formCart}*}
    {if $config.profil eq '1' && $profilExist}
        {if $smarty.session.idprofil && $smarty.session.keyuniqid_pr}
            {$profilExist}
        {else}
            {capture name="loginAlert"}<a class="btn btn-box btn-flat btn-main-theme" href="{geturl}/{getlang}/profil/login_redirect" title="{#connect_profil_label#|ucfirst}">{#login_alert#}</a>{/capture}
            <div class="alert alert-warning">
                {$smarty.config.login_alert_cart|sprintf:$smarty.capture.loginAlert}
            </div>
        {/if}
    {else}
        {include file="cartpay/forms/default-forms.tpl"}
    {/if}
</div>