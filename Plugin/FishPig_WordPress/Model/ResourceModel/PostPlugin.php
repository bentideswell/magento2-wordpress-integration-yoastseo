<?php
/*
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.co.uk)
 */
namespace FishPig\WordPress_Yoast\Plugin\FishPig_WordPress\Model\ResourceModel;

/* Parent Class */
use \FishPig\WordPress_Yoast\Plugin\FishPig_WordPress\Model\AbstractPlugin;

/* Subject */
use \FishPig\WordPress\Model\ResourceModel\Post as PostResource;

/* Misc */
use \Magento\Framework\DB\Select;

class PostPlugin extends AbstractPlugin
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

		$tempPostModel = $this->context->getPostFactory()->create()->setId($postId);

		if ($categoryId = $tempPostModel->getMetaValue('_yoast_wpseo_primary_category')) {
			$select->reset(\Zend_Db_Select::ORDER)->where('_term.term_id=?', $categoryId);
		}
		
		return $this;
	}
}
