<?php
/**
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.com)
 * @url     https://fishpig.co.uk/magento/wordpress-integration/yoast/
 */
declare(strict_types=1);

namespace FishPig\WordPress_Yoast\Plugin\FishPig_WordPress\Model\ResourceModel\Post;

use FishPig\WordPress\Model\ResourceModel\Post\Permalink;

class PermalinkPlugin
{
    /**
     * @auto
     */
    protected $config = null;

    /**
     * @auto
     */
    protected $postFactory = null;

    /**
     * @const string
     */
    const META_KEY = '_yoast_wpseo_primary_category';

    /**
     * @param  \FishPig\WordPress_Yoast\Model\Config $config
     * @param  \FishPig\WordPress\Model\PostFactory $postFactory
     */
    public function __construct(
        \FishPig\WordPress_Yoast\Model\Config $config,
        \FishPig\WordPress\Model\PostFactory $postFactory
    ) {
        $this->config = $config;
        $this->postFactory = $postFactory;
    }
    
    /**
     * @param  Permalink $subject
     * @param  \Closure  $callback
     * @param  int       $postId
     * @param  string    $taxonomy
     * @return int
     */
    public function aroundGetParentTermId(Permalink $subject, \Closure $callback, int $postId, string $taxonomy): int
    {
        try {
            if ($this->config->isEnabled() && $taxonomy === 'category') {
                if ($termId = (int)$this->postFactory->create()->setId($postId)->getMetaValue(self::META_KEY)) {
                    return $termId;
                }
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) { // phpcs:ignore -- empty catch
            // We can ignore this exception and just return the callback
        }

        return $callback($postId, $taxonomy);
    }
}
