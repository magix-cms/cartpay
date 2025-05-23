{*<pre>{$cart|print_r}</pre>*}
<form id="cart_form" method="post" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=edit" class="validate_form edit_form col-ph-12 col-sm-10 col-md-8 col-lg-6">
    <fieldset>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <div class="form-group">
                    <label for="order_num">{#order#|ucfirst} :</label>
                    <input id="order_num" type="text" name="order_num" value="{$cart.id_order}" placeholder="{#order#}" disabled class="form-control" />
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="form-group">
                    <label for="email_ac">{#email#|ucfirst} :</label>
                    <input id="email_ac" type="text" name="cart[email_ac]" value="{$cart.email}" placeholder="{#ph_email#}" disabled class="form-control" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <div class="form-group">
                    <label for="lastname_ac">{#lastname#|ucfirst} :</label>
                    <input id="lastname_ac" type="text" name="cart[lastname_ac]" value="{$cart.lastname}" placeholder="{#ph_lastname#}" disabled class="form-control" />
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="form-group">
                    <label for="firstname_ac">{#firstname#|ucfirst} :</label>
                    <input id="firstname_ac" type="text" name="cart[firstname_ac]" value="{$cart.firstname}" placeholder="{#ph_firstname#}" disabled class="form-control" />
                </div>
            </div>
        </div>
    </fieldset>
</form>
{*<pre>{$product|print_r}</pre>
<pre>{$scheme|print_r}</pre>*}
{include file="section/form/table-form-3.tpl" data=$product idcolumn='id_items' checkbox=false dlt=false edit=false activation=false search=false sortable=false controller="cartpay"}
{if $cart.type_cart eq "sale"}
<form id="cart_form_order" method="post" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=edit" class="validate_form edit_form col-ph-12 col-sm-10 col-md-8 col-lg-6">
    <fieldset>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <div class="form-group">
                    <label for="amount_order">{#amount_order#|ucfirst} :</label>
                    <input id="amount_order" type="text" name="cart[amount_order]" value="{$cart.amount_order|round:1|number_format:2:',':' '|decimal_trim:','}" placeholder="{#amount_order#}" disabled class="form-control" />
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="form-group">
                    <label for="currency_order">{#currency_order#|ucfirst} :</label>
                    <input id="currency_order" type="text" name="cart[currency_order]" value="{$cart.currency_order}" placeholder="{#currency_order#}" disabled class="form-control" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <div class="form-group">
                    <label for="payment_order">{#payment_order#|ucfirst} :</label>
                    <input id="payment_order" type="text" name="cart[payment_order]" value="{#$cart.payment_order#}" placeholder="{#payment_order#}" disabled class="form-control" />
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <label for="status_order">{#status_order#|ucfirst} :</label>
                <select name="status_order" id="status_order" class="form-control required" required>
                    <option value="paid"{if $cart.status_order eq "paid"} selected{/if}>{#paid#}</option>
                    <option value="pending"{if $cart.status_order eq "pending"} selected{/if}>{#pending#}</option>
                    <option value="failed"{if $cart.status_order eq "failed"} selected{/if}>{#failed#}</option>
                </select>
            </div>
        </div>
        {if isset($cartpay_params) && is_array($cartpay_params)}
            {foreach $cartpay_params as $param}
                {include file="$param"}
            {/foreach}
        {/if}
        <fieldset>
            <input type="hidden" name="id" value="{$cart.id_cart}" />
            <button class="btn btn-main-theme" type="submit" name="action" value="edit">{#save#|ucfirst}</button>
        </fieldset>
    </fieldset>
</form>
    {else}
<form id="cart_form_quotation" method="post" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=edit" class="validate_form edit_form col-ph-12 col-sm-10 col-md-8 col-lg-6">
    <fieldset>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <div class="form-group">
                    <label for="amount_q">{#amount_order#|ucfirst} :</label>
                    <input id="amount_q" type="text" name="cart[amount_q]" value="{$cart.amount_q}" placeholder="{#amount_order#}" disabled class="form-control" />
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="form-group">
                    <label for="currency_q">{#currency_order#|ucfirst} :</label>
                    <input id="currency_q" type="text" name="cart[currency_q]" value="{$cart.currency_q}" placeholder="{#currency_order#}" disabled class="form-control" />
                </div>
            </div>
        </div>
    </fieldset>
</form>
{/if}