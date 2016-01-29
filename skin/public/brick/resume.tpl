<div class="col-sm-6">
    <p>
        <strong>{#pn_contact_lastname#|firststring}: </strong>
        {$lastname_cart}
    </p>
    <p>
        <strong>{#pn_contact_firstname#|firststring}: </strong>
        {$firstname_cart}
    </p>
    <p>
        <strong>{#pn_contact_mail#|firststring}: </strong>
        {$email_cart}
    </p>
    <p>
        <strong>{#pn_contact_phone#|firststring}: </strong>
        {$phone_cart}
    </p>
    <p>
        <strong>{#pn_contact_tva#|firststring}: </strong>
        {$tva_cart}
    </p>
</div>
<div class="col-sm-6">
    <p>
        <strong>{#pn_contact_address#|firststring}: </strong>
        {$street_cart}
    </p>
    <p>
        <strong>{#pn_contact_postal#|firststring}: </strong>
        {$postal_cart}
    </p>
    <p>
        <strong>{#pn_contact_locality#|firststring}: </strong>
        {$city_cart}
    </p>
    <p>
        <strong>{#pn_contact_country#|firststring}: </strong>
        {$country_cart}
    </p>
</div>
<div class="clearfix"></div>
<div class="col-sm-12">
    <p>
        <strong>{#pn_contact_message#|firststring}: </strong><br />
        {$message_cart}
    </p>
</div>
<h2>{#coordonnees_liv#|ucfirst}</h2>
<div class="col-sm-12">
    {if $street_liv_cart != null OR $postal_liv_cart != null OR $city_liv_cart != null OR $country_liv_cart != null}
        <p>
            <strong>{#pn_contact_address#|firststring}: </strong>
            {$street_liv_cart}
        </p>
        <p>
            <strong>{#pn_contact_postal#|firststring}: </strong>
            {$postal_liv_cart}
        </p>
        <p>
            <strong>{#pn_contact_locality#|firststring}: </strong>
            {$city_liv_cart}
        </p>
        <p>
            <strong>{#pn_contact_country#|firststring}: </strong>
            {$country_liv_cart}
        </p>
    {else}
        <p>
            <strong>{#pn_contact_address#|firststring}: </strong>
            {$street_cart}
        </p>
        <p>
            <strong>{#pn_contact_postal#|firststring}: </strong>
            {$postal_cart}
        </p>
        <p>
            <strong>{#pn_contact_locality#|firststring}: </strong>
            {$city_cart}
        </p>
        <p>
            <strong>{#pn_contact_country#|firststring}: </strong>
            {$country_cart}
        </p>
    {/if}
</div>