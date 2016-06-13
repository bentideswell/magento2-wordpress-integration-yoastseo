<?php
/**
 *
**/
namespace FishPig\WordPress_Yoast\Plugin;

use \FishPig\WordPress\Model\AbstractModel;

class Term extends AbstractPlugin
{
	/**
	 * Yoast field mappings for Posts
	 *
	 * @const string
	**/
	const FIELD_PAGE_TITLE = 'wpseo_title';
	const FIELD_META_DESCRIPTION = 'wpseo_desc';
	const FIELD_META_KEYWORDS = 'wpseo_metakey';
	const FIELD_NOINDEX = 'wpseo_noindex';
	const FIELD_CANONICAL = 'wpseo_canonical';
	const FIELD_CONFIG_OPTION = 'wpseo_taxonomy_meta';
	
	/**
	 * Taxonomy meta data cache
	 *
	 * @var array|null|false
	**/
	protected $_taxonomyMetaData = null;
	
	/**
	 * Get the Yoast Page title
	 *
	 * @param AbstractModel $object
	 * @return string|null
	**/
	protected function _aroundGetPageTitle(AbstractModel $object)
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
	 * @param AbstractModel $object
	 * @return string|null
	**/
	protected function _aroundGetMetaDescription(AbstractModel $object)
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
	 * @param AbstractModel $object
	 * @return string|null
	**/
	protected function _aroundGetMetaKeywords(AbstractModel $object)
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
	 * @param AbstractModel $object
	 * @return string|null
	**/
	protected function _aroundGetRobots(AbstractModel $object)
	{
		$this->_isSubPageNoindex();
		$robots = ['index' => 'index', 'follow' => 'follow'];

		if (($value = trim($this->_getTaxonomyMeta($object, self::FIELD_NOINDEX))) !== '') {
			$robots['index'] = $value;
		}

		return $robots;
	}
	
	/**
	 * Get the Yoast canonical URL
	 *
	 * @param AbstractModel $object
	 * @return string|null
	**/
	protected function _aroundGetCanonicalUrl(AbstractModel $object)
	{
		if (($value = trim($this->_getTaxonomyMeta($object, self::FIELD_CANONICAL))) !== '') {
			return $value;
		}

		return null;
	}
	
	/**
	 * Get a meta field for a taxonomy object
	 *
	 * @param AbstractModel $object
	 * @param string $key
	 * @return string
	**/
	protected function _getTaxonomyMeta(AbstractModel $object, $key)
	{
		$taxonomy = $object->getTaxonomyType();
		$id = $object->getId();
		
		if ($this->_taxonomyMetaData === null) {
			
			if ($meta = @unserialize($this->_config->getOption(self::FIELD_CONFIG_OPTION))) {
				$this->_taxonomyMetaData = $meta;
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
