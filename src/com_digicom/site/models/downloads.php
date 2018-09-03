<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

$com_path = JPATH_SITE . '/components/com_digicom/';
require_once $com_path . 'helpers/route.php';

JModelLegacy::addIncludePath($com_path . '/models', 'DigicomModel');

jimport('joomla.filesystem.file');
use Joomla\Registry\Registry;
// TODO : Remove JRequest to JInput and php visibility

class DigiComModelDownloads extends JModelList
{

	/**
	 * Model context string.
	 *
	 * @var    string
	 * @since  3.1
	 */
	public $_context = 'com_digicom.downloads';

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @note Calling getState in this method will result in recursion.
	 *
	 * @since   3.1
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('site');

		// $offset = $app->input->get('limitstart', 0, 'uint');
		// $this->setState('list.offset', $offset);
		$app = JFactory::getApplication();

		// Load the parameters. Merge Global and Menu Item params into new object
		$appparams = $app->getParams();
		$menuParams = new Registry;

		if ($menu = $app->getMenu()->getActive())
		{
			$menuParams->loadString($menu->params);
		}

		$params = clone $menuParams;
		$params->merge($appparams);

		$this->setState('params', $params);

		$this->setState('list.limit', $params->get('maximum', 200));
		$this->setState('filter.published', 1);

		$this->setState('list.ordering', $this->_buildCategoryOrderBy());
		$this->setState('list.itemsordering', $this->_buildItemOrderBy());


