{widget_cartpay_session}
<div id="cart" class="block-cartpay-header">
    {widget_cart_nbr_items}
    {if $collectionCart.nbr_items > 0}
        {$prix_total = "{$smarty.config.total_cart|ucfirst} : "}
    {/if}
    <div class="dropdown" role="menu">
        <a class="dropdown-toggle" type="button" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="true" role="button">
            <span class="fa fa-shopping-cart"></span>
            <span class="cart-resume-nbr-items{if !$collectionCart.nbr_items} hide{/if}">
                {$collectionCart.nbr_items}
            </span>
            <small class="empty-cart{if $collectionCart.nbr_items} hide{/if}">{#empty_cart_label#|upper}</small>
        </a>
        <div id="nav-user" class="dropdown-menu" aria-labelledby="menu-user" role="menu">
            <p>
                <span class="cart-resume-nbr-items">{$collectionCart.nbr_items}</span>
                {if $collectionCart.nbr_items > 1}{#product_name_plural#} {else}{#product_name#} {/if}
                {#in_your_cart#}
            </p>
            <hr />
            <p class="pull-right">
                <span class='cart-resume-price-items'>{$collectionCart.price_items}</span>&thinsp;€
            </p>
            {*if $smarty.session.idprofil && $smarty.session.keyuniqid_pr*}
            <div class="btn-cart{if !$collectionCart.nbr_items} hide{/if}">
                <hr />
                <a class="btn btn-block btn-box btn-flat btn-dark-theme" href="{geturl}/{getlang}/cartpay/" data-toggle="tooltip" data-placement="bottom" title="{#go_to_cartpay#|ucfirst}">
                    {#go_to_cartpay#|ucfirst}
                </a>
            </div>
            {*/if*}
        </div>
    </div>
</div>