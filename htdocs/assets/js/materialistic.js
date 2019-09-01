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
		$('#bookmarklet').click(MaterialIstic.bookmarklet);
		autosize(document.querySelector('#description'));
	},

	bookmarklet : function(){
		window.alert("Can't click this!\nDrag it into your bookmarks to use it to send Crowdfunder projects into Materialistic");
		return false;
	},

	autogrow : function(e) {
		var ta = document.querySelector('#description');
		autosize.update(ta);
	}
}

$( document ).ready(function() {
	$('table').tablecloth({
		sortable: true,
	});

	$("#description").bind('change keyup', MaterialIstic.autogrow);
	
	// console.log($("#description"));

	MaterialIstic.init()
});
