{extends file="cartpay/step.tpl"}

{block name="step:formclass"} cartpay-form{/block}
{block name="step:name"}{#billing_information#}{/block}
{block name="step:content"}
    <div class="row row-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6">
            <div class="form-group">
                <label for="address" class="is_empty">{#billing_address#}*&nbsp;:</label>
                <input id="address" type="text" name="billing[address]" placeholder="{#ph_billing_address#}" class="form-control required"{if $buyer.address} value="{$buyer.address}"{/if} required/>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="postcode" class="is_empty">{#billing_postcode#}*&nbsp;:</label>
                        <input id="postcode" type="text" name="billing[postcode]" placeholder="{#ph_billing_postcode#}" class="form-control required"{if $buyer.postcode} value="{$buyer.postcode}"{/if} required/>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="city" class="is_empty">{#billing_city#}*&nbsp;:</label>
                        <input id="city" type="text" name="billing[city]" placeholder="{#ph_billing_city#}" class="form-control required"{if $buyer.city} value="{$buyer.city}"{/if} required/>
                    </div>
                </div>
            </div>
            <div class="form-group">
                {*<input id="country" type="text" name="billing[country]" placeholder="{#ph_billing_country#}" class="form-control required"{if $buyer.country} value="{$buyer.country}"{/if} required/>
                <label for="country" class="is_empty">{#billing_country#}*&nbsp;:</label>*}
                <label for="country">{#pn_transport_country#|ucfirst}&nbsp;*:</label>
                {country_data}
                <select name="billing[country]" id="country" class="form-control required" required>
                    <option disabled selected>-- {#pn_transport_country#|ucfirst} --</option>
                    {*{foreach $countries as $key}
                        <option value="{$key.id}"{if isset($account.address.delivery) && !empty($account.address.delivery) && {#$account.address.delivery.country#} === $key.country} selected{/if}>{$key.country}</option>
                    {/foreach}*}
                    {foreach $countries as $country}
                        <option value="{$country.name}"{if isset($account.address.delivery) && !empty($account.address.delivery) && {#$account.address.delivery.country#} === $country.name} selected{/if}>{#$country.name#}</option>
                    {/foreach}
                </select>
            </div>
            <small class="text-center help-block">{#cartpay_fiels_resquest#}</small>
        </div>
    </div>
{/block}
{block name="step:submit:hidden"}
    <input type="hidden" name="billing[id_buyer]" value="{$buyer.id}">
{/block}