<?php
/**
 *
**/
namespace FishPig\WordPress_Yoast\Plugin;

use \FishPig\WordPress\Model\AbstractModel;

class User extends AbstractPlugin
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
			$this->_getPageTitleFormat('author_wpseo')
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
			$this->_getMetaDescriptionFormat('author_wpseo')
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
			$this->_getMetaKeywordsFormat('author_wpseo')
		);
	}
	
	/**
	 * Get the Yoast robots tag value
	 *
	 * @param AbstractModel $object
	 * @return string|null
	**/
	protected function _aroundGetRobots(AbstractModel $object)
	{
		$robots = ['index' => 'index', 'follow' => 'follow'];

		if ($this->_isNoindex('author_wpseo')) {
			$robots['index'] = 'noindex';
		}
	
		return $robots;
	}
}
