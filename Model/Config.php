<?php
/**
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.com)
 * @url     https://fishpig.co.uk/magento/wordpress-integration/yoast/
 */
declare(strict_types=1);

namespace FishPig\WordPress_Yoast\Model;

class Config
{
    /**
     * @const string
     */
    const FIELD_TITLE = '_yoast_wpseo_title';
    const FIELD_META_DESCRIPTION = '_yoast_wpseo_metadesc';
    const FIELD_META_KEYWORDS = '_yoast_wpseo_metakeywords';
    const FIELD_NOINDEX = '_yoast_wpseo_meta-robots-noindex';
    const FIELD_NOFOLLOW = '_yoast_wpseo_meta-robots-nofollow';
    const FIELD_ROBOTS_ADVANCED = '_yoast_wpseo_meta-robots-adv';
    const FIELD_CANONICAL = '_yoast_wpseo_canonical';
    
    /**
     * @var array
     */
    private $pluginOptions = null;

    /**
     * @return void
     */
    public function __construct(
        \FishPig\WordPress\Model\OptionRepository $optionRepository,
        \FishPig\WordPress\Model\PluginManager $pluginManager
    ) {
        $this->optionRepository = $optionRepository;
        $this->pluginManager = $pluginManager;
    }
    
    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->pluginManager->isEnabled('wordpress-seo/wp-seo.php')
            || $this->pluginManager->isEnabled('wordpress-seo-premium/wp-seo-premium.php');
    }

    /**
     * @return bool
     */
    public function canShowBreacrumbs(): bool
    {
        return (int)$this->getPluginOption('breadcrumbs_enable') === 1;
    }

    /**
     * @param string $key
     * @return string
     */
    public function getTitleFormat(string $key): string
    {
        return trim($this->getPluginOption('title_' . $key, ''));
    }

    /**
     * @param  string $key
     * @return string
     */
    public function getMetaDescriptionFormat(string $key): string
    {
        return trim($this->getPluginOption('metadesc_' . $key, ''));
    }
    
    /**
     * @param  string $key
     * @return bool
     */
    public function isTypeNoindex(string $key): bool
    {
        return (int)$this->getPluginOption('noindex_' . $key) === 1;
    }
    
    /**
     * @return bool
     */
    public function canStripCategoryUrlBase(): bool
    {
        return (int)$this->getPluginOption('stripcategorybase') === 1;
    }
    
    /**
     * @param  string $key = null
     * @param  mixed  $default = null
     * @return mixed
     */
    public function getPluginOption(string $key = null, $default = null)
    {
        if ($this->pluginOptions === null) {
            $this->pluginOptions = [];

            foreach($this->getPluginOptionKeys() as $pluginOptionKey) {
                if ($options = $this->optionRepository->getUnserialized($pluginOptionKey)) {
                    foreach($options as $optionKey => $optionValue) {
                        $this->pluginOptions[str_replace('-', '_', $optionKey)] = $optionValue;
                    }
                }
            }

            $this->pluginOptions = array_filter($this->pluginOptions);
        }

        if ($key === null) {
            return $this->pluginOptions;
        } elseif (isset($this->pluginOptions[$key])) {
            return $this->pluginOptions[$key];
        }

        return $default;
    }

    /**
     * @return array
     */
    private function getPluginOptionKeys(): array
    {
        return [
            'wpseo',
            'wpseo_titles',
            'wpseo_xml',
            'wpseo_social',
            'wpseo_rss',
            'wpseo_internallinks',
            'wpseo_permalinks'
        ];
    }
}
