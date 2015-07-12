<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined ('_JEXEC') or die ("Go away.");
$app		= JFactory::getApplication();
$input	= $app->input;
$tab		= $input->get('tab','sales');

JFactory::getDocument()->addStylesheet(JURI::root().'media/digicom/assets/c3js/c3.min.css');
JFactory::getDocument()->addScript(JURI::root().'media/digicom/assets/c3js/d3.min.js');
JFactory::getDocument()->addScript(JURI::root().'media/digicom/assets/c3js/c3.min.js');

?>
<form action="<?php echo JRoute::_('index.php?option=com_digicom&view=reports'); ?>" method="post" name="adminFormStats" autocomplete="off" class="form-validate form-horizontal">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>

		<p class="dg-alert dg-alert-danger">
			<?php echo JText::_(' Reports is now on the reactor and we need additional flux capacitor in ThemeXpert to generate 1.21 gigawatts reporting feature. Its coming with next Beta version.'); ?>
		</p>

		<div class="navbar">
			<div class="navbar-inner">
				<ul class="nav">
					<li<?php echo ($tab == 'sales' ? ' class="active"' : '');?>>
						<a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=sales&report=sales_by_date&range=7day');?>">Sales</a>
					</li>
					<li<?php echo ($tab == 'customers' ? ' class="active"' : '');?>>
						<a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=customers&report=customers_new&range=7day');?>">Customers</a>
					</li>
					<li<?php echo ($tab == 'downloads' ? ' class="active"' : '');?>>
						<a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=downloads&report=downloads_top&range=7day');?>">Downloads</a>
					</li>
				</ul>
			</div>
		</div>
		<p class="clearfix"></p>

		<section class="reportsWrapper">
			<?php echo $this->loadTemplate($tab); ?>
		</section>

		<input type="hidden" name="view" value="reports" />
		<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="task" value="showStats" />
	</div>
</form>


<div class="dg-footer">
	<?php echo JText::_('COM_DIGICOM_CREDITS'); ?>
</div>
