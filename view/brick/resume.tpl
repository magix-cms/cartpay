<div class="col-sm-6">
    <p>
        <strong>{#pn_cartpay_lastname#|ucfirst}: </strong>
        {$lastname_cart}
    </p>
    <p>
        <strong>{#pn_cartpay_firstname#|ucfirst}: </strong>
        {$firstname_cart}
    </p>
    <p>
        <strong>{#pn_cartpay_mail#|ucfirst}: </strong>
        {$email_cart}
    </p>
    <p>
        <strong>{#pn_cartpay_phone#|ucfirst}: </strong>
        {$phone_cart}
    </p>
    <p>
        <strong>{#pn_cartpay_vat#|ucfirst}: </strong>
        {$vat_cart}
    </p>
</div>
<div class="col-sm-6">
    <p>
        <strong>{#pn_cartpay_address#|ucfirst}: </strong>
        {$street_cart}
    </p>
    <p>
        <strong>{#pn_cartpay_postal#|ucfirst}: </strong>
        {$postal_cart}
    </p>
    <p>
        <strong>{#pn_cartpay_locality#|ucfirst}: </strong>
        {$city_cart}
    </p>
    <p>
        <strong>{#pn_cartpay_country#|ucfirst}: </strong>
        {$country_cart}
    </p>
    <p>
        <strong>{#pn_cartpay_company#|ucfirst}: </strong>
        {$company_cart}
    </p>
</div>
<div class="clearfix"></div>
<h2>{#coordonnees_liv#|ucfirst}</h2>
<div class="col-sm-12">
    {if $street_liv_cart != null OR $postal_liv_cart != null OR $city_liv_cart != null OR $country_liv_cart != null}
        <p>
            <strong>{#pn_cartpay_lastname#|ucfirst}: </strong>
            {$lastname_liv_cart}
        </p>
        <p>
            <strong>{#pn_cartpay_firstname#|ucfirst}: </strong>
            {$firstname_liv_cart}
        </p>
        <p>
            <strong>{#pn_cartpay_address#|ucfirst}: </strong>
            {$street_liv_cart}
        </p>
        <p>
            <strong>{#pn_cartpay_postal#|ucfirst}: </strong>
            {$postal_liv_cart}
        </p>
        <p>
            <strong>{#pn_cartpay_locality#|ucfirst}: </strong>
            {$city_liv_cart}
        </p>
        <p>
            <strong>{#pn_cartpay_country#|ucfirst}: </strong>
            {$country_liv_cart}
        </p>
    {else}
        <p>
            <strong>{#pn_cartpay_lastname#|ucfirst}: </strong>
            {$lastname_cart}
        </p>
        <p>
            <strong>{#pn_cartpay_firstname#|ucfirst}: </strong>
            {$firstname_cart}
        </p>
        <p>
            <strong>{#pn_cartpay_address#|ucfirst}: </strong>
            {$street_cart}
        </p>
        <p>
            <strong>{#pn_cartpay_postal#|ucfirst}: </strong>
            {$postal_cart}
        </p>
        <p>
            <strong>{#pn_cartpay_locality#|ucfirst}: </strong>
            {$city_cart}
        </p>
        <p>
            <strong>{#pn_cartpay_country#|ucfirst}: </strong>
            {$country_cart}
        </p>
        <p>
            <strong>{#pn_cartpay_vat#|ucfirst}: </strong>
            {$vat_cart}
        </p>
        <p>
            <strong>{#pn_cartpay_company#|ucfirst}: </strong>
            {$company_cart}
        </p>
    {/if}
</div>