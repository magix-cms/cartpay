<div class="row">
<form id="edit_config" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;tabs=config&amp;action=edit" method="post" class="validate_form edit_form col-ph-12 col-sm-10 col-md-6 col-lg-6">
    <fieldset>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <div class="form-group">
                    <label for="email_config">{#email_config#|ucfirst}&nbsp;</label>
                    <input type="text" name="acConfig[email_config]" id="email_config" class="form-control" placeholder="{#email_config#}" value="{$config.email_config}" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <div class="form-group">
                    <label for="email_config">{#email_config_from#|ucfirst}&nbsp;</label>
                    <input type="text" name="acConfig[email_config_from]" id="email_config_from" class="form-control" placeholder="{#email_config_from#}" value="{$config.email_config_from}" />
                </div>
            </div>
        </div>
        {*<div class="row">
            <div class="col-xs-12 col-md-6">
                <div class="form-group">
                    <label for="type_order">{#select_type#|ucfirst}&nbsp;*:</label>
                    <select name="acConfig[type_order]" id="type_order" class="form-control required" required>
                        <option value="sale"{if $config.type_order eq "sale"} selected{/if}>Vente</option>
                        <option value="quotation"{if $config.type_order eq "quotation"} selected{/if}>Devis</option>
                    </select>
                </div>
            </div>
        </div>*}
        <hr>
        <div class="form-group">
            <div class="switch">
                <input type="checkbox" id="quotation_enabled" name="acConfig[quotation_enabled]" class="switch-native-control type_order"{if $config.quotation_enabled || (!$config.quotation_enabled && !$config.order_enabled)} checked{/if} />
                <div class="switch-bg">
                    <div class="switch-knob"></div>
                </div>
            </div>
            <label for="quotation_enabled">{#quotation_enabled#}&nbsp;?</label>
        </div>
        <div class="form-group">
            <div class="switch">
                <input type="checkbox" id="order_enabled" name="acConfig[order_enabled]" class="switch-native-control type_order"{if $config.order_enabled} checked{/if} />
                <div class="switch-bg">
                    <div class="switch-knob"></div>
                </div>
            </div>
            <label for="order_enabled">{#order_enabled#}&nbsp;?</label>
        </div>
        <div class="form-group">
            <div class="switch">
                <input type="checkbox" id="retreive_enabled" name="acConfig[retreive_enabled]" class="switch-native-control type_order"{if $config.retreive_enabled} checked{/if} />
                <div class="switch-bg">
                    <div class="switch-knob"></div>
                </div>
            </div>
            <label for="retreive_enabled">{#retreive_enabled#}&nbsp;?</label>
        </div>
        <hr>
        <div class="form-group">
            <div class="switch">
                <input type="checkbox" id="billing_address" name="acConfig[billing_address]" class="switch-native-control"{if $config.billing_address} checked{/if} />
                <div class="switch-bg">
                    <div class="switch-knob"></div>
                </div>
            </div>
            <label for="billing_address">{#billing_address#}&nbsp;?</label>
        </div>
        <div class="form-group">
            <div class="switch">
                <input type="checkbox" id="show_price" name="acConfig[show_price]" class="switch-native-control"{if $config.show_price} checked{/if} />
                <div class="switch-bg">
                    <div class="switch-knob"></div>
                </div>
            </div>
            <label for="show_price">{#show_price#}&nbsp;?</label>
        </div>
        <div class="form-group">
            <div class="switch">
                <input type="checkbox" id="bank_wire" name="acConfig[bank_wire]" class="switch-native-control"{if $config.bank_wire} checked{/if} />
                <div class="switch-bg">
                    <div class="switch-knob"></div>
                </div>
            </div>
            <label for="bank_wire">{#bank_wire#}&nbsp;?</label>
        </div>
        <div id="bank" class="collapse{if $config.bank_wire} in{/if}">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="account_owner">{#account_owner#|ucfirst}&nbsp;</label>
                        <input type="text" name="acConfig[account_owner]" id="account_owner" class="form-control" placeholder="{#account_owner#}" value="{$config.account_owner}" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="bank_account">{#bank_account#|ucfirst}&nbsp;</label>
                        <input type="text" name="acConfig[bank_account]" id="bank_account" class="form-control" placeholder="{#bank_account#}" value="{$config.bank_account}" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="bank_link">{#bank_link#|ucfirst}&nbsp;</label>
                        <input type="text" name="acConfig[bank_link]" id="bank_link" class="form-control" placeholder="{#bank_link#}" value="{$config.bank_link}" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label for="bank_address">{#bank_address#|ucfirst} :</label>
                        <textarea cols="30" rows="4" name="acConfig[bank_address]" id="bank_address" class="form-control">{call name=cleantextarea field=$config.bank_address}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <div id="submit">
        <button class="btn btn-main-theme" type="submit" name="action" value="edit">{#save#|ucfirst}</button>
    </div>
</form>
    <form id="remove_cart" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;tabs=config&amp;action=edit" method="post" class="validate_form edit_form col-ph-12 col-sm-10 col-md-6 col-lg-6">
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <input type="hidden" name="remove_abandoned_cart" value="true">
                <div class="alert alert-warning"><span class="fa fa-warning margin-right-sm"></span> Ce bouton vide les paniers abandonnés</div>
            </div>
        </div>
        <div id="submit">
            <button class="btn btn-main-theme" type="submit">{#remove_abandoned_cart#|ucfirst}</button>
        </div>
    </form>
</div>