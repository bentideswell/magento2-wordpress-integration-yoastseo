<?php
/*
 *
 */

namespace FishPig\WordPress_Yoast\Plugin;

use \FishPig\WordPress_Yoast\Helper\Data as DataHelper;
use \FishPig\WordPress\Model\Config as WPConfig;

class Config
{
	/*
	 * @var \FishPig\WordPress_Yoast\Helper\Data as DataHelper;
	 */
	protected $dataHelper = null;
	
	/*
	 * @param \FishPig\WordPress_Yoast\Helper\Data as DataHelper
	 */
	public function __construct(DataHelper $dataHelper)
	{
		$this->dataHelper = $dataHelper;
	}
	
	/*
	 * @param \FishPig\WordPress\Model\Config;
	 * @param \Closure $callback
	 * @return string
	 */
	public function aroundGetBlogBreadcrumbsLabel(WPConfig $subject, \Closure $callback)
	{
		if (($label = trim($this->dataHelper->getConfigOption('breadcrumbs_home'))) !== '') {
			return $label;
		}
		
		return $callback();
	}
}