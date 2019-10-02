<?php
/*
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.co.uk)
 */
namespace FishPig\WordPress_Yoast\Model;

use FishPig\WordPress\Model\OptionManager;
use FishPig\WordPress_Yoast\Helper\Data as YoastHelper;
use FishPig\WordPress\Helper\Date as DateHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Layout;
use FishPig\WordPress\Model\SearchFactory;

class Context
{	
	/**
	 * @var
	 */
	protected $optionManager;

	/**
	 * @var
	 */
	protected $yoastHelper;
	
	/**
	 * @var
	 */
	protected $dateHelper;
	
	/**
	 * @var
	 */
	protected $registry;
	
	/**
	 * @var
	 */
	protected $layout;
	
	/*
	 *
	 *
	 */
	public function __construct(
    OptionManager $optionManager,
    YoastHelper $yoastHelper,
    DateHelper $dateHelper,
    Registry $registry,
    Layout $layout,
    SearchFactory $searchFactory
  )
	{
		$this->optionManager = $optionManager;
		$this->yoastHelper   = $yoastHelper;
		$this->dateHelper    = $dateHelper;
		$this->registry      = $registry;
		$this->layout        = $layout;
		$this->searchFactory = $searchFactory;
	}

  /**
   * @return
   */
  public function getOptionManager()
  {
    return $this->optionManager;
  }
  
  /**
   * @return
   */
  public function getYoastHelper()
  {
    return $this->yoastHelper;
  }
  
  /**
   * @return
   */
  public function getDateHelper()
  {
    return $this->dateHelper;
  }
  
  /**
   * @return
   */
  public function getRegistry()
  {
    return $this->registry;
  }
  
  /**
   * @return
   */
  public function getLayout()
  {
    return $this->layout;
  }
  
  /**
   * @return
   */
  public function getSearchFactory()
  {
    return $this->searchFactory;
  }
}
