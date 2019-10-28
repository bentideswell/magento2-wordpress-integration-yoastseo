<?php
/*
 *
 */
namespace FishPig\WordPress_Yoast\Block;

/* Parent Class */
use FishPig\WordPress\Block\AbstractBlock;

/* Constructor Args */
use Magento\Framework\View\Element\Template\Context;
use FishPig\WordPress\Model\Context as WPContext;
use FishPig\WordPress_Yoast\Helper\Data as YoastHelper;

class OpenGraph extends AbstractBlock
{
	/*
	 * @var FishPig\WordPress_Yoast\Helper\Data
	 */
	protected $yoastHelper;

	/*
	 * @var FishPig\WordPress\Model\AbstractModel
	 */
	protected $entity;
	
	/*
	 *
	 *
	 *
	 */
  public function __construct(Context $context, WPContext $wpContext, YoastHelper $yoastHelper, array $data = [])
	{
		$this->yoastHelper = $yoastHelper;
		
		parent::__construct($context, $wpContext, $data);
	}
	
	/*
	 * @return array
	 */
	protected function _getTagDefaults()
	{
		return [
			'locale' => $this->yoastHelper->getLocaleCode(),
			'type' => 'blog',
			'title' => $this->optionManager->getOption('blogname'),
			'description' => $this->optionManager->getOption('blogdescription'),
			'url' => $this->url->getUrl(),
			'site_name' => $this->optionManager->getOption('blogname'),
			'article:publisher' => $this->yoastHelper->getConfigOption('facebook_site'),
			'image' => $this->yoastHelper->getConfigOption('og_default_image'),
		];
	}

	/*
	 * @return array
	 */
	public function getTags()
	{
		$object = $this->_getEntity();
		$tags = [];
		
		if ($object instanceof \FishPig\WordPress\Model\Homepage) {
			$tags = array(
				'description' => $this->yoastHelper->getConfigOption('og_frontpage_desc'),
				'image' => $this->yoastHelper->getConfigOption('og_frontpage_image'),
			);
		}
		else if ($object instanceof \FishPig\WordPress\Model\Post) {
			$tags = array(
				'type' => 'article',
				'title' => $object->getName(),
				'description' => $object->getMetaDescription(),
				'url' => $object->getUrl(),
				'image' => $object->getImage() ? $object->getImage()->getFullSizeImage() : '',
				'updated_time' => $object->getPostModifiedDate('c'),
				'article:author' => $object->getUser()->getMetaValue('facebook'),
				'article:published_time' => $object->getPostDate('c'),
				'article:modified_time' => $object->getPostModifiedDate('c'),
			);

			foreach(['title', 'description', 'image'] as $key) {
				if ($value = $object->getMetaValue('_yoast_wpseo_opengraph-' . $key)) {
					$tags[$key] = $value;
				}
			}
		}
		else if ($object instanceof \FishPig\WordPress\Model\Term) {
			$tags = array(
				'type' => 'object',
				'title' => $object->getName(),
				'url' => $object->getUrl(),
				'description' => $object->getDescription()
			);
		}
		else {
			$tags = [];	
		}

		$tags = $this->_mergeTags($this->_getTagDefaults(), $tags);

		return $tags;
	}
	
	/*
	 * @return array
	 */
	protected function _mergeTags($a, $b)
	{
		foreach($b as $key => $value) {
			if (trim($value)) {
				$a[$key] = $value;
			}
		}
		
		foreach($a as $key => $value) {
			if (!trim($value)) {
				unset($a[$key]);
			}
		}
		
		return $a;
	}
	
	/*
	 * @return FishPig\WordPress\Model\AbstractModel
	 */
	protected function _getEntity()
	{
		if ($this->entity !== null) {
			return $this->entity;
		}
		
		$this->entity = false;
		
		if ($object = $this->registry->registry('wordpress_homepage')) {
			$this->entity = $object;
		}
		else if ($object = $this->registry->registry('wordpress_post')) {
			$this->entity = $object;
		}
		else if ($object = $this->registry->registry('wordpress_term')) {
			$this->entity = $object;
		}

		return $this->entity;
	}
}
