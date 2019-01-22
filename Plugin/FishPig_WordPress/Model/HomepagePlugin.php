<?php
/*
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.co.uk)
 */
namespace FishPig\WordPress_Yoast\Plugin\FishPig_WordPress\Model;

/* Misc */
use FishPig\WordPress\Api\Data\Entity\ViewableInterface;

class HomepagePlugin extends AbstractPlugin
{
	/**
	 * Get the Yoast Page title
	 *
	 * @param ViewableInterface $object
	 * @return string|null
	**/
	protected function _aroundGetPageTitle(ViewableInterface $object)
	{	
		if ($object->getFrontStaticPage()) {
			return $object->getFrontStaticPage()->getPageTitle();
		}

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
        if ($object->getFrontStaticPage()) {
            return $object->getFrontStaticPage()->getMetaDescription();
        }
        
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
