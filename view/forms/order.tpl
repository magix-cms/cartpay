<div id="form-cart-send-container">
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
    <form id="form-cart-send" action="{geturl}/{getlang}/cartpay/payment/" method="post" class="form">
        {include file="cartpay/forms/client-infos.tpl"}
        {include file="cartpay/forms/common.tpl"}
    </form>
</div>