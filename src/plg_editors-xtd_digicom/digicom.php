<?php
/*-------------------------------------------------------------------------
# plg_layerslider - Layer Slider editor extend
# -------------------------------------------------------------------------
# @ author    Janos Biro
# @ copyright Copyright (C) 2014 ThemezArt.COM  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.themezart.com
-------------------------------------------------------------------------*/

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_digicom/helpers/digicom.php';

class PlgButtonDigiCom extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	public function onDisplay($name)
	{
		$js = "
		function jSelectProduct(id, name, catid)
		{
			// price = false;
			// description = false;
			// addtocart = false;
			// if (confirm('Show Price ?')) {
			// 	price = true;
			// }
			//
			// if (confirm('Show description ?')) {
			// 	description = true;
			// }
			//
			// if (confirm('Show addtocart ?')) {
			// 	addtocart = true;
			// }
			// console.log(price);
			// console.log(description);
			// console.log(addtocart);

			var tag = '{digicom id='+id+'}';
			jInsertEditorText(tag, '".$name."');
			SqueezeBox.close();
		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		JHtml::_('behavior.modal');

		$link = 'index.php?option=com_digicom&view=products&layout=modal&tmpl=component';

		$button = new JObject;
		$button->modal = true;
		$button->class = 'btn';
		$button->link = $link;
		$button->text = JText::_('PLG_EDITORS-XTD_DIGICOM_DIGICOM_TITLE');
		$button->name = 'copy';
		$button->options = "{handler: 'iframe', size: {x: 800, y: 500}}";

		return $button;
	}
}
