<?php
/**
 *
**/
namespace FishPig\WordPress_Yoast\Plugin;

use \FishPig\WordPress\Model\AbstractModel;

class Homepage extends AbstractPlugin
{
	/**
	 * Get the Yoast Page title
	 *
	 * @param AbstractModel $object
	 * @return string|null
	**/
	protected function _aroundGetPageTitle(AbstractModel $object)
	{
		return $this->_rewriteString(
			$this->_getPageTitleFormat('home_wpseo')
		);
	}
	
	/**
	 * Get the Yoast meta description
	 *
	 * @param AbstractModel $object
	 * @return string|null
	**/
	protected function _aroundGetMetaDescription(AbstractModel $object)
	{
		return $this->_rewriteString(
			$this->_getMetaDescriptionFormat('home_wpseo')
		);
	}
	
	/**
	 * Get the Yoast meta keywords
	 *
	 * @param AbstractModel $object
	 * @return string|null
	**/
	protected function _aroundGetMetaKeywords(AbstractModel $object)
	{
		return $this->_rewriteString(
			$this->_getMetaKeywordsFormat('home_wpseo')
		);
	}
}
