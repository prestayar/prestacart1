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
{if isset($ws_response['subscribe_info']) AND !empty($ws_response['subscribe_info']) }
    {if isset($ws_response['subscribe_info']['remainder_days'])  }
        {if $ws_response['subscribe_info']['remainder_days'] <= 10}
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        تنها {$ws_response['subscribe_info']['remainder_days']} روز از زمان اشتراک ویژه شما باقی مانده است ،<a href="{$ws_response['subscribe_info']['link_detail']}">برای تمدید اشتراک کلیک کنید.</a>
                    </div>
                </div>
            </div>
        {elseif $ws_response['subscribe_info']['remainder_days'] <= 60}
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-warning">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        تنها {$ws_response['subscribe_info']['remainder_days']} روز از زمان اشتراک ویژه شما باقی مانده است ،<a href="{$ws_response['subscribe_info']['link_detail']}">برای تمدید اشتراک کلیک کنید.</a>
                    </div>
                </div>
            </div>
        {/if}
    {/if}
{/if}

{if !empty($ws_response['product_info']['latest_version_message']) && $exist_new_version}
	<div class="row">
		<div class="col-sm-12">
			<div class="alert alert-warning">
					{$ws_response['product_info']['latest_version_message']}
			</div>
		</div>
	</div>
{/if}

<div class="panel">
				
	<div class="panel-heading">
		<i class="icon-rocket"></i>&nbsp;&nbsp;
		 {$module_name}
	</div>

	<div class="row">

		<div style="font-size: 13px;">
			{$module_full_desc}
		</div>

	<hr>
			
			{if isset($ws_response['product_info']['intro_link']) AND !empty($ws_response['product_info']['intro_link']) }
				<a href="{$ws_response['product_info']['intro_link']}" class="btn btn-default">
					<i class="icon-info-circle"></i>&nbsp;
					توضیحات و معرفی ماژول
				</a>
			{/if}
			{if isset($ws_response['product_info']['help_link']) AND !empty($ws_response['product_info']['help_link']) }
				<a href="{$ws_response['product_info']['help_link']}" class="btn btn-default">
					<i class="icon-life-ring"></i>&nbsp;
					آموزش و راهنمایی
				</a>
			{/if}
			{if isset($ws_response['product_info']['qa_link']) AND !empty($ws_response['product_info']['qa_link']) }
				<a href="{$ws_response['product_info']['qa_link']}" class="btn btn-default">
					<i class="icon-question-circle"></i>&nbsp;
					پرسش و پاسخ
				</a>
			{/if}
			{if isset($ws_response['product_info']['update_link']) AND !empty($ws_response['product_info']['update_link']) AND $exist_new_version }
				<a href="{$ws_response['product_info']['update_link']}" class="btn btn-warning pull-right">
					<i class="icon-refresh"></i>&nbsp;
					به روز رسانی
				</a>
			{/if}
	</div>
	
</div>
{if $dbs_offer }
	<div class="panel">		
		<div class="panel-heading">
			<i class="icon-gift"></i>
			&nbsp;&nbsp; پیشنهاد ویژه دی بی اس تم
		</div>
		<div class="row">
			{$dbs_offer}
		</div>
	</div>
{/if}
{if isset($ws_response['subscribe_info']) AND !empty($ws_response['subscribe_info']) }
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-gift"></i>
            &nbsp;&nbsp; اطلاعات اشتراک ویژه پرستافا
        </div>
        <div class="row">
            <div style="font-size: 13px;">
                <p>شما در حال استفاده از اشتراک ویژه پرستافا هستید.</p>
                <p>تاریخ پایان اشتراک : <b>{$ws_response['subscribe_info']['expire_date']}</b></p>
                <p>تعداد روز باقی مانده : <b>{$ws_response['subscribe_info']['remainder_days']}</b></p>
            </div>
            {if isset($ws_response['subscribe_info']['remainder_days']) AND $ws_response['subscribe_info']['remainder_days'] <= 60 }
                <hr>
                <a href="{$ws_response['subscribe_info']['link_detail']}" class="btn btn-success pull-right">
                    <i class="icon-refresh"></i>&nbsp;
                    تمدید اشتراک
                </a>
            {/if}
        </div>
    </div>
{/if}
{if isset($ws_response['links']) AND count($ws_response['links']) }
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-link"></i>
			&nbsp;&nbsp; لینک های پیشنهادی
		</div>

		<div class="row">
			<table class="table">
				<tbody>
				
					{foreach from=$ws_response['links'] key=link_title item=link_url}
						<tr class="{cycle values='odd,even'}">		
							<td class="pointer">
								<i class="icon-caret-right"></i>
								<a class="active" href="{$link_url}">
									{$link_title}
								</a>
							</td>
						</tr>
					{/foreach}
				
				</tbody>
			</table>
		</div>
	</div>
{/if}