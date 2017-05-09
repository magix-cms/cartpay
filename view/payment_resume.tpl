{extends file="layout.tpl"}
{block name="title"}{seo_rewrite config_param=['level'=>'0','idmetas'=>'1','default'=>#seo_t_static_plugin_cart#]}{/block}
{block name="description"}{seo_rewrite config_param=['level'=>'0','idmetas'=>'2','default'=>#seo_d_static_plugin_cart#]}{/block}
{block name='body:id'}contact{/block}

{block name="article:content"}
    <h1>{#order_resume#|firststring}</h1>
    <div id="resume_cart">
        <div id="cart_container">
            <h2>{#recap_cart#|ucfirst}</h2>
            {*{$table_products}*}
            {include file="cartpay/loop/cart.tpl"}
        </div>
        <div class="col-sm-12">
            <p>
                <strong>{#pn_cartpay_message#|firststring}: </strong><br />
                {$message_cart}
            </p>
        </div>
        <h2>{#coordonnees_cart#|ucfirst}</h2>
        {include file="cartpay/brick/resume.tpl"}
        <div class="clearfix">
            {if $getDataConfig.online_payment eq '1'}
                {if $getDataConfig.hipay eq '1'}
                <div class="col-xs-6">
                {$hipayProcess}
                </div>
                {/if}
                {if $getDataConfig.ogone eq '1'}
                <div class="col-xs-6">
                    {$ogoneProcess}
                </div>
                {/if}
                {if $getDataConfig.atos eq '1'}
                    <div class="col-xs-6">
                        {$atosProcess}
                    </div>
                {/if}
            {else}
                <div class="col-xs-6">
                    <form action="" method="post" id="form-cart-devis">
                        <p class="col valid">
                            <input type="hidden" id="id_cart_to_send" name="id_cart_to_send" value="{$id_cart}" />
                            <input type="submit" id="sendDevis" class="btn btn-danger btn-lg" value="{#pn_cartpay_send_devis#|ucfirst}" />
                        </p>
                    </form>
                </div>
            {/if}
        </div>
        {if $getDataConfig.bank_wire eq '1'}
        <div  class="clearfix" id="bank-wire">
            <div class="col-xs-6">
                <h3>
                    {#bank_wire#}
                </h3>
            <table class="table table-condensed">
                <caption class="text-center">
                    {#account_owner#} : {$getDataConfig.account_owner}
                </caption>
                <tr>
                    <td>
                        <p><strong>{#contact_details#}</strong> :<br /> {$getDataConfig.contact_details|nl2br}</p>
                    </td>
                    <td>
                        <p><strong>{#bank_address#}</strong> :<br /> {$getDataConfig.bank_address|nl2br}</p>
                    </td>
                </tr>
            </table>
            </div>
        </div>
        {/if}
    </div>
    <div class="mc-message"></div>
{/block}
{block name="foot" append}
    {script src="/min/?g=form" concat=$concat type="javascript"}
    {capture name="formjs"}{strip}
        /min/?f=skin/{template}/js/form.min.js,
        libjs/vendor/redirect.js
    {/strip}{/capture}
    {script src=$smarty.capture.formjs concat=$concat type="javascript" load='async'}
    {capture name="scriptProduct"}{strip}
        /min/?f=
        libjs/vendor/localization/messages_{getlang}.js,
        plugins/cartpay/js/public.js
    {/strip}{/capture}
    {script src=$smarty.capture.scriptProduct concat=$concat type="javascript"}
    <script type="text/javascript">
        $.nicenotify.notifier = {
            box:"",
            elemclass : '.mc-message'
        };
        var iso = '{getlang}';
        $(function(){
            var id_cart = {$id_cart};
            if (typeof cartProduct == "undefined")
            {
                console.log("cartProduct is not defined");
            }else{
                cartProduct.runSend(id_cart,'cart_container','cart_table','form-cart-devis',iso);
            }
        });
    </script>
{/block}