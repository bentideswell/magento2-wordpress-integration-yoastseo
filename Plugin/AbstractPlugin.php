<?php
/**
 *
**/
namespace FishPig\WordPress_Yoast\Plugin;

use \FishPig\WordPress\Model\Config as WPConfig;
use \FishPig\WordPress\Model\App\Factory;
use \FishPig\WordPress_Yoast\Helper\Data as DataHelper;
use \FishPig\WordPress\Helper\View as ViewHelper;
use \FishPig\WordPress\Api\Data\Entity\ViewableInterface;
use \Magento\Framework\Registry;

abstract class AbstractPlugin extends \Magento\Framework\DataObject implements \FishPig\WordPress\Plugin\SeoInterface
{	
	/**
	 * Separator map
	 *
	 * @var array
	**/
	protected $_separators = array(
		'sc-dash'   => '-',
		'sc-ndash'  => '&ndash;',
		'sc-mdash'  => '&mdash;',
		'sc-middot' => '&middot;',
		'sc-bull'   => '&bull;',
		'sc-star'   => '*',
		'sc-smstar' => '&#8902;',
		'sc-pipe'   => '|',
		'sc-tilde'  => '~',
		'sc-laquo'  => '&laquo;',
		'sc-raquo'  => '&raquo;',
		'sc-lt'     => '&lt;',
		'sc-gt'     => '&gt;',
	);
	
	/**
	 * @ \FishPig\WordPress\Model\Config
	**/
	protected $_config = null;
	
	/**
	 * @ \FishPig\WordPress_Yoast\Helper\Data
	**/
	protected $_dataHelper = null;

	/**
	 * @ \FishPig\WordPress\Helper\View
	**/	
	protected $_viewHelper = null;

	/**
	 * @ \FishPig\WordPress\Model\App\Factory
	**/	
	protected $_factory = null;
	
	/**
	 * Constructor
	 *
	 * @param \FishPig\WordPress\Model\Config $config,
	 * @param \FishPig\WordPress\Helper\Plugin $pluginHelper,
	 * @param \FishPig\WordPress\Helper\View $viewHelper,
	 * @param \Magento\Framework\Registry $registry,
	 * @param $data = []
	**/
	public function __construct(WPConfig $config, DataHelper $dataHelper, ViewHelper $viewHelper, Registry $registry, Factory $factory, $data = [])
	{
		$this->_config = $config;
		$this->_dataHelper = $dataHelper;
		$this->_viewHelper = $viewHelper;
		$this->_registry = $registry;
		$this->_factory = $factory;
	}

	/**
	 * Determine whether the plugin is enabled in WordPress
	 *
	 * @return bool
	**/
	public function isEnabled()
	{
		return $this->_dataHelper->isEnabled();
	}
	
	/**
	 * Get the page title
	 *
	 * @param $object
	 * @param $callback
	 * @return string
	**/
	public function aroundGetPageTitle($object, $callback)
	{
		if ($this->isEnabled()) {
			$this->_setupRewriteData($object);
			
			if ($value = $this->_aroundGetPageTitle($object)) {
				return $value;
			}
		}

		return $callback();
	}
	
	/**
	 * Get the page title
	 *
	 * @param ViewableInterface $object
	 * @return string|null
	**/
	protected function _aroundGetPageTitle(ViewableInterface $object)
	{
		return null;
	}
	
	/**
	 * Get the meta description
	 *
	 * @param $object
	 * @param $callback
	 * @return string
	**/
	public function aroundGetMetaDescription($object, $callback)
	{
		if ($this->isEnabled()) {
			$this->_setupRewriteData($object);
			
			if ($value = $this->_aroundGetMetaDescription($object)) {
				return $value;
			}
		}

		return $callback();
	}

	/**
	 * Get the meta description
	 *
	 * @param ViewableInterface $object
	 * @return string|null
	**/
	protected function _aroundGetMetaDescription(ViewableInterface $object)
	{
		return null;
	}
	
