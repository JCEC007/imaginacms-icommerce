<?php

namespace Modules\Icommerce\Repositories\Eloquent;

use Modules\Icommerce\Repositories\CartRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

class EloquentCartRepository extends EloquentBaseRepository implements CartRepository
{
  public function getItemsBy($params)
  {
    // INITIALIZE QUERY
    $query = $this->model->query();

    // RELATIONSHIPS
    $defaultInclude = ['products'];
    $query->with(array_merge($defaultInclude,$params->include));

    // FILTERS
    if($params->filter) {
      $filter = $params->filter;

      //add filter by search
      if (isset($filter->search)) {
        //find search in columns
        $query->where('id', 'like', '%' . $filter->search . '%')
          ->orWhere('updated_at', 'like', '%' . $filter->search . '%')
          ->orWhere('created_at', 'like', '%' . $filter->search . '%');
      }

      if (isset($filter->ip)){
          $query->where('ip', $filter->ip);
      }

      if (isset($filter->user)){
          $query->where('user_id', $filter->user);
      }

    }
    /*== FIELDS ==*/
    if (isset($params->fields) && count($params->fields))
      $query->select($params->fields);

    /*== REQUEST ==*/
    if (isset($params->page) && $params->page) {
      return $query->paginate($params->take);
    } else {
      $params->take ? $query->take($params->take) : false;//Take
      return $query->get();
    }
  }


  public function getItem($criteria, $params)
  {
    // INITIALIZE QUERY
    $query = $this->model->query();

    $query->where('id', $criteria);

    // RELATIONSHIPS
    $includeDefault = ['products'];
    $query->with(array_merge($includeDefault, $params->include));

    // FIELDS
    if ($params->fields) {
      $query->select($params->fields);
    }
    return $query->first();

  }

  public function create($data){

    $cart = $this->model->create($data);

    //sync tables
    $cart->products()->sync(array_get($data, 'products', []));

    return $cart;
  }

  public function updateBy($criteria, $data, $params){

    // INITIALIZE QUERY
    $query = $this->model->query();

    // FILTER
    if (isset($params->filter)) {
      $filter = $params->filter;

      if (isset($filter->field))//Where field
        $query->where($filter->field, $criteria);
      else//where id
        $query->where('id', $criteria);
    }

    // REQUEST
    $model = $query->first();

    if($model){
      $model->update($data);

      //sync tables
      $model->products()->sync(array_get($data, 'products', []));
    }

    return $model;

  }

  public function deleteBy($criteria, $params)
  {
    // INITIALIZE QUERY
    $query = $this->model->query();

    // FILTER
    if (isset($params->filter)) {
      $filter = $params->filter;

      if (isset($filter->field)) //Where field
        $query->where($filter->field, $criteria);
      else //where id
        $query->where('id', $criteria);
    }


    // REQUEST
    $model = $query->first();

    if($model) {
      $model->delete();
    }
  }


}