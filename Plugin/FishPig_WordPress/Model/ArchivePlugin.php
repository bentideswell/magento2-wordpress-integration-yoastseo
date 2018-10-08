<?php
/*
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.co.uk)
 */
namespace FishPig\WordPress_Yoast\Plugin\FishPig_WordPress\Model;

/* Misc */
use FishPig\WordPress\Api\Data\Entity\ViewableInterface;


class ArchivePlugin extends AbstractPlugin
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
	 * @param ViewableInterface $object
	 * @return string|null
	**/
	protected function _aroundGetPageTitle(ViewableInterface $object)
	{
		return $this->_rewriteString(
			$this->_getPageTitleFormat('archive_wpseo')
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
			$this->_getMetaDescriptionFormat('archive_wpseo')
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
			$this->_getMetaKeywordsFormat('archive_wpseo')
		);
	}
	
	/**
	 * Get the Yoast robots tag value
	 *
	 * @param ViewableInterface $object
	 * @return string|null
	**/
	protected function _aroundGetRobots(ViewableInterface $object)
	{
		$robots = ['index' => 'index', 'follow' => 'follow'];

		if ($this->_isNoindex('archive_wpseo')) {
			$robots['index'] = 'noindex';
		}
	
		return $robots;
	}
}
