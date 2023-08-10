{extends file="cartpay/step.tpl"}

{block name="step:formclass"} nice-form{/block}
{block name="step:name"}{#personal_informations#}{/block}
{block name="step:content"}
    <div class="row row-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6">
        {include file="cartpay/forms/buyer.tpl"}
        </div>
    </div>
{/block}