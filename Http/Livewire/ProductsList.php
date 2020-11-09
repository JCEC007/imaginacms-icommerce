<?php

namespace Modules\Icommerce\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

use Modules\Icommerce\Repositories\ProductRepository;

// Transformers
use Modules\Icommerce\Transformers\ProductTransformer;

use Illuminate\Http\Request;

class ProductsList extends Component
{

	use WithPagination;

	private $order;
	private $firstRequest;

	public $category;
	public $totalProducts;
	public $orderBy;
	public $mainLayout;
	public $layoutClass;

	public $priceMin;
	public $priceMax;
	public $filters;

	public $dataRequest;

	protected $listeners = ['updateFilter'];

	protected $queryString = ['orderBy','priceMin','priceMax','page'];

	protected $emitProductListRendered;
	

	/*
    * Runs once, immediately after the component is instantiated, 
    * but before render() is called
    */
	public function mount(Request $request,$category)
	{

	    $this->category = $category;
	    $this->totalProducts = 0;
	    $this->filters = [];
	    $this->orderBy = "nameaz";
	    $this->order = config("asgard.icommerce.config.orderByOptions")[$this->orderBy]['order'];
	    
	    $this->mainLayout = "four";
	    $this->setClassToLayout();

	    $this->priceMin = null;
	    $this->priceMax = null;

	    $this->dataRequest = $request->all();
	    $this->firstRequest = true;

	    $this->emitProductListRendered = false;

	}

	/*
    * Updating Attribute OrderBy
    *
    */
	public function updatingOrderBy(){
		$this->emitProductListRendered = false;
        $this->resetPage();
    }
    
  
  /*
  * Listener - Update Filters
  *
  */
  public function updateFilter($filter){
    
    //\Log::info("filter: ".json_encode($filter));

    $this->emitProductListRendered = true;
    $this->filters = array_merge($this->filters, $filter);
    $this->resetPage();
    
  }

    /*
    * Function Frontend - When change the layout
    *
    */
    public function changeLayout($c){
    	$this->mainLayout = $c;
    	$this->setClassToLayout();
    }

    /*
    * Set class to layout products
    *
    */
    public function setClassToLayout(){
    	switch ($this->mainLayout) {
    	  case "four":
    	  	$this->layoutClass = "col-12 col-md-4 col-lg-3";
    	  	break;
		  case "three":
		   	$this->layoutClass = "col-12 col-md-4 col-lg-4";
		    break;
		  case "one":
			$this->layoutClass = "col-12";
			break;
		}
    }

    /*
    * Update Parameters Url to keep the Filters
    *
    */
    public function updateParametersUrl(){

     
    	$paramsUrl = http_build_query([
    	  "page" => $this->page ?? 1,
        "filter" => $this->filters,
        "order" => $this->order]
      );
 
        $this->emit('urlChange', $paramsUrl);
        
    }

    /*
    * Check Values From Request
    * just First Request
    */
    public function checkValuesFromRequest(){
        
        if(!empty($this->dataRequest)){

            foreach ($this->queryString as $key => $value) {
                if(isset($this->dataRequest[$value]))
                    $this->{$value} = $this->dataRequest[$value];
            }
   
        }

        $this->firstRequest = false;
    }

    /*
    * Make params to Repository
    * before execcute the query
    */
    public function makeParamsToRepository(){

        /*
    	if($this->firstRequest)
    		$this->checkValuesFromRequest();
        */

      $this->order = config("asgard.icommerce.config.orderByOptions")[$this->orderBy]['order'];
    	$params = [
    		"include" => ['category','categories','manufacturer'],
    		"take" => setting('icommmerce::product-per-page',null,12),
    		"page" => $this->page ?? 1,
    		"filter" => $this->filters,
            "order" =>  $this->order
      ];
    	
    	if(isset($this->category->id))
    		$params["filter"]["category"] = $this->category->id; 
    	

	    return $params;
    	
    	}

	private function getProductRepository(){
		return app('Modules\Icommerce\Repositories\ProductRepository');
    }
    
    /*
    * Render 
    *
    */
    public function render(){
     	
     	
     	$params = $this->makeParamsToRepository();

      //	\Log::info("params: ".json_encode($params));
    	$products = $this->getProductRepository()->getItemsBy(json_decode(json_encode($params)));

    	$this->totalProducts = $products->total();

    	$tpl = 'icommerce::frontend.livewire.products-list';
    	$ttpl = 'icommerce.livewire.products-list';

    	if (view()->exists($ttpl)) $tpl = $ttpl;

    	//Updates Parameters URL
    	$this->updateParametersUrl();

  		// Emit Finish Render
		\Log::info("Emit list rendered: ".json_encode($this->emitProductListRendered));
		$this->emitProductListRendered ? $this->emit('productListRendered', $params) : false;

        return view($tpl,['products'=> ProductTransformer::collection($products), 'params' => $params]);
    }

}