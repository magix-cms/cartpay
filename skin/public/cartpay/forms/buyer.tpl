<div class="form-group">
    <input id="firstname" type="text" name="coor[firstname]" placeholder="{#ph_buyer_firstname#}" class="form-control required"{if $buyer.firstname} value="{$buyer.firstname}"{/if} required/>
    <label for="firstname" class="is_empty">{#buyer_firstname#}*&nbsp;:</label>
</div>
<div class="form-group">
    <input id="lastname" type="text" name="coor[lastname]" placeholder="{#ph_buyer_lastname#}" class="form-control required"{if $buyer.lastname} value="{$buyer.lastname}"{/if} required/>
    <label for="lastname" class="is_empty">{#buyer_lastname#}*&nbsp;:</label>
</div>
<div class="form-group">
    <input id="email" type="email" name="coor[email]" placeholder="{#ph_buyer_mail#}" class="form-control required"{if $buyer.email} value="{$buyer.email}"{/if} required/>
    <label for="email" class="is_empty">{#buyer_mail#}*&nbsp;:</label>
</div>
<div class="form-group">
    <input id="phone" type="tel" name="coor[phone]" placeholder="{#ph_buyer_phone#}" class="form-control phone"{if $buyer.phone} value="{$buyer.phone}"{/if} pattern="{literal}^((?=[0-9\+ \(\)-]{9,20})(\+)?\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,3}(-| )?\d{1,3}(-| )?\d{1,3}(-| )?\d{1,3})${/literal}" maxlength="20" />
    <label for="phone" class="is_empty">{#buyer_phone#}&nbsp;:</label>
</div>
<div class="form-group">
    <input id="company" type="text" name="coor[company]" placeholder="{#ph_buyer_company#}" class="form-control"{if $buyer.company} value="{$buyer.company}"{/if}/>
    <label for="company" class="is_empty">{#buyer_company#}&nbsp;:</label>
</div>
<div class="form-group">
    <input id="vat" type="text" name="coor[vat]" placeholder="{#ph_buyer_vat#}" class="form-control"{if $buyer.vat} value="{$buyer.vat}"{/if}/>
    <label for="vat" class="is_empty">{#buyer_vat#}&nbsp;:</label>
</div>
<div class="form-group">
    <textarea id="info" name="coor[info]" rows="5" class="form-control" placeholder="{#ph_buyer_info#}">{$buyer.info}</textarea>
    <label for="info" class="is_empty">{#buyer_info#}&nbsp;:</label>
</div>{strip}
{capture name="cond_gen"}
    <a class="targetblank" href="{$url}{#cartpay_cond_gen_url#}" title="{#cartpay_cond_gen_title#}">{#cartpay_cond_gen_label#}</a>
{/capture}
{capture name="private_laws"}
    <a class="targetblank" href="{$url}{#cartpay_private_laws_url#}" title="{#cartpay_private_laws_title#}">{#cartpay_private_laws_label#}</a>
{/capture}
{/strip}
<div class="form-group">
    <div class="checkbox">
        <label for="cond_gen">
            <input type="checkbox" name="cond_gen" id="cond_gen" class="required" required><small>&nbsp;{#cartpay_cond_gen#|ucfirst|sprintf:$smarty.capture.cond_gen:$smarty.capture.private_laws}&nbsp;*</small>
        </label>
    </div>
</div>
<small class="text-center help-block">{#cartpay_fiels_resquest#}</small>