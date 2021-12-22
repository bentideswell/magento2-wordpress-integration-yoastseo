<?php
/**
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.com)
 * @url     https://fishpig.co.uk/magento/wordpress-integration/yoast/
 */
declare(strict_types=1);

namespace FishPig\WordPress_Yoast\Controller\Post\View;

class SeoMetaDataProvider extends \FishPig\WordPress\Controller\Post\View\SeoMetaDataProvider
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
        \FishPig\WordPress\Api\Data\ViewableModelInterface $post
    ): void {
        parent::addMetaData($resultPage, $post);
        
        if (!$this->config->isEnabled()) {
            return;
        }

        // Meta Title
        $this->setMetaTitle(
            $this->stringRewriter->rewrite(
                $post->getMetaValue($this->config::FIELD_TITLE) ?: $this->config->getTitleFormat($post->getPostType()),
                $post
            )
        );

        // Meta Description
        if ($metaDesc = $this->stringRewriter->rewrite(
            // phpcs:ignore -- long line
            $post->getMetaValue($this->config::FIELD_META_DESCRIPTION) ?: $this->config->getMetaDescriptionFormat($post->getPostType()),
            $post
        )) {
            $this->setMetaDescription($metaDesc);
        }

        // Canonical
        $this->setCanonicalUrl(
            $post->getMetaValue($this->config::FIELD_CANONICAL) ?? $post->getUrl()
        );

        // Robots
        if ($this->getBlogInfo()->isBlogPublic()) {
            $robots = ['index' => 'index', 'follow' => 'follow'];
            
            if ($this->config->isTypeNoindex($post->getPostType())) {
                $robots['index'] = 'noindex';
            }
            
            switch ((int)$post->getMetaValue($this->config::FIELD_NOINDEX)) {
                case 1:
                    $robots['index'] = 'noindex';
                    break;
                case 2:
                    $robots['index'] = 'index';
                    break;
            }
    
            if ((int)$post->getMetaValue($this->config::FIELD_NOFOLLOW) === 1) {
                $robots['follow'] = 'nofollow';
            }
    
            if (($advancedRobots = $post->getMetaValue($this->config::FIELD_ROBOTS_ADVANCED)) !== '') {
                if ($advancedRobots !== 'none') {
                    $robots['advanced'] = $advancedRobots;
                }
            }
    
            if ($robots = array_filter($robots)) {
                $this->setRobots(implode(',', $robots));
            }
        }
    }
}
