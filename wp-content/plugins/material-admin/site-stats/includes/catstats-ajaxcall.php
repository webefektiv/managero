<div class="mtrl-wid-admin">

<script>		
	jQuery(document).ready(function(){
			function cat_stats_by_year(){
				jQuery.ajax(
						{
					type: 'POST',
					url: mtrlwid_ajax.mtrlwid_ajaxurl,
					data: {"action": "mtrlwid_catstats"},
					success: function(data)
							{
								jQuery(".mtrl_catstats").html(data);
							}
				});
			}
			cat_stats_by_year();
			setInterval(function(){
				cat_stats_by_year();	
			}, 300000)

	        jQuery(document).on('click', "#catstats_wp_dashboard .ui-sortable-handle", function () {
	                            if(!jQuery(this).parent().hasClass("closed")){
						jQuery(".mtrl_catstats").html("Loading...");
						cat_stats_by_year();
						//console.log("recall");
	                            }
	        });
	});
			
</script>


<div class="mtrl_catstats">
	
</div>


</div>