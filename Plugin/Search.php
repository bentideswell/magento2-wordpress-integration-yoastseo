<?php
/**
 *
**/
namespace FishPig\WordPress_Yoast\Plugin;

use \FishPig\WordPress\Model\AbstractModel;

class Search extends AbstractPlugin
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
			$this->_getPageTitleFormat('search_wpseo')
		);
	}
}
