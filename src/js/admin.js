jQuery(document).ready(function($) {
	
	// make the two list boxes connected sortables
	 $( "#csbsort1, #csbsort2" ).sortable({
			connectWith: ".connectedSortable",
			update: function() {
				var newList = $('#csbsort2 li').map(function() {
					// For each <li> in the list, the id as a text array
					return $(this).attr('id');
				}).get();
				
				$('.csb-services').val(newList);
			}
	}).disableSelection();
 
	// initial run to populate the hidden field correctly
	$('.csb-services').val($('#csbsort2 li').map(function() {
		return $(this).attr('id');
	}).get());
	
	$('.csb-image-set').change(function() {
		var imageset = $(this).val();
		var basePath = $(".csb-image-preview").attr('data-base');
		
		$.each($(".csb-services img"), function(index, item) {
				var fileSrc = $(item).attr('src');
				var oldSet = $(item).attr('data-image-set');
				var	newPath = fileSrc.replace(oldSet, imageset);
				console.log(newPath);
				$(item).attr('src', newPath);
				$(item).attr('data-image-set', imageset);
		});
	});
	  
});