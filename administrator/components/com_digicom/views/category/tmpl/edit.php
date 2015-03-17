<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_digicom
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$input = $app->input;

$assoc = JLanguageAssociations::isEnabled();

$input->set('layout', 'dgform');

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "category.cancel" || document.formvalidator.isValid(document.getElementById("item-form")))
		{
			' . $this->form->getField("description")->save() . '
			Joomla.submitform(task, document.getElementById("item-form"));
		}
	};
');

?>

<form action="<?php echo JRoute::_('index.php?option=com_digicom&extension=' . $input->getCmd('extension', 'com_digicom') . '&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="">
<?php else : ?>
	<div id="j-main-container" class="">
<?php endif;?>

		<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

		<div class="form-horizontal">
			<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('JCATEGORY', true)); ?>
			<div class="row-fluid">
				<div class="span9">
					<?php echo $this->form->getLabel('description'); ?>
					<?php echo $this->form->getInput('description'); ?>
				</div>
				<div class="span3">
					<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
				</div>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('COM_DIGICOM_CATEGORY_PUBLISHING_OPTION', true)); ?>
			<div class="row-fluid form-horizontal-desktop">
				<div class="span6">
					<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
				</div>
				<div class="span6">
					<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
				</div>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php if ($assoc) : ?>
				<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'associations', JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true)); ?>
				<?php echo $this->loadTemplate('associations'); ?>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php endif; ?>

			<?php if ($this->canDo->get('core.admin')) : ?>
				<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'rules', JText::_('COM_DIGICOM_CATEGORY_PERMISSIONS', true)); ?>
				<?php echo $this->form->getInput('rules'); ?>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php endif; ?>

			<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

			<?php echo JHtml::_('bootstrap.endTabSet'); ?>

			<?php echo $this->form->getInput('extension'); ?>
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</div>
</form>
