<div id="form-cart-send-container">
    <h2>{#coordonnees_cart#|ucfirst}</h2>
    <div class="alert alert-info">
        {#form_legend#}
    </div>
    {if $config.online_payment eq '1'}
        {capture name="formCart"}
            <form id="form-cart-send" action="{geturl}/{getlang}/cartpay/payment/" method="post" class="form">
        {/capture}
    {elseif $config.online_payment eq '0'}
        {capture name="formCart"}
            <form id="form-cart-devis" action="" method="post" class="form">
                <div class="mc-message"></div>
        {/capture}
    {/if}
    {$smarty.capture.formCart}
        <div id="form-cart">
            <div class="row">
                <div class="col-sm-6">
                    <label class="control-label" for="lastname_cart">{#pn_cartpay_lastname#|ucfirst} * :</label>
                    <input class="form-control" type="text" id="lastname_cart" name="lastname_cart" value=""  />
                </div>
                <div class="col-sm-6">
                    <label class="control-label" for="firstname_cart">{#pn_cartpay_firstname#|ucfirst} * :</label>
                    <input class="form-control" type="text" id="firstname_cart" name="firstname_cart" value="" />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <label class="control-label" for="email_cart">{#pn_cartpay_mail#|ucfirst} * :</label>
                    <input class="form-control" type="text" id="email_cart" name="email_cart" value="" size="20" />
                </div>
                <div class="col-sm-6">
                    <label class="control-label" for="phone_cart">{#pn_cartpay_phone#|ucfirst} :</label>
                    <input class="form-control" type="text" id="phone_cart" name="phone_cart" value="" size="20" />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <label class="control-label" for="street_cart">{#pn_cartpay_street#|ucfirst} * :</label>
                    <input class="form-control" type="text" id="street_cart" name="street_cart" value="" size="52" />
                </div>
                <div class="col-sm-6">
                    <label class="control-label" for="postal_cart">{#pn_cartpay_postal#|ucfirst} * :</label>
                    <input class="form-control" type="text" id="postal_cart" name="postal_cart"  value="" size="20" />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <label class="control-label" for="city_cart">{#pn_cartpay_locality#|ucfirst} * :</label>
                    <input class="form-control" type="text" id="city_cart" name="city_cart"  value="" size="20" />
                </div>
                <div class="col-sm-6">
                    <label class="control-label" for="country_cart">{#pn_cartpay_country#|ucfirst} * :</label>
                    <select class="form-control" id="country_cart" name="country_cart">
                        <option value="">{$smarty.config.select_a_x|sprintf:$smarty.config.country|ucfirst}</option>
                        {foreach $getItemsTvaData as $key nocache}
                            <option value="{$key.country}">{#$key.iso#|ucfirst}</option>
                        {/foreach}
                    </select>
                </div>

            </div>
            <div class="row">
                <div class="col-sm-6">
                    <label class="control-label" for="society_cart">{#pn_cartpay_society#|ucfirst} :</label>
                    <input class="form-control" type="text" id="society_cart" name="society_cart"  value="" size="30" />
                </div>
                <div class="col-sm-6">
                    <label class="control-label" for="tva_cart">{#pn_cartpay_tva#|ucfirst} :</label>
                    <input class="form-control" type="text" id="tva_cart" name="tva_cart"  value="" size="30" />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <label class="name block" for="message_cart">{#pn_cartpay_more_explain#|ucfirst} :</label>
                    <textarea id="message_cart" name="message_cart" class="form-control" rows="6" cols="36"></textarea>
                </div>
            </div>
            <div class="row adressliv">
                <div class="col-sm-12">
                    <label class="name block" for="adressliv">
                        <input id="group" type="checkbox" name="adressliv" value="Oui">
                        {#pn_cartpay_adressliv#|ucfirst}
                    </label>
                </div>
            </div>
        </div>
        <div id="delivery">
            <h2>{#coordonnees_liv#|ucfirst}</h2>
            <div class="row">
                <div class="col-sm-6">
                    <label class="control-label" for="street_liv_cart">{#pn_cartpay_street_liv#|ucfirst}:</label>
                    <input class="form-control" type="text" id="street_liv_cart" name="street_liv_cart" value="{$street_liv_cart}" size="30" />
                </div>
                <div class="col-sm-6">
                    <label class="control-label" for="city_liv_cart">{#pn_cartpay_localite_liv#|ucfirst} :</label>
                    <input class="form-control" type="text" id="city_liv_cart" name="city_liv_cart" value="{$city_liv_cart}" size="30" />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <label class="control-label" for="postal_liv_cart">{#pn_cartpay_postal_liv#|ucfirst} :</label>
                    <input class="form-control" type="text" id="postal_liv_cart" name="postal_liv_cart" value="{$postal_liv_cart}" size="30" />
                </div>
                <div class="col-sm-6">
                    <label class="control-label" for="country_liv_cart">{#pn_cartpay_pays_liv#|ucfirst} :</label>
                    <input class="form-control" type="text" id="country_liv_cart" name="country_liv_cart" value="{$country_liv_cart}" size="30" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <p>
                        {capture name="terms_and_conditions_link"}
                            <a href="{#terms_and_conditions_url#}">{#terms_and_conditions_name#}</a>
                        {/capture}
                        <input type="checkbox" id="confidentiality_control" name="confidentiality_control" /> <span>{$smarty.config.terms_and_conditions|sprintf:$smarty.capture.terms_and_conditions_link}</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="cart-valid">
            {if $config.online_payment eq '1'}
                {capture name="btnCart"}sendCart{/capture}
            {elseif $config.online_payment eq '0'}
                {capture name="btnCart"}sendDevis{/capture}
            {/if}
            <input type="hidden" id="id_cart_to_send" name="id_cart_to_send" value="{$id_cart}" />
            <input type="hidden" name="amount_to_pay" id="amount_to_pay_hidden" value="{$amount_order}" />
            <input type="submit" id="{$smarty.capture.btnCart}" class="btn btn-primary" value="{#pn_cartpay_send_cart#|ucfirst}" />
        </div>
    </form>
</div>