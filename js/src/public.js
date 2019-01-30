const cartpay = (function($) {
    'use strict';
    return {
        cart: function () {
            $.jmRequest({
                handler: "ajax",
                url: '/'+iso+'/cartpay?action=add',
                method: 'POST',
                data: {
                    "id_product" : id_product,
                    "id_cart" : id_cart
                },
                //dataType: 'json',
                success: function (e) {
                    $.jmRequest.initbox(e, {
                            display: false
                        }
                    );
                    console.log(e);
                    //var result = JSON.parse(e);
                    /*$('#menuMainDeposit span:first-child').text(e.result.credit);

                    if (e.status === true) {
                        if(e.result.status === "open"){
                            console.log(e.result.credit);
                        }else if(e.result.status === "closed"){
                            Chatstack.disconnectChat();
                            Chatstack.closeChat();
                            stopTimer(timeout);
                        }

                    }else if(e.status === false){
                        stopTimer(timeout);
                        //console.log('stop timer');
                    }
                    if(e.result.modal === "open") {
                        $('#lowBalance').modal('show');
                    }*/
                }
            });
            return false;
        }
    }
})(jQuery);

$(document).ready(function(){

});