{extends file="mail/layout.tpl"}
{block name='body:content'}
    <table class="spacer">
        <tbody>
        <tr>
            <td height="16px" style="font-size:16px;line-height:16px;">&#xA0;</td>
        </tr>
        </tbody>
    </table>
    <h1 class="text-center">{#quotation_h1_mail#}</h1>
    <table class="spacer">
        <tbody>
        <tr>
            <td height="16px" style="font-size:16px;line-height:16px;">&#xA0;</td>
        </tr>
        </tbody>
    </table>
    <p class="text-left">{#cart_firstname#|ucfirst} : {$account.firstname_ac}</p>
    <p class="text-left">{#cart_lastname#|ucfirst} : {$account.lastname_ac}</p>
    <p class="text-left">{#cart_mail#|ucfirst} : {$account.email_ac}</p>
    <table class="spacer">
        <tbody>
        <tr>
            <td height="16px" style="font-size:16px;line-height:16px;">&#xA0;</td>
        </tr>
        </tbody>
    </table>
    {*<p class="text-center">{#signup_text_mail#}</p>*}
    <table class="spacer">
        <tbody>
        <tr>
            <td height="16px" style="font-size:16px;line-height:16px;">&#xA0;</td>
        </tr>
        </tbody>
    </table>
    {*<table class="button large expand">
    <tr>
        <td>
            <table>
            <tr>
                <td>
                    <center data-parsed="">
                        <a href="{geturl}/{getlang}/account/{$data.keyuniqid_ac}/activate/" align="center" class="float-center">{#signup_title_mail#}</a>
                    </center>
                </td>
            </tr>
            </table>
        </td>
        <td class="expander"></td>
    </tr>
    </table>*}
    <table>
        <thead>
        <tr>
            <th>
                {#product#|ucfirst}
            </th>
            <th>
                {#quantity#|ucfirst}
            </th>
        </tr>
        </thead>
        <tbody>
        {foreach $product as $key}
            <tr>
                <td>
                    <a href="{geturl}{$key.url}">{$key.name_p}</a>
                </td>
                <td>
                    {$key.quantity}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    <table class="spacer">
        <tbody>
        <tr>
            <td height="16px" style="font-size:16px;line-height:16px;">&#xA0;</td>
        </tr>
        </tbody>
    </table>
    <p class="text-center">{#quotation_text_footer_mail#|sprintf:$companyData.name}</p>
    <hr>
    <p class="text-left"><small>{#noreply_mail#}</small></p>
{/block}