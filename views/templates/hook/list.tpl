{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="panel"><h3><i class="icon-list-ul"></i> {l s='backups list' d='Modules.Imagebackupr.Admin'}
	<span class="panel-heading-action">
		<a id="desc-product-new" class="list-toolbar-btn" href="{$link->getAdminLink('AdminModules')}&configure=rj_backup&addbackup=1">
			<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Add new' d='Admin.Actions'}" data-html="true">
				<i class="process-icon-new "></i>
			</span>
		</a>
	</span>
	</h3>
	<div id="backupsContent">
		<div id="backups">
			{foreach from=$backups item=backup}
				<div id="backups_{$backup.file}" class="panel">
					<div class="row">
						<div class="col-md-3">
							<a href="{$dir}{$backup.file}" alt="{$backup.file}" class="">{$backup.file}</a>
						</div>
						<div class="col-md-8">
							<h4 class="pull-left">
								#{$backup.size} - {$backup.date}
								{if $backup.size}
									<div>
										<span class="label color_field pull-left" style="background-color:#108510;color:white;margin-top:5px;">
											{l s='Shared backup' d='Modules.Imagebackupr.Admin'}
										</span>
									</div>
								{/if}
							</h4>
							<div class="btn-group-action pull-right">
								<a class="btn btn-default"
									href="{$dir}{$backup.file}">
									<i class="icon-download"></i>
									{l s='Download' d='Admin.Actions'}
								</a>
								<a class="btn btn-default"
									href="{$link->getAdminLink('AdminModules')}&configure=rj_backup&delete_id_backup={$backup.file}">
									<i class="icon-trash"></i>
									{l s='Delete' d='Admin.Actions'}
								</a>
								<a class="btn btn-default"
									href="{$link->getAdminLink('AdminModules')}&configure=rj_backup&send_ftp={$backup.file}">
									<i class="icon-send"></i>
									{l s='send' d='Admin.Actions'}
								</a>
							</div>
						</div>
					</div>
				</div>
			{/foreach}
		</div>
	</div>
</div>
