<?php

namespace Modules\Icommerce\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
  
  protected $table = 'icommerce__order_item';

  protected $fillable = [
    'order_id',
    'product_id',
    'item_type_id',
    'title',
    'reference',
    'quantity',
    'price',
    'total',
    'tax',
    'reward',
    'options'
  ];
  protected $fakeColumns = ['options'];
  
  protected $casts = [
    'options' => 'array'
  ];
  public function orderOption(){
    return $this->hasMany(OrderOption::class);
  }
  public function type(){
    return $this->belongsTo(ItemType::class);
  }
}