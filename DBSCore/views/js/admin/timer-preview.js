$(document).ready( function () {
	dbsAjaxTab();
});

function dbsAjaxTab()
{
	$('.dbsAjaxTab').click(function()
	{
		if ($(this).hasClass('active'))
			return false;
		$('.dbsTabLink').removeClass('active');
		$(this).addClass('active');
		try
		{
			var requestTabURL = $(this).attr('href')+'&rand=' + new Date().getTime(),
			resAjax = $.ajax({
				type:"POST",
				url : $(this).attr('href')+'&rand=' + new Date().getTime(),
				headers: {"cache-control": "no-cache"},
				async: true,
				cache: false,
				data : {
					ajax : "1",
					token : DBSgetToken(DBSgetURLParameter(requestTabURL, 'token')), //token,
					controller : DBSgetController(DBSgetURLParameter(requestTabURL, 'controller')), //help_class_name,
					action : "DBSActionTabs"
				},
				beforeSend: function(xhr){
					$('#moduleContainer').html('<div class="dbs_loading"></div>');
				},
				success: function(data, status, request){
					if (request.getResponseHeader('Login') === 'true')
						return window.location.reload();

					$('#moduleContainer').html(data);
					$('.dropdown-toggle').dropdown();
					$('.help-tooltip').tooltip();
				}
			});
		}
		catch(e){}
		return false;
	});
}
	
function DBSgetURLParameter(url, name) {
	// var requestTabURL = $(this).attr('href')+'&rand=' + new Date().getTime(),
	//DBSgetURLParameter(requestTabURL, 'token') // use
   return (RegExp(name + '=' + '(.+?)(&|$)').exec(url)||[,null])[1];
}
function DBSgetToken(urlToken) {
	if( urlToken )
		return urlToken;
	else
		return token;
}
function DBSgetController(urlController) {
	if( urlController )
		return urlController;
	else
		return help_class_name;
}

