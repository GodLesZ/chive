<?php echo CHtml::form('', 'post', ['id' => CHtml::$idPrefix]); ?>
<?php echo CHtml::hiddenField('type', $type); ?>
<h1>
	<?php echo Yii::t('core', ($routine->isNewRecord ? 'add' : 'edit').ucfirst($type)); ?>
</h1>
<?php echo CHtml::errorSummary($routine, false); ?>
<?php $this->widget('AceEditor', [
	'id'          => 'query',
	'htmlOptions' => ['name' => 'query'],
	'value'       => $query,
	'height'      => 200,
]); ?>
<div class="buttonContainer">
	<?php echo Html::submitFormArea(false); ?>
</div>
</form>