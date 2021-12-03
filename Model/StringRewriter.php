<?php
/**
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.com)
 * @url     https://fishpig.co.uk/magento/wordpress-integration/yoast/
 */
declare(strict_types=1);

namespace FishPig\WordPress_Yoast\Model;

use FishPig\WordPress\Api\Data\ViewableModelInterface;

class StringRewriter
{
    /**
     * @const string
     */
    const RWTS = '%%';

    /**
     * @var array
     */
    private $cache = [];

    /**
     * @var array
     */
    private $separators = array(
        'sc-dash'   => '-',
        'sc-ndash'  => '&ndash;',
        'sc-mdash'  => '&mdash;',
        'sc-middot' => '&middot;',
        'sc-bull'   => '&bull;',
        'sc-star'   => '*',
        'sc-smstar' => '&#8902;',
        'sc-pipe'   => '|',
        'sc-tilde'  => '~',
        'sc-laquo'  => '&laquo;',
        'sc-raquo'  => '&raquo;',
        'sc-lt'     => '&lt;',
        'sc-gt'     => '&gt;',
    );

    /**
     * @return void
     */
    public function __construct(
        \FishPig\WordPress_Yoast\Model\Config $config,
        \FishPig\WordPress\Model\OptionRepository $optionRepository,
        \FishPig\WordPress\Model\Search $search,
        \Magento\Framework\View\Layout $layout
    ) {
        $this->config = $config;
        $this->optionRepository = $optionRepository;
        $this->search = $search;
        $this->layout = $layout;
    }

    
    /**
     * @param  string $str
     * @return string
     */
    public function rewrite(string $str, ?ViewableModelInterface $object = null): string
    {
        if (strpos($str, self::RWTS) === false) {
            return $str;
        }

        $rwt = '%%';
        $rewrittenString = [];
        $stringTokens = preg_split(
            '/(' . self::RWTS . '[a-z_-]{1,}' . self::RWTS . ')/iU', 
            $str, 
            -1, 
            PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY
        );

        foreach ($stringTokens as $stringToken) {
            $isStringToken = strlen($stringToken) >= 5 
                && substr($stringToken, 0, 2) === self::RWTS 
                && substr($stringToken, -2) === self::RWTS;

            if ($isStringToken) {
                $stringToken = $this->renderVariable(trim($stringToken, self::RWTS), $object);
            }

            $rewrittenString[] = $stringToken;
        }
        
        if (!$rewrittenString) {
            return '';
        }
        
        $rewrittenString = trim(implode('', $rewrittenString));
        
        return preg_replace('/\s+/', ' ', $rewrittenString);exit;
    }

    /**
     * Setup the rewrite data for $object
     *
     * @param \ViewableModelInterface $object
     * @return $this
     */
    private function renderVariable(string $key, ?ViewableModelInterface $object = null)
    {
        if ($key === 'sitename') {
            return $this->optionRepository->get('blogname');
        } elseif ($key === 'sitedesc') {
            return $this->optionRepository->get('blogdescription');
        } elseif ($key === 'currenttime') {
            return $this->dateHelper->formatTime(date('Y-m-d H:i:s'));
        } elseif ($key === 'currentdate') {
            return $this->dateHelper->formatDate(date('Y-m-d H:i:s'));
        } elseif ($key === 'currentmonth') {
            return date('F');
        } elseif ($key === 'currentyear') {
            return date('Y');
        } elseif ($key === 'sep') {
            $value = '|';
            
            if ($sep = $this->config->getPluginOption('separator')) {
                if (isset($this->separators[$sep])) {
                    $value = $this->separators[$sep];
                }
            }
            
            return $value;
        } elseif ($key === 'pagenumber') {
           $value =  max(1, (int)$this->yoastHelper->getRequest()->getParam('page'));
        } elseif ($key === 'searchphrase') {
            return $this->search->getSearchTerm();
        } elseif (in_array($key, ['pagenumber', 'pagetotal', 'page'])) {
            return $this->renderCollectionSizeVariable($key);
        }

        if ($object === null) {
            return '';
        }

        if ($object instanceof \FishPig\WordPress\Model\Post) {
            if ($key === 'date') {
                return $object->getPostDate();
            } elseif ($key === 'title') {
                return $object->getPostTitle();
            } elseif ($key === 'excerpt') {
                return trim($object->getExcerpt(30));
            } elseif ($key === 'category') {
                $categories = [];

                foreach ($object->getTermCollection('category')->load() as $category) {
                    $categories[] = $category->getName();
                }

                return implode(', ', $categories);
            } elseif ($key === 'modified') {
                return $object->getPostModified();
            } elseif ($key === 'id') {
                return $object->getId();
            } elseif ($key === 'name') {
                return $object->getUser()->getUserNicename();
            } elseif ($key === 'userid') {
                return $object->getUser()->getId();
            }
        } elseif ($object instanceof \FishPig\WordPress\Model\Term) {
            if ($key === 'term_description') {
                return trim(strip_tags($object->getDescription()));
            } elseif ($key === 'term_title') {
                return $object->getName();
            }
        } elseif ($object instanceof \FishPig\WordPress\Model\Archive) {
            if ($key === 'date') {
                return $object->getName();
            }
        } elseif ($object instanceof \FishPig\WordPress\Model\User) {
            if ($key === 'name') {
                return $object->getDisplayName();
            }
        } elseif ($object instanceof \FishPig\WordPress\Model\PostType) {
            if ($key === 'pt_plural') {
                return $object->getPluralName();
            }
        }

        // Add custom field data
        /*
        if (strpos($key, '%%cf_') !== false && $object instanceof \FishPig\WordPress\Model\AbstractMetaModel) {
                if (preg_match_all('/\%\%cf_([^\%]{1,})\%\%/', $key, $matches)) {
                    foreach($matches[1] as $customField) {
                        if ($cfValue = $object->getMetaValue($customField)) {
                            if (!is_array($cfValue)) {
                                $data['cf_' . $customField] = $cfValue;
                            }
                        }
                    }
                }
            }
        }*/
        
        return '';
    }

    /**
     * @param  string $key
     * @return string
     */
    private function renderCollectionSizeVariable(string $key): string
    {
        if (!isset($this->cache[$key])) {
            if ($pagerBlock = $this->layout->getBlock('wp.post_list.pager')) {
                $this->cache['pagenumber'] = $pagerBlock->getCurrentPage();
                $this->cache['pagetotal'] = $pagerBlock->getLastPage();
                $this->cache['page'] = sprintf('Page %d of %s', $this->cache['pagenumber'], $this->cache['pagetotal']);
            }
        }
        
        return isset($this->cache[$key]) ? (string)$this->cache[$key] : '';
    }
}
