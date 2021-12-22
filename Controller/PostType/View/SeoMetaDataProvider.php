<?php
/**
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.com)
 * @url     https://fishpig.co.uk/magento/wordpress-integration/yoast/
 */
declare(strict_types=1);

namespace FishPig\WordPress_Yoast\Controller\PostType\View;

class SeoMetaDataProvider extends \FishPig\WordPress\Controller\PostType\View\SeoMetaDataProvider
{
    /**
     * @param \FishPig\WordPress\Helper\BlogInfo $blogInfo
     */
    public function __construct(
        \FishPig\WordPress\Helper\BlogInfo $blogInfo,
        \FishPig\WordPress_Yoast\Model\Config $config,
        \FishPig\WordPress_Yoast\Model\StringRewriter $stringRewriter,
        \FishPig\WordPress\Helper\FrontPage $frontPage,
        \FishPig\WordPress_Yoast\Controller\Post\View\SeoMetaDataProviderFactory $postViewSeoMetaDataProviderFactory
    ) {
        $this->config = $config;
        $this->stringRewriter = $stringRewriter;
        $this->frontPage = $frontPage;
        $this->postViewSeoMetaDataProviderFactory = $postViewSeoMetaDataProviderFactory;
        parent::__construct($blogInfo);
    }
    
    /**
     * @param  \Magento\Framework\View\Result\Page $resultPage,
     * @param  \FishPig\WordPress\Api\Data\ViewableModelInterface $object
     * @return void
     */
    public function addMetaData(
        \Magento\Framework\View\Result\Page $resultPage,
        \FishPig\WordPress\Api\Data\ViewableModelInterface $postType
    ): void {
        parent::addMetaData($resultPage, $postType);
        
        if (!$this->config->isEnabled()) {
            return;
        }

        if ($postType->getPostType() === 'post') {
            if ($this->frontPage->isFrontPageStaticPage()) {
                if ($postsPage = $this->frontPage->getPostsPage()) {
                    $this->postViewSeoMetaDataProviderFactory->create()->addMetaData($resultPage, $postsPage);
                }
            } else {
                // Meta Title
                $this->setMetaTitle(
                    $this->stringRewriter->rewrite(
                        $this->config->getTitleFormat('home_wpseo'),
                        $postType
                    )
                );
        
                // Meta Description
                if ($metaDesc = $this->stringRewriter->rewrite(
                    $this->config->getMetaDescriptionFormat('home_wpseo'),
                    $postType
                )) {
                    $this->setMetaDescription($metaDesc);
                }
            }

            return;
        }

        // Meta Title
        $this->setMetaTitle(
            $this->stringRewriter->rewrite(
                $this->config->getTitleFormat('ptarchive_' . $postType->getPostType()),
                $postType
            )
        );

        // Meta Description
        if ($metaDesc = $this->stringRewriter->rewrite(
            $this->config->getMetaDescriptionFormat('metadesc_ptarchive_' . $postType->getPostType()),
            $postType
        )) {
            $this->setMetaDescription($metaDesc);
        }

        // Robots
        if ($this->getBlogInfo()->isBlogPublic()) {
            $robots = ['index' => 'index', 'follow' => 'follow'];
            
            switch ((int)$this->config->getPluginOption('noindex_ptarchive_' . $object->getPostType())) {
                case 1:
                    $robots['index'] = 'noindex';
                    break;
                case 2:
                    $robots['index'] = 'index';
                    break;
            }

            $this->setRobots($robots);
        }
    }
}
