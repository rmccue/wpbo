jQuery(document).ready( function($) {
	$("#wpbo-date").datepicker( { dateFormat: 'yy-mm-dd' } );

	$('.wpbo-delete').click(function () {
		row = $(this).parent().parent();
		var id = row.attr("id").replace("wpbo-person-", '');
		var data = {
			action: "wpbo_remove",
			"wpbo-nonce": $("#wpbo-remove-nonce").val(),
			"wpbo-id": $("#post_ID").val(),
			"wpbo-name": id
		};
		jQuery.post(ajaxurl, data, function(response) {
			if(response == "removed") {
				row.fadeOut(function () { row.remove(); });
			}
			else
				alert("Error!");
		});
	});
});