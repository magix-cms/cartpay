{extends file="layout.tpl"}
{block name="styleSheet" append}
    {include file="css.tpl"}
{/block}
{block name='body:id'}plugins-{$pluginName}{/block}
{block name="article:content"}
    <h1>{$pluginName|ucfirst}</h1>
    {include file="section/tab.tpl"}
    <h2>Liste des commandes</h2>
    <table class="table table-condensed table-bordered">
        <tr>
            <th></th>
            {*<th>Numéro de commande</th>*}
            <th>Nom</th>
            <th>Prénom</th>
            <th>Type de paiement</th>
            <th>VAT</th>
            <th>Frais de port</th>
            <th>{#to_pay_htva#}</th>
            <th>Montant TTC</th>
            <th>Date & heure de la commande</th>
        </tr>
        {*<pre>{$getOrderData|print_r}</pre>*}
        {include file="loop/order.tpl"}
    </table>
    <div id="list-{$pluginName}-data" class="list-{$pluginName}-data"></div>
    {$pagination}
{/block}
{block name="javascript"}
    {include file="js.tpl"}
{/block}
{block name="modal"}
    <div id="window-dialog"></div>
{/block}