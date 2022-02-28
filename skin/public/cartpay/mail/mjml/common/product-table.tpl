<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
    <tr>
        <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
            <table cellpadding="0" cellspacing="0" width="100%" border="0" style="color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:22px;table-layout:auto;width:100%;border:none;">
                <tr>
                    <th style="border-bottom: 1px solid #ccc; padding: 4px 8px 4px 0;">{#product#|ucfirst}</th>
                    <th style="border-bottom: 1px solid #ccc; padding: 4px 8px; text-align:center;">{#quantity#|ucfirst}</th>
                    <th style="border-bottom: 1px solid #ccc; padding: 4px 8px; text-align:center;">{#unit_price#|ucfirst}</th>
                    <th style="border-bottom: 1px solid #ccc; padding: 4px 8px; text-align:center;">{#vat_rate#|ucfirst}</th>
                    <th style="border-bottom: 1px solid #ccc; padding: 4px 0 4px 8px; text-align:right;">{#price#|ucfirst}</th>
                </tr>
                {foreach $data['cart']['items'] as $item}
                <tr>
                    <td style="border-bottom: 1px solid #ccc; padding: 4px 8px 4px 0;">
                        {$item['name']}&nbsp;
                        {if is_array($item['params']) && !empty($item['params'])}
                        {foreach $item['params'] as $param}
                        {if is_array($param.value)}
                        {foreach $param.value as $value}
                            <br><small>{$value}</small>
                        {/foreach}
                        {else}
                        {*<br><small>{$param.value}</small>*}
                        <br><small>{*{$param}&nbsp;: *}{$param.value}{if !empty($value.price.price)}&thinsp;:&thinsp;{math equation="price * (1 + (vat / 100))" price=$param.price.price vat=$param.price.vat format="%.2f"}&nbsp;€&nbsp;{#tax_included#}{/if}</small>
                        {/if}
                        {if !empty($param.info) && is_array($param.info)}
                        {foreach $param.info as $info}
                        {if !empty($info.value)}
                        <br><small><b>{$info.name}&nbsp;:</b></small>
                        <br><small>{$info.value}</small>
                        {/if}
                        {/foreach}
                        {/if}
                        {/foreach}
                        {/if}
                    </td>
                    <td style="border-bottom: 1px solid #ccc; padding: 4px 8px; text-align:center;">{$item['q']}</td>
                    <td style="border-bottom: 1px solid #ccc; padding: 4px 8px; text-align:center;">{$item['unit_price']}&nbsp;€</td>
                    <td style="border-bottom: 1px solid #ccc; padding: 4px 8px; text-align:center;">{$item['vat']}&nbsp;%</td>
                    <td style="border-bottom: 1px solid #ccc; padding: 4px 0 4px 8px; text-align:right;">{$item['total_inc']}&nbsp;€</td>
                </tr>
                {/foreach}
                {if is_array($data['cart']['fees']) && !empty($data['cart']['fees'])}
                {foreach $data['cart']['fees'] as $fees => $fee}
                <tr>
                    <td style="border-bottom: 1px solid #ccc; border-top: 3px doubled #ccc; border-bottom: 1px solid #ccc; padding: 4px 8px 4px 0; text-align:right;" colspan="4">{#$fees#}</td>
                    <td style="border-bottom: 1px solid #ccc; border-top: 3px doubled #ccc; border-bottom: 1px solid #ccc; padding: 4px 0 4px 8px; text-align:right;">{$fee.price_inc}&nbsp;€</td>
                </tr>
                {/foreach}
                {/if}
                <tr>
                    <td style="border-bottom: 1px solid #ccc; border-top: 3px doubled #ccc; border-bottom: 1px solid #ccc; padding: 4px 8px 4px 0; text-align:right;" colspan="4">{#total_exc#}</td>
                    <td style="border-bottom: 1px solid #ccc; border-top: 3px doubled #ccc; border-bottom: 1px solid #ccc; padding: 4px 0 4px 8px; text-align:right;">{$data['cart']['total']['exc']}&nbsp;€</td>
                </tr>
                {foreach $data['cart']['total']['vat'] as $rate => $ttax}
                <tr>
                    <td style="border-bottom: 1px solid #ccc; padding: 4px 8px 4px 0; text-align:right;" colspan="4">{#total_vat#}&nbsp;<small>({$rate}%)</small></td>
                    <td style="border-bottom: 1px solid #ccc; padding: 4px 0 4px 8px; text-align:right;">{$ttax}&nbsp;€</td>
                </tr>
                {/foreach}
                <tr>
                    <td style="border-top: 3px doubled #ccc; padding: 4px 8px 4px 0; text-align:right;" colspan="4">{#total_inc#}</td>
                    <td style="border-top: 3px doubled #ccc; padding: 4px 0 4px 8px; text-align:right;">
                        <h4>{$data['cart']['total']['inc']}&nbsp;€</h4>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>