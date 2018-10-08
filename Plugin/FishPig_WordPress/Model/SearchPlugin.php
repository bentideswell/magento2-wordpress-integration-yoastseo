<?php
/*
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.co.uk)
 */
namespace FishPig\WordPress_Yoast\Plugin\FishPig_WordPress\Model;

/* Misc */
use FishPig\WordPress\Api\Data\Entity\ViewableInterface;

class SearchPlugin extends AbstractPlugin
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
