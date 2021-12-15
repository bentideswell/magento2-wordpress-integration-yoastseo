<?php
/**
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.com)
 * @url     https://fishpig.co.uk/magento/wordpress-integration/yoast/
 */
declare(strict_types=1);

namespace FishPig\WordPress_Yoast\Controller\Term\View;

class SeoMetaDataProvider extends \FishPig\WordPress\Controller\Term\View\SeoMetaDataProvider
{
    /**
     * @var array
     */
    private $taxonomyMeta = null;

    /**
     * @param \FishPig\WordPress\Helper\BlogInfo $blogInfo
     */
    public function __construct(
        \FishPig\WordPress\Helper\BlogInfo $blogInfo,
        \FishPig\WordPress_Yoast\Model\Config $config,
        \FishPig\WordPress_Yoast\Model\StringRewriter $stringRewriter,
        \FishPig\WordPress\Model\OptionRepository $optionRepository
    ) {
        $this->config = $config;
        $this->stringRewriter = $stringRewriter;
        $this->optionRepository = $optionRepository;
        parent::__construct($blogInfo);
    }
    
    /**
     * @param  \Magento\Framework\View\Result\Page $resultPage,
     * @param  \FishPig\WordPress\Api\Data\ViewableModelInterface $object
     * @return void
     */
    public function addMetaData(
        \Magento\Framework\View\Result\Page $resultPage,
        \FishPig\WordPress\Api\Data\ViewableModelInterface $term
    ): void 
    {
        parent::addMetaData($resultPage, $term);
        
        if (!$this->config->isEnabled()) {
            return;
        }

        // Meta Title
        $this->setMetaTitle(
            $this->stringRewriter->rewrite(
                $this->getMetaValue($term, 'wpseo_title') ?: $this->config->getTitleFormat('tax_' . $term->getTaxonomy()),
                $term
            )
        );

        // Meta Description
        if ($metaDesc = $this->stringRewriter->rewrite(
            $this->getMetaValue($term, 'wpseo_desc') ?? $this->config->getMetaDescriptionFormat('tax_' . $term->getTaxonomy()),
            $term
        )) {
            $this->setMetaDescription($metaDesc);
        }

        // Canonical
        $this->setCanonicalUrl(
            $this->getMetaValue($term, $this->config::FIELD_CANONICAL) ?: $term->getUrl()
        );

        // Robots
        if ($this->getBlogInfo()->isBlogPublic()) {
            $robots = ['index' => 'index', 'follow' => 'follow'];
            
            if ($this->config->isTypeNoindex('tax_' . $term->getTaxonomy())) {
                $robots['index'] = 'noindex';
            }
    
            if (($value = trim($this->getMetaValue($term, $this->config::FIELD_NOINDEX))) !== '') {
                $robots['index'] = $value;
            }
    
            if ($robots = array_filter($robots)) {
                $this->setRobots(implode(',', $robots));
            }
        }
    }
    
    /**
     * @param  \FishPig\WordPress\Model\Term $term
     * @param  string $key
     * @return string
     */
    private function getMetaValue(\FishPig\WordPress\Model\Term $term, string $key)
    {
        if ($this->taxonomyMeta === null) {
            $this->taxonomyMeta = $this->optionRepository->getUnserialized('wpseo_taxonomy_meta');
        }

        return isset($this->taxonomyMeta[$term->getTaxonomy()][$term->getId()][$key])
            ? $this->taxonomyMeta[$term->getTaxonomy()][$term->getId()][$key]
            : '';
    }
}
