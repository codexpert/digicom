<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html/');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$user		= JFactory::getUser();
$userId		= $user->get('id');
$canOrder	= $user->authorise('core.edit.state', 'com_digicom.component');

$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;

$saveOrder	= $listOrder == 'a.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_digicom&controller=products&task=saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'productList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$assoc		= JLanguageAssociations::isEnabled();

$sortFields = $this->getSortFields();

JFactory::getDocument()->addScriptDeclaration('
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != "' . $listOrder . '")
		{
			dirn = "asc";
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, "");
	};
');
?>


?>
<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_digicom&controller=products'); ?>" method="post" name="adminForm" autocomplete="off" class="form-horizontal">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="">
<?php else : ?>
	<div id="j-main-container" class="">
<?php endif;?>
		<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>

		<!-- 
		<div class="js-stools">
			<div class="clearfix">
				<div class="btn-wrapper input-append">
					<input id="filter_search" type="text" name="filter_search" placeholder="<?php echo JText::_('DSSEARCH'); ?>" value="<?php echo $search_session; ?>"/>		
					<button type="submit" class="btn hasTooltip" title="" data-original-title="Search">
						<i class="icon-search"></i>
					</button>
					<button type="button" class="btn hasTooltip js-stools-btn-clear" onclick="document.id('filter_search').value='';this.form.submit();">
						<i class="icon-remove"></i>	
					</button>
				</div>
				
				<div class="btn-wrapper pull-right">
					<?php echo $this->csel; ?>
					
					<select name="filter_published" onchange="document.adminForm.submit();">
						<option value="" <?php if($state_filter == ""){echo 'selected="selected"'; } ?>><?php echo JText::_("JALL"); ?></option>
						<option value="1" <?php if($state_filter == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("HELPERPUBLISHED"); ?></option>
						<option value="0" <?php if($state_filter == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("HELPERUNPUBLICHED"); ?></option>
					</select>
					
					
					<div class="btn-group pull-right">
						<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
						<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
							<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
						</select>
					</div>
					<div class="btn-group pull-right hidden-phone">
						<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
						<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
							<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
							<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
						</select>
					</div>
					<div class="btn-group pull-right hidden-phone">
						<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
						<?php echo $this->pagination->getLimitBox(); ?>
					</div>
				</div>
			</div>
		</div>
		<br> -->
		<div class="alert alert-info">
			<?php echo JText::_("HEADER_PRODUCTS"); ?>
		</div>
		<div id="editcell" >

			<table class="adminlist table table-striped table-hover" id="productList">

				<thead>

					<tr>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
						</th>
						<th width="1%">
							<span><?php echo JHtml::_('grid.checkall'); ?></span>
						</th>
						<th width="1%">
							<?php echo JHtml::_('grid.sort', 'COM_DIGICOM_PRODUCTS_STATUS', 'published', $listDirn, $listOrder); ?>
						</th>
						<th width="1%">
							<?php echo JHtml::_('grid.sort', 'COM_DIGICOM_PRODUCTS_IMG', 'id', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'VIEWPRODNAME', 'name', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_DIGICOM_PRODUCTS_TYPE', 'product_type', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo JHtml::_('grid.sort', 'COM_DIGICOM_PRODUCTS_PRICE', 'hide_public', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo JHtml::_('grid.sort', 'PRODUCT_IS_VISIBLE', 'hide_public', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort',  'JAUTHOR', 'a.created_by', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JText::_('VIEWPRODCATEGORY'); ?>
						</th>
						<th width="1%">
							<?php echo JHtml::_('grid.sort', 'VIEWPRODID', 'id', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
						</th>
						
					</tr>

				</thead>

				<tbody>

				<?php foreach ($this->items as $i => $item) :
					$item->max_ordering = 0;
					$ordering   = ($listOrder == 'a.ordering');
					$canCreate  = $user->authorise('core.create',     'com_content.category.' . $item->catid);
					$canEdit    = $user->authorise('core.edit',       'com_content.article.' . $item->id);
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
					$canEditOwn = $user->authorise('core.edit.own',   'com_content.article.' . $item->id) && $item->created_by == $userId;
					$canChange  = $user->authorise('core.edit.state', 'com_content.article.' . $item->id) && $canCheckin;
					?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">
						<td class="order nowrap center hidden-phone">
							<?php
							$iconClass = '';
							if (!$canChange)
							{
								$iconClass = ' inactive';
							}
							elseif (!$saveOrder)
							{
								$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
							}
							?>
							<span class="sortable-handler<?php echo $iconClass ?>">
								<i class="icon-menu"></i>
							</span>
							<?php if ($canChange && $saveOrder) : ?>
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
							<?php endif; ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<?php //echo $checked; ?>
								
								<?php echo JHtml::_('jgrid.published', $prod->published, $i); ?>
								<?php echo JHtml::_('featured.featured', $prod->featured, $i, $canChange); ?>
								<?php
								// Create dropdown prods
								$action = $archived ? 'unarchive' : 'archive';
								//JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'articles');

								$action = $trashed ? 'untrash' : 'trash';
								//JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'articles');

								// Render dropdown list
								//$label, $icon = '', $id = '', $task = ''
								JHtml::_('actionsdropdown.addCustomItem', JText::_('EDIT'),'edit','cb'.$i,'edit');
								JHtml::_('actionsdropdown.addCustomItem', JText::_('DUPLICATE'),'copy','cb'.$i,'copy');
								JHtml::_('actionsdropdown.addCustomItem', JText::_('JREMOVE'),'remove','cb'.$i,'remove');
								echo JHtml::_('actionsdropdown.render', $this->escape($prod->name));
								?>
							</div>
						</td>
						<td align="center">
							<?php if(!empty($item->images)): ?>
							<img src="<?php echo JUri::root() . $item->images; ?>" height="48" width="48">
							<?php endif; ?>
						</td>
						<td class="has-context">
							<div class="pull-left break-word">
								<?php if ($item->checked_out) : ?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'articles.', $canCheckin); ?>
								<?php endif; ?>
								<?php if ($item->language == '*'):?>
									<?php $language = JText::alt('JALL', 'language'); ?>
								<?php else:?>
									<?php $language = $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
								<?php endif;?>
								<?php if ($canEdit || $canEditOwn) : ?>
									<a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_digicom&controller=products&task=edit&cid[]=' . $item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
										<?php echo $this->escape($item->name); ?></a>
								<?php else : ?>
									<span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias)); ?>"><?php echo $this->escape($item->title); ?></span>
								<?php endif; ?>
								<span class="small break-word">
									<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
								</span>
								<div class="small">
									<?php echo JText::_('JCATEGORY') . ": " . $this->escape($item->category_title); ?>
								</div>
							</div>
						</td>
						<?php if ($assoc) : ?>
						<td class="hidden-phone">
							<?php if ($item->association) : ?>
								<?php echo JHtml::_('contentadministrator.association', $item->id); ?>
							<?php endif; ?>
						</td>
						<?php endif;?>
						<td class="small hidden-phone">
							<a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . (int) $item->created_by); ?>" title="<?php echo JText::_('JAUTHOR'); ?>">
							<?php echo $this->escape($item->author_name); ?></a>
						</td>
						<td class="small hidden-phone">
							<?php if ($item->language == '*'):?>
								<?php echo JText::alt('JALL', 'language'); ?>
							<?php else:?>
								<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
							<?php endif;?>
						</td>
						<td class="nowrap small hidden-phone">
							<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
						</td>
						<td class="center hidden-phone">
							<?php echo (int) $item->hits; ?>
						</td>
						<td class="center hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>

				<tfoot>
					<tr>
						<td colspan="8">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="products" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listOrder; ?>" />

</form>
