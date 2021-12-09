<?php
/**
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.com)
 * @url     https://fishpig.co.uk/magento/wordpress-integration/yoast/
 */
declare(strict_types=1);

namespace FishPig\WordPress_Yoast\Block;

class OpenGraph extends \FishPig\WordPress\Block\AbstractBlock
{
    /**
     * @var FishPig\WordPress\Model\AbstractModel
     */
    private $entity = null;

    /**
     * @param  \Magento\Framework\View\Element\Template\Context $context,
     * @param  \FishPig\WordPress\Block\Context $wpContext,
     * @param  array $data = []
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \FishPig\WordPress\Block\Context $wpContext,
        \FishPig\WordPress_Yoast\Model\Config $config,
        \FishPig\WordPress\Helper\BlogInfo $blogInfo,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->config = $config;
        $this->blogInfo = $blogInfo;
        $this->storeManager = $storeManager;
        parent::__construct($context, $wpContext, $data);   
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }
        
        $html = '';
        
        foreach ($this->getTags() as $tagName => $tagValue) {
            $html .= sprintf(
                '<meta property="og:%s" content="%s"/>',
                $this->escapeHtmlAttr($tagName),
                $this->escapeHtml($tagValue),
            ) . "\n";
        }
        
        return $html;
    }

    /**
     * @return array
     */
    private function getTags()
    {
        $object = $this->getCurrentObject();
        $tags = [];

        if ($object instanceof \FishPig\WordPress\Model\Homepage) {
            $tags = array(
                'description' => $this->config->getPluginOption('og_frontpage_desc'),
                'image' => $this->config->getPluginOption('og_frontpage_image'),
                'url' => $object->getUrl(),
            );
        } elseif ($object instanceof \FishPig\WordPress\Model\Post) {
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

            foreach (['title', 'description', 'image'] as $key) {
                if ($value = $object->getMetaValue('_yoast_wpseo_opengraph-' . $key)) {
                    $tags[$key] = $value;
                }
            }
        } elseif ($object instanceof \FishPig\WordPress\Model\Term) {
            $tags = array(
                'type' => 'object',
                'title' => $object->getName(),
                'url' => $object->getUrl(),
                'description' => $object->getDescription()
            );
        }

        $tags = array_merge(
            [
                'locale' => $this->config->getLocaleCode(),
                'type' => 'blog',
                'title' => $this->blogInfo->getBlogName(),
                'description' => $this->blogInfo->getBlogDescription(),
                'url' => $this->url->getUrl(),
                'site_name' => $this->blogInfo->getBlogName(),
                'article:publisher' => $this->config->getPluginOption('facebook_site'),
                'image' => $this->config->getPluginOption('og_default_image'),
            ],
            array_filter($tags)
        );

        return $tags;
    }

    /**
     * @return \FishPig\WordPress\Model\AbstractModel|false
     */
    private function getCurrentObject()
    {
        if ($this->entity === null) {
            $this->entity = false;
            
            foreach (['wordpress_post_type', 'wordpress_post', 'wordpress_term'] as $key) {
                if ($object = $this->registry->registry($key)) {
                    $this->entity = $object;
                    break;
                }
            }
        }
        
        return $this->entity;
    }        
}
