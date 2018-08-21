<ul class="nav nav-tabs clearfix">
    <li{if !$smarty.get.tab} class="active"{/if}>
        <a href="/admin/plugins.php?name=profil">Liste des membres</a>
    </li>
    <li{if $smarty.get.tab eq "config"} class="active"{/if}>
        <a href="/admin/plugins.php?name=profil&amp;tab=config">Configuration</a>
    </li>
</ul>