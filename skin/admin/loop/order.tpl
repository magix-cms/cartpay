{if is_array($getOrderData) && !empty($getOrderData)}
{foreach $getOrderData as $key => $value nocache}
    <tr>
        <td>
            <a class="view-block" href="#trHidden{$value.id_order}">
                <span class="fa fa-plus"></span>
            </a>
        </td>
        {*<td>{$value.id_cart}</td>*}
        {*<td>{$value.date_order|date_format:" %d%m%Y%H%M%S"}</td>*}
        <td>{$value.lastname_cart}</td>
        <td>{$value.firstname_cart}</td>
        <td>{if $value.payment_order eq 'bank_wire' OR $value.payment_order eq 'hipay' OR $value.payment_order eq 'ogone'}{#$value.payment_order#|ucfirst}{else}{$value.payment_order}{/if}</td>
        <td>{$value.amount_tva}%</td>
        <td>{$value.shipping_price_order} {$value.currency_order}</td>
        <td>{($value.amount_order-$value.amount_tax)} {$value.currency_order}</td>
        <td>{$value.amount_order} {$value.currency_order}</td>
        <td>{$value.date_order|date_format:" %d/%m/%Y - %H:%M"}</td>
    </tr>
    <tr id="trHidden{$value.id_order}" class="collapse-block">
        <td colspan="9">
            <table class="table table-condensed table-hover">
                <tr>
                    <td colspan="3">
                        <h3>{#commande_number#} {$value.id_cart}  </h3>
                    </td>
                </tr>
                {foreach $value.catalog as $val => $key1 nocache}
                    {assign var='total_price' value={$key1.CATALOG_LIST_QUANTITY}*{$key1.CATALOG_LIST_PRICE}}
                    <tr>
                        <td colspan="3">
                            <h4>{$key1.CATALOG_LIST_NAME}</h4>
                            <ul>
                                <li>{#quantity_cart#} : {$key1.CATALOG_LIST_QUANTITY}</li>
                                <li>{#price_items#} : {$key1.CATALOG_LIST_PRICE} €</li>
                                <li>{#total_products#} : {$total_price|string_format:"%.2f"} €</li>
                            </ul>
                        </td>
                    </tr>
                {/foreach}
                <tr>
                    <td colspan="3">
                        <h3>Coordonnées : </h3>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>
                            E-mail :
                        </label>
                        {$value.email_cart}
                    </td>
                    <td>
                        <label>
                            Téléphone :
                        </label>
                        {$value.phone_cart}
                    </td>
                    <td>
                        <label>
                            T.V.A. :
                        </label>
                        {$value.tva_cart}
                    </td>
                </tr>
                {if $value.street_liv_cart != null OR $value.postal_liv_cart != null OR $value.city_liv_cart != null OR $value.country_liv_cart != null}
                    <tr>
                        <td colspan="3">
                            <label>
                                Adresse de facturation :
                            </label><br />
                            {$value.street_cart} {$value.postal_cart} <br />
                            {$value.city_cart} {$value.country_cart}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <label>
                                Adresse de livraison :
                            </label><br />
                            {$value.street_liv_cart} {$value.postal_liv_cart} <br />
                            {$value.city_liv_cart} {$value.country_liv_cart}
                        </td>
                    </tr>
                {else}
                    <tr>
                        <td colspan="3">
                            <label>
                                Adresse de facturation et de livraison :
                            </label><br />
                            {$value.street_cart} {$value.postal_cart} <br />
                            {$value.city_cart} {$value.country_cart}
                        </td>
                    </tr>
                {/if}
                <tr>
                    <td colspan="3">
                        <label>
                            Remarques complémentaires :
                        </label>
                        <br />
                        {$value.message_cart}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
{/foreach}
{/if}