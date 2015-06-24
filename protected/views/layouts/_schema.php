<div class="tabMenu">
	<?php $this->widget('TabMenu', [
		'items' => [
			[
				'label'   => Yii::t('core', 'tables'),
				'icon'    => 'table',
				'link'    => [
					'url'         => 'tables',
					'htmlOptions' => ['class' => 'icon'],
				],
				'visible' => true,
			],
			[
				'label'   => Yii::t('core', 'views'),
				'icon'    => 'view',
				'link'    => [
					'url'         => 'views',
					'htmlOptions' => ['class' => 'icon'],
				],
				'visible' => true,
			],
			[
				'label'   => Yii::t('core', 'sql'),
				'icon'    => 'sql',
				'link'    => [
					'url'         => 'sql',
					'htmlOptions' => ['class' => 'icon'],
				],
				'visible' => true,
			],
			[
				'label'   => Yii::t('core', 'export'),
				'icon'    => 'save',
				'link'    => [
					'url'         => 'export',
					'htmlOptions' => ['class' => 'icon'],
				],
				'visible' => true,
			],
			[
				'label'   => Yii::t('core', 'import'),
				'icon'    => 'import',
				'link'    => [
					'url'         => 'import',
					'htmlOptions' => ['class' => 'icon'],
				],
				'visible' => true,
			],
			[
				'label'   => Yii::t('core', 'routines'),
				'icon'    => 'procedure',
				'link'    => [
					'url'         => 'routines',
					'htmlOptions' => ['class' => 'icon'],
				],
				'visible' => true,
			],
			[
				'label'   => Yii::t('core', 'drop'),
				'icon'    => 'delete',
				'link'    => [
					'url'         => 'javascript:void(0)',
					'htmlOptions' => [
						'class'   => 'icon',
						'onclick' => 'schemaGeneral.dropSchema()'
					],
				],
				'visible' => Yii::app()->user->privileges->checkSchema($this->schema, 'DROP'),
			],
		],
	]);
	?>
</div>

<div id="dropSchemaDialog" title="<?php echo Yii::t('core', 'confirm'); ?>" style="display: none">
	<?php echo Yii::t('core', 'doYouReallyWantToDropSchema'); ?>
	<ul></ul>
</div>

<div id="content-inner">
	<?php echo $content; ?>
</div>

<script type="text/javascript">
	var schema = '<?php echo $this->schema; ?>';
	schemaGeneral.setupDialogs();
	breadCrumb.set([
		{
			icon: 'database',
			href: 'javascript:chive.goto(\'tables\')',
			text: schema
		}
	]);
</script>