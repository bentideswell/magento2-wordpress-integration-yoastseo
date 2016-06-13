<?php
/**
 *
**/
namespace FishPig\WordPress_Yoast\Plugin;

use \FishPig\WordPress\Model\AbstractModel;

class Archive extends AbstractPlugin
{
	/**
	 * Yoast field mappings for Posts
	 *
	 * @const string
	**/
	const FIELD_PAGE_TITLE = '_yoast_wpseo_title';
	const FIELD_META_DESCRIPTION = '_yoast_wpseo_metadesc';
	const FIELD_META_KEYWORDS = '_yoast_wpseo_metakeywords';
	const FIELD_NOINDEX = '_yoast_wpseo_meta-robots-noindex';
	const FIELD_NOFOLLOW = '_yoast_wpseo_meta-robots-nofollow';
	const FIELD_ROBOTS_ADVANCED = '_yoast_wpseo_meta-robots-adv';
	const FIELD_CANONICAL = '_yoast_wpseo_canonical';

	/**
	 * Get the Yoast Page title
	 *
	 * @param AbstractModel $object
	 * @return string|null
	**/
	protected function _aroundGetPageTitle(AbstractModel $object)
	{
		return $this->_rewriteString(
			$this->_getPageTitleFormat('archive_wpseo')
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
			$this->_getMetaDescriptionFormat('archive_wpseo')
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
			$this->_getMetaKeywordsFormat('archive_wpseo')
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

		if ($this->_isNoindex('archive_wpseo')) {
			$robots['index'] = 'noindex';
		}
	
		return $robots;
	}
}
