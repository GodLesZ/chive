<div class="tabMenu">
	<?php $this->widget('TabMenu', [
		'items' => [
			[
				'label' => Yii::t('core', 'browse'),
				'icon'  => 'browse',
				'link'  => [
					'url'         => 'views/'.$this->view.'/browse',
					'htmlOptions' => ['class' => 'icon'],
				],
			],
			[
				'label' => Yii::t('core', 'structure'),
				'icon'  => 'structure',
				'link'  => [
					'url'         => 'views/'.$this->view.'/structure',
					'htmlOptions' => ['class' => 'icon'],
				],
			],
			[
				'label' => Yii::t('core', 'sql'),
				'icon'  => 'structure',
				'link'  => [
					'url'         => 'views/'.$this->view.'/sql',
					'htmlOptions' => ['class' => 'icon'],
				],
			],
			[
				'label' => Yii::t('core', 'search'),
				'icon'  => 'search',
				'link'  => [
					'url'         => 'views/'.$this->view.'/search',
					'htmlOptions' => ['class' => 'icon'],
				],
			],
			[
				'label'   => Yii::t('core', 'insert'),
				'icon'    => 'insert',
				'link'    => [
					'url'         => 'views/'.$this->view.'/insert',
					'htmlOptions' => ['class' => 'icon'],
				],
				'visible' => $this->loadView()->getIsUpdatable(),
			],
			[
				'label'   => Yii::t('core', 'drop'),
				'icon'    => 'delete',
				'link'    => [
					'url'         => 'javascript:void(0)',
					'htmlOptions' => [
						'class'   => 'icon',
						'onclick' => 'viewGeneral.drop("'.$this->schema.'","'.$this->view.'");'
					],
				],
				'visible' => Yii::app()->user->privileges->checkTable($this->schema, $this->view, 'DROP'),
			],
		],
	]);
	?>
</div>

<div id="dropViewDialog" title="<?php echo Yii::t('core', 'dropView'); ?>" style="display: none">
	<?php echo Yii::t('core', 'doYouReallyWantToDropView'); ?>
	<ul></ul>
</div>

<div id="content-inner">
	<?php echo $content; ?>
</div>

<script type="text/javascript">
	var schema = '<?php echo $this->schema; ?>';
	var view = '<?php echo $this->view; ?>';
	viewGeneral.setupDialogs();
	breadCrumb.set([
		{
			icon: 'database',
			href: 'javascript:chive.goto(\'tables\')',
			text: schema
		},
		{
			icon: 'view',
			href: 'javascript:chive.goto(\'views/' + view + '/structure\')',
			text: view
		}
	]);
	sideBar.activate(1);
</script>