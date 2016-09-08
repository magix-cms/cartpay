<div id="delivery_info">
    <fieldset>
        <legend class="h3">{#coordonnees_liv#|ucfirst}</legend>
        <div class="adressliv form-group">
            <label class="name block" for="adressliv">
                <input id="adressliv" type="checkbox" name="adressliv" value="Oui">
                {#pn_cartpay_adressliv#|ucfirst}
            </label>
        </div>
        <div id="delivery">
            <div class="row form-group">
                <div class="col-sm-6">
                    <label class="control-label" for="lastname_liv_cart">{#pn_cartpay_lastname_liv#|ucfirst}*&nbsp;:</label>
                    <input class="form-control" type="text" id="lastname_liv_cart" name="lastname_liv_cart" value="{$lastname_liv_cart}" size="30" />
                </div>
                <div class="col-sm-6">
                    <label class="control-label" for="firstname_liv_cart">{#pn_cartpay_firstname_liv#|ucfirst}*&nbsp;:</label>
                    <input class="form-control" type="text" id="firstname_liv_cart" name="firstname_liv_cart" value="{$firstname_liv_cart}" size="30" />
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-6">
                    <label class="control-label" for="street_liv_cart">{#pn_cartpay_street_liv#|ucfirst}*&nbsp;:</label>
                    <input class="form-control" type="text" id="street_liv_cart" name="street_liv_cart" value="{$street_liv_cart}" size="30" />
                </div>
                <div class="col-sm-6">
                    <label class="control-label" for="city_liv_cart">{#pn_cartpay_localite_liv#|ucfirst}*&nbsp;:</label>
                    <input class="form-control" type="text" id="city_liv_cart" name="city_liv_cart" value="{$city_liv_cart}" size="30" />
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-6">
                    <label class="control-label" for="postal_liv_cart">{#pn_cartpay_postal_liv#|ucfirst}*&nbsp;:</label>
                    <input class="form-control" type="text" id="postal_liv_cart" name="postal_liv_cart" value="{$postal_liv_cart}" size="30" />
                </div>
                <div class="col-sm-6">
                    <label class="control-label" for="country_liv_cart">{#pn_cartpay_pays_liv#|ucfirst}*&nbsp;:</label>
                    <input class="form-control" type="text" id="country_liv_cart" name="country_liv_cart" value="{$country_liv_cart}" size="30" />
                </div>
            </div>
        </div>
    </fieldset>
</div>

<fieldset>
    <legend class="h3">Confirmation</legend>
    <div class="form-group">
        <label for="confidentiality_control">
            {strip}
                {capture name="terms_and_conditions_link"}
                    {if {#terms_and_conditions_url#} != '#'}
                        <a href="{geturl}/{getlang}/pages/{#terms_and_conditions_url#}">{#terms_and_conditions_name#}</a>
                    {else}
                        {#terms_and_conditions_name#}
                    {/if}
                {/capture}
            {/strip}
            <input type="checkbox" id="confidentiality_control" name="confidentiality_control" /> <span>{$smarty.config.terms_and_conditions|sprintf:$smarty.capture.terms_and_conditions_link}</span>
        </label>
    </div>
    <div class="cart-valid">
        {*{if $config.online_payment eq '1'}
            {capture name="btnCart"}sendCart{/capture}
        {elseif $config.online_payment eq '0'}
            {capture name="btnCart"}sendDevis{/capture}
        {/if}*}
        <input type="hidden" id="id_cart_to_send" name="id_cart_to_send" value="{$id_cart}" />
        <input type="hidden" name="amount_to_pay" id="amount_to_pay_hidden" value="{$amount_order}" />
        <input type="submit" id="sendCart" class="btn btn-box btn-flat btn-dark-theme" value="{#pn_cartpay_send_cart#|ucfirst}" />
    </div>
</fieldset>