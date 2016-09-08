{extends file="cartpay/mail/layout.tpl"}
<!-- Wrapper/Container Table: Use a wrapper table to control the width and the background color consistently of your email. Use this approach instead of setting attributes on the body tag. -->
{block name='body:content'}
    {autoload_i18n}
    {widget_about_data}
<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable">
    <tr>
        <td valign="top">
            <!-- Tables are the most common way to format your email consistently. Set your table widths inside cells and in most cases reset cellpadding, cellspacing, and border to zero. Use nested tables as a way to space effectively in your message. -->
            <table cellpadding="0" cellspacing="0" border="0" align="center">
                <tr>
                    <td width="800" style="border-bottom: 10px solid #d6b170;background: #222;padding: 15px 0;text-align: center;" valign="top" colspan="2">
                        <!-- Gmail/Hotmail image display fix -->
                        <a href="{geturl}" target ="_blank" title="{$companyData.name}" style="text-decoration: none;font-size: 46px;">
                            <img src="{geturl}/skin/{template}/img/logo/{#logo_img_small#}" alt="{#logo_img_alt#|ucfirst}" height="60" width="229"/>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="800" style="background: #FFFFFF;padding: 0 15px 15px; border-right: 1px solid #e3e3e3; border-left: 1px solid #e3e3e3;" valign="top" colspan="2">
                    <table class="table table-condensed">
                    {if isset($getCartData.id_cart)}
                        {$data = $getCartData}
                    {/if}
                    {*<pre>{$data|print_r}</pre>*}
                    {if is_array($data) && !empty($data)}
                        <tr>
                            <td>
                                <h2>{#commande_number#} {$data.id_cart}</h2>
                                <p>
                                    {$smarty.config.auto_message_mail|sprintf:{$data.date_order|date_format:"%d/%m/%Y"}:{$data.date_order|date_format:"%H:%M"}}
                                </p>
                                {assign var='to_pay_htva' value=($data.amount_order - $data.tax_amount)}
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th colspan="2">{#coordonnees_cart#|ucfirst}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td style="text-align: right; width: 50%">{#pn_cartpay_lastname#|ucfirst}</td>
                                        <td><strong>{$data.lastname_cart}</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; width: 50%">{#pn_cartpay_firstname#|ucfirst}</td>
                                        <td><strong>{$data.firstname_cart}</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; width: 50%">{#pn_cartpay_street#|ucfirst}</td>
                                        <td><strong>{$data.street_cart}</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; width: 50%">{#pn_cartpay_locality#|ucfirst}</td>
                                        <td><strong>{$data.city_cart}</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; width: 50%">{#pn_cartpay_postal#|ucfirst}</td>
                                        <td><strong>{$data.postal_cart}</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; width: 50%">{#pn_cartpay_country#|ucfirst}</td>
                                        <td><strong>{$data.country_cart|ucfirst}</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; width: 50%">{#pn_cartpay_mail#|ucfirst}</td>
                                        <td><strong>{$data.email_cart}</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; width: 50%">{#pn_cartpay_phone#|ucfirst}</td>
                                        <td><strong>{if !empty($data.phone_cart)}{$data.phone_cart}{else}-{/if}</strong></td>
                                    </tr>
                                    </tbody>
                                </table>

                                {if $data.street_liv_cart != null OR $data.postal_liv_cart != null OR $data.city_liv_cart != null OR $data.country_liv_cart != null}
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th colspan="2">{#coordonnees_liv#|ucfirst}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td style="text-align: right; width: 50%">{#pn_cartpay_lastname#|ucfirst}</td>
                                            <td><strong>{$data.lastname_liv_cart}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; width: 50%">{#pn_cartpay_firstname#|ucfirst}</td>
                                            <td><strong>{$data.firstname_liv_cart}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; width: 50%">{#pn_cartpay_street#|ucfirst}</td>
                                            <td><strong>{$data.street_liv_cart}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; width: 50%">{#pn_cartpay_locality#|ucfirst}</td>
                                            <td><strong>{$data.city_liv_cart}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; width: 50%">{#pn_cartpay_postal#|ucfirst}</td>
                                            <td><strong>{$data.postal_liv_cart}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; width: 50%">{#pn_cartpay_country#|ucfirst}</td>
                                            <td><strong>{$data.country_liv_cart}</strong></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h3 class="bg-primary">{#order_resume#} : </h3>
                                {foreach $data.catalog as $val => $key1}
                                    {assign var='total_price' value={$key1.CATALOG_LIST_QUANTITY}*{$key1.CATALOG_LIST_PRICE}}
                                    {assign var='price_hvat' value=($key1.CATALOG_LIST_PRICE / (1 + ($data.amount_tva / 100)))}
                                    {assign var='total_price_hvat' value=($total_price - $data.tax_amount)}
                                    {*<h4 style="font-weight: bold;">{$key1.CATALOG_LIST_NAME}</h4>*}
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th style="text-align: center" colspan="2">{$key1.CATALOG_LIST_NAME}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {if isset($key1.CATALOG_LIST_ATTR)}
                                        <tr>
                                            <td style="text-align: right; width: 50%">{#attr_cart#}</td>
                                            <td><strong>{$key1.CATALOG_LIST_ATTR}</strong></td>
                                        </tr>
                                        {/if}
                                        <tr>
                                            <td style="text-align: right; width: 50%">{#quantity_cart#}</td>
                                            <td><strong>{$key1.CATALOG_LIST_QUANTITY}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; width: 50%">{#to_pay_htva#|ucfirst}</td>
                                            <td><strong>{$price_hvat|string_format:"%.2f"}</strong> €</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; width: 50%">{#price_items#}</td>
                                            <td><strong>{$key1.CATALOG_LIST_PRICE}</strong> €</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; width: 50%">{#to_pay#}</td>
                                            <td><strong>{$total_price|string_format:"%.2f"}</strong> €</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                {/foreach}
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center" colspan="2">
                                            {#commande_total#|ucfirst}&nbsp;:
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="text-align: right; width: 50%">{#to_pay_htva#|ucfirst}</td>
                                            <td><strong>{$to_pay_htva}</strong> €</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; width: 50%">{#shipping#|ucfirst}</td>
                                            <td><strong>{$data.shipping_htva}</strong> €</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; width: 50%">{#tax_amount#|ucfirst}</td>
                                            <td><strong>{$data.amount_tax}</strong> €</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; width: 50%">{#to_pay_ttc#|ucfirst}</td>
                                            {$total = $data.amount_order+$data.shipping_price_order}
                                            <td><strong>{$total|number_format:2:'.':''}</strong> €</td>
                                        </tr>
                                    </tbody>
                                </table>
                                {if $data.message_cart != null}
                                <p>
                                    <label>
                                        {#pn_cartpay_message#|ucfirst} :
                                    </label>
                                    <br />
                                    {$data.message_cart}
                                <p>
                                {/if}
                            </td>
                        </tr>
                    {/if}
                    </table>
                    </td>
                </tr>
                <tr>
                    <td width="400" style="background: #d6b170;color:#333;padding:15px;" valign="top">
                        <ul style="padding-left: 0;list-style: none;">
                            <li>
                                {$companyData.name}
                            </li>
                            <li>
                                {$companyData.contact.adress.street},<br/>
                                {$companyData.contact.adress.postcode} {$companyData.contact.adress.city}
                            </li>
                        </ul>
                    </td>
                    <td width="400" style="background: #d6b170;color:#333;padding:15px 15px 0;" valign="top">
                        <ul style="padding-left: 0;list-style: none;">
                            <li>
                                {$companyData.contact.mail}
                            </li>
                            {if $companyData.contact.phone}
                            <li>
                                {$companyData.contact.phone}
                            </li>
                            {/if}
                            {if $companyData.contact.mobile}
                            <li>
                                {$companyData.contact.mobile}
                            </li>
                            {/if}
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td style="background: #d6b170;color:#333;padding:0 15px 15px;" valign="top" colspan="2">
                        {$smarty.config.footer_mail_line1|sprintf:{#website_name#}:$companyData.tva}
                    </td>
                </tr>
            </table>
            <!-- End example table -->
            {*
            <!-- Working with telephone numbers (including sms prompts).  Use the "mobile" class to style appropriately in desktop clients
            versus mobile clients. -->
            <span class="mobile_link">123-456-7890</span>
            *}
        </td>
    </tr>
</table>
{/block}
<!-- End of wrapper table -->