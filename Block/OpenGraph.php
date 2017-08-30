<?php
/**
 * @category Fishpig
 * @package Fishpig_Wordpress_Yoast
 * @author Ben Tideswell <help@fishpig.co.uk>
 */

namespace FishPig\WordPress_Yoast\Block;

use \FishPig\WordPress\Block\AbstractBlock;

class OpenGraph extends AbstractBlock
{
	/**
	 * @var FishPig\WordPress_Yoast\Helper\Data
	**/
	protected $_helper = null;

	/**
	 * @var FishPig\WordPress\Model\AbstractModel
	**/	
	protected $_entity = null;
	
	/**
	 * @return FishPig\WordPress_Yoast\Helper\Data
	**/
	protected function _getHelper()
	{
		if ($this->_helper === null) {
			$this->_helper = \Magento\Framework\App\ObjectManager::getInstance()->get('FishPig\WordPress_Yoast\Helper\Data');
		}	
		
		return $this->_helper;
	}
	
	/**
	 * @return array
	**/
	protected function _getTagDefaults()
	{
		return [
			'locale' => $this->_config->getLocaleCode(),
			'type' => 'blog',
			'title' => $this->_config->getOption('blogname'),
			'description' => $this->_config->getOption('blogdescription'),
			'url' => $this->_app->getWpUrlBuilder()->getUrl(),
			'site_name' => $this->_config->getOption('blogname'),
			'article:publisher' => $this->_getHelper()->getConfigOption('facebook_site'),
			'image' => $this->_getHelper()->getConfigOption('og_default_image'),
		];
	}

	/**
	 * @return array
	**/
	public function getTags()
	{
		$object = $this->_getEntity();
		$tags = [];
		
		if ($object instanceof \FishPig\WordPress\Model\Homepage) {
			$tags = array(
				'description' => $this->_getHelper()->getConfigOption('og_frontpage_desc'),
				'image' => $this->_getHelper()->getConfigOption('og_frontpage_image'),
			);
		}
		else if ($object instanceof \FishPig\WordPress\Model\Post) {
			$tags = array(
				'type' => 'article',
				'title' => $object->getName(),
				'description' => $object->getMetaDescription(),
				'url' => $object->getUrl(),
				'image' => $object->getImage() ? $object->getImage()->getAvailableImage() : '',
				'updated_time' => $object->getPostModifiedDate('c'),
				'article:author' => $object->getUser()->getMetaValue('facebook'),
				'article:published_time' => $object->getPostDate('c'),
				'article:modified_time' => $object->getPostModifiedDate('c'),
			);

			foreach(['title', 'description', 'image'] as $key) {
				if ($value = $object->getMetaValue('_yoast_wpseo_opengraph-' . $key)) {
					$tags[$key] = $value;
				}
			}
		}
		else if ($object instanceof \FishPig\WordPress\Model\Term) {
			$tags = array(
				'type' => 'object',
				'title' => $object->getName(),
				'url' => $object->getUrl(),
				'description' => $object->getDescription()
			);
		}
		else {
			$tags = [];	
		}

		$tags = $this->_mergeTags($this->_getTagDefaults(), $tags);

		return $tags;
	}
	
	/**
	 * @return array
	**/
	protected function _mergeTags($a, $b)
	{
		foreach($b as $key => $value) {
			if (trim($value)) {
				$a[$key] = $value;
			}
		}
		
		foreach($a as $key => $value) {
			if (!trim($value)) {
				unset($a[$key]);
			}
		}
		
		return $a;
	}
	
	/**
	 * @return FishPig\WordPress\Model\AbstractModel
	**/
	protected function _getEntity()
	{
		if ($this->_entity !== null) {
			return $this->_entity;
		}
		
		$this->_entity = false;
		
		if ($object = $this->_registry->registry('wordpress_homepage')) {
			$this->_entity = $object;
		}
		else if ($object = $this->_registry->registry('wordpress_post')) {
			$this->_entity = $object;
		}
		else if ($object = $this->_registry->registry('wordpress_term')) {
			$this->_entity = $object;
		}

		return $this->_entity;
	}
}
