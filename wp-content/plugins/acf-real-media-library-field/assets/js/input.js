/* global jQuery acf */
(function($){
	function fieldInit() {
		$("select.select2").filter(function() {
			return !$(this).data("select2");
		}).select2();
	}
	
	if (acf.add_action) {
		acf.add_action('ready append', fieldInit);
	}else{
		$(document).on('acf/setup_fields', fieldInit);
	}
})(jQuery);