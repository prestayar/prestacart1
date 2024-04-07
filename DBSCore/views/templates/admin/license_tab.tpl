{if !empty($flash_message)  }
	<div class="row">
		<div class="col-sm-12">
			<div class="alert {if isset($flash_message['css-alert'])}{$flash_message['css-alert']}{else}alert-info{/if} ">
				<button type="button" class="close" data-dismiss="alert">×</button>
				{$flash_message['message']}
			</div>
		</div>
	</div>
{/if}

<form id="dbs-ajax-form" class="defaultForm form-horizontal" method="post" enctype="multipart/form-data" novalidate="">
	<input type="hidden" id="dbs-request-tab" name="request_tab" value="{$request_tab}">
	<div class="panel" id="fieldset_0">
		<div class="panel-heading">
			درج اجازه نامه معتبر
		</div>
		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3 required">
					کلید لایسنس
				</label>
				<div class="col-lg-9 ">
					<input type="text" name="{$licenseName}" id="DBSTHEME_LICENSE" value="" class="" size="20" required="required">
					<p class="help-block">
						لطفاً کد لایسنس معتبری که از وب سایت پرستایار دریافت نموده اید اینجا وارد نمایید.
					</p>
				</div>
			</div>
		</div><!-- /.form-wrapper -->
		
		<div class="panel-footer">
			<button type="submit" value="1" id="dbs_tab_submit" name="dbs_tab_submit" class="">
				<i class="process-icon-save"></i> 
				ذخیره
			</button>
		</div>
	</div>
</form>

<script>
{literal}
	$("#dbs-ajax-form").submit(function() {
		try
		{
			$.ajax({
				type:"POST",
				url : currentIndex +'&rand=' + new Date().getTime(),
				headers: {"cache-control": "no-cache"},
				async: true,
				cache: false,
				data : {
					ajax : "1",
					token : token,
					controller : help_class_name,
					action : "DBSActionTabs",
					dbs_tab : $('#dbs-request-tab').val(),
					dbsAjaxData : $("#dbs-ajax-form").serialize(),
					
				},
				beforeSend: function(xhr){
					$('#moduleContainer').html('<div class="dbs_loading"></div>');
				},
				success: function(data, status, request){
					if (request.getResponseHeader('Login') === 'true')
						return window.location.reload();

					$('#moduleContainer').html(data);
				}
			});
		}
		catch(e){}
		return false;
	});
	{/literal}
</script>



