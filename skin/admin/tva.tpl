{extends file="layout.tpl"}
{block name="styleSheet" append}
    {include file="css.tpl"}
{/block}
{block name='body:id'}plugins-{$pluginName}{/block}
{block name="article:content"}
    <h1>{$pluginName|ucfirst}</h1>
    {include file="section/tab.tpl"}
    <h2>Gestion de la TVA</h2>
    <div class="mc-message clearfix"></div>
    <h3>Configuration</h3>
    <form id="cartpay_tva_conf" method="post" action="" class="form-horizontal">
    <fieldset>
        <div class="form-group">
            <div class="col-sm-2">
                <label for="amount_tva">Montant :</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="amount_tva_1" name="amount_tva_1" value="{$getConfDataTVA[0].amount_tva}" size="50" />
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-sm-2">
                <label for="amount_tva">Zone :</label>
                <input type="text" class="form-control" id="zone_tva_1" name="zone_tva_1" readonly="readonly" value="zone_1" size="30" />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-2">
                <label for="amount_tva">Montant :</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="amount_tva_2" name="amount_tva_2" value="{$getConfDataTVA[1].amount_tva}" size="50" />
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-sm-2">
                <label for="amount_tva">Zone :</label>
                <input type="text" class="form-control" id="zone_tva_2" name="zone_tva_2" readonly="readonly" value="zone_2" size="30" />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-10">
                <button type="submit" class="btn btn-primary">{#save#|ucfirst}</button>
            </div>
        </div>
    </fieldset>
    </form>
    <hr />
    <h3>Gestion des pays</h3>
    <p id="addbtn">
        <a class="toggleModal btn btn-primary" data-toggle="modal" data-target="#add-tva" href="#">
            <span class="fa fa-plus"></span>
            Ajouter la TVA a un pays
        </a>
    </p>
    <div id="list-tva"></div>
{/block}
{block name="javascript"}
    {include file="js.tpl"}
{/block}
{block name="modal"}
    <div class="modal fade" id="add-tva" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"> Ajouter la TVA a un pays</h4>
                </div>
                <div class="mc-message-tva clearfix"></div>
                <form id="cartpay_tva" method="post" action="" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-sm-6">
                            <label for="iso">Pays :</label>
                            <select class="form-control" id="iso" name="iso">
                                {*<option value="">Sélectionner un pays</option>
                                {foreach $countryTools as $key => $val nocache}
                                    {$selected  =   ''}
                                    {if $data.country_pr == $key}
                                        {$selected  =   ' selected'}
                                    {/if}
                                    <option{$selected} value="{$key}">{$val|ucfirst}</option>
                                {/foreach}*}
                                <option value="">{#select_country#}</option>
                                {foreach $countryTools as $key => $val}
                                    <option value="{$val.iso}"{if $data.iso eq $val.iso} selected{/if} data-country="{$val.country}">{#$val.iso#|ucfirst}</option>
                                {/foreach}
                            </select>
                            <input type="hidden" id="country" name="country" value="" />
                        </div>
                        <div class="col-sm-6">
                            <label for="country">Zone :</label>
                            <select class="form-control" id="idtvac" name="idtvac">
                                <option value="">Sélectionner une zone</option>
                                {foreach $getConfDataTVA as $key nocache}
                                    <option value="{$key.idtvac}">{$key.zone_tva|ucfirst}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">{#save#|ucfirst}</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    {include file="modal/delete.tpl"}
    <div id="window-dialog"></div>
{/block}