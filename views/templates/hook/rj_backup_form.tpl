<div class="panel"><h3><i class="icon-list-ul"></i> {l s='Create backup' d='Modules.Rjbackup.Admin'}
	<span class="panel-heading-action">
		<a id="desc-product-new" class="list-toolbar-btn" href="{*$link->getAdminLink('AdminModules')*}&configure=rj_backup&addBackup=1">
			<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Add new' d='Admin.Actions'}" data-html="true">
				<i class="process-icon-new "></i>
			</span>
		</a>
	</span>
	</h3>
	<div id="BackupContent">
		<a class="btn btn-default"
			href="{$link->getAdminLink('AdminModules')}&configure=rj_backup&create_Backup=create">
			<i class="process-icon-new"></i>{l s='Create' d='Admin.Actions'}
			{l s='I have read the disclaimer. Please create a new backup.' d='Modules.Rjbackup.Admin'}
		</a>
        {* <form action="#" method="post">
            {* <input type="hidden" name="_token" value="{{ csrf_token('backup') }}"> *}

            {* <button type="submit" class="btn btn-primary">
                <i class="process-icon-new"></i>{l s='Create' d='Admin.Actions'}
                {l s='I have read the disclaimer. Please create a new backup.' d='Modules.Rjbackup.Admin'}
            </button> 
        </form> *}
    </div>
</div>