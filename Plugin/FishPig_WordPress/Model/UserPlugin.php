<?php
/**
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.co.uk)
 */
namespace FishPig\WordPress_Yoast\Plugin\FishPig_WordPress\Model;

use FishPig\WordPress\Api\Data\Entity\ViewableInterface;

class UserPlugin extends AbstractPlugin
{
    /**
     * Get the Yoast Page title
     *
     * @param ViewableInterface $object
     * @return string|null
     */
    protected function _aroundGetPageTitle(ViewableInterface $object)
    {
        if (($customValue = trim($object->getMetaValue('wpseo_title'))) !== '') {
            return $customValue;
        }

        return $this->_rewriteString($this->_getPageTitleFormat('author_wpseo'));
    }

    /**
     * Get the Yoast meta description
     *
     * @param ViewableInterface $object
     * @return string|null
     */
    protected function _aroundGetMetaDescription(ViewableInterface $object)
    {
        if (($customValue = trim($object->getMetaValue('wpseo_metadesc'))) !== '') {
            return $customValue;
        }

        return $this->_rewriteString($this->_getMetaDescriptionFormat('author_wpseo'));
    }

    /**
     * Get the Yoast meta keywords
     *
     * @param ViewableInterface $object
     * @return string|null
     */
    protected function _aroundGetMetaKeywords(ViewableInterface $object)
    {
        return $this->_rewriteString($this->_getMetaKeywordsFormat('author_wpseo'));
    }

    /**
     * Get the Yoast robots tag value
     *
     * @param ViewableInterface $object
     * @return string|null
     */
    protected function _aroundGetRobots(ViewableInterface $object)
    {
        $robots = ['index' => 'index', 'follow' => 'follow'];

        if ($this->_isNoindex('author_wpseo')) {
            $robots['index'] = 'noindex';
        }

        if (($customValue = trim($object->getMetaValue('wpseo_noindex_author'))) === 'on') {
            $robots['index'] = 'noindex';
        }

        return $robots;
    }
}
