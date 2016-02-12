<form id="cartpay_config" method="post" action="" class="form-horizontal">
    <fieldset>
        <div class="form-group">
            <label for="mail_sale" class="col-sm-3 control-label">{#mail_order#|ucfirst} <span class="fa fa-envelope"></span></label>
            <div class="col-sm-4">
                <input type="email" class="form-control" id="mail_order" name="mail_order" value="{$data.mail_order}" placeholder="{#mail_order_ph#|ucfirst}">
            </div>
        </div>
        <div class="form-group">
            <label for="mail_sale" class="col-sm-3 control-label">{#mail_order_from#|ucfirst} <span class="fa fa-envelope"></span></label>
            <div class="col-sm-4">
                <input type="email" class="form-control" id="mail_order_from" name="mail_order_from" value="{$data.mail_order_from}" placeholder="{#mail_order_from_ph#|ucfirst}">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-6">
                <label for="online_payment" class="col-sm-6 control-label">Paiement en ligne&nbsp;</label>
                <div class="col-sm-4">
                    <div class="checkbox">
                        <label>
                            <input{if $data.online_payment eq '1'} checked{/if} id="online_payment" name="online_payment" data-toggle="toggle" type="checkbox" data-on="oui" data-off="non" data-onstyle="primary" data-offstyle="default">
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <label for="shipping" class="col-sm-6 control-label">Livraison&nbsp;</label>
                <div class="col-sm-4">
                    <div class="checkbox">
                        <label>
                            <input{if $config.shipping eq '1'} checked{/if} id="shipping" name="shipping" data-toggle="toggle" type="checkbox" data-on="oui" data-off="non" data-onstyle="primary" data-offstyle="default" >
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <h3>
            Moyens de paiement
        </h3>
        <div class="form-group">
            <label for="bank_wire" class="col-sm-3 control-label">{#bank_wire#}</label>
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input {if $data.bank_wire eq '1'} checked{/if} type="checkbox" name="bank_wire" id="bank_wire" value="1" data-toggle="toggle" type="checkbox" data-on="oui" data-off="non" data-onstyle="primary" data-offstyle="default">
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-6">
                <label for="hipay" class="col-sm-6 control-label">{#hipay#}</label>
                <div class="col-sm-4">
                    <div class="checkbox">
                        <label>
                            <input {if $data.hipay eq '1'} checked{/if} type="checkbox" name="hipay" id="hipay" value="1" data-toggle="toggle" type="checkbox" data-on="oui" data-off="non" data-onstyle="primary" data-offstyle="default">
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <label for="ogone" class="col-sm-6 control-label">{#ogone#}</label>
                <div class="col-sm-4">
                    <div class="checkbox">
                        <label>
                            <input {if $data.ogone eq '1'} checked{/if} type="checkbox" name="ogone" id="ogone" value="1" data-toggle="toggle" type="checkbox" data-on="oui" data-off="non" data-onstyle="primary" data-offstyle="default">
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <h3>
            Coordonnées bancaire
        </h3>
        <div class="form-group">
            <label for="account_owner" class="col-sm-3 control-label">Titulaire :</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="account_owner" name="account_owner" value="{$data.account_owner}" size="30" />
            </div>
        </div>
        <div class="form-group">
            <label for="contact_details" class="col-sm-3 control-label">Détails :</label>
            <div class="col-sm-4">
                <textarea class="form-control" id="contact_details" name="contact_details" rows="4" cols="20">{$data.contact_details}</textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="bank_address" class="col-sm-3 control-label">Adresse de la banque :</label>
            <div class="col-sm-4">
                <textarea class="form-control" id="bank_address" name="bank_address" rows="4" cols="20">{$data.bank_address}</textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">{#save#|ucfirst}</button>
            </div>
        </div>
    </fieldset>
</form>