{*
* @Module Name: Presta Cart
* @Website: prestayar.com - prestashop cart
* @author Hashem Afkhami <hashem_afkhami@yahoo.com>
* @copyright  2013-2017 prestayar.com
*}
<!-- @file modules\psf_prestacart\views\templates\admin\fields_config -->
<form id="configuration_form" class="defaultForm form-horizontal pscart" method="post" >
	<input name="submitPSCart" value="1" type="hidden">
	<div class="panel" >	
			<div class="panel-heading">
				شخصی سازی فیلد های آدرس و ثبت نام
			</div>
			<div class="form-wrapper">
				<table class="table  meta">
					<thead>
						<tr class="nodrag nodrop">
							<th class="center">
								<span class="title_box ">عنوان فیلد</span>
							</th>						
							<th class="center">
								<span class="title_box ">فعال</span>
							</th>
							<th class="center">
								<span class="title_box ">اجباری</span>
							</th>
							<th class="center">
								<span class="title_box ">فعال - محصول مجازی</span>
							</th>
							<th class="center">
								<span class="title_box ">اجباری - محصول مجازی</span>
							</th>
							<th class="center">
								<span class="title_box ">موقعیت</span>
							</th>							
						</tr>
					</thead>
					<tbody>
					{foreach $fields_address as $key => $field}
						<tr class="odd">
							<td class="center">{$field.title}</td>
							<td class="center">				
								<a 	class="list-action-enable  action-{if $field.data.enable == '0'}disabled{else}enabled{/if}" data-id="#{$key}_enable">
									<i class="icon-check {if $field.data.enable == '0'}hidden{/if}"></i>
									<i class="icon-remove {if $field.data.enable == '1'}hidden{/if}"></i>
									<input type="hidden" id="{$key}_enable" name="{$key}_enable"  value="{$field.data.enable}" >
								</a>								
							</td>								
							<td class="center">				
								<a 	class="list-action-enable  action-{if $field.data.required == '0'}disabled{else}enabled{/if}" data-id="#{$key}_required">
									<i class="icon-check {if $field.data.required == '0'}hidden{/if}"></i>
									<i class="icon-remove {if $field.data.required == '1'}hidden{/if}"></i>
									<input type="hidden" id="{$key}_required" name="{$key}_required"  value="{$field.data.required}" >
								</a>								
							</td>
							<td class="center">				
								{if $field.virtual == '1'}
								<a 	class="list-action-enable  action-{if $field.data.enable_virtual == '0'}disabled{else}enabled{/if}" data-id="#{$key}_enable_virtual">
									<i class="icon-check {if $field.data.enable_virtual == '0'}hidden{/if}"></i>
									<i class="icon-remove {if $field.data.enable_virtual == '1'}hidden{/if}"></i>
									<input type="hidden" id="{$key}_enable_virtual" name="{$key}_enable_virtual"  value="{$field.data.enable_virtual}" >
								</a>
								{else}
								-<input type="hidden" id="{$key}_enable_virtual" name="{$key}_enable_virtual"  value="{$field.data.enable_virtual}" >
								{/if}
							</td>								
							<td class="center">				
								{if $field.virtual == '1'}
								<a 	class="list-action-enable  action-{if $field.data.required_virtual == '0'}disabled{else}enabled{/if}" data-id="#{$key}_required_virtual">
									<i class="icon-check {if $field.data.required_virtual == '0'}hidden{/if}"></i>
									<i class="icon-remove {if $field.data.required_virtual == '1'}hidden{/if}"></i>
									<input type="hidden" id="{$key}_required_virtual" name="{$key}_required_virtual"  value="{$field.data.required_virtual}" >
								</a>
								{else}
								-<input type="hidden" id="{$key}_required_virtual" name="{$key}_required_virtual"  value="{$field.data.required_virtual}" >
								{/if}								
							</td>
							<td class="center">
								<input type="text" name="{$key}_position"  value="{$field.data.position}" style="width:40px" >
							</td>
						</tr>
					{/foreach}
					</tbody>						
				</table>
			</div><!-- /.form-wrapper -->
			<div class="panel-footer">
				<button type="submit" value="1" id="configuration_form_submit_btn" name="submitPSCart" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> ذخیره
			</button>
		</div>	
	</div>
</form>

<script  type="text/javascript" >
$(document).ready(function () {
	$('.list-action-enable').click(function() {
		var input = $(this).attr('data-id');
		var value = $(input).val();
		$(this).children('i').removeClass('hidden');
		if(value == '1'){
			$(this).children('i.icon-check').addClass('hidden');
			$(this).removeClass('action-enabled').addClass('action-disabled');
			$(input).val('0');
		}else{
			$(this).children('i.icon-remove').addClass('hidden');
			$(this).removeClass('action-disabled').addClass('action-enabled');
			$(input).val('1');
		}
		return false;
	});
});
</script>


