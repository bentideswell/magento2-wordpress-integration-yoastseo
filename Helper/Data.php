<?php
/*
 *
 */
namespace FishPig\WordPress_Yoast\Helper;

/* Constructor Args */
use Magento\Framework\App\Helper\Context;
use FishPig\WordPress\Model\Plugin;
use Magento\Store\Model\StoreManagerInterface;
use FishPig\WordPress\Model\OptionManager;

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
	
	/*
	 *
	 * @var
	 *
	 */
	protected $plugin;
	
	/**
	 *
	 *
	 * @return 
	**/
	public function __construct(Context $context, Plugin $plugin, StoreManagerInterface $storeManager, OptionManager $optionManager)
	{
		$this->plugin        = $plugin;
		$this->storeManager  = $storeManager;
		$this->optionManager = $optionManager;
		
		parent::__construct($context);
		
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
			if ($options = $this->plugin->getOption($type)) {
				if (is_array($options)) {
					foreach($options as $key => $value) {
						$data[str_replace('-', '_', $key)] = $value;
					}
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
		return $this->plugin->isEnabled(self::PLUGIN_FILE_FREE) || $this->plugin->isEnabled(self::PLUGIN_FILE_PREMIUM);
	}
	
	/*
	 *
	 *
	 */
	public function getRequest()
	{
		return $this->_getRequest();
	}
	
	/*
	 *
	 * @return string
	 */
	public function getLocaleCode()
	{
		return $this->storeManager->getStore()->getLocaleCode();
	}

	/*
	 *
	 *
	 */
	public function canDiscourageSearchEngines()
	{
		return (int)$this->optionManager->getOption('blog_public') === 0;
	}
	
	/*
	 *
	 *
	 */
	public function canShowBreacrumbs()
	{
		return (int)$this->getConfigOption('breadcrumbs_enable') === 1;
	}
}
