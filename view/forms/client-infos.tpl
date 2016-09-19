<div class="alert alert-info">
    {#form_legend#}
</div>
<div id="form-cart">
    <fieldset>
        <legend class="h3">{#coordonnees_cart#|ucfirst}</legend>
        <div class="row">
            <div class="form-group col-sm-6">
                <label class="control-label" for="lastname_cart">{#pn_cartpay_lastname#|ucfirst} * :</label>
                <input class="form-control" type="text" id="lastname_cart" name="lastname_cart" value=""  />
            </div>
            <div class="form-group col-sm-6">
                <label class="control-label" for="firstname_cart">{#pn_cartpay_firstname#|ucfirst} * :</label>
                <input class="form-control" type="text" id="firstname_cart" name="firstname_cart" value="" />
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-6">
                <label class="control-label" for="email_cart">{#pn_cartpay_mail#|ucfirst} * :</label>
                <input class="form-control" type="text" id="email_cart" name="email_cart" value="" size="20" />
            </div>
            <div class="form-group col-sm-6">
                <label class="control-label" for="phone_cart">{#pn_cartpay_phone#|ucfirst} :</label>
                <input class="form-control" type="text" id="phone_cart" name="phone_cart" value="" size="20" />
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-6">
                <label class="control-label" for="street_cart">{#pn_cartpay_street#|ucfirst} * :</label>
                <input class="form-control" type="text" id="street_cart" name="street_cart" value="" size="52" />
            </div>
            <div class="form-group col-sm-6">
                <label class="control-label" for="postal_cart">{#pn_cartpay_postal#|ucfirst} * :</label>
                <input class="form-control" type="text" id="postal_cart" name="postal_cart"  value="" size="20" />
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-6">
                <label class="control-label" for="city_cart">{#pn_cartpay_locality#|ucfirst} * :</label>
                <input class="form-control" type="text" id="city_cart" name="city_cart"  value="" size="20" />
            </div>
            <div class="form-group col-sm-6">
                <label class="control-label" for="country_cart">{#pn_cartpay_country#|ucfirst} * :</label>
                <select class="form-control" id="country_cart" name="country_cart">
                    <option value="">{$smarty.config.select_a_x|sprintf:$smarty.config.country|ucfirst}</option>
                    {foreach $getItemsCountryData as $key nocache}
                        <option value="{$key.country}">{#$key.iso#|ucfirst}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend class="h3">{#more_information#}</legend>
        <div class="row">
            <div class="form-group col-sm-6">
                <label class="control-label" for="company_cart">{#pn_cartpay_society#|ucfirst} :</label>
                <input class="form-control" type="text" id="company_cart" name="company_cart"  value="" size="30" />
            </div>
            <div class="form-group col-sm-6">
                <label class="control-label" for="vat_cart">{#pn_cartpay_tva#|ucfirst} :</label>
                <input class="form-control" type="text" id="vat_cart" name="vat_cart"  value="" size="30" />
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <label class="name block" for="message_cart">{#pn_cartpay_more_explain#|ucfirst} :</label>
                <textarea id="message_cart" name="message_cart" class="form-control" rows="6" cols="36"></textarea>
            </div>
        </div>
    </fieldset>
</div>