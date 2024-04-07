$(document).ready( function () {
	dbsAjaxTab();
});

function dbsAjaxTab()
{
	$('.dbsAjaxTab').click(function()
	{
		if ($(this).hasClass('active'))
			return false;
		$('.dbsAjaxTab').removeClass('active');
		$(this).addClass('active');
		try
		{
			resAjax = $.ajax({
				type:"POST",
				url : $(this).attr('href')+'&rand=' + new Date().getTime(),
				headers: {"cache-control": "no-cache"},
				async: true,
				cache: false,
				data : {
					ajax : "1",
					token : token,
					controller : help_class_name,
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
	


