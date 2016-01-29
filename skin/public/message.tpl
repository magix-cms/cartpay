{autoload_i18n}
{switch $message}
    {case 'refused' break}
    {capture name="alert"}
        {#payment_refused#}
    {/capture}
    {capture name="class_alert"}
        alert-danger
    {/capture}

    {case 'cancel' break}
    {capture name="alert"}
        {#payment_cancel#}
    {/capture}
    {capture name="class_alert"}
        alert-warning
    {/capture}

    {case 'success' break}
    {capture name="alert"}
        {#payment_success#}
    {/capture}
    {capture name="class_alert"}
        alert-success
    {/capture}

    {case 'exception' break}
    {capture name="alert"}
        {#payment_exception#}
    {/capture}
    {capture name="class_alert"}
        alert-warning
    {/capture}

    {case 'empty' break}
    {capture name="alert"}
        {#empty_cart#}
    {/capture}
    {capture name="class_alert"}
        alert-danger
    {/capture}
{/switch}

<p class="col-sm-6 alert {$smarty.capture.class_alert} fade in">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="icon-ok"></span> {$smarty.capture.alert}
</p>