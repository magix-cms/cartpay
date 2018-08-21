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
        }
    };
})(jQuery, window, document);