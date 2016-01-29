{extends file="layout.tpl"}
{block name="title"}{seo_rewrite config_param=['level'=>'0','idmetas'=>'1','default'=>#seo_t_static_plugin_cart#]}{/block}
{block name="description"}{seo_rewrite config_param=['level'=>'0','idmetas'=>'2','default'=>#seo_d_static_plugin_cart#]}{/block}
{block name='body:id'}cartpay{/block}

{block name='article'}
    <article id="article" class="col-xs-12 col-sm-12 col-md-12">
        {block name="article:content"}
            <h1>{#order_cart#|ucfirst}</h1>
            <h2>{#order_resume#|ucfirst}</h2>
            <div id="cart_container"></div>
            {include file="cartpay/brick/quantity.tpl"}
            {include file="cartpay/forms/order.tpl" data=$dataAccount config=$getDataConfig}
        {/block}
    </article>
{/block}

{block name="aside"}{/block}
{block name="foot" append}
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
            {if $getDataConfig.online_payment eq '1'}
            var idform = 'form-cart-send';
            {elseif $getDataConfig.online_payment eq '0'}
            var idform = 'form-cart-devis';
            {/if}
            if (typeof cartProduct == "undefined")
            {
                console.log("cartProduct is not defined");
            }else{
                cartProduct.runCart(id_cart,'cart_container','cart_table',idform,iso);
            }
        });
    </script>
{/block}