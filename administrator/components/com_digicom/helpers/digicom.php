<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Weblinks helper.
 *
 * @since  1.6
 */
class DigiComHelperDigiCom extends JHelperContent{
	
	/**
	 * Configure the Linkbar.
		*
	 * @param   string  $vName  The name of the active view.
		*
	 * @return  void
		*
	 * @since   1.6
	 */

	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_DASHBOARD'),
			'index.php?option=com_digicom',
			$vName == 'digicom'
		);
		/*JHtmlSidebar::addEntry(
			JText::_('VIEWTREESETTINGMANAGER'),
			'index.php?option=com_digicom&controller=configs',
			$vName == 'configs'
		);*/
		
		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_CATEGORIES'),
			'index.php?option=com_digicom&view=categories',
			$vName == 'categories'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_PRODUCTS'),
			'index.php?option=com_digicom&view=products',
			$vName == 'products'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_FILE_MANAGER'),
			'index.php?option=com_digicom&view=filemanager',
			$vName == 'filemanager'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_CUSTOMERS'),
			'index.php?option=com_digicom&view=customers',
			$vName == 'customers'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_ORDERS'),
			'index.php?option=com_digicom&view=orders',
			$vName == 'orders'
		);
		/*
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREELICENCES'),
			'index.php?option=com_digicom&controller=licenses',
			$vName == 'licenses'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREEATTR'),
			'index.php?option=com_digicom&controller=attributes',
			$vName == 'attributes'
		);
		*/
		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_DISCOUNTS'),
			'index.php?option=com_digicom&view=discounts',
			$vName == 'discounts'
		);
		/*
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREESUBSCRIP'),
			'index.php?option=com_digicom&controller=subscription',
			$vName == 'subscription'
		);
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREETAX'),
			'index.php?option=com_digicom&controller=tax',
			$vName == 'logs'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREEPSYSTEMEMAILS'),
			'index.php?option=com_digicom&view=email',
			$vName == 'email'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREEEMAILREMINDER'),
			'index.php?option=com_digicom&controller=emailreminders',
			$vName == 'emailreminders'
		);
		*/
		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_REPORTS'),
			'index.php?option=com_digicom&view=stats',
			$vName == 'stats'
		);
		
		/*JHtmlSidebar::addEntry(
			JText::_('VIEWTREELOGS'),
			'index.php?option=com_digicom&controller=logs',
			$vName == 'logs'
		);*/
		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_ABOUT'),
			'index.php?option=com_digicom&view=about',
			$vName == 'about'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_COLLAPSE'),
			'#togglesidebar'
		);
		
	}
	
	public static function renderSidebar(){
		// Collect display data
		$data                 = new stdClass;
		$data->list           = JHtmlSidebar::getEntries();
		$data->filters        = JHtmlSidebar::getFilters();
		$data->action         = JHtmlSidebar::getAction();
		$data->displayMenu    = count($data->list);
		$data->displayFilters = count($data->filters);
		$data->hide           = JFactory::getApplication()->input->getBool('hidemainmenu');

		// Create a layout object and ask it to render the sidebar
		$layout      = new JLayoutFile('sidebars.submenu');
		$sidebarHtml = $layout->render($data);

		return $sidebarHtml;
		
	}
	
	
	public static function format_price ($amount, $ccode, $add_sym = true, $configs) {

		$db = JFactory::getDBO();

		$code = 0;

		$price_format = '%'.$configs->get('totaldigits','5').'.'.$configs->get('decimaldigits','2').'f';
		$res =  sprintf($price_format,$amount) ;//. " " . $tax['currency'] . '<br>';

		if ($add_sym) {
			if ($configs->get('currency_position','1'))
				$res = $res . " " . $ccode;
			else
				$res = $ccode. " " . $res;
		}

		return $res; 
	}

	
	public static function cleanUpImageFolders($root, $folders) {

		foreach ($folders as $i => $folder) {
			$x = explode (myDC, $folder);
			if (trim($x[0]) == $root) unset($x[0]);

			$folders[$i] = implode(myDC, $x);

		}
		return $folders;
	}

	
	public static function getLiveSite() {

		// Check if a bypass url was set
		$config 	= JFactory::getConfig();
		$live_site 	= $config->get('live_site');

		// Determine if the request was over SSL (HTTPS)
		if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) {
			$https = 's://';
		} else {
			$https = '://';
		}

		$subdom = $_SERVER['PHP_SELF']	;
		$subdom = explode ("/", $subdom);
		$res = array();
		foreach ($subdom as $i => $v) {
			if (strtolower(trim($v)) != "index.php") $res[] = trim($v);
			else break;
		}
		$subdom = implode ("/", $res);
		/*
		* Since we are assigning the URI from the server variables, we first need
		* to determine if we are running on apache or IIS.  If PHP_SELF and REQUEST_URI
		* are present, we will assume we are running on apache.
		*/
		if (!empty ($_SERVER['PHP_SELF']) && !empty ($_SERVER['REQUEST_URI'])) {

			/*
			 * To build the entire URI we need to prepend the protocol, and the http host
			 * to the URI string.
			 */
			if (!empty($live_site)) {
				$theURI = $live_site;// . $_SERVER['REQUEST_URI'];
			} else {
				$theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $subdom;// . $_SERVER['REQUEST_URI'];
			}

		/*
		* Since we do not have REQUEST_URI to work with, we will assume we are
		* running on IIS and will therefore need to work some magic with the SCRIPT_NAME and
		* QUERY_STRING environment variables.
		*/
		} else {
			// IIS uses the SCRIPT_NAME variable instead of a REQUEST_URI variable... thanks, MS
			if (!empty($live_site)) {
					$theURI = $live_site . $_SERVER['SCRIPT_NAME'];
			} else {
					$theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $subdom;//. $_SERVER['SCRIPT_NAME'];
			}

			// If the query string exists append it to the URI string
			if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
			//					$theURI .= '?' . $_SERVER['QUERY_STRING'];
			}
		}

		return $theURI;
	}

	
	public static function CreateIndexFile($dir)
	{
		if (file_exists($dir))
		{
			if (!file_exists($dir.DS."index.html"))
			{
				$handle = @fopen($dir.DS."index.html", "w");
				@fwrite($handle, '<html><body bgcolor="#FFFFFF"></body></html>');
				@fclose($handle);
			}
		}
	}

	
	/**
	 * Get latest orders, to use with DS Dashboard
	 * @return unknown
	 */
	public static function getOrders($limit) {
		$db = JFactory::getDBO();
		$sql = '
			SELECT o.*, u.username, c.firstname, c.lastname
			FROM
				#__digicom_orders o,
				#__users u,
				#__digicom_customers c
			WHERE
				`u`.`id`=`o`.`userid` AND
				`c`.`id`=`u`.`id` AND 
				`status` = "Active"
			ORDER BY `o`.`order_date` DESC
			LIMIT '.$limit.'';
		$db->setQuery($sql);
		if (!$orders = $db->loadObjectList()) {
			echo $db->getErrorMsg();
		}
		return $orders;
	}

	/**
	 * Get latest products, to use with DS Dashboard
	 * @return unknown
	 */
	public static function getProducts($limit) {
		$db = JFactory::getDBO();
		$sql = '
			SELECT
				DISTINCT p.id,p.name,p.catid, p.description, p.publish_up,
				c.name AS category
			FROM
				#__digicom_products p,
				#__digicom_categories c
			WHERE
				p.published = 1 AND
				c.published = 1 AND
				p.catid = c.id
			ORDER BY p.id DESC
			LIMIT '.$limit.'
		';
		$db->setQuery($sql);
		if (!$products = $db->loadObjectList()) {
			echo $db->getErrorMsg();
		}
		return $products;
	}
	/**
	 * Get latest products, to use with DS Dashboard
	 * @return unknown
	 */
	public static function getMostSoldProducts($limit) {
		
		$db = JFactory::getDbo();
		// Create a new query object.

		$query = $db->getQuery(true);
		$query->select( 'SUM('.$db->quoteName('od.quantity') .') as total');
		$query->select($db->quoteName(array('od.productid', 'od.package_type')));
		
		$query->select($db->quoteName(array('p.name','p.price')));

		$query->from($db->quoteName('#__digicom_orders_details').' od');
		$query->from($db->quoteName('#__digicom_products').' p');

		$query->where($db->quoteName('p.id') . '= '. $db->quoteName('od.productid'));

		$date = DigiComHelperDigiCom::getStartEndDateMonth();
		$startdate_str = $date["0"];
		$enddate_str = $date["1"];

		$query->where($db->quoteName('od.purchase_date') . ' >= '. $db->quote($startdate_str));
		$query->where($db->quoteName('od.purchase_date') . ' < '. $db->quote($enddate_str));

		$query->where($db->quoteName('od.published') . ' = '. $db->quote('1'));

		$query->group($db->quoteName('od.productid'));
		$query->order($db->quoteName('total').' DESC');
		$query->setLimit($limit);

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		
		if (!$products = $db->loadObjectList()) {
			echo $db->getErrorMsg();
		}
		return $products;
	}
	
	public static function getStartEndDateMonth(){
		$return = array();
		$date = new DateTime('now');
		$date->modify('first day of this month');
		$return[] = $date->format('Y-m-d') . ' 00:00:00';

		$date->modify('first day of next month');
		$return[] = $date->format('Y-m-d') . ' 00:00:00';

		return $return;
	}


	/**
	 * Get Top Customers, to use with DS Dashboard
	 * @param unknown $limit
	 */
	public static function getCustomers($limit) {
		$db = JFactory::getDBO();
		$sql = '
			SELECT o.*, u.username, c.firstname, c.lastname
			FROM
				#__digicom_orders o,
				#__users u,
				#__digicom_customers c
			WHERE
				u.id=o.userid AND
				c.id=u.id AND 
				status = "Active"
			GROUP BY o.userid
			LIMIT '.$limit.'
		';
		$db->setQuery($sql);
		if (!$customers = $db->loadObjectList()) {
			echo $db->getErrorMsg();
		}
		return $customers;
	}
	
	public static function publishAndExpiryHelper(&$img, &$alt, &$times, &$status, $timestart, $timeend, $published, $configs, $limit = 0, $used = 0) {

		$now = time();
		$nullDate = 0;

		if ( $now <= $timestart && $published == "1" ) {
					$img = "tick.png";
					$alt = JText::_('HELPERPUBLISHED');
		} else if ($limit > 0 && $used >= $limit) {
				$img = "publish_r.png";
				$alt = JText::_('HELPERUSEAGEEXPIRED');
		} else if ( ( $now <= $timeend || $timeend == $nullDate ) && $published == "1" ) {
				$img = "tick.png";
				$alt = JText::_('HELPERPUBLISHED');
		} else if ( $now > $timeend && $published == "1" && $timeend != $nullDate) {
				$img = "publish_r.png";
				$alt = JText::_('HELPEREXPIRED');
		} elseif ( $published == "0" ) {
				$img = "publish_x.png";
				$alt = JText::_('HELPERUNPUBLICHED');
		}
		$times = '';

		if (isset( $timestart)) {
			if ( $timestart == $nullDate) {
					$times .= "<tr><td>".(JText::_("HELPERALWAWSPUB"))."</td></tr>";
				} else {
					$times .= "<tr><td>".(JText::_("HELPERSTARTAT"))." ".date($configs->get('time_format','DD-MM-YYYY'), $timestart)."</td></tr>";
				}
		}

		if ( isset( $timeend ) ) {
			if ( $timeend == $nullDate) {
				$times .= "<tr><td>".(JText::_("HELPERNEVEREXP"))."</td></tr>";
			} else {
				$times .= "<tr><td>".(JText::_("HELPEXPAT"))." ".date($configs->get('time_format','DD-MM-YYYY'), $timeend)."</td></tr>";
			}
		}

		$status = '';
		$promo = new stdClass();
		if (!isset ($promo->codelimit)) {
			$promo->codelimit = 0;
		}
		if (!isset ($promo->used)) {
			$promo->used = 0;
		}

		$remain = $promo->codelimit - $promo->used;
		if (($timeend > $now || $timeend == $nullDate )&& ($limit == 0 || $used < $limit) && $published == "1") {
			$status = JText::_("HELPERACTIVE");
		} else if ($published == "0") {
			$status = "<span style='color:red'>".(JText::_("HELPERUNPUBLISHED"))." </span>";
		} else if ($limit >0  && $used  >= $limit) {
			$status = "<span style='color:red'>".(JText::_("HELPEREXPIRE")).": (".(JText::_("Amount")).")</span>";
		} else if ($timeend != $nullDate && $timeend < $now && ($remain < 1 && $promo->codelimit > 0)) {
			$status = "<span style='color:red'>".(JText::_("HELPEREXPIRE")).": (".(JText::_("Date"))." ,".(JText::_("Amount")).")</span>";
		} else if ($timeend < $now && $timeend != $nullDate){
			$status = "<span style='color:red'>".(JText::_("HELPEREXPIRE")).": (".(JText::_("Date")).")</span>";
		} else {
			$status = "<span style='color:red'>".(JText::_("HELPERPROMOERROR"))."</span>";
		}
	}
	
	public static function setSidebarRight(){
		
		$input = JFactory::getApplication()->input;
		$tmpl = $input->get('tmpl','');
		$ajax = $input->get('ajax','');
		if($tmpl == 'component' or $ajax =='1') return;
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'layouts'.DS.'toolbar'.DS.'sidebar-right.php');
		return true;
	}

	public static function addAdminStyles(){
		
		// load core script
		$document = JFactory::getDocument();
		$document->addScript(JURI::root(true).'/media/digicom/assets/js/digicom.js?v=1.0.0&amp;sitepath='.JURI::root(true).'/');
		$document->addStyleSheet(JURI::root(true).'/media/digicom/assets/css/digicom-admin.css');
	}


}
