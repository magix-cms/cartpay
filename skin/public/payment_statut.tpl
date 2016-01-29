{extends file="layout.tpl"}
{block name="title"}{seo_rewrite config_param=['level'=>'0','idmetas'=>'1','default'=>#seo_t_static_plugin_cart#]}{/block}
{block name="description"}{seo_rewrite config_param=['level'=>'0','idmetas'=>'2','default'=>#seo_d_static_plugin_cart#]}{/block}
{block name='body:id'}contact{/block}

{block name="article:content"}
<h1>{#order_resume#|firststring}</h1>
    {$statut_message}
{/block}