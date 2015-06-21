function pbrf_ajax(blank) {
    jQuery("#ajax_running").show();
    jQuery.ajax({
        url: '/modules/blockpbrf/helper.php',
        type: 'post',
        data: 'action=pbrf&blank=' + blank + '&order_id=' + jQuery('#pbrf_order_id').val() + '&cen=' + jQuery('#cen').val() + '&nalojka=' + jQuery('#nalojka').val(),
        success: function (response) {
            if (response) {
                window.open(response, '_blank');
                jQuery("#ajax_running").hide();
                //jQuery("#PbrfResponce").html(response);
                //jQuery("#PbrfResponce").fadeIn();

            }
        }
    });
    // jQuery("#ajax_running").hide();

}
function pbrf(order_id) {
    jQuery('#PbrfMessage').fadeIn();
}
jQuery(document).ready(function () {
    var order_total = parseFloat(jQuery('tr#total_order>td.amount').text().replace(",", "."));
    jQuery('div.well>a.btn').before('<input type="button" class="pbrf_button" name="pbrf" title="Распечатать бланки Почты России" onclick="jQuery(\'#cen\').val(\'' + order_total + '\');jQuery(\'#nalojka\').val(\'' + order_total + '\');jQuery(\'#span_pbrf\').html(\'Заказ № ' + id_order + '\');jQuery(\'#pbrf_order_id\').val(' + id_order + ');jQuery(\'#PbrfMessage\').fadeIn();" /> &nbsp;&nbsp;&nbsp;');


    el = jQuery(".container-command-top-spacing");
    
    el.after("<div id=\"PbrfMessage\" class='onboarding'></div><div id=\"PbrfResponce\"></div>")
    jQuery("#PbrfMessage").load("/modules/blockpbrf/views/tmpl/pbrf.html");
    
});