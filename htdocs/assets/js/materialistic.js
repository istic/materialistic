MaterialIstic = {

	prefillRewards: function(){
		selected = $($("option:selected", $('#rewards'))[0]);
		$('#backing_tier').val(selected.data('tier')) 
		$('#description').val(selected.data('desc')).change();
		$('#date_promised').val(selected.data('promised'))
		$('#value').val(selected.data('value'));

	},


	init : function(){
		if($('#rewards').length){
			$('#rewards').on('change', MaterialIstic.prefillRewards);
		}
	},

	autogrow : function(e) {
		console.log("Hello");
		$(this).height(0);
	    while($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth"))) {
	        $(this).height($(this).height()+1);
	    };
	}
}

$( document ).ready(function() {
	$('table').tablecloth({
		sortable: true,
	});

	$("#description").bind('change keyup', MaterialIstic.autogrow);
	
	console.log($("#description"));

	MaterialIstic.init()
});
