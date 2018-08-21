{extends file="layout.tpl"}
{block name='head:title'}{#cartpay_plugin#}{/block}
{block name='body:id'}cartpay{/block}
{block name='article:header'}
    <h1 class="h2"><a href="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}" title="Afficher la liste des commandes">{#cartpay_plugin#}</a></h1>
{/block}
{block name='article:content'}
    {if {employee_access type="edit" class_name=$cClass} eq 1}
        <div class="panels row">
            <section class="panel col-xs-12 col-md-12">
                {if $debug}
                    {$debug}
                {/if}
                <header class="panel-header {*panel-nav*}">
                    <h2 class="panel-heading h5">{#edit_account#|ucfirst}</h2>
                </header>
                <div class="panel-body panel-body-form">
                    <div class="mc-message-container clearfix">
                        <div class="mc-message"></div>
                    </div>
                    {include file="form/cart.tpl"}
                </div>
            </section>
        </div>
    {/if}
{/block}