{widget_about_data}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{$smarty.config.subject_contact}</title>
    {literal}
        <style type="text/css">
            /* Based on The MailChimp Reset INLINE: Yes. */
            /* Client-specific Styles */
            #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
            body{background:#FFFFFF; width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                color: #333;}
            /* Prevent Webkit and Windows Mobile platforms from changing default font sizes.*/
            .ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
            .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;}
            /* Forces Hotmail to display normal line spacing.  More on that: http://www.emailonacid.com/forum/viewthread/43/ */
            #backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}
            /* End reset */

            /* Some sensible defaults for images
            Bring inline: Yes. */
            img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic;}
            a img {border:none;}
            .image_fix {display:block;}

            /* Yahoo paragraph fix
            Bring inline: Yes. */
            p {margin: 1em 0;}

            /* Hotmail header color reset
            Bring inline: Yes. */
            h1,
            h2,
            h3,
            h4,
            h5,
            h6,
            .h1,
            .h2,
            .h3,
            .h4,
            .h5,
            .h6 {
                font-weight: 500;
                line-height: 1.1;
            }

            h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {color: blue !important;}

            h1 a:active, h2 a:active,  h3 a:active, h4 a:active, h5 a:active, h6 a:active {
                color: red !important; /* Preferably not the same color as the normal header link color.  There is limited support for psuedo classes in email clients, this was added just for good measure. */
            }

            h1 a:visited, h2 a:visited,  h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited {
                color: purple !important; /* Preferably not the same color as the normal header link color. There is limited support for psuedo classes in email clients, this was added just for good measure. */
            }

            /* Outlook 07, 10 Padding issue fix
            Bring inline: No.*/
            table td {border-collapse: collapse;}

            /* Remove spacing around Outlook 07, 10 tables
            Bring inline: Yes */
            table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }

            table {
                max-width: 100%;
                background-color: transparent;
            }
            th {
                text-align: left;
            }
            .table {
                width: 100%;
                margin-bottom: 20px;
            }
            .table > thead > tr > th,
            .table > tbody > tr > th,
            .table > tfoot > tr > th,
            .table > thead > tr > td,
            .table > tbody > tr > td,
            .table > tfoot > tr > td {
                padding: 8px;
                line-height: 1.428571429;
                vertical-align: top;
                border-top: 1px solid #dddddd;
            }
            .table > thead > tr > th {
                vertical-align: bottom;
                border-bottom: 2px solid #dddddd;
            }
            .table > caption + thead > tr:first-child > th,
            .table > colgroup + thead > tr:first-child > th,
            .table > thead:first-child > tr:first-child > th,
            .table > caption + thead > tr:first-child > td,
            .table > colgroup + thead > tr:first-child > td,
            .table > thead:first-child > tr:first-child > td {
                border-top: 0;
            }
            .table > tbody + tbody {
                border-top: 2px solid #dddddd;
            }
            table .table {
                background-color: #FFFFFF;
            }
            .table-condensed > thead > tr > th,
            .table-condensed > tbody > tr > th,
            .table-condensed > tfoot > tr > th,
            .table-condensed > thead > tr > td,
            .table-condensed > tbody > tr > td,
            .table-condensed > tfoot > tr > td {
                padding: 5px;
            }
            .table-bordered {
                border: 1px solid #dddddd;
            }
            .table-bordered > thead > tr > th,
            .table-bordered > tbody > tr > th,
            .table-bordered > tfoot > tr > th,
            .table-bordered > thead > tr > td,
            .table-bordered > tbody > tr > td,
            .table-bordered > tfoot > tr > td {
                border: 1px solid #dddddd;
            }
            .table-bordered th,
            .table-bordered td {
                border: 1px solid #ddd !important;
            }
            .table-bordered > thead > tr > th,
            .table-bordered > thead > tr > td {
                border-bottom-width: 2px;
            }
            .table-striped > tbody > tr:nth-child(odd) > td,
            .table-striped > tbody > tr:nth-child(odd) > th {
                background-color: #f9f9f9;
            }
            .table-hover > tbody > tr:hover > td,
            .table-hover > tbody > tr:hover > th {
                background-color: #f5f5f5;
            }
            table col[class*="col-"] {
                position: static;
                float: none;
                display: table-column;
            }
            table td[class*="col-"],
            table th[class*="col-"] {
                float: none;
                display: table-cell;
            }
            .table > thead > tr > .active,
            .table > tbody > tr > .active,
            .table > tfoot > tr > .active,
            .table > thead > .active > td,
            .table > tbody > .active > td,
            .table > tfoot > .active > td,
            .table > thead > .active > th,
            .table > tbody > .active > th,
            .table > tfoot > .active > th {
                background-color: #f5f5f5;
            }
            .table-hover > tbody > tr > .active:hover,
            .table-hover > tbody > .active:hover > td,
            .table-hover > tbody > .active:hover > th {
                background-color: #e8e8e8;
            }
            .table > thead > tr > .success,
            .table > tbody > tr > .success,
            .table > tfoot > tr > .success,
            .table > thead > .success > td,
            .table > tbody > .success > td,
            .table > tfoot > .success > td,
            .table > thead > .success > th,
            .table > tbody > .success > th,
            .table > tfoot > .success > th {
                background-color: #dff0d8;
            }
            .table-hover > tbody > tr > .success:hover,
            .table-hover > tbody > .success:hover > td,
            .table-hover > tbody > .success:hover > th {
                background-color: #d0e9c6;
            }
            .table > thead > tr > .danger,
            .table > tbody > tr > .danger,
            .table > tfoot > tr > .danger,
            .table > thead > .danger > td,
            .table > tbody > .danger > td,
            .table > tfoot > .danger > td,
            .table > thead > .danger > th,
            .table > tbody > .danger > th,
            .table > tfoot > .danger > th {
                background-color: #f2dede;
            }
            .table-hover > tbody > tr > .danger:hover,
            .table-hover > tbody > .danger:hover > td,
            .table-hover > tbody > .danger:hover > th {
                background-color: #ebcccc;
            }
            .table > thead > tr > .warning,
            .table > tbody > tr > .warning,
            .table > tfoot > tr > .warning,
            .table > thead > .warning > td,
            .table > tbody > .warning > td,
            .table > tfoot > .warning > td,
            .table > thead > .warning > th,
            .table > tbody > .warning > th,
            .table > tfoot > .warning > th {
                background-color: #fcf8e3;
            }
            .table-hover > tbody > tr > .warning:hover,
            .table-hover > tbody > .warning:hover > td,
            .table-hover > tbody > .warning:hover > th {
                background-color: #faf2cc;
            }
            table td {border-collapse: collapse;}

            .table-bordered th,
            .table-bordered td {
                border: 1px solid #ddd !important;
            }
            .table td,
            .table th {
                background-color: #fff !important;
            }
            .table {
                border-collapse: collapse !important;
            }

            /* Remove spacing around Outlook 07, 10 tables
            Bring inline: Yes */
            table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }

            /* Styling your links has become much simpler with the new Yahoo.  In fact, it falls in line with the main credo of styling in email and make sure to bring your styles inline.  Your link colors will be uniform across clients when brought inline.
            Bring inline: Yes. */
            a {color: #428bca;}
            ul {padding: 0;list-style-type: none}
            li {padding: 5px 0;}
            .blue {color: #3778af;}
            .green {color: #489944;}

            /***************************************************
            ****************************************************
            MOBILE TARGETING
            ****************************************************
            ***************************************************/
            @media only screen and (max-device-width: 480px) {
                /* Part one of controlling phone number linking for mobile. */
                a[href^="tel"], a[href^="sms"] {
                    text-decoration: none;
                    color: blue; /* or whatever your want */
                    pointer-events: none;
                    cursor: default;
                }

                .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
                    text-decoration: default;
                    color: orange !important;
                    pointer-events: auto;
                    cursor: default;
                }

            }

            /* More Specific Targeting */

            @media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
                /* You guessed it, ipad (tablets, smaller screens, etc) */
                /* repeating for the ipad */
                a[href^="tel"], a[href^="sms"] {
                    text-decoration: none;
                    color: blue; /* or whatever your want */
                    pointer-events: none;
                    cursor: default;
                }

                .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
                    text-decoration: default;
                    color: orange !important;
                    pointer-events: auto;
                    cursor: default;
                }
            }

            @media only screen and (-webkit-min-device-pixel-ratio: 2) {
                /* Put your iPhone 4g styles in here */
            }

            /* Android targeting */
            @media only screen and (-webkit-device-pixel-ratio:.75){
                /* Put CSS for low density (ldpi) Android layouts in here */
            }
            @media only screen and (-webkit-device-pixel-ratio:1){
                /* Put CSS for medium density (mdpi) Android layouts in here */
            }
            @media only screen and (-webkit-device-pixel-ratio:1.5){
                /* Put CSS for high density (hdpi) Android layouts in here */
            }
            /* end Android targeting */

        </style>
    {/literal}
    <!-- Targeting Windows Mobile -->
    <!--[if IEMobile 7]>
    <style type="text/css">

    </style>
    <![endif]-->

    <!-- ***********************************************
    ****************************************************
    END MOBILE TARGETING
    ****************************************************
    ************************************************ -->

    <!--[if gte mso 9]>
    <style>
        /* Target Outlook 2007 and 2010 */
    </style>
    <![endif]-->
</head>
<body>
{block name='body:content'}{/block}
</body>
</html>