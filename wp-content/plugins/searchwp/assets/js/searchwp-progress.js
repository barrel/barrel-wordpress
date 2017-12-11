jQuery(document).ready(function($){
	var progress = [-1];
	var waiting = [];

	function getProgress() {
		var data = {
			action: 'swp_progress',
			nonce: ajax_object.nonce,
			time: new Date().getTime()
		};

		// retreive the indexer progress and whether it's waiting due to server load
		$.post(ajax_object.ajax_url + '?' + data.time, data, function(response) {

			if(response&&response!==''){
				// response returned as JSON, decode it
				try {
					response = $.parseJSON(response);
					progress.push(parseFloat(response.progress));

					// check to see if the last 10 progress updates were the same
					if(progress.length>10) {
						var recentProgressUpdates = progress.slice(progress.length-10);
						var uniqueProgressPoints = _.uniq(recentProgressUpdates, false);

						// if indexing isn't complete, issue a jumpstart
						if(uniqueProgressPoints.length==1&&uniqueProgressPoints[0]!==100) {
							$.get('options-general.php?page=searchwp&swpjumpstart&' + data.time, function(data){});
						}

					}
                    $('.swp-label > span').text(response.progress+'%');
                    $('.swp-progress-bar').css('width',response.progress+'%');
					if(response.progress==100) {
						// indexing is complete, hide the progress bar
						setTimeout(function(){
							$('.swp-in-progress')
                                .addClass('swp-in-progress-complete')
                                .removeClass('swp-waiting');
						},1000);
					} else {
						// update progress bar and label
						$('.swp-in-progress').removeClass('swp-in-progress-done').removeClass('swp-in-progress-complete');

						// if the indexer is waiting, call that out so users don't think it's stalled
						if(response.waiting){
							waiting.push(true);
							$('.swp-in-progress').addClass('swp-waiting');
						}else{
							// we don't want the 'waiting' message to toggle too much, so we're going to wait
							// for three cycles of NOT waiting before actually hiding it
							if(waiting.length>3){
								waiting = [];
								$('.swp-in-progress').removeClass('swp-waiting');
							}
						}
					}
				}catch(e){
					// malformed JSON
				}
			}
			setTimeout(function(){
				getProgress();
			},5000);
		});
	}
	getProgress();
});
