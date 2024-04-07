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

<div class="row">
	<div class="col-sm-12">
		<div class="alert alert-info ">
			اگر چه این قسمت برای ارتباط راحتتر مشتریان دی بی اس تم ایجاد شده اما پیشنهاد میکنیم درخواستهای مهم را از وبسایت دی بی اس تم ارسال نمایید چرا که ممکن است به دلایل فنی ، مشکلات هاستینگ و میزبانی و همچنین تنظیمات فروشگاه ، پیام شما از این قسمت به گروه دی بی اس تم نرسد ! 
		</div>
	</div>
</div>

<form id="dbs-ajax-form" class="defaultForm form-horizontal" method="post" enctype="multipart/form-data" novalidate="">
<div class="panel">
	<div class="panel-heading">
		ارتباط با گروه دی بی اس تم
	</div>
	<div class="form-wrapper">
		<input type="hidden" id="dbs-request-tab" name="request_tab" value="{$request_tab}">
		<div class="form-group">
			<label class="control-label col-lg-3">
				نام و نام خانوادگی
			</label>
			<div class="col-lg-9 ">
				<input type="text" name="name_family" value="{$name_family}" class="" size="20">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3">
			نشانی ایمیل
			</label>
			<div class="col-lg-9 ">
				<input type="text" name="user_email" value="{$user_email}" class="" size="20">
				<p class="help-block">
					نشانی ایمیل خود را برای دریافت پاسخ ، وارد نمایید.
				</p>
			</div>
		</div>
		<hr>
		<div class="form-group">
			<label class="control-label col-lg-3">
			انتخاب دپارتمان
			</label>
			<div class="col-lg-8 ">
				<select name="department">
					<option value="support">پشتیبانی مشتریان</option>
					<option value="sale">فروش</option>
					<option value="programming">برنامه نویسی</option>
					<option value="designing">طراحی و گرافیک</option>
				</select>
				<p class="help-block">
				انتخاب صحیح ، موجب تسریع در انجام امور شما خواهد شد.
				</p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3 required">
			متن پیام
			</label>
			<div class="col-lg-9 ">
			<textarea rows="12" name="email_message" class="textarea-autosize"></textarea>
			</div>
		</div>
	</div><!-- /.form-wrapper -->
	
	<div class="panel-footer">
		<button type="submit" name="dbs_submit" value="1" class="button">
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



