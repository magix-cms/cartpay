# cartpay
  Simple and powerfull eCommerce plugin for Magix CMS.
  
### version 

[![release](https://img.shields.io/github/release/magix-cms/cartpay.svg)](https://github.com/magix-cms/cartpay/releases/latest)

Authors
-------

* Gerits Aurelien (aurelien[at]magix-cms[point]com)

## Description
Ce plugin est dédié a Magix CMS.

## Installation
* Décompresser l'archive dans le dossier "plugins" de magix cms
* Connectez-vous dans l'administration de votre site internet
* Cliquer sur l'onglet plugins du menu déroulant pour sélectionner Cartpay.
* Une fois dans le plugin, laisser faire l'auto installation
* Il ne reste que la configuration du plugin pour correspondre avec vos données.

#### Modifications :
Ouvrez le fichier catalog/index.tpl de votre skin et ajouter les lignes suivantes : 

```smarty
{script src="/min/?g=form" concat=$concat type="javascript"}
    {capture name="formjs"}{strip}
        /min/?f=skin/{template}/js/form.min.js
    {/strip}{/capture}
    {script src=$smarty.capture.formjs concat=$concat type="javascript" load='async'}
    {script src="/min/?f=plugins/cartpay/js/public.js" concat=$concat type="javascript"}
    <script>
        $(function() {
            if (typeof cartProduct == "undefined")
            {
                console.log("cartProduct is not defined");
            }else{
                cartProduct.runFast('.add-to-cart');
            }
        });
    </script>
````
Ouvrez le fichier catalog/product.tpl de votre skin et ajouter les lignes suivantes : 
```smarty
<div class="col-xs-12 col-sm-3 col-md-2 col-lg-3">
   {if $product.price}
       <p class="price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
           <span itemprop="price">{$product.price}</span> <span itemprop="priceCurrency" content="EUR">€</span> <small>TTC</small>
       </p>
       <form action="" id="form_add_cart" method="post">
           <div id="quantity">
               <div class="form-group">
                   <label class="control-label" for="product-quantity">{#quantity_cart#|ucfirst}</label>
                   <input type="number" id="product_quantity" name="product_quantity" class="form-control" min="1" value="1" />
               </div>
               <input id="product_price" name="product_price" type="hidden" value="{$product.price}" />
           </div>
           <input type="hidden" name="idcatalog" value="{$product.idcatalog}"/>
           <input id="submit_cart" class="btn btn-block btn-box btn-flat btn-main-theme" type="submit" value="{#add_cart#|ucfirst}" />
       </form>
   {else}
       <form action="{geturl}/{getlang}/contact/" method="post">
           <fieldset>
               <p>Intéressé par {$product.name}&thinsp;?</p>
               <p>
                   <input type="hidden" name="moreinfo" value="{#contact_quotation#|ucfirst} : {$product.name}"/>
                   <button id="more-info" type="submit" class="btn btn-box btn-flat btn-main-theme btn-lg">{#contact_quotation#|ucfirst}</button>
               </p>
           </fieldset>
       </form>
   {/if}
</div>

{block name="foot" append}
   <script>
       var edit = "{$product.idcatalog}";
       var iso = '{getlang}';
       $(function() {
           var idform = 'form_add_cart';
           if (typeof cartProduct == "undefined")
           {
               console.log("cartProduct is not defined");
           }else{
               cartProduct.run("{$product.idcatalog}",idform);
           }
           $('#product_quantity').on('change',function(){
               var prix_unitaire = $('#product_price').val();
               var quantite_default = $('#product_quantity').val();
               //alert(quantite_default);
               var p = quantite_default*prix_unitaire,total;
               if(isNaN(p)){
                   total = 0;
               }else{
                   total = p.toFixed(2);
               }
               $('#total .prodprice').html(total + ' €');
           });
       });
   </script>
{/block}
````
Ouvrez le fichier section/menu/primary.tpl de votre skin et ajouter les lignes suivantes : 

```smarty
{include file="cartpay/brick/cart-header.tpl"}
````