	/**
	 * Get the meta keywords
	 *
	 * @param $object
	 * @param $callback
	 * @return string
	**/
	public function aroundGetMetaKeywords($object, $callback)
	{
		if ($this->isEnabled()) {
			$this->_setupRewriteData($object);
			
			if ($value = $this->_aroundGetMetaKeywords($object)) {
				return $value;
			}
		}
		
		return $callback();
	}
	
	/**
	 * Get the meta keywords
	 *
	 * @param ViewableInterface $object
	 * @return string|null
	**/
	protected function _aroundGetMetaKeywords(ViewableInterface $object)
	{
		return null;
	}
	
	/**
	 * Get the meta robots value
	 *
	 * @param $object
	 * @param $callback
	 * @return string
	**/
	public function aroundGetRobots($object, $callback)
	{		
		if ($this->isEnabled()) {
			if (!$this->_viewHelper->canDiscourageSearchEngines()) {
				$this->_setupRewriteData($object);
				
				if ($value = $this->_aroundGetRobots($object)) {
					if ($this->_isNoindex('subpages_wpseo') && (int)$this->_viewHelper->getRequest()->getParam('page') > 1) {
						$value['index'] = 'noindex';
					}

					return strtoupper(implode(',', $value));
				}
			}
		}
		
		return $callback();
	}
	
	/**
	 * Get the meta robots value
	 *
	 * @param ViewableInterface $object
	 * @return string|null
	**/
	protected function _aroundGetRobots(ViewableInterface $object)
	{
		return null;
	}
	
	/**
	 * Get the canonical URL
	 *
	 * @param $object
	 * @param $callback
	 * @return string
	**/
	public function aroundGetCanonicalUrl($object, $callback)
	{
		if ($this->isEnabled()) {
			$this->_setupRewriteData($object);
			
			if ($value = $this->_aroundGetCanonicalUrl($object)) {
				return $value;
			}
		}
		
		return $callback();
	}
	
	/**
	 * Get the canonical URL
	 *
	 * @param ViewableInterface $object
	 * @return string|null
	**/
	protected function _aroundGetCanonicalUrl(ViewableInterface $object)
	{
		return null;
	}

	/**
	 * Given a key that determines which format to load
	 * and a data array, merge the 2 to create a valid title
	 *
	 * @param string $key
	 * @param array $data
	 * @return string|false
	 */
	protected function _rewriteString($format, $data = [])
	{
		if (strpos($format, '%%page%%') !== false || strpos($format, '%%pagetotal%%') !== false) {
			if ($pagerBlock = $this->_viewHelper->getLayout()->getBlock('wp.post_list.pager')) {
				if ($listBlock = $pagerBlock->getParentBlock()->getParentBlock()) {
					$listBlock->getPostListHtml();

					if ($pagerBlock->getCollection()) {
						$data = $this->getRewriteData();
						
						$lastPageNumber = $pagerBlock->getLastPageNum();
						$data['pagetotal'] = $lastPageNumber;
						$data['page'] = sprintf('Page %d of %d', $data['pagenumber'], $data['pagetotal']);
						
						$this->setRewriteData($data);
					}
				}
			}
		}
		
		if (!$data) {
			$data = $this->getRewriteData();
		}

		$rwt = '%%';
		$value = array();
		$parts = preg_split("/(" . $rwt . "[a-z_-]{1,}" . $rwt . ")/iU", $format, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);

		foreach($parts as $part) {
			if (substr($part, 0, strlen($rwt)) === $rwt && substr($part, -(strlen($rwt))) === $rwt) {
				$part = trim($part, $rwt);
				
				if (isset($data[$part])) {
					$value[] = $data[$part];
				}
			}
			else {
				$value[] = $part;
			}
		}

		if (($value = trim(implode('', $value))) !== '') {
			return $value;
		}
		
		return false;
	}

