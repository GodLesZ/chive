<div class="tabMenu">
	<?php $this->widget('TabMenu', [
		'items' => [
			[
				'label'   => Yii::t('core', 'browse'),
				'icon'    => 'browse',
				'link'    => [
					'url'         => 'tables/'.$this->table.'/browse',
					'htmlOptions' => ['class' => 'icon'],
				],
				'visible' => true,
			],
			[
				'label'   => Yii::t('core', 'structure'),
				'icon'    => 'structure',
				'link'    => [
					'url'         => 'tables/'.$this->table.'/structure',
					'htmlOptions' => ['class' => 'icon'],
				],
				'visible' => true,
			],
			[
				'label'   => Yii::t('core', 'sql'),
				'icon'    => 'structure',
				'link'    => [
					'url'         => 'tables/'.$this->table.'/sql',
					'htmlOptions' => ['class' => 'icon'],
				],
				'visible' => true,
			],
			[
				'label'   => Yii::t('core', 'search'),
				'icon'    => 'search',
				'link'    => [
					'url'         => 'tables/'.$this->table.'/search',
					'htmlOptions' => ['class' => 'icon'],
				],
				'visible' => true,
			],
			[
				'label'   => Yii::t('core', 'insert'),
				'icon'    => 'insert',
				'link'    => [
					'url'         => 'tables/'.$this->table.'/insert',
					'htmlOptions' => ['class' => 'icon'],
				],
				'visible' => true,
			],
			[
				'label'   => Yii::t('core', 'export'),
				'icon'    => 'save',
				'link'    => [
					'url'         => 'tables/'.$this->table.'/export',
					'htmlOptions' => ['class' => 'icon'],
				],
				'visible' => true,
			],
			[
				'label'   => Yii::t('core', 'import'),
				'icon'    => 'import',
				'link'    => [
					'url'         => 'tables/'.$this->table.'/import',
					'htmlOptions' => ['class' => 'icon'],
				],
				'visible' => true,
			],
			[
				'label'   => Yii::t('core', 'truncate'),
				'icon'    => 'truncate',
				'link'    => [
					'url'         => 'javascript:void(0)',
					'htmlOptions' => [
						'class'   => 'icon',
						'onclick' => 'tableGeneral.truncate()'
					],
				],
				'visible' => Yii::app()->user->privileges->checkTable($this->schema, $this->table, 'DELETE'),
			],
			[
				'label'   => Yii::t('core', 'drop'),
				'icon'    => 'delete',
				'link'    => [
					'url'         => 'javascript:void(0)',
					'htmlOptions' => [
						'class'   => 'icon',
						'onclick' => 'tableGeneral.drop()'
					],
				],
				'visible' => Yii::app()->user->privileges->checkTable($this->schema, $this->table, 'DROP'),
			],
		],
	]);
	?>
</div>

<div id="truncateTableDialog" title="<?php echo Yii::t('core', 'truncateTable'); ?>" style="display: none">
	<?php echo Yii::t('core', 'doYouReallyWantToTruncateTable', ['{tableName}' => $this->table]); ?>
	<ul></ul>
</div>
<div id="dropTableDialog" title="<?php echo Yii::t('core', 'dropTable'); ?>" style="display: none">
	<?php echo Yii::t('core', 'doYouReallyWantToDropTable', ['{tableName}' => $this->table]); ?>
	<ul></ul>
</div>

<div id="content-inner">
	<?php echo $content; ?>
</div>

<script type="text/javascript">
	var schema = '<?php echo $this->schema; ?>';
	var table = '<?php echo $this->table; ?>';
	tableGeneral.setupDialogs();
	breadCrumb.set([
		{
			icon: 'database',
			href: 'javascript:chive.goto(\'tables\')',
			text: schema
		},
		{
			icon: 'table',
			href: 'javascript:chive.goto(\'tables/' + table + '/structure\')',
			text: table
		}
	]);
	sideBar.activate(0);
</script>