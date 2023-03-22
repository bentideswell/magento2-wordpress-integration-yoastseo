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
     * @auto
     */
    protected $config = null;

    /**
     * @auto
     */
    protected $optionRepository = null;

    /**
     * @auto
     */
    protected $search = null;

    /**
     * @auto
     */
    protected $layout = null;

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
    private $separators = [
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
    ];

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request = null;

    /**
     * @return void
     */
    public function __construct(
        \FishPig\WordPress_Yoast\Model\Config $config,
        \FishPig\WordPress\Model\OptionRepository $optionRepository,
        \FishPig\WordPress\Model\Search $search,
        \Magento\Framework\View\Layout $layout,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->config = $config;
        $this->optionRepository = $optionRepository;
        $this->search = $search;
        $this->layout = $layout;
        $this->request = $request;
    }

    /**
     * @param  string $str
     * @return string
     */
    public function rewrite(?string $str, ?ViewableModelInterface $object = null): string
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
                $stringToken = $this->renderVariable(trim($stringToken, self::RWTS), $object) ?: $stringToken;
            }

            $rewrittenString[] = $stringToken;
        }

        if (!$rewrittenString) {
            return '';
        }

        $rewrittenString = trim(implode('', $rewrittenString));

        return preg_replace('/\s+/', ' ', $rewrittenString);
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
            $value =  max(1, (int)$this->request->getParam('page'));
        } elseif ($key === 'searchphrase') {
            return $this->search->getSearchTerm();
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
            } elseif ($key === 'primary_category') {
                if ($term = $object->getParentTerm('category')) {
                    return $term->getName();
                }

                return '';
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
}
