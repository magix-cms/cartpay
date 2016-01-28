{if is_array($getItemsTvaData) && !empty($getItemsTvaData)}
    <table class="table table-condensed table-hover">
    <thead>
    <tr>
        <th>
            Pays
        </th>
        <th>
            Zone
        </th>
        <th>
            Montant
        </th>
    </tr>
    </thead>
    <tbody>
    {foreach $getItemsTvaData as $key => $value nocache}
    <tr>
        <td>
            {$value.country|ucfirst}
        </td>
        <td>
            {$value.zone}
        </td>
        <td>
            {$value.amount} %
        </td>
        <td>
            <a class="toggleModal remove-tva" data-toggle="modal" data-target="#deleteModal" data-remove="{$value.idtva}" href="#">
                <span class="fa fa-trash-o"></span>
            </a>
        </td>
    </tr>
    {/foreach}
    </tbody>
    </table>
{/if}