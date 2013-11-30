var sourceCodeSnippetsList = {};
var continuation = '';
var scriptsRequest, chunksRequest, successorsRequests = [], scriptSaveRequests = [];
var isDownloading = false;

(function($){
     $.fn.extend({
        center: function () {
            return this.each(function() {
                var top = ($(window).height() - $(this).outerHeight()) / 2;
                var left = ($(window).width() - $(this).outerWidth()) / 2;
                $(this).css({position:'absolute', margin:0, top: (top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
            });
        }
    }); 
})(jQuery);
	
jQuery(document).ready(function() {
	var tutorialsUrl = 'https://www.touchdevelop.com/api/search'
	var scriptsUrl = 'https://www.touchdevelop.com/api/scripts'
	var data = {
		q: '%23docs',
		count: 100
	}
    
	$(window).bind('resize', function() {
        $('#output img').center();
    });
    
	jQuery('#download_chunks').click(function() {
		setLoadImage()
		$('#output img').center()
		getChunksData(tutorialsUrl, data)
	})

	jQuery('#download_scripts').click(function() {
		setLoadImage()
		$('#output img').center()
		getData(scriptsUrl, {})
	})
	jQuery('#download_libraries').click(function() {
		setLoadImage()
		$('#output img').center()
		$.ajax({url: "api/map_scripts_libraries.php",
            dataType: "text",
            success: function(text) {
                if(text != '0') {
                    $('#output').html('Libraries mapping has been successfully added. Number of added mappings: ' + text);
                } else {
                    $('#output').html('No libraries mapping added');
                }
            }
        });
	})
})

function clearRequests () {
	if(!$.isEmptyObject(successorsRequests)) {
		successorsRequests = successorsRequests.filter(function(item) {return item.readyState != 4} )
	}
	if(!$.isEmptyObject(scriptSaveRequests)) {
		scriptSaveRequests = scriptSaveRequests.filter(function(item) {return item.readyState != 4} )
	}
}

$(document).keydown(function(event) {
	if(event.keyCode == 27) { // ESC 
		$('#output').html('Scripts download has been stopped');
		if(scriptsRequest) {
			isDownloading = false;
			scriptsRequest.abort();
		}
		clearRequests()
		if(!$.isEmptyObject(successorsRequests)) {
			$.each(successorsRequests, function(i, request) {
				request.abort();
			})
		}
		if(!$.isEmptyObject(scriptSaveRequests)) {
			$.each(scriptSaveRequests, function(i, request) {
				request.abort();
			})
		}
	}
});	

function setLoadImage() {
	$('#output').html('<img src="img/load.gif" />')
}

function getChunksData(url, data) {
	if ( continuation && !isDownloading && (continuation > 0 || continuation != '' )) {
		data['continuation'] = continuation;
	}
	isDownloading = true;
	chunksRequest = $.ajax({url: url, 
		dataType: "json",
		data: data,
		success: function(json) {
			$.each(json['items'], function(i, item) { 
				var scriptid = item['id']
				scriptSaveRequests.push($.ajax({
					url: 'https://www.touchdevelop.com/api/' + scriptid + '/successors',
					dataType: "json",
					success: function(json) {
						if(json.items.length == 0) { // there are no successors
							clearRequests()
							getScriptSource(scriptid)
						}
					}
				}))
			})
			//console.log('key: ' + key + ", value: " + value)
			if(data['continuation']) {
				continuation = data['continuation'];
				console.log("fetching next " + data['continuation'] + " items... ")
			}
			if(json['continuation'] > 0) {
				data['continuation'] = json['continuation']
				getChunksData(url, data)
			} else {
				 $(document).ajaxStop(function () { // wait for all ajax calls to complete
					 $('#output').html('')
					 $('#output').append('<ul></ul>')
					 $.each(sourceCodeSnippetsList, function (key, val){
						$('#output ul').append('<li><a href="http://touchdevelop.com/api/' + key+ '">' + key + '</a>:</li>')
						$.each(val, function(i, chunk) {
							$('#output ul').append('<div class="code_snippet">' + chunk.replace('\n', '<br />') + '</div>')
						}) 
					 })
					 isDownloading = false;
					//console.log(sourceCodeSnippetsList)
				});
			}
		}
	});
}

function getData(url, data) {
	if ( continuation && !isDownloading && (continuation > 0 || continuation != '' )) {
		data['continuation'] = continuation;
	}
	isDownloading = true;
	scriptsRequest = $.ajax({url: url, 
		dataType: "json",
		data: data,
		success: function(json) {
			$.each(json['items'], function(i, item) { 
				var scriptid = item['id']
				clearRequests()
				successorsRequests.push($.ajax({
					url: 'https://www.touchdevelop.com/api/' + scriptid + '/successors',
					dataType: "json",
					success: function(json) {
						if(json.items.length == 0) { // there are no successors
							clearRequests()
							scriptSaveRequests.push($.ajax({ url: "api/save_script.php",
								data: {id: scriptid},
								type: 'post',
								dataType: 'json',
								success: function(json) {
									var results = d.toLocaleTimeString() + ": "; 
									results += "Scripts: " + json.scripts + "; ";
									results += "Authors: " + json.authors + "; "
									results += "Features: " + json.features + "; "
									results += "Tutorials: " + json.tutorials + "; "
									results += "Hashtags: " + json.hashtags + "; "
									results += "Libraries: " + json.libraries + "; "
									console.log(results);
								}
							}))
						}
					}
				}))
			})
			//console.log('key: ' + key + ", value: " + value)
			if(data['continuation']) {
				console.log("fetching next " + data['continuation'] + " items... ")
			}
			if(json['continuation'] > 0 || json['continuation'] != '' ) {
				continuation = data['continuation'];
				data['continuation'] = json['continuation'];
				getData(url, data)
			} else {
				$(document).ajaxStop(function () { // wait for all ajax calls to complete
					 $('#output').html('Scripts have been successfully downloaded')
					 isDownloading = false;
				});
			}
		}
	});
}

function getScriptUrlById(id) {
	return 'https://www.touchdevelop.com/api/' + id + '/text'
}

function getScriptSource(scriptid) {
	var url = getScriptUrlById(scriptid)
	request = $.ajax({url: url,
		dataType: "text",
		success: function(source) {
			console.log(url)
			var lines = [], chunk = [], chunks = [], chunkCount = 0, chunkFlag = false, mainFlag = false;
			lines = source.split('\n')
			$.each(lines, function(i, line) {
				line = line.replace(/^\s{2}/, '') // get rid of first two empty characters
				if(line.indexOf('//') != 0) {
					if(mainFlag && line == '}') {
						mainFlag = false
						chunkFlag = false
					}
					if(mainFlag) {
						chunkFlag = true
						chunk.push(line)
					} else if(line == 'action main() {') {
						mainFlag = true
					}
				} else if(chunkFlag) {
					chunks.push(chunk.join('\n'))
					chunk = []
					chunkFlag = false
				} 
			})
			if(chunks.length > 0) {
				sourceCodeSnippetsList[scriptid] = chunks
				$.each(chunks, function(i, chunk_source) {
					scriptSaveRequests.push($.ajax({ url: "api/save_chunk.php",
						data: {id: scriptid, chunk: chunk_source},
						type: 'post'
					}))
				})
			}
			/*$.each(chunks, function(i, chunk) {
				console.log("	" + i + ": " + chunk)
			})*/
		}
	})
}