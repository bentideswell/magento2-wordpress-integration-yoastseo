<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

namespace FishPig\WordPress_Yoast\Helper;

use \Magento\Framework\App\Helper\Context;
use \FishPig\WordPress\Helper\Plugin as PluginHelper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 * Free plugin filename in WordPress
	 *
	 * @var string
	**/
	const PLUGIN_FILE_FREE = 'wordpress-seo/wp-seo.php';
	
	/**
	 * Premium plugin filename in WordPress
	 *
	 * @var string
	**/
	const PLUGIN_FILE_PREMIUM = 'wordpress-seo-premium/wp-seo-premium.php';
	
	/**
	 * @ \FishPig\WordPress\Helper\Plugin
	**/
	protected $_pluginHelper = null;
	
	/**
	 *
	 *
	 * @return 
	**/
	public function __construct(
		Context $context, 
		PluginHelper $pluginHelper
	)
	{
		parent::__construct($context);
		
		$this->_pluginHelper = $pluginHelper;
		
		$this->_init();
	}
	
	/**
	 * Initialise the plugin with plugin data
	 *
	 * @return $this
	**/
	protected function _init()
	{
		if (!$this->isEnabled()) {
			return $this;
		}
		
		$types = ['wpseo', 'wpseo_titles', 'wpseo_xml', 'wpseo_social', 'wpseo_rss', 'wpseo_internallinks', 'wpseo_permalinks'];
		$data = array();
		
		foreach($types as $type) {
			if ($options = $this->_pluginHelper->getOption($type)) {
				foreach($options as $key => $value) {
					$data[str_replace('-', '_', $key)] = $value;
				}
			}
		}
		
		$this->_configData = $data;
	}
	
	/**
	 * Get a config option for the plugin
	 *
	 * @param string $key = null
	 * @return string
	**/
	public function getConfigOption($key = null)
	{
		if (!$key) {
			return $this->_configData;
		}
		
		return isset($this->_configData[$key]) ? $this->_configData[$key] : false;
	}
	
	/**
	 * Determine whether the plugin is enabled in WordPress
	 *
	 * @return bool
	**/
	public function isEnabled()
	{
		return $this->_pluginHelper->isEnabled(self::PLUGIN_FILE_FREE)
			|| $this->_pluginHelper->isEnabled(self::PLUGIN_FILE_PREMIUM);
	}
}
