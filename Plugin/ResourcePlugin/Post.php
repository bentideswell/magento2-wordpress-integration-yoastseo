<?php
/**
 *
**/
namespace FishPig\WordPress_Yoast\Plugin\ResourcePlugin;

use \FishPig\WordPress_Yoast\Plugin\AbstractPlugin;
use \FishPig\WordPress\Model\ResourceModel\Post as PostResource;
use \Magento\Framework\DB\Select;

class Post extends AbstractPlugin
{
	/**
	 * Add the primary category to a select object
	 *
	 * @param PostResource $postResource
	 * @param \Closure $callback
	 * @param Magento\Framework\DB\Select 
	 * @param array|int $postId
	 * @return $this
	**/
	public function aroundAddPrimaryCategoryToSelect(PostResource $postResource, \Closure $callback, Select $select, $postId)
	{
		if (!$this->isEnabled()) {
			return $this;
		}
		
		if (is_object($postId)) {
			$post = $postId->getId();
		}
		else if (is_array($postId)) {
			$postId = array_shift($postId);
		}

		$tempPostModel = $this->_factory->getFactory('Post')->create()->setId($postId);

		if ($categoryId = $tempPostModel->getMetaValue('_yoast_wpseo_primary_category')) {
			$select->reset(\Zend_Db_Select::ORDER)->where('_term.term_id=?', $categoryId);
		}
		
		return $this;
	}
}
