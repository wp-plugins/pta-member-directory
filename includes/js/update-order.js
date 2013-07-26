jQuery(document).ready(function($) {
	$('.pta-categories').sortable({
		items: '.list_item',
		opacity: 0.6,
		cursor: 'move',
		axis: 'y',
		update: function() {
			var order = $(this).sortable('serialize') + '&action=pta_directory_update_order';
			$.post(ajaxurl, order, function(response) {

			});
		}
	});
});