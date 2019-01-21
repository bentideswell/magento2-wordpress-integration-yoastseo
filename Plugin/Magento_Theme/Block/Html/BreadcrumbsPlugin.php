<?php
/*
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.co.uk)
 */
namespace FishPig\WordPress_Yoast\Plugin\Magento_Theme\Block\Html;

/* Subject */
use Magento\Theme\Block\Html\Breadcrumbs;

/* Constructor Args */
use \FishPig\WordPress_Yoast\Helper\Data as YoastHelper;

class BreadcrumbsPlugin
{
	/*
	 * @var YoastHelper
	 */
	protected $yoastHelper;
	
	/*
	 * @param YoastHelper $yoastHelper
	 */
	public function __construct(YoastHelper $yoastHelper)
	{
		$this->yoastHelper = $yoastHelper;
	}
	
	/*
	 *
	 * @param  Breadcrumbs $subject
	 * @param  string      $crumbName
	 * @param  array       $crumbInfo
	 * @return array
	 */
	public function beforeAddCrumb(Breadcrumbs $subject, $crumbName, $crumbInfo)
	{
		if ($crumbName === 'blog' && $this->isYoastBreadcrumbsEnabled()) {
			if (($label = trim($this->yoastHelper->getConfigOption('breadcrumbs_home'))) !== '') {
				$crumbInfo['label'] = $label;
			}
		}
		
		return [$crumbName, $crumbInfo];
	}
	
	/**
	 * Determine if Yoast Breadcrumbs are enabled in WordPress Yoast config
	 *
	 * @return bool
	 */
	protected function isYoastBreadcrumbsEnabled()
	{
		return (int)$this->yoastHelper->getConfigOption('breadcrumbs_enable') === 1;
	}
}
