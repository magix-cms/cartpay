{extends file="layout.tpl"}
{block name='head:title'}{#cartpay_plugin#}{/block}
{block name='body:id'}cartpay{/block}
{block name='article:header'}
    <h1 class="h2"><a href="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}" title="Afficher la liste des commandes">{#cartpay_plugin#}</a></h1>
{/block}
{block name='article:content'}
    {if {employee_access type="view" class_name=$cClass} eq 1}
        <div class="panels">
            <section class="panel">
                {if $debug}
                    {$debug}
                {/if}
                <header class="panel-header panel-nav">
                    <h2 class="panel-heading h5">{#cartpay_management#|ucfirst}</h2>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab">{#order#}</a></li>
                        <li role="presentation"><a href="#config" aria-controls="config" role="tab" data-toggle="tab">{#config#}</a></li>
                    </ul>
                </header>
                <div class="panel-body panel-body-form">
                    <div class="mc-message-container clearfix">
                        <div class="mc-message">{if $message}{$message}{/if}</div>
                    </div>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="general">
                            {include file="section/form/table-form-3.tpl" idcolumn='id_cart' data=$carts activation=false sortable=false controller="cartpay" change_offset=true}
                        </div>
                        <div role="tabpanel" class="tab-pane" id="config">
                            {include file="form/config.tpl"}
                        </div>
                    </div>
                </div>
            </section>
        </div>
        {include file="modal/delete.tpl" data_type='cart' title={#modal_delete_title#|ucfirst} info_text=true}
        {include file="modal/error.tpl"}
    {else}
        {include file="section/brick/viewperms.tpl"}
    {/if}
{/block}

{block name="foot" append}
    {capture name="scriptForm"}/{baseadmin}/min/?f=libjs/vendor/additional-methods.1.17.0.min.js,plugins/cartpay/js/admin.min.js{/capture}
    {script src=$smarty.capture.scriptForm type="javascript"}
    <script type="text/javascript">
        $(function() {
            if (typeof cartpay == "undefined") {
                console.log("cartpay is not defined");
            } else
            {
                cartpay.run();
            }
        });
    </script>
{/block}