		$user = new DigiComSiteHelperSession();
		$this->setState('filter.userid', $user->_customer->id);

	}

	/**
	 * Build the orderby for the query
	 *
	 * @return  string	$orderby portion of query
	 *
	 * @since   1.5
	 */
	protected function _buildCategoryOrderBy()
	{
		$app		= JFactory::getApplication('site');
		$db			= $this->getDbo();
		$params		= $this->state->params;

		$productOrderby		= $params->get('orderby', 'rdate');
		$productOrderDate	= $params->get('order_date','');
		$orderquery				= DigiComSiteHelperQuery::orderbyDownload($productOrderby, $productOrderDate, 'c','title');
		// echo $orderquery;die;
		return $orderquery;
	}
	/**
	 * Build the orderby for the query
	 *
	 * @return  string	$orderby portion of query
	 *
	 * @since   1.5
	 */
	protected function _buildItemOrderBy()
	{
		$app		= JFactory::getApplication('site');
		$db			= $this->getDbo();
		$params		= $this->state->params;

		$productOrderby		= $params->get('iorderby', 'order');
		$productOrderDate	= $params->get('order_date','');
		$orderquery				= DigiComSiteHelperQuery::orderbyDownload($productOrderby, $productOrderDate);
		// echo $orderquery;die;
		return $orderquery;
	}

	/**
	 * Redefine the function and add some properties to make the styling more easy
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   3.1
	 */
	public function getItems()
	{
		$db = $this->getDbo();
		// Invoke the parent getItems method to get the main list
		$items = parent::getItems();

		// //$ordering =  $this->state->get('list.ordering', 'c.title ASC');
		// $ordering =  'c.title ASC';
		$itemsOrdering =  $this->state->get('list.itemsordering', 'p.ordering DESC');
		//echo $itemsOrdering;die;

		if (!count($items))
		{
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new Registry;

			if ($active)
			{
				$params->loadString($active->params);
			}
		}
		//products
		if ($items)
		{

			$bundleItems = array();
			foreach($items as $key=>$product){
				if($product->type != 'reguler'){
					switch($product->bundle_source){
						case 'category':
							$BundleTable = JTable::getInstance('Bundle', 'Table');
							$BundleList = $BundleTable->getFieldValues('product_id',$product->productid,$product->bundle_source);
							$bundle_ids = $BundleList->bundle_id;
							if($bundle_ids){

								$query = $db->getQuery(true)
									->select(array('p.id as productid','p.name','p.catid','p.images','p.introtext','p.attribs','p.publish_up'))
									->from($db->quoteName('#__digicom_products','p'))
									//->from($db->quoteName('#__categories','c'))
									->where($db->quoteName('p.bundle_source').' IS NULL')
									->where($db->quoteName('p.catid').' in ('.$bundle_ids.')')
									// ->where($db->quoteName('c.id').' in ('.$bundle_ids.')')
									//->order($db->quoteName('id').' DESC');
									//->order($itemsOrdering . ', ' . $ordering);
									->order($itemsOrdering);
									//echo $query->__toString();die;
								$db->setQuery($query);
								$bundleItems[] = $db->loadObjectList();
								//we should show only items
							}
							//print_r($bundleItems);die;
							unset($items[$key]);

							break;
						case 'product':
						default:
							// its bundle by product
							$BundleTable = JTable::getInstance('Bundle', 'Table');
							$BundleList = $BundleTable->getFieldValues('product_id',$product->productid,$product->bundle_source);
							$bundle_ids = $BundleList->bundle_id;
							//echo $bundle_ids;die;
							if($bundle_ids){
								$db = $this->getDbo();
								$query = $db->getQuery(true)
									->select(array('p.id as productid','p.name','p.catid','p.images','p.introtext','p.attribs','p.publish_up'))
									->from($db->quoteName('#__digicom_products','p'))
									->where($db->quoteName('p.bundle_source').' IS NULL')
									->where($db->quoteName('p.id').' in ('.$bundle_ids.')')
									//->order($db->quoteName('id').' DESC');
									->order($itemsOrdering);
								$db->setQuery($query);
								$bundleItems[] = $db->loadObjectList();
							}
							//we should show only items
							unset($items[$key]);

							break;
					}
				}
			}
			//print_r($items);die;
			//print_r($bundleItems);die;
			//we got all our items
			// now add bundle item to the items array
			if(count($bundleItems) >0){
				foreach($bundleItems as $keybundle2=>$item2){

					foreach($item2 as $item3){
						$items[] = $item3;
					}
				}
			}
			
			//print_r($items);die;
			// check and add products files
			$configs = JComponentHelper::getComponent('com_digicom')->params;
			$pagination = $configs->get('download_pagination', 0);
			if(!$pagination){

				$productAdded = array();

				foreach($items as $key=>$product){

					$query = $db->getQuery(true);
					$query->select($db->quoteName(array('id', 'name', 'url', 'hits')));
					$query->from($db->quoteName('#__digicom_products_files'));
					$query->where($db->quoteName('product_id') . ' = '. $db->quote($product->productid));
					$query->order('ordering ASC');
					// Reset the query using our newly populated query object.
					$db->setQuery($query);
					$files = $db->loadObjectList();

					if(count($files) >0){
						foreach($files as $key2=>$item){
							$downloadid = array(
								'fileid' => $item->id
							);
							$downloadcode = json_encode($downloadid);
							$item->downloadid = base64_encode($downloadcode);

							$parsed = parse_url($item->url);
							if (empty($parsed['scheme'])) {
								$fileLink = JPATH_BASE.DIRECTORY_SEPARATOR.$item->url;
							}else{
								$fileLink = $item->url;
							}
							if (JFile::exists($fileLink)) {
								$filesize = filesize ($fileLink);
								$item->filesize = DigiComSiteHelperDigiCom::FileSizeConvert($filesize);
								$item->filemtime = date("d F Y", filemtime($fileLink));
							}else{

								$parsed = parse_url($fileLink);
								if (empty($parsed['scheme'])){
									$item->filesize = JText::_('COM_DIGICOM_FILE_DOESNT_EXIST');
									$item->filemtime = JText::_('COM_DIGICOM_FILE_DOESNT_EXIST');
								}else{
									$item->filesize = '';
									$item->filemtime = '';
								}
							}

						}

						$product->files = $files;
						if(isset($productAdded[$product->productid])) unset($items[$key]);
						$productAdded[$product->productid] = true;

					}else{
						unset($items[$key]);
					}


				}
			} // end if pagination

			$itemsArray = array();
			$catkey = array();

			//print_r($items);die;

			foreach($items as $key=>$item){
				if(in_array($item->catid, $catkey)){
					$itemsArray[$item->catid]['items'][] = $item;
				}else{
					$catkey[] = $item->catid;
					$itemsArray[$item->catid] = array();

					$options    = array();
					$categories = JCategories::getInstance('Digicom', $options);
					$category   = $categories->get($item->catid);

					$itemsArray[$item->catid]['title'] = $category->title;
					$itemsArray[$item->catid]['catid'] = $item->catid;
					$itemsArray[$item->catid]['items'] = array();
					$itemsArray[$item->catid]['items'][] = $item;
				}
			}

		}else{
			$itemsArray = array();
		}

		return $itemsArray;
	}


	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		$app = JFactory::getApplication('site');
		$published = $this->state->params->get('published', 1);

		// $ordering =  $this->state->get('list.ordering', 'p.ordering ASC');
		$itemsOrdering =  $this->state->get('list.itemsordering', 'p.ordering DESC');
		$db = JFactory::getDBO();

		$search = JRequest::getVar('search', '');
		$search = trim($search);

		$query = $db->getQuery(true);
		// Select required fields from the downloads.
		//$query->select('DISTINCT(p.id) as productid')
		$query->select('DISTINCT p.id as productid')
			  ->select(array('p.name,p.catid,p.images,p.introtext,p.bundle_source,p.product_type as type, p.attribs, p.publish_up'))
			  ->from($db->quoteName('#__digicom_licenses') . ' AS l')
			  ->join('inner', '#__digicom_products AS p ON l.productid = p.id');
		
		if ($this->state->params->get('show_pagination_limit'))
		{
			$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'uint');
		}
		else
		{
			$limit = $this->state->params->get('maximum', 20);
		}

		// TODO:: should be $limit instead 999, no pagination now
		$this->setState('list.limit', 999);

		$offset = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $offset);

		// Optionally filter on entered value
		//search
		if ($this->state->get('list.filter'))
		{
			$query->where(
				$db->quoteName('p.name') . ' LIKE '	 . $db->quote('%' . $this->state->get('list.filter') . '%')
				.
				' or '
				.
				$db->quoteName('l.licenseid') . ' = '	 . $db->quote( $this->state->get('list.filter') )
			);
		}

		$query->where($db->quoteName('l.active') . ' = ' . (int) $published);
		$query->where($db->quoteName('p.published') . ' = ' . (int) $published);

		// filter by userid
		$userid = $this->state->get('filter.userid');
		$query->where($db->quoteName('l.userid') . ' = ' . $userid);

		// query by expire
		$query->where(' ( DATEDIFF(`expires`, now()) > -1 or DATEDIFF(`expires`, now()) IS NULL )' );

		// Add the list ordering clause.
		$query->order($itemsOrdering);
		//echo $query->__tostring();die;
		return $query;
	}

	function getfileinfo()
	{

		$jinput = JFactory::getApplication()->input;
		$fileid = $jinput->get('downloadid', '0');
		
		if($fileid == '0')
		{
			$fileid = $jinput->get('token', '0');
			if($fileid == '0') return false;
		}
		
		//echo $fileid;die;
		$fileid = base64_decode($fileid);
		$fileid = json_decode($fileid);
		//print_r( $fileid );die;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('p.name','product_name'));
		$query->select($db->quoteName(array('pf.id', 'pf.product_id','pf.name', 'pf.url', 'pf.hits')));
		$query->from($db->quoteName('#__digicom_products_files','pf'));
		$query->join('INNER', $db->quoteName('#__digicom_products','p') . ' ON ( '.$db->quoteName('pf.product_id') . ' = ' . $db->quoteName('p.id') .')' );
		$query->where($db->quoteName('pf.id') . ' = '. $db->quote($fileid->fileid));
		$query->order('id DESC');
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		return $db->loadObject();

	}

}
