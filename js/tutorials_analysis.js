var sourceCodeSnippetsList = {};
var continuation = '';
var scriptsRequest, chunksRequest, successorsRequests = [], scriptSaveRequests = [];
var isDownloading = false;
var missing_scripts_count = 0;
var waitingOutputFlag = false;

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
		getChunksData(tutorialsUrl, data);
	})

	jQuery('#download_scripts').click(function() {
		setLoadImage()
		$('#output img').center()
		getData(scriptsUrl, {})
	})
    
	jQuery('#download_missing_scripts').click(function() {
		setLoadImage()
		$('#output img').center()
        $.ajax({url: "api/get_count_of_missing_scripts.php",
            dataType: 'text',
            success: function(count) {
                missing_scripts_count = parseInt(count);
                console.log("Total number of missing scripts: " + missing_scripts_count);
                var url = "api/get_missing_scripts.php";
                getMissingScripts(url, {})
            }
        });
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

function createCookie(name, value, expires, path, domain) {
  var cookie = name + "=" + escape(value) + ";";
 
  if (expires) {
    // If it's a date
    if(expires instanceof Date) {
      // If it isn't a valid date
      if (isNaN(expires.getTime()))
       expires = new Date();
    }
    else
      expires = new Date(new Date().getTime() + parseInt(expires) * 1000 * 60 * 60 * 24);
 
    cookie += "expires=" + expires.toGMTString() + ";";
  }
 
  if (path)
    cookie += "path=" + path + ";";
  if (domain)
    cookie += "domain=" + domain + ";";
 
  document.cookie = cookie;
}

function getCookie(name) {
  var regexp = new RegExp("(?:^" + name + "|;\s*"+ name + ")=(.*?)(?:;|$)", "g");
  var result = regexp.exec(document.cookie);
  return (result === null) ? null : result[1];
}

function clearRequests () {
	if(!$.isEmptyObject(successorsRequests)) {
		successorsRequests = successorsRequests.filter(function(item) {return item.readyState != 4 && item.readyState != 0} )
	}
	if(!$.isEmptyObject(scriptSaveRequests)) {
		scriptSaveRequests = scriptSaveRequests.filter(function(item) {return item.readyState != 4 && item.readyState != 0} )
	}
}

$(document).keydown(function(event) {
	if(event.keyCode == 27) { // ESC 
		$('#output').html('Scripts download has been stopped');
		if(scriptsRequest) {
			isDownloading = false;
			scriptsRequest.abort();
		}
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
		clearRequests()
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

function getMissingScripts(url, data) {
	if ( continuation && !isDownloading && (continuation > 0 || continuation != '' )) {
		data['continuation'] = continuation;
	}
	isDownloading = true;
    
    scriptsRequest = $.ajax({url: url, 
        dataType: "json",
        data: data,
        success: function(json) {
            $.each(json['items'], function(i, item) { 
                console.log("Saving script " + item + " ...");
                var scriptid = item;

                scriptSaveRequests.push($.ajax({ url: "api/save_script.php",
                    data: {id: scriptid},
                    type: 'post',
                    dataType: 'json',
                    success: function(json) {
                        var results = new Date().toLocaleTimeString() + ": "; 
                        results += "Scripts: " + json.scripts + "; ";
                        results += "Authors: " + json.authors + "; "
                        results += "Features: " + json.features + "; "
                        results += "Tutorials: " + json.tutorials + "; "
                        results += "Hashtags: " + json.hashtags + "; "
                        results += "Libraries: " + json.libraries + "; "
                        console.log(results);
                    }
                }));
            });
            waitForScriptsToFinish();
            //console.log('key: ' + key + ", value: " + value)
            if(data['continuation']) {
                console.log("fetching next " + data['continuation'] + " items out of " + missing_scripts_count + "... ")
            }
            if((json['continuation'] > 0 && json['continuation'] < missing_scripts_count) ) {
                continuation = data['continuation'];
                data['continuation'] = json['continuation'];
                if(isDownloading) {
                    getMissingScripts(url, data);
                }
            } else {
                $(document).ajaxStop(function () { // wait for all ajax calls to complete
                     $('#output').html('Scripts have been successfully downloaded')
                     isDownloading = false;
                });
            }
        }
    });
}

function waitForScriptsToFinish() {
    if(scriptSaveRequests.length == 0 && successorsRequests.length == 0) {
        return true;
    } else {
        var timeout = 10000;
        clearRequests();
        if(waitingOutputFlag) {
            console.log("Waiting " + (timeout / 1000) + " sec more for " + 
                (scriptSaveRequests.length + successorsRequests.length) + " scripts to finish...");
        }
        setTimeout(waitForScriptsToFinish, timeout);
    }
}

function getData(url, data) {
	if ( continuation && !isDownloading && (continuation > 0 || continuation != '' )) {
		data['continuation'] = continuation;
	}
	isDownloading = true;
    //waitForSaveScriptsToFinish();
	scriptsRequest = $.ajax({url: url, 
		dataType: "json",
		data: data,
		success: function(json) {
			$.each(json['items'], function(i, item) { 
				var scriptid = item['id']
//                if(scriptSaveRequests.length > 80) {
//                    setTimeout(function() {console.log("Resumed scripts execution after 0.5s pause")}, 500 );
//                }
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
                                    var results = new Date().toLocaleTimeString() + ": "; 
                                    results += "";
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
//                bake_cookie('continuation', continuation);
                createCookie('continuation', continuation);
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
					} else if(/action (.*)main\(\) {/.test(line)) {
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