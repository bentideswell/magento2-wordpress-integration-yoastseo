<?php
/*
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.co.uk)
 */
namespace FishPig\WordPress_Yoast\Plugin\FishPig_WordPress\Model;

/* Misc */
use FishPig\WordPress\Api\Data\Entity\ViewableInterface;

class TaxonomyPlugin extends AbstractPlugin
{
	/*
	 *
	 *
	 */
	public function aroundGetSlug($object, $callback)
	{
		if ($this->isEnabled()) {
			if (1 === (int)$this->yoastHelper->getConfigOption('stripcategorybase')) {
				if ('category' === $object->getTaxonomyType()) {
					$object->setSlug('');			
				}
			}
		}
		
		return $callback();
	}
}
