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
	el=jQuery("#form-order");
	if(el.length>0){
		jQuery('input.noborder').each(function () {
			var order_total=parseFloat(jQuery(this).parent().next('td').next('td').next('td').next('td').next('td').next('td').text().replace(",", "."));
			jQuery(this).after('<input type="button" class="pbrf_button" name="pbrf" title="Распечатать бланки Почты России" onclick="jQuery(\'#cen\').val(\''+order_total+'\');jQuery(\'#nalojka\').val(\''+order_total+'\');jQuery(\'#span_pbrf\').html(\'Заказ № '+jQuery(this).val()+'\');jQuery(\'#pbrf_order_id\').val('+jQuery(this).val()+');jQuery(\'#PbrfMessage\').fadeIn();" /> ');
		})
	} else {
		jQuery('tbody>.row_hover>td:first-child').each(function () {
			var order_total=parseFloat(jQuery(this).next('td').next('td').next('td').next('td').next('td').text().replace(",", "."));
			var order_id=parseInt(jQuery(this).next('td').text(),10);
			jQuery(this).html(jQuery(this).html()+'<input type="button" class="pbrf_button" name="pbrf" title="Распечатать бланки Почты России" onclick="jQuery(\'#cen\').val(\''+order_total+'\');jQuery(\'#nalojka\').val(\''+order_total+'\');jQuery(\'#span_pbrf\').html(\'Заказ № '+order_id+'\');jQuery(\'#pbrf_order_id\').val('+order_id+');jQuery(\'#PbrfMessage\').fadeIn();" /> ');
		})
	
		el=jQuery("form.form");
		
	}
    el.after("<div id=\"PbrfMessage\" class='onboarding'></div><div id=\"PbrfResponce\"></div>")
    jQuery("#PbrfMessage").load("/modules/blockpbrf/views/tmpl/pbrf.html");
    
});