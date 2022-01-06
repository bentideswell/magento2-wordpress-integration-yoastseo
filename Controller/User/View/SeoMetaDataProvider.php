<?php
/**
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.com)
 * @url     https://fishpig.co.uk/magento/wordpress-integration/yoast/
 */
declare(strict_types=1);

namespace FishPig\WordPress_Yoast\Controller\User\View;

class SeoMetaDataProvider extends \FishPig\WordPress\Controller\User\View\SeoMetaDataProvider
{
    /**
     * @param \FishPig\WordPress\Helper\BlogInfo $blogInfo
     */
    public function __construct(
        \FishPig\WordPress\Helper\BlogInfo $blogInfo,
        \FishPig\WordPress_Yoast\Model\Config $config,
        \FishPig\WordPress_Yoast\Model\StringRewriter $stringRewriter
    ) {
        $this->config = $config;
        $this->stringRewriter = $stringRewriter;
        parent::__construct($blogInfo);
    }
    
    /**
     * @param  \Magento\Framework\View\Result\Page $resultPage,
     * @param  \FishPig\WordPress\Api\Data\ViewableModelInterface $object
     * @return void
     */
    public function addMetaData(
        \Magento\Framework\View\Result\Page $resultPage,
        \FishPig\WordPress\Api\Data\ViewableModelInterface $user
    ): void {
        parent::addMetaData($resultPage, $user);
        
        if (!$this->config->isEnabled()) {
            return;
        }

        // Meta Title
        $this->setMetaTitle(
            $this->stringRewriter->rewrite(
                $user->getMetaValue('wpseo_title') ?: $this->config->getTitleFormat('author_wpseo'),
                $user
            )
        );

        // Meta Description
        if ($metaDesc = $this->stringRewriter->rewrite(
            $user->getMetaValue('wpseo_metadesc') ?: $this->config->getMetaDescriptionFormat('author_wpseo'),
            $user
        )) {
            $this->setMetaDescription($metaDesc);
        }

        // Robots
        if ($this->getBlogInfo()->isBlogPublic()) {
            $robots = ['index' => 'index', 'follow' => 'follow'];

            if ($this->config->isTypeNoindex('author_wpseo')) {
                $robots['index'] = 'noindex';
            }

            if (($customValue = trim((string)$user->getMetaValue('wpseo_noindex_author'))) === 'on') {
                $robots['index'] = 'noindex';
            }
            
            $this->setRobots($robots);
        }
    }
}
