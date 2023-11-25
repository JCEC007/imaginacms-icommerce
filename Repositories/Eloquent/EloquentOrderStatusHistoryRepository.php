<?php

namespace Modules\Icommerce\Repositories\Eloquent;

use Modules\Icommerce\Events\OrderStatusHistoryWasCreated;
use Modules\Icommerce\Events\OrderWasProcessed;
use Modules\Icommerce\Events\OrderWasUpdated;
use Modules\Icommerce\Repositories\OrderStatusHistoryRepository;
use Modules\Core\Icrud\Repositories\Eloquent\EloquentCrudRepository;

class EloquentOrderStatusHistoryRepository extends EloquentCrudRepository implements OrderStatusHistoryRepository
{
  /**
   * Filter names to replace
   * @var array
   */
  protected $replaceFilters = [];

  /**
   * Relation names to replace
   * @var array
   */
  protected $replaceSyncModelRelations = [];

  /**
   * Filter query
   *
   * @param $query
   * @param $filter
   * @param $params
   * @return mixed
   */
  public function filterQuery($query, $filter, $params)
  {

    /**
     * Note: Add filter name to replaceFilters attribute before replace it
     *
     * Example filter Query
     * if (isset($filter->status)) $query->where('status', $filter->status);
     *
     */

    //Response
    return $query;
  }

  /**
   * Method to sync Model Relations
   *
   * @param $model ,$data
   * @return $model
   */
  public function syncModelRelations($model, $data)
  {
    //Get model relations data from attribute of model
    $modelRelationsData = ($model->modelRelations ?? []);

    /**
     * Note: Add relation name to replaceSyncModelRelations attribute before replace it
     *
     * Example to sync relations
     * if (array_key_exists(<relationName>, $data)){
     *    $model->setRelation(<relationName>, $model-><relationName>()->sync($data[<relationName>]));
     * }
     *
     */

    //Response
    return $model;
  }
  
  public function create($data)
  {
    $orderhistory = parent::create($data); // TODO: Change the autogenerated stub
    
    //====== Update Order
    $orderhistory->order->update([
      'status_id' => $orderhistory->status
    ]);
    event(new OrderWasUpdated($orderhistory->order));
    //====== End Update Order
  
    event(new OrderStatusHistoryWasCreated($orderhistory));
  
    if($orderhistory->status == 13) {// Processed
      event(new OrderWasProcessed($orderhistory->order));
    }
  
    return $orderhistory;
    
  }
}
