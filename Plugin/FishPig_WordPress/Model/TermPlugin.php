<?php
/*
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.co.uk)
 */
namespace FishPig\WordPress_Yoast\Plugin\FishPig_WordPress\Model;

/* Misc */
use FishPig\WordPress\Api\Data\Entity\ViewableInterface;


class TermPlugin extends AbstractPlugin
{
	/**
	 * Yoast field mappings for Posts
	 *
	 * @const string
	**/
	const FIELD_PAGE_TITLE       = 'wpseo_title';
	const FIELD_META_DESCRIPTION = 'wpseo_desc';
	const FIELD_META_KEYWORDS    = 'wpseo_metakey';
	const FIELD_NOINDEX          = 'wpseo_noindex';
	const FIELD_CANONICAL        = 'wpseo_canonical';
	const FIELD_CONFIG_OPTION    = 'wpseo_taxonomy_meta';
	
	/**
	 * Taxonomy meta data cache
	 *
	 * @var array|null|false
	**/
	protected $_taxonomyMetaData = null;
	
	/**
	 * Get the Yoast Page title
	 *
	 * @param ViewableInterface $object
	 * @return string|null
	**/
	protected function _aroundGetPageTitle(ViewableInterface $object)
	{
		$rewriteData = [];
		
		if (($value = trim($this->_getTaxonomyMeta($object, self::FIELD_PAGE_TITLE))) !== '') {
			$rewriteData = $this->getRewriteData(array('term_title' => $value));
		}

		return $this->_rewriteString(
			$this->_getPageTitleFormat($object->getTaxonomyType()),
			$rewriteData
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
		if (($value = trim($this->_getTaxonomyMeta($object, self::FIELD_META_DESCRIPTION))) !== '') {
			return $value;
		}
		
		return $this->_rewriteString(
			$this->_getMetaDescriptionFormat($object->getTaxonomyType())
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
		if (($value = trim($this->_getTaxonomyMeta($object, self::FIELD_META_KEYWORDS))) !== '') {
			return $value;
		}
		
		return $this->_rewriteString(
			$this->_getMetaKeywordsFormat($object->getTaxonomyType())
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
		$this->_isSubPageNoindex();
		$robots = ['index' => 'index', 'follow' => 'follow'];

		if (($value = trim($this->_getTaxonomyMeta($object, self::FIELD_NOINDEX))) !== '') {
			$robots['index'] = $value;
		}
		else if ($this->_isNoindex('tax_' . $object->getTaxonomy())) {
			$robots['index'] = 'NOINDEX';
		}


		return $robots;
	}
	
	/**
	 * Get the Yoast canonical URL
	 *
	 * @param ViewableInterface $object
	 * @return string|null
	**/
	protected function _aroundGetCanonicalUrl(ViewableInterface $object)
	{
		if (($value = trim($this->_getTaxonomyMeta($object, self::FIELD_CANONICAL))) !== '') {
			return $value;
		}

		return null;
	}
	
	/**
	 * Get a meta field for a taxonomy object
	 *
	 * @param ViewableInterface $object
	 * @param string $key
	 * @return string
	**/
	protected function _getTaxonomyMeta(ViewableInterface $object, $key)
	{
		$taxonomy = $object->getTaxonomyType();
		$id = $object->getId();
		
		if ($this->_taxonomyMetaData === null) {
			if ($meta = @unserialize($this->optionManager->getOption(self::FIELD_CONFIG_OPTION))) {
				$this->_taxonomyMetaData = $meta;
			}
			else {
				return $this->_taxonomyMetaData = false;
			}
		}

		if (!isset($this->_taxonomyMetaData[$taxonomy])) {
			return '';
		}

		if (!isset($this->_taxonomyMetaData[$taxonomy][$id])) {		
			return '';
		}

		return isset($this->_taxonomyMetaData[$taxonomy][$id][$key])
			? $this->_taxonomyMetaData[$taxonomy][$id][$key]
			: '';
	}
	
	/**
	 * Retrieve the title format for the given key
	 *
	 * @param string $key
	 * @return string
	 */
	protected function _getPageTitleFormat($key)
	{
		return parent::_getPageTitleFormat('tax_' . $key);
	}
	
	/**
	 * Retrieve the title format for the given key
	 *
	 * @param string $key
	 * @return string
	 */
	protected function _getMetaDescriptionFormat($key)
	{
		return parent::_getMetaDescriptionFormat('tax_' . $key);
	}
	
	/**
	 * Retrieve the title format for the given key
	 *
	 * @param string $key
	 * @return string
	 */
	protected function _getMetaKeywordsFormat($key)
	{
		return parent::_getMetaKeywordsFormat('tax_' . $key);
	}
}
