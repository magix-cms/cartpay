{extends file="cartpay/mail/layout.tpl"}
<!-- Wrapper/Container Table: Use a wrapper table to control the width and the background color consistently of your email. Use this approach instead of setting attributes on the body tag. -->
{block name='body:content'}
<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable">
    <tr>
        <td valign="top">
            <!-- Tables are the most common way to format your email consistently. Set your table widths inside cells and in most cases reset cellpadding, cellspacing, and border to zero. Use nested tables as a way to space effectively in your message. -->
            <table cellpadding="0" cellspacing="0" border="0" align="center">
                <tr>
                    <td width="800" style="background: #FFF;padding:5px;" valign="top">
                        <table>
                            <tr>
                                <td width="273"></td>
                                <td>
                                    <!-- Gmail/Hotmail image display fix -->
                                    <a href="{geturl}" target ="_blank" title="{#website#}">
                                        <img class="image_fix" src="{geturl}/skin/{template}/img/logo-clfa-horizontal.png" alt="{#website#}" title="{#website#}" />
                                    </a>
                                </td>
                                <td width="273"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="800" style="background: #FFFFFF;padding:5px;" valign="top">
                    <table class="table table-condensed">
                    {if isset($getBookingData.idbooking)}
                        {$data = $getBookingData}
                    {/if}
                    {*<pre>{$data|print_r}</pre>*}
                    {if is_array($data) && !empty($data)}
                        <tr>
                            <td>
                                <p>
                                    {$smarty.config.auto_booking_mail|sprintf:{$data.date|date_format:"%d/%m/%Y"}:{$data.date|date_format:"%H:%M"}}
                                </p>
                                {#pn_cartpay_lastname#|ucfirst} : <strong>{$data.lastname}</strong><br />
                                {#pn_cartpay_firstname#|ucfirst} : <strong>{$data.firstname}</strong><br />
                                {#pn_cartpay_mail#|ucfirst} : <strong>{$data.email}</strong><br />
                                {#product#|ucfirst} : <strong>{$data.product}</strong><br />
                                {#quantity_cart#|ucfirst} : <strong>{$data.quantity}</strong>
                            </td>
                        </tr>
                    {/if}
                    </table>
                    </td>
                </tr>
                <tr>
                    <td width="800" style="background: #E6E6E6;color:#FFFFFF;padding:10px;" valign="top">
                        <ul style="padding-left: 0;list-style: none;">
                           <li>
                                {#footer_mail_line1#}
                            </li>
                            <li>
                                {#footer_mail_line2#}
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
            <!-- End example table -->
            {*
            <!-- Working with telephone numbers (including sms prompts).  Use the "mobile" class to style appropriately in desktop clients
            versus mobile clients. -->
            <span class="mobile_link">123-456-7890</span>
            *}
        </td>
    </tr>
</table>
{/block}
<!-- End of wrapper table -->