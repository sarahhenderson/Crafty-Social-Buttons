jQuery(document).ready(function($) {
	
	// make the two list boxes connected sortables
	 $( "#csbsort1, #csbsort2" ).sortable({
			connectWith: ".connectedSortable",
			update: function() {
                var newList;
                newList = $('#csbsort2 li').map(function () {
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

		$.each($(".csb-services img"), function(index, item) {

            var url = $(item).attr('data-url');
            var altUrl = $(item).attr('data-alt-url');
            var filename = $(item).attr('data-filename');

            var newPath = url + imageset + '/' + filename;
            var altPath = altUrl + imageset + '/' + filename;

            $(item).attr('src', newPath);
            $(item).attr('data-image-set', imageset);

            $.ajax(newPath, { method: 'get', // make sure the file exists
                error: function(jqXHR, status, error) {
                    $(item).attr('src', altPath);
                }
            });
		});
	});

	$('#share_image_size').bind('input', function() {
		var imagesize = $(this).val();
		
		$.each($(".csb-services img"), function(index, item) {
				$(item).attr('width', imagesize);
				$(item).attr('height', imagesize);
		});
	});

	$('#link_image_size').bind('input', function() {
		var imagesize = $(this).val();
		
		$.each($(".csb-services img"), function(index, item) {
				$(item).attr('width', imagesize);
				$(item).attr('height', imagesize);
		});
	});

	  
});