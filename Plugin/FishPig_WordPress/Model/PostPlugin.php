<?php
/**
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.com)
 * @url     https://fishpig.co.uk/magento/wordpress-integration/yoast/
 */
declare(strict_types=1);

namespace FishPig\WordPress_Yoast\Plugin\FishPig_WordPress\Model;

use FishPig\WordPress\Model\Post;

class PostPlugin
{
    /**
     *
     */
    private $cache = [];

    /**
     * @param  \FishPig\WordPress_Yoast\Model\Config $config
     * @param  \FishPig\WordPress\Model\PostFactory $postFactory
     */
    public function __construct(
        \FishPig\WordPress_Yoast\Model\Config $config,
        \FishPig\WordPress_Yoast\Controller\Post\View\SeoMetaDataProvider $seoMetaDataProvider
    ) {
        $this->config = $config;
        $this->seoMetaDataProvider = $seoMetaDataProvider;
    }

    /**
     * @param  Permalink $subject
     * @param  \Closure  $callback
     * @param  int       $postId
     * @param  string    $taxonomy
     * @return int
     */
    public function aroundIsPublic(Post $subject, \Closure $proceed): bool
    {
        if (!$this->config->isEnabled() || !$subject->getId()) {
            return $proceed();
        }

        if (!isset($this->cache[$subject->getId()])) {
            if (true === ($this->cache[$subject->getId()] = $proceed())) {
                $robots = $this->seoMetaDataProvider->getRobots($subject);
                $this->cache[$subject->getId()] = isset($robots['index']) && strtolower($robots['index']) !== 'noindex';
            }
        }

        return $this->cache[$subject->getId()];
    }
}
