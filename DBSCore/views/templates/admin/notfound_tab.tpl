{if !empty($flash_message)  }
	<div class="row">
		<div class="col-sm-12">
			<div class="alert {if isset($flash_message['css-alert'])}{$flash_message['css-alert']}{else}alert-info{/if} ">
				{$flash_message['message']}
			</div>
		</div>
	</div>
{/if}