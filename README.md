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
Ouvrez le fichier layout.tpl de votre skin et ajouter la ligne suivant au debut :
```smarty
{strip}
{cartpay_data}
{/strip}
````

Ouvrez le fichier header.tpl de votre skin et ajouter la ligne suivant à la ligne 36 :
```smarty
<div class="nav-cart">
    <a href="#" id="shopping-cart-btn"><i class="material-icons ico ico-shopping_cart"></i>&nbsp;{#my_cart#}&nbsp;<span class="badge cart-total-items">{$cart.nb_items}</span></a>
    <div id="shopping-float-cart">
        {include file="cartpay/brick/float-cart.tpl"}
    </div>
</div>
````
Ouvrez le fichier catalog/product.tpl de votre skin et ajouter les lignes suivantes a la ligne 43 (juste après le prix) :
```smarty
{include file="cartpay/brick/add-to-cart.tpl"}
````

Ajouter a la fin du fichier :

```smarty
{block name="scripts"}
{$jquery = true}
{$js_files = [
'group' => [
'form'
],
'normal' => [
],
'defer' => [
"/skin/{$theme}/js/{if $setting.mode === 'dev'}src/{/if}form{if !$setting.mode === 'dev'}.min{/if}.js",
"/skin/{$theme}/js/vendor/localization/messages_{$lang}.js"
]
]}
{if {$lang} !== "en"}{$js_files['defer'][] = "/libjs/vendor/localization/messages_{$lang}.js"}{/if}
{/block}
````

Ouvrez le fichier layout.tpl de votre skin et ajouter les lignes suivantes: 
#### JS
```smarty
"/skin/{$theme}/js/{if $setting.mode === 'dev'}src/{/if}cart{if $setting.mode !== 'dev'}.min{/if}.js"
````
#### CSS
```smarty
"/skin/{$theme}/css/cartpay{if $setting.mode !== 'dev'}.min{/if}.css"
````