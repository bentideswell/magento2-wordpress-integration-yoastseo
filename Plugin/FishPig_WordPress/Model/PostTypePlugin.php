<?php
/*
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.co.uk)
 */
namespace FishPig\WordPress_Yoast\Plugin\FishPig_WordPress\Model;

/* Misc */
use FishPig\WordPress\Api\Data\Entity\ViewableInterface;

class PostTypePlugin extends AbstractPlugin
{
	/**
	 * Yoast field mappings for Posts
	 *
	 * @const string
	**/
	const FIELD_PAGE_TITLE       = 'ptarchive_';
	const FIELD_META_DESCRIPTION = 'metadesc_ptarchive_';
	const FIELD_META_KEYWORDS    = 'metakey_ptarchive_';
	const FIELD_NOINDEX          = 'noindex_ptarchive_';
	
	/**
	 * Get the Yoast Page title
	 *
	 * @param ViewableInterface $object
	 * @return string|null
	**/
	protected function _aroundGetPageTitle(ViewableInterface $object)
	{
		return $this->_rewriteString(
			$this->_getPageTitleFormat(self::FIELD_PAGE_TITLE . $object->getPostType())
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
			$this->_getMetaDescriptionFormat(self::FIELD_META_DESCRIPTION . $object->getPostType())
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
		if (($value = trim($object->getMetaValue(self::FIELD_META_KEYWORDS))) !== '') {
			return $value;
		}

		return $this->_rewriteString(
			$this->_getMetaKeywordsFormat(self::FIELD_META_KEYWORDS . $object->getPostType())
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


		switch((int)$this->getData(self::FIELD_NOINDEX . $object->getPostType())) {
			case 1:  $robots['index'] = 'noindex';   break;
			case 2:  $robots['index'] = 'index';       break;
		}

		return $robots;
	}
}
