MaterialIstic = {

	prefillRewards: function(){
		selected = $($("option:selected", $('#rewards'))[0]);
		$('#backing_tier').val(selected.data('tier')) 
		$('#description').val(selected.text())
		$('#date_promised').val(selected.data('promised'))
		$('#value').val(selected.data('value'))
	},


	init : function(){
		if($('#rewards').length){
			$('#rewards').on('change', MaterialIstic.prefillRewards);
		}
	},
}

$( document ).ready(function() {
	$('.datepicker').datepicker({
		'format' : 'yyyy-mm-dd'
	});

	$('table').tablecloth({
		sortable: true,
	});

	MaterialIstic.init()
});