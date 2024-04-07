<div class="row">
	<div class="col-md-6">
		<div class="dbstheme_logo pull-left"></div>
		{if $show_admin_panel_btn }
			<br>
			<a class="btn btn-default pull-left" href="{$go_to_admin_panel_url}" onclick="" title="">
				<i class="icon-reply"></i> بازگشت به مدیریت ماژول
			</a>
		{/if}
	</div>	
	<div class="col-md-6">
		
		{if $exist_new_version  }
			<div class="alert alert-warning pull-right">
					&nbsp;&nbsp; ویرایش جدید این افزونه منتشر شده است!
			</div>
		{/if}
	</div>
</div>
	
{if isset($ws_response['important_notice']) }
	<div class="row">
		<div class="col-sm-12">
			<div class="alert {if isset($ws_response['important_notice']['css_class']) }{$ws_response['important_notice']['css_class']}{else}alert-info{/if}">
				{$ws_response['important_notice']['message']}
			</div>
		</div>
	</div>
{/if}