<?php
/**
 *
**/
namespace FishPig\WordPress_Yoast\Plugin;

use \FishPig\WordPress\Api\Data\Entity\ViewableInterface;

class Search extends AbstractPlugin
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
			$this->_getPageTitleFormat('search_wpseo')
		);
	}
}
