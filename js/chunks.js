(function($){
	$(function(){
	
	    // Hide the huge textarea
		$('#chunks').on('mousedown', '.sort-handle', function(e){
			$('.chunky').hide();
		});
		
		// Attach sortable to the page chunks ul
		$("#chunks").sortable({
			opacity:0.3,
			handle: ".sort-handle",
			placeholder: 'sort-placeholder',
			forcePlaceholderSize: true,
			items: 'li',
			cursor:"move",
			start: function () {
				$('.wysiwyg-advanced, .wysiwyg-simple').each(function() {
					$(this).ckeditorGet().destroy();
				});
			},
			stop: function(ev,ui){
				$('.chunky').show();
				pyro.init_ckeditor();
			}
		});
		
		// add another page chunk
		$('a.add-chunk').live('click', function(e){
			e.preventDefault();

			// The date in hexdec
			key = Number(new Date()).toString(16).substr(-5, 5);

			$('#chunks').append('<li class="chunk">' +
				'<input class="label" type="text" name="chunk_slug[' + key + ']" value="' + key + '"/>' +
				'<input class="label" type="text" name="chunk_class[' + key + ']" />' +
				'<select name="chunk_type[' + key + ']">' +
				'<option value="html">html</option>' +
				'<option value="markdown">markdown</option>' +
				'<option value="wysiwyg-simple">wysiwyg-simple</option>' +
				'<option selected="selected" value="wysiwyg-advanced">wysiwyg-advanced</option>' +
				'</select>' +
				'<div class="alignright">' +
				'<a href="javascript:void(0)" class="remove-chunk btn red">' + pyro.lang.remove + '</a>' +
				'<span class="sort-handle"></span>' +
				'</div><br style="clear:both" />' +
				'<span class="chunky"><textarea id="' + key + '" class="pages wysiwyg-advanced" rows="20" style="width:100%" name="chunk_body[' + key + ']"></textarea>' +
				'</span></li>');

			// initialize the editor using the view from fragments/wysiwyg.php
			pyro.init_ckeditor();
			$("#chunks").sortable("refresh");

			// Update Chosen
			pyro.chosen();
		});

		$('a.remove-chunk').live('click', function(e) {
			e.preventDefault();

			var removemsg = $(this).attr('title');

			if (confirm(removemsg || pyro.lang.dialog_message))
			{
				$(this).closest('li.chunk').slideUp('slow', function(){ $(this).remove(); });
				if ($('#page-content').find('li.chunk').length < 2) {
				}
			}
		});

		$('select[name^=chunk_type]').live('change', function() {
			chunk = $(this).closest('li.chunk');
			textarea = $('textarea', chunk);

			// Destroy existing WYSIWYG instance
			if (textarea.hasClass('wysiwyg-simple') || textarea.hasClass('wysiwyg-advanced'))
			{
				textarea.removeClass('wysiwyg-simple');
				textarea.removeClass('wysiwyg-advanced');

				var instance = CKEDITOR.instances[textarea.attr('id')];
				instance && instance.destroy();
			}

			// Set up the new instance
			textarea.addClass(this.value);

			pyro.init_ckeditor();
		});
	    
	});
})(jQuery);