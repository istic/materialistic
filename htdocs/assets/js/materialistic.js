MaterialIstic = {

}

$( document ).ready(function() {
	$('.datepicker').datepicker({
		'format' : 'yyyy-mm-dd'
	});

	$('table').tablecloth({
		sortable: true,
	});
});