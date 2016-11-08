<?php
/**
 *
**/
namespace FishPig\WordPress_Yoast\Plugin;

use \FishPig\WordPress\Api\Data\Entity\ViewableInterface;

class Homepage extends AbstractPlugin
{
	/**
	 * Get the Yoast Page title
	 *
	 * @param ViewableInterface $object
	 * @return string|null
	**/
	protected function _aroundGetPageTitle(ViewableInterface $object)
	{
		return $this->_rewriteString(
			$this->_getPageTitleFormat('home_wpseo')
		);
	}
	
	/**
	 * Get the Yoast meta description
	 *
	 * @param ViewableInterface $object
	 * @return string|null
	**/
	protected function _aroundGetMetaDescription(ViewableInterface $object)
	{
		return $this->_rewriteString(
			$this->_getMetaDescriptionFormat('home_wpseo')
		);
	}
	
	/**
	 * Get the Yoast meta keywords
	 *
	 * @param ViewableInterface $object
	 * @return string|null
	**/
	protected function _aroundGetMetaKeywords(ViewableInterface $object)
	{
		return $this->_rewriteString(
			$this->_getMetaKeywordsFormat('home_wpseo')
		);
	}
}
