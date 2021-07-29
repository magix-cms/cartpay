var cartpay = (function($, window, document, undefined){
    'use strict';

    return {
        // -- Fonction public
        run : function(){
            $('#bank_wire').on('change', function() {
                if($(this).is(':checked')) {
                    $('#bank').collapse('show');
                    $('#bank_account').rules('add',{required: true});
                }
                else {
                    $('#bank').collapse('hide');
                    $('#bank_account').rules('remove');
                }
            });
            /*$("#edit_config").validate({
                rules: {
                    "acConfig[quotation_enabled]": {
                        require_from_group: [1, ".type_order"]
                    },
                    "acConfig[order_enabled]": {
                        require_from_group: [1, ".type_order"]
                    },
                }
            });*/
            $("#quotation_enabled").rules('add',{require_from_group: [1, ".type_order"]});
            $("#order_enabled").rules('add',{require_from_group: [1, ".type_order"]});
        }
    };
})(jQuery, window, document);