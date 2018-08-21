{script src="/{baseadmin}/min/?f=plugins/{$pluginName}/js/admin.js" concat={$concat} type="javascript"}
<script type="text/javascript">
    var edit = "{$smarty.get.edit}";
    $(function(){
        if (typeof MC_profil == "undefined"){
            console.log("MC_profil is not defined");
        }else{
            {if $smarty.get.tab eq "config"}
                MC_profil.runConfig(baseadmin,'config');
            {else}
                {if $smarty.get.edit}
                    MC_profil.runEdit(baseadmin,null,edit);
                {else}
                    MC_profil.run(baseadmin);
                {/if}
            {/if}
        }
    });
</script>