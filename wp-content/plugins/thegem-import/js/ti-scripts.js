(function($) {
	$(function() {

		$('.thegem-import-output').each(function() {
			var $importOutput = $(this);
			var $progressBlock = $('<div class="progress-import-block" />');
			var $importStatus = $('<div class="import-status" />').appendTo($progressBlock);
			var $progressBar = $('<div class="progress-bar" />').appendTo($progressBlock);
			var $progressBarLine = $('<div class="progress-bar-line" />').appendTo($progressBar);
			var $importMessages = $('<div class="import-messages" />').appendTo($progressBlock);
			var $importButtons = $('.import-button', $importOutput);

			$('.import-variants', $importOutput).accordion({
				collapsible: true,
				header: 'h3',
				heightStyle: 'content'
			});
			$('.import-tabs', $importOutput).tabs({});

			window.onbeforeunload = function(e) {
				if($importOutput.data('proccess'))
				return 1;
			}

			var files_list = [];

			var import_start = function(import_part, import_pack, callback) {
				if(import_part == 'full') {
					import_start('posts', import_pack, function() { import_start('media', import_pack); });
				} else {
					$.ajax({
						url: thegem_import_data.ajax_url,
						data: { action: 'thegem_import_files_list', import_part: import_part, import_pack: import_pack},
						method: 'POST',
						timeout: 30000
					}).done(function(msg) {
						msg = jQuery.parseJSON(msg);
						$importStatus.html('<p>'+msg.status_text+'</p>');
						$importMessages.html('<p>'+msg.message+'</p>');
						if(msg.status) {
							files_list = msg.files_list;
							import_file(0, import_pack, callback);
						} else {
							$importStatus.add($importMessages).addClass('failed');
							$importOutput.data('proccess', false);
						}
					}).fail(function() {
						$importStatus.remove();
						$importMessages.html('<p>Ajax error. Try again...</p>');
						$importOutput.data('proccess', false);
					});
				}
			}

			var import_file = function(num, import_pack, callback) {
				if(files_list[num] != undefined){
					$progressBarLine.css({
						width: 100*num/files_list.length + '%'
					});
					$progressBarLine.text(parseFloat(100*num/files_list.length).toFixed(1) + '%');
					$.ajax({
						url: ajaxurl,
						data: {action: 'thegem_import_file', import_pack: import_pack, filename: files_list[num]},
						method: 'POST',
						timeout: 50000
					}).done(function(msg) {
						msg = jQuery.parseJSON(msg);
						import_file(num+1, import_pack, callback);
					}).fail(function() {
						import_file(num+1, import_pack, callback);
					});
				} else {
					$progressBarLine.css({
						width: '100%'
					});
					$progressBarLine.text('100%');
					if($.isFunction(callback)) {
						callback();
					} else {
						$importStatus.remove();
						$importOutput.data('proccess', false);
						$importMessages.html('<p>All done. Have fun! ;)</p>');
					}
				}
			}

			$importButtons.click(function(e) {
				e.preventDefault();
				var $button = $(this);
				var import_part = $button.data('import-part');
				var import_pack = $button.data('import-pack');
				$importOutput.data('proccess', true)
				$('.thegem-import-prevent-message').remove();
				$button.closest('.import-variants').remove();
				$progressBlock.appendTo($importOutput);
				import_start(import_part, import_pack);
			});

		});


	});
})(jQuery);