	/**
	 * Setup the rewrite data for $object
	 *
	 * @param ViewableInterface $object
	 * @return $this
	**/
	protected function _setupRewriteData(ViewableInterface $object)
	{
		if (!$this->hasRewriteData()) {
			$data = array(
				'sitename' => $this->_config->getOption('blogname'),
				'sitedesc' => $this->_config->getOption('blogdescription'),
				'currenttime' => $this->_viewHelper->formatTime(date('Y-m-d H:i:s')),
				'currentdate' => $this->_viewHelper->formatDate(date('Y-m-d H:i:s')),
				'currentmonth' => date('F'),
				'currentyear' => date('Y'),
				'sep' => '|',
				'pagenumber' => max(1, (int)$this->_viewHelper->getRequest()->getParam('page')),
			);

			if ($sep = $this->getConfigOption('separator')) {
				if (isset($this->_separators[$sep])) {
					$data['sep'] = $this->_separators[$sep];
				}
			}

			if (($value = trim($this->_viewHelper->getSearchTerm(true))) !== '') {
				$data['searchphrase'] = $value;
			}

			if ($object  instanceof \FishPig\WordPress\Model\Post) {
				$data['date'] = $object->getPostDate();
				$data['title'] = $object->getPostTitle();
				$data['excerpt'] = trim($object->getExcerpt(30));
				$data['excerpt_only'] = $data['excerpt'];
				
				$categories = array();

				foreach($object->getTermCollection('category')->load() as $category) {
					$categories[] = $category->getName();	
				}
				
				$data['category'] = implode(', ', $categories);
				$data['modified'] = $object->getPostModified();
				$data['id'] = $object->getId();
				$data['name'] = $object->getUser()->getUserNicename();
				$data['userid'] = $object->getUser()->getId();
			}
			else if ($object instanceof \FishPig\WordPress\Model\Term) {
				$data['term_description'] = trim(strip_tags($object->getDescription()));
				$data['term_title'] = $object->getName();
			}
			else if ($object instanceof \FishPig\WordPress\Model\Archive) {
				$data['date'] = $object->getName();
			}
			else if ($object instanceof \FishPig\WordPress\Model\User) {
				$data['name'] = $object->getDisplayName();
			}	
			
			
			if ($object instanceof \FishPig\WordPress\Model\Post\Type) {
				$data['pt_plural'] = $object->getPluralName();
			}

			$this->setRewriteData($data);		
		}

		return $this;
	}
	
	/**
	 * Retrieve the rewrite data
	 *
	 * @return array
	 */
	public function getRewriteData(array $updates = [])
	{
		$rewriteData = $this->getData('rewrite_data');//$this->getConfigOption('rewrite_data');
		
		if (!is_array($rewriteData)) {
			$rewriteData = array();
		}
		
		if (!is_array($updates)) {
			$updates = array();
		}
		
		return $updates ? array_merge($rewriteData, $updates) : $rewriteData;
	}
	
	/**
	 * Retrieve the title format for the given key
	 *
	 * @param string $key
	 * @return string
	 */
	protected function _getPageTitleFormat($key)
	{
		return trim($this->getConfigOption('title_' . $key));
	}
	
	/**
	 * Retrieve the title format for the given key
	 *
	 * @param string $key
	 * @return string
	 */
	protected function _getMetaDescriptionFormat($key)
	{
		return trim($this->getConfigOption('metadesc_' . $key));
	}
	
	/**
	 * Retrieve the title format for the given key
	 *
	 * @param string $key
	 * @return string
	 */
	protected function _getMetaKeywordsFormat($key)
	{
		return trim($this->getConfigOption('metakey_' . $key));
	}
	
	/**
	 * Retrieve the title format for the given key
	 *
	 * @param string $key
	 * @return string
	 */
	protected function _isNoindex($key)
	{
		return (int)$this->getConfigOption('noindex_' . $key) === 1;
	}
	
	/**
	 * Determine whether subpages (eg. page/2/) should always be noindex
	 *
	 * @return bool
	**/
	public function _isSubPageNoindex()
	{
		return $this->_isNoindex('subpages_wpseo');
	}
	
	/**
	 * @param string $key
	 * @return mixed
	**/
	public function getConfigOption($key = null)
	{
		return $this->_dataHelper->getConfigOption($key);
	}
}
