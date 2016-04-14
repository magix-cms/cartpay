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
                    <td width="800" style="padding: 15px;background: #1671BF;border-bottom: 5px solid #FF9E13;" valign="top">
                        <!-- Gmail/Hotmail image display fix -->
                        <a href="{geturl}" target ="_blank" title="{$companyData.name}" style="text-decoration: none;font-size: 46px;">
                            <img style="padding-top: 13px;" src="{geturl}/skin/{template}/img/logo/{#logo_img_small#}" alt="{#logo_img_alt#|ucfirst}" width="269" height="50" />
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="800" style="background: #FFFFFF;padding:5px;" valign="top">
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
                                {assign var='to_pay_htva' value=($data.amount_order - $data.amount_tax)}
                                {#pn_cartpay_lastname#|ucfirst} : <strong>{$data.lastname_cart}</strong><br />
                                {#pn_cartpay_firstname#|ucfirst} : <strong>{$data.firstname_cart}</strong><br />
                                {if $data.tva_cart}{#pn_cartpay_tva#|ucfirst} : <strong>{$data.tva_cart}</strong><br />{/if}
                                {if $data.street_liv_cart != null OR $data.postal_liv_cart != null OR $data.city_liv_cart != null OR $data.country_liv_cart != null}
                                <label>
                                    {#coordonnees_cart#|ucfirst} :
                                </label><br />
                                    {#pn_cartpay_street#|ucfirst} : <strong>{$data.street_cart}</strong><br />
                                    {#pn_cartpay_locality#|ucfirst} : <strong>{$data.city_cart}</strong><br />
                                    {#pn_cartpay_postal#|ucfirst} : <strong>{$data.postal_cart}</strong><br />
                                    {#pn_cartpay_country#|ucfirst} : <strong>{$data.country_cart}</strong><br />
                                <label>
                                    {#coordonnees_liv#|ucfirst} :
                                </label><br />
                                    {#pn_cartpay_lastname#|ucfirst} : <strong>{$data.lastname_liv_cart}</strong><br />
                                    {#pn_cartpay_firstname#|ucfirst} : <strong>{$data.firstname_liv_cart}</strong><br />
                                    {#pn_cartpay_street#|ucfirst} : <strong>{$data.street_liv_cart}</strong><br />
                                    {#pn_cartpay_locality#|ucfirst} : <strong>{$data.city_liv_cart}</strong><br />
                                    {#pn_cartpay_postal#|ucfirst} : <strong>{$data.postal_liv_cart}</strong><br />
                                    {#pn_cartpay_country#|ucfirst} : <strong>{$data.country_liv_cart}</strong><br />
                                    {else}
                                    {*<label>
                                        {#coordonnees_liv_and_cart#|ucfirst} :
                                    </label><br />*}
                                    {#pn_cartpay_street#|ucfirst} : <strong>{$data.street_cart}</strong><br />
                                    {#pn_cartpay_locality#|ucfirst} : <strong>{$data.city_cart}</strong><br />
                                    {#pn_cartpay_postal#|ucfirst} : <strong>{$data.postal_cart}</strong><br />
                                    {#pn_cartpay_country#|ucfirst} : <strong>{$data.country_cart}</strong><br />
                                    {/if}
                                {#pn_cartpay_mail#|ucfirst} : <strong>{$data.email_cart}</strong><br />
                                {#pn_cartpay_phone#|ucfirst} : <strong>{$data.phone_cart}</strong><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table class="table table-condensed">
                                    <tr>
                                        <td colspan="3">
                                            <h3 class="bg-primary">{#order_resume#} : </h3>
                                        </td>
                                    </tr>
                                    {foreach $data.catalog as $val => $key1}
                                        <tr>
                                            <td colspan="3">
                                                {assign var='total_price' value={$key1.CATALOG_LIST_QUANTITY}*{$key1.CATALOG_LIST_PRICE}}
                                                {assign var='price_hvat' value=($key1.CATALOG_LIST_PRICE - $data.amount_tax)}
                                                {assign var='total_price_hvat' value=($total_price - $data.amount_tax)}
                                                <h4 style="font-weight: bold;">{$key1.CATALOG_LIST_NAME}</h4>
                                                <ul style="padding-left: 0;list-style: none;">
                                                    <li>{#quantity_cart#} : {$key1.CATALOG_LIST_QUANTITY}</li>
                                                    <li>{#to_pay_htva#|ucfirst} : <strong>{$price_hvat|string_format:"%.2f"}</strong> €</li>
                                                    <li>{#price_items#} : <strong>{$key1.CATALOG_LIST_PRICE}</strong> €</li>
                                                    <li>{#to_pay#} : <strong>{$total_price|string_format:"%.2f"}</strong> €</li>
                                                </ul>
                                            </td>
                                        </tr>
                                    {/foreach}
                                    <tr>
                                        <td colspan="3">
                                            <h4 class="bg-primary">{#commande_total#|ucfirst} : </h4>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {#to_pay_htva#|ucfirst} : <strong>{$to_pay_htva}</strong> €<br />
                                            {#tax_amount#|ucfirst} : <strong>{$data.amount_tax}</strong> €<br />
                                            {#shipping#|ucfirst} : <strong>{$data.shipping_price_order}</strong> €<br />
                                            {#to_pay_ttc#|ucfirst} : <strong>{$data.amount_order+$data.shipping_price_order}</strong> €</p>
                                        </td>
                                    </tr>
                                    {if $data.message_cart != null}
                                    <tr>
                                        <td colspan="3">
                                            <label>
                                                {#pn_cartpay_message#|ucfirst} :
                                            </label>
                                            <br />
                                            {$data.message_cart}
                                        </td>
                                    </tr>
                                    {/if}
                                </table>
                            </td>
                        </tr>
                    {/if}
                    </table>
                    </td>
                </tr>
                <tr>
                    <td width="800" style="background: #E6E6E6;color:#333;padding:10px;" valign="top">
                        <ul style="padding-left: 0;list-style: none;">
                           <li>
                                {#footer_mail_line1#}
                            </li>
                            <li>
                                {#footer_mail_line2#}
                            </li>
                        </ul>
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