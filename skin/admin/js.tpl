{script src="/{baseadmin}/min/?f=plugins/{$pluginName}/js/bootstrap2-toggle.min.js,plugins/{$pluginName}/js/admin.js" concat={$concat} type="javascript"}
<script type="text/javascript">
    $(function(){
        if (typeof MC_cartPay == "undefined"){
            console.log("MC_cartPay is not defined");
        }else{
            {if $smarty.get.tab eq "config"}
            MC_cartPay.runConfig(baseadmin,'config');
            {elseif $smarty.get.tab eq "tva"}
            MC_cartPay.runTva(baseadmin,'tva');
            {else}
            MC_cartPay.run();
            {/if}
        }
    });
</script>