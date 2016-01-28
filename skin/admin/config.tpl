{extends file="layout.tpl"}
{block name="styleSheet" append}
    {include file="css.tpl"}
{/block}
{block name='body:id'}plugins-{$pluginName}{/block}
{block name="article:content"}
    <h1>{$pluginName|ucfirst}</h1>
    {include file="section/tab.tpl"}
    <h2>Configuration</h2>
    <div class="mc-message clearfix"></div>
    {include file="form/config.tpl" data=$getDataConfig}
{/block}
{block name="javascript"}
    {include file="js.tpl"}
{/block}
{block name="modal"}
    <div id="window-dialog"></div>
{/block}