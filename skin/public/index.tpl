{extends file="layout.tpl"}
{block name="title"}{seo_rewrite conf=['level'=>'root','type'=>'title','default'=>#seo_cartpay_title#]}{/block}
{block name="description"}{seo_rewrite conf=['level'=>'root','type'=>'description','default'=>#seo_cartpay_desc#]}{/block}
{block name='body:id'}private{/block}

{block name="article:content"}
    <header>
        <h1 class="text-center">{#my_cart#|ucfirst}</h1>
    </header>
    {*<pre>
        {$config_cart|print_r}
    {$product_cart|print_r}
    {$session_cart|print_r}
    {$account|print_r}
    </pre>*}
    {if $config_cart.type_order eq "quotation"}
    <table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>
                {#product#|ucfirst}
            </th>
            <th>
                {#quantity#|ucfirst}
            </th>
        </tr>
    </thead>
        <tbody>
        {foreach $product_cart as $key}
        <tr>
            <td>
                <a href="{$key.url}">{$key.name_p}</a>
            </td>
            <td>
                {$key.quantity}
            </td>
        </tr>
        {/foreach}
        </tbody>
    </table>
        {if $account}
        <ul class="list-unstyled">
            <li>
                {#lastname_ac#|ucfirst} : {$account.lastname_ac}
            </li>
            <li>
                {#firstname_ac#|ucfirst} : {$account.firstname_ac}
            </li>
            <li>
                {#email_ac#|ucfirst} : {$account.email_ac}
            </li>
        </ul>
            {if $product_cart != null}
                <form id="cartpay-form" class="validate_form cartpay_refresh" method="post" action="{geturl}/{getlang}/cartpay/?action=send">
                    {*<div class="row">
                        <fieldset class="col-ph-12 col-md-6">
                            <legend>{#particulars#|ucfirst}</legend>
                            <div class="row">
                                <div class="col-ph-12 col-sm-6">
                                    <div class="form-group">
                                        <label for="firstname_ac">{#firstname_ac#|ucfirst} :</label>
                                        <input id="firstname_ac" type="text" name="cart[firstname_ac]" value="{$account.firstname_ac}" placeholder="{#ph_firstname#|ucfirst}" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-ph-12 col-sm-6">
                                    <div class="form-group">
                                        <label for="lastname_ac">{#lastname_ac#|ucfirst} :</label>
                                        <input id="lastname_ac" type="text" name="cart[lastname_ac]" value="{$account.lastname_ac}" placeholder="{#ph_lastname#|ucfirst}" class="form-control"  />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email_ac">{#email_ac#|ucfirst}&nbsp;*</label>
                                <input id="email_ac" type="email" name="cart[email_ac]" value="{$account.email_ac}" placeholder="{#ph_email#}" class="form-control required" required/>
                            </div>
                        </fieldset>
                    </div>*}
                    <button type="submit" class="btn btn-box btn-invert btn-main-theme">{#pn_cartpay_send#|ucfirst}</button>
                </form>
            {/if}
            {else}
            {#connect_to_quotation#} {#connection#}
        {/if}
    {/if}
{/block}

{block name="foot"}

    {script src="/min/?g=form" concat=$concat type="javascript"}
    {script src="/min/?f=skin/{template}/js/form.min.js" concat=$concat type="javascript"}
    {if {getlang} !== "en"}
        {script src="/min/?f=libjs/vendor/localization/messages_{getlang}.js" concat=$concat type="javascript"}
    {/if}
    <script type="text/javascript">
        $(function(){
            if (typeof globalForm == "undefined")
            {
                console.log("globalForm is not defined");
            }else{
                globalForm.run();
            }
        });
    </script>
{/block}