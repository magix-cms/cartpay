{autoload_i18n}{widget_about_data}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{$smarty.config.subject_contact}</title>
    {literal}
        <style>
            .header {
                background: #f2f2f2;
            }

            .header .columns {
                padding-bottom: 0;
            }

            .header p {
                color: #fff;
                margin-bottom: 0;
            }

            .header .wrapper-inner {
                padding: 0;
                /*controls the height of the header*/
            }

            .header .container {
                background: #f2f2f2;
            }

            .header .container td {
                padding: 15px;
            }

            .spacer.spacer-hr td{
                border-top: 1px solid #eeeeee;
            }
            .footer{
                background: #333333;
            }
            .footer p{
                color: #fff;
                margin-bottom: 0;
            }
            .footer ul{
                list-style: none;
            }
            .footer ul li{
                color: #fff;
            }
            .footer .container {
                background: #333333;
            }
            .footer .container td{
                padding: 15px;
            }
        </style>
    {/literal}
</head>
<body>
<!-- <style> -->
<table class="body" data-made-with-foundation>
    <tr>
        <td class="float-center" align="center" valign="top">
            <center data-parsed>
                <table align="center" class="container header float-center">
                    <tr>
                        <td class="wrapper-inner">
                            <table align="center" class="container">
                                <tbody>
                                <tr>
                                    <td valign="middle">
                                        <!-- Gmail/Hotmail image display fix -->
                                        <a href="{geturl}" target ="_blank" title="{$companyData.name}" style="text-decoration: none;font-size: 46px;">
                                            <img src="{geturl}/skin/{template}/img/logo/{#logo_img_mail#}" alt="{#logo_img_alt#|ucfirst}" width="270" height="88"/>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
                {block name='body:content'}{/block}
                <table  align="center" class="container footer">
                    <tr>
                        <td class="wrapper-inner">
                            <table  align="center" class="container">
                                <tbody>
                                <tr>
                                    <td>
                                        <ul>
                                            <li>
                                                {$companyData.name}
                                            </li>
                                            <li>
                                                {$companyData.contact.adress.street},<br/>
                                                {$companyData.contact.adress.postcode} {$companyData.contact.adress.city}
                                            </li>
                                        </ul>
                                    </td>
                                    <td>
                                        <ul>
                                            <li>
                                                {$companyData.contact.mail}
                                            </li>
                                            {if $companyData.contact.phone}
                                                <li>
                                                    {$companyData.contact.phone}
                                                </li>
                                            {/if}
                                            {if $companyData.contact.mobile}
                                                <li>
                                                    {$companyData.contact.mobile}
                                                </li>
                                            {/if}
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p>{$smarty.config.footer_mail_line1|sprintf:{#website_name#}:$companyData.tva}</p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </center>
        </td>
    </tr>
</table>
</body>
</html>