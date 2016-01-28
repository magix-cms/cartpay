<ul class="nav nav-tabs clearfix">
    <li{if !$smarty.get.tab} class="active"{/if}>
        <a href="/admin/plugins.php?name=cartpay">Commandes</a>
    </li>
    <li{if $smarty.get.tab eq "config"} class="active"{/if}>
        <a href="/admin/plugins.php?name=cartpay&amp;tab=config">Configuration</a>
    </li>
    <li{if $smarty.get.tab eq 'tva'} class="active"{/if}>
        <a href="/admin/plugins.php?name=cartpay&amp;tab=tva">TVA</a>
    </li>
    <li{if $smarty.get.tab eq 'about'} class="active"{/if}>
        <a href="/{baseadmin}/plugins.php?name=cartpay&amp;tab=about">About</a>
    </li>
</ul>