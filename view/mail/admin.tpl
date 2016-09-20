{extends file="cartpay/mail/layout.tpl"}
<!-- Wrapper/Container Table: Use a wrapper table to control the width and the background color consistently of your email. Use this approach instead of setting attributes on the body tag. -->
{block name='body:content'}
    <!-- move the above styles into your custom stylesheet -->
    <table align="center" class="container content float-center">
        <tbody>
        <tr>
            <td>
                <table class="spacer">
                    <tbody>
                    <tr>
                        <td height="16px" style="font-size:16px;line-height:16px;">&#xA0;</td>
                    </tr>
                    </tbody>
                </table>
                <table class="row">
                    <tbody>
                    <tr>
                        <td class="small-12 large-12 first last">
                            <table class="spacer spacer-hr">
                                <tbody>
                                <tr>
                                    <td height="16px" style="font-size:16px;line-height:16px;">&#xA0;</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="small-12 large-6 columns first last">
                            <table>
                                <tr>
                                    <td>
                                        <table>
                                            {if isset($getCartData.id_cart)}
                                                {$data = $getCartData}
                                            {/if}
                                            {*<pre>{$data|print_r}</pre>*}
                                            {if is_array($data) && !empty($data)}
                                                <tr>
                                                    <td class="small-12 large-6 columns first last">
                                                        <h2>{#commande_number#} {$data.id_cart}</h2>
                                                        <p>
                                                            {$smarty.config.auto_message_mail|sprintf:{$data.date_order|date_format:"%d/%m/%Y"}:{$data.date_order|date_format:"%H:%M"}}
                                                        </p>
                                                        {assign var='to_pay_htva' value=($data.amount_order - $data.tax_amount)}
                                                        <table>
                                                            <thead>
                                                            <tr>
                                                                <th>{#coordonnees_cart#|ucfirst}</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td>{#pn_cartpay_lastname#|ucfirst}</td>
                                                                <td><strong>{$data.lastname_cart}</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>{#pn_cartpay_firstname#|ucfirst}</td>
                                                                <td><strong>{$data.firstname_cart}</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>{#pn_cartpay_street#|ucfirst}</td>
                                                                <td><strong>{$data.street_cart}</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>{#pn_cartpay_locality#|ucfirst}</td>
                                                                <td><strong>{$data.city_cart}</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>{#pn_cartpay_postal#|ucfirst}</td>
                                                                <td><strong>{$data.postal_cart}</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>{#pn_cartpay_country#|ucfirst}</td>
                                                                <td><strong>{$data.country_cart|ucfirst}</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>{#pn_cartpay_mail#|ucfirst}</td>
                                                                <td><strong>{$data.email_cart}</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>{#pn_cartpay_phone#|ucfirst}</td>
                                                                <td><strong>{if !empty($data.phone_cart)}{$data.phone_cart}{else}-{/if}</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>{#pn_cartpay_vat#|ucfirst}</td>
                                                                <td><strong>{if !empty($data.vat_cart)}{$data.vat_cart}{else}-{/if}</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>{#pn_cartpay_company#|ucfirst}</td>
                                                                <td><strong>{if !empty($data.company_cart)}{$data.company_cart}{else}-{/if}</strong></td>
                                                            </tr>
                                                            </tbody>
                                                        </table>

                                                        {if $data.street_liv_cart != null OR $data.postal_liv_cart != null OR $data.city_liv_cart != null OR $data.country_liv_cart != null}
                                                            <table>
                                                                <thead>
                                                                <tr>
                                                                    <th>{#coordonnees_liv#|ucfirst}</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tr>
                                                                    <td>{#pn_cartpay_lastname#|ucfirst}</td>
                                                                    <td><strong>{$data.lastname_liv_cart}</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{#pn_cartpay_firstname#|ucfirst}</td>
                                                                    <td><strong>{$data.firstname_liv_cart}</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{#pn_cartpay_street#|ucfirst}</td>
                                                                    <td><strong>{$data.street_liv_cart}</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{#pn_cartpay_locality#|ucfirst}</td>
                                                                    <td><strong>{$data.city_liv_cart}</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{#pn_cartpay_postal#|ucfirst}</td>
                                                                    <td><strong>{$data.postal_liv_cart}</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{#pn_cartpay_country#|ucfirst}</td>
                                                                    <td><strong>{$data.country_liv_cart}</strong></td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        {/if}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="small-12 large-6 columns first last">
                                                        <h3>{#order_resume#} : </h3>
                                                        {foreach $data.catalog as $val => $key1}
                                                            {assign var='total_price' value={$key1.CATALOG_LIST_QUANTITY}*{$key1.CATALOG_LIST_PRICE}}
                                                            {assign var='price_hvat' value=($key1.CATALOG_LIST_PRICE / (1 + ($data.amount_tva / 100)))}
                                                            {assign var='total_price_hvat' value=($total_price - $data.tax_amount)}
                                                            {*<h4 style="font-weight: bold;">{$key1.CATALOG_LIST_NAME}</h4>*}
                                                            <table>
                                                                <thead>
                                                                <tr>
                                                                    <th>{$key1.CATALOG_LIST_NAME}</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                {if isset($key1.CATALOG_LIST_ATTR)}
                                                                    <tr>
                                                                        <td>{#attr_cart#}</td>
                                                                        <td><strong>{$key1.CATALOG_LIST_ATTR}</strong></td>
                                                                    </tr>
                                                                {/if}
                                                                <tr>
                                                                    <td>{#quantity_cart#}</td>
                                                                    <td><strong>{$key1.CATALOG_LIST_QUANTITY}</strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{#to_pay_htva#|ucfirst}</td>
                                                                    <td><strong>{$price_hvat|string_format:"%.2f"}</strong> €</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{#price_items#}</td>
                                                                    <td><strong>{$key1.CATALOG_LIST_PRICE}</strong> €</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>{#to_pay#}</td>
                                                                    <td><strong>{$total_price|string_format:"%.2f"}</strong> €</td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        {/foreach}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="small-12 large-12 first last">
                                                        <table class="spacer spacer-hr">
                                                            <tbody>
                                                            <tr>
                                                                <td height="16px" style="font-size:16px;line-height:16px;">&#xA0;</td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="small-12 large-6 columns first last">
                                                        <table>
                                                            <thead>
                                                            <tr>
                                                                <th>
                                                                    {#commande_total#|ucfirst}&nbsp;:
                                                                </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td>{#to_pay_htva#|ucfirst}</td>
                                                                <td><strong>{$to_pay_htva}</strong> €</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{#shipping#|ucfirst}</td>
                                                                <td><strong>{$data.shipping_htva}</strong> €</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{#tax_amount#|ucfirst}</td>
                                                                <td><strong>{$data.amount_tax}</strong> €</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{#to_pay_ttc#|ucfirst}</td>
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
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="small-12 large-12 first last">
                            <table class="spacer spacer-hr">
                                <tbody>
                                <tr>
                                    <td height="16px" style="font-size:16px;line-height:16px;">&#xA0;</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
{/block}
<!-- End of wrapper table -->