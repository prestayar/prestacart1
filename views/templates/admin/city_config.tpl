{*
* @Module Name: Presta Cart
* @Website: prestayar.com - prestashop cart
* @author Hashem Afkhami <hashem_afkhami@yahoo.com>
* @copyright  2013-2017 prestayar.com
*}
<!-- @file modules\psf_prestacart\views\templates\admin\city_config -->
<form id="configuration_form" class="defaultForm form-horizontal pscart" method="post" >
	<input name="submitPSCartCity" value="1" type="hidden">
	<div class="panel" >	
			<div class="panel-heading">
				شهر و استان مبدا را مشخص کنید
			</div>
			<div class="form-wrapper">
					{foreach $items as $key => $option}
						{if $option.type == 'selectState'}	
							<div class="form-group">
								<label class="control-label col-lg-3 required">{$option.label}</label>		
								<div class="col-lg-9">
									{if $option.value and $option.htmlEdit}
										{$option.htmlEdit['select']}
										{foreach $option.htmlEdit['options'] as $value => $title}
											<option value="{$value}"
												{if $value == $option.value} selected="selected"{/if}
											>{$title}</option>
										{/foreach}
										</select>
									{else}
										{$option.html}
									{/if}								
								</div>
							 </div>
						{elseif $option.type == 'selectCity'}
							<div class="form-group">
								<label class="control-label col-lg-3 required">{$option.label}</label>
								<div class="col-lg-9">							
									{$option.html}
									{if $option.value}
										<script type="text/javascript" >
											var city = "{$option.value}";
											$(document).ready(function(){
												$("#id_city").val(city);
											});								
										</script>							
									{/if}
								</div>
							</div>
						{/if}
                   
					{/foreach}	
			</div><!-- /.form-wrapper -->
			<div class="panel-footer">
				<button type="submit" value="1" id="configuration_form_submit_btn" name="submitPSCartCity" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> ذخیره
			</button>
		</div>	
	</div>
</form>