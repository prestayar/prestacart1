{*
* @Module Name: Presta Cart
* @Website: prestayar.com - prestashop cart
* @author Hashem Afkhami <hashem_afkhami@yahoo.com>
* @copyright  2013-2017 prestayar.com
*}
<!-- @file modules\psf_prestacart\views\templates\hook\displayAdminOrder -->
{if $version5}
<fieldset >
	<legend>سفارش در پنل واسطه</legend>
{else}

<div class="row">
<div class="col-lg-12">

<div class="panel kpi-container">
	<div class="row">

{/if}
		<div class="col-xs-12 col-sm-12">
		{if isset($result) }
			{if !$result.hasError}
				<div class="alert alert-success">
					سفارش در پنل پستی ثبت شد ، کد رهگیری : <p style=" display: inline;">{$result.rahgiriCod}</p>
				</div>
				{if isset($result.error) }
					<div class="alert alert-warning">
						{$result.error}
					</div>					
				{/if}
			{else}
				<div class="alert alert-danger">
					خطا در ثبت سفارش : {$result.message}
				</div>		
			{/if}
		{elseif isset($state_order) }
			{if $state_order.result }
				<div class="alert alert-success">
					{$state_order.message}
				</div>
				{if isset($state_order.error) }
					<div class="alert alert-warning">
						{$state_order.error}
					</div>					
				{/if}
			{else}
				<div class="alert alert-danger">
					{$state_order.message}
				</div>		
			{/if}		
		{/if}

		{if $statusOrder == $statusRigester }
			<div class="alert alert-warning">
				سفارش در پنل پستی  {$titleCod} به ثبت نرسیده است.
			</div>
		{else}

		{/if}
		</div>		
		<div class="col-xs-1 col-sm-1">
			<p>
				<img src="{$dir}../logo.png" title="DBSTheme" alt="DBSTheme" width="80"  />
				<br/><br/>
				<!--img src="{$dir}img/safir.png" title="DBSTheme" alt="DBSTheme" width="80" /-->
			</p>			
		</div>
		<div class="col-xs-11 col-sm-11">
		{if $statusOrder == $statusRigester }
			<form class="form-horizontal well" method="post" action="#">
				<div class="row">
					{foreach $panel as $key => $option}
						{if $option.type == 'selectState'}	
							<div class="col-lg-12 form-group">
									<label class="control-label" style="margin-bottom:2px;">استان مقصد</label>						
									{if $option.value and $option.htmlEdit}
										{$option.htmlEdit['select']}
										{foreach $option.htmlEdit['options'] as $value => $title}
											<option value="{$value}">{$title}</option>
										{/foreach}
										</select>
									{else}
										{$option.html}
									{/if}
							</div>	 
						{elseif $option.type == 'selectCity'}
							<div class="col-lg-12 form-group">
								<label class="control-label" style="margin-bottom:2px;">شهر مقصد</label>							
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
						{/if}
					{/foreach}						
					<input type="hidden" name='id_order' value="{$orderId|escape:'intval'}">
					<button class="btn btn-primary" name="submitRigDBS" type="submit">
						ثبت سفارش در {$titleCod}
					</button>
				</div>
			</form>			
		{elseif isset($dbsOrderCod) }
			<div class="alert alert-warning">
				برای بررسی وضعیت سفارش در پنل پستی از دکمه زیر استفاده کنید.
			</div>		
			<form class="form-horizontal well" method="post" action="#">
				<div class="row">
					<div class="col-xs-6 col-sm-6">
						<input type="hidden" name='id_order' value="{$orderId|escape:'intval'}">
						<button class="btn btn-primary" name="submitChangeOrderDBS" type="submit">
							بررسی تغییر وضعیت سفارش  در {$titleCod}
						</button>
						<p style="height:20px"></p>

							{* #dbs# check version 3.1 *}
							{* if $dbsOrderCod.active == 1}
								<button  class="btn btn-danger" name="submitChangeAutoDBS" type="submit">غیرفعال کردن بررسی اتوماتیک وضعیت سفارش</button>
							{else}
								<button  class="btn btn-success" name="submitChangeAutoDBS" type="submit">فعال کردن بررسی اتوماتیک وضعیت سفارش</button>
							{/if *}
												
					</div>
					<div class="col-xs-6 col-sm-6">
						<div><b style="padding-left:5px;">کد رهگیری پست :</b> {$dbsOrderCod.post_tracking_number}</div>
						<div><b style="padding-left:5px;">شناسه پنل پستی :</b> <p style=" display: inline;">{$dbsOrderCod.cod_tracking_number}</p></div>
						<div><b style="padding-left:5px;">تاریخ آخرین تغییروضعیت :</b> {$dbsOrderCod.date_change_cod}</div>
						<div><b style="padding-left:5px;">تاریخ آخرین بررسی :</b> {$dbsOrderCod.date_change_state}</div>
						{if $dbsOrderCod.ensraf != '' }
						<div><b style="padding-left:5px;">دلیل انصراف :</b> {$dbsOrderCod.ensraf}</div>
						{/if}
					</siv>
				</div>
			</form>			
		{/if}
		</div>
{if $version5}
</fieldset>

{else}
	</div>
</div>
	</div>
</div>
		
{/if}		
