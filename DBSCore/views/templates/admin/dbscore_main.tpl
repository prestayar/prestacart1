<!-- DBS Modules - DBSTheme.com -->

		<div class="row">
			<div class="col-md-6">
				<div class="prestayar_logo pull-left"></div>
				{if $show_configure_btn }
					<br>
					<a class="btn btn-default pull-left" href="{$configure_url}" onclick="" title="">
						<i class="icon-wrench"></i> پیکربندی ماژول
					</a>
				{/if}
			</div>	
			<div class="col-md-6">
				
				{if $exist_new_version  }
					<div class="alert alert-warning pull-right">
							&nbsp;&nbsp; ویرایش جدید این ماژول منتشر شده است!
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
	
			<div class="panel">
				<!--start sidebar module-->
				<div class="row">
					<div class="col-md-3">
						
						{foreach from=$htmltab_pos item=position}
						
							{if $position.html_hr } <hr> {/if}
							<div class="list-group">
							
								{foreach from=$html_tabs key=tab_key_name item=html_tab}
									{if $html_tab.active }
										{if $html_tab.position eq $position.name }
											<a class="{if isset($html_tab.ajax) }dbsAjaxTab{/if} dbsTabLink list-group-item {if $request_tab eq $tab_key_name } active {/if}" href="{if isset($html_tab.link) }{$html_tab.link}{else}{$current_url}{/if}{$tab_key_name}">
												<i class="{if isset($html_tab.css_icon) }{$html_tab.css_icon}{else}icon-caret-right{/if}"></i>&nbsp;
												{$html_tab.title}
												{if isset($number_htmltab[$tab_key_name]) } <span class="badge pull-right">{$number_htmltab[$tab_key_name]}</span> {/if}
											</a>
										{/if}
									{/if}
								{/foreach}
								
							</div>
							
						{/foreach}

					</div>
					<div id="moduleContainer" class="col-md-9">
						{$tab_content}
					</div>
				</div>
				
				<div class="panel-footer">
				
						<div class="row">
							<div class="col-md-1">
								<div class="prestayar_footer_icon"></div>
							</div>
							<div class="col-md-11">
								<br />
								<span>
									تمامی حقوق این ماژول برای <a href="http://www.prestayar.com/">پرستایار</a>    محفوظ است و  استفاده غیر مجاز پیگرد قانونی خواهد داشت.
								</span>
							</div>
						</div>					
				</div>
			</div>


<!-- / DBS Modules - DBSTheme.com -->
