{widget_cartpay_session}
<section id="cartpay" class="hidden-xs col-sm-4 col-md-3 col-lg-3">
<div class="block-cartpay-header">
    <h3><span class="fa fa-shopping-cart"></span> {#my_cart#|ucfirst}</h3>
    {* Si le panier est vide *}
    {*<p>votre panier ne contient aucun article.</p>*}
    {* Sinon *}
    {widget_cart_nbr_items}
    <table class="table table-condensed">
        <tr>
            <td>{#nbre_art#|ucfirst}</td>
            <td id="cart-resume-nbr-items">{$collectionCart.nbr_items}</td>
        </tr>
        <tr>
            <td>{#total_cart#|ucfirst}</td>
            <td id="cart-resume-price-items">{$collectionCart.price_items}&thinsp;€</td>
        </tr>
    </table>
    {* fin condition *}
    <a id="btn-cart" href="{geturl}/{getlang}/cartpay/" title="{#go_to_cartpay#|ucfirst}" class="btn btn-sm btn-main-theme pull-right">{#go_to_cartpay#|ucfirst}</a>
</div>
</section>