{autoload_i18n}{widget_about_data}
{switch $message}
{case 'add_to_cart' break}
{capture name="alert_type"}success{/capture}
{capture name="icon"}check{/capture}
{capture name="alert_message"}{#request_success_add_to_cart#}{/capture}
{/switch}
<p class="alert alert-{$smarty.capture.alert_type} fade in">
    <span class="ico ico-{$smarty.capture.icon}"></span> {$smarty.capture.alert_message}
</p>