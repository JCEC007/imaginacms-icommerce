<?php

namespace Modules\Icommerce\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Icommerce\Entities\ItemType;
use Modules\Icommerce\Entities\OrderStatus;

class ItemTypeTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Model::unguard();
    
    $itemTypes = config('asgard.icommerce.config.itemTypes');
    
    foreach ($itemTypes as $type) {
      
      $statusTrans = $type['title'];
      
      foreach (['en', 'es'] as $locale) {
        
        if ($locale == 'en') {
          $type['title'] = trans($statusTrans, [], $locale);
          $itemType = ItemType::create($type);
        } else {
          $title = trans($statusTrans, [], $locale);
          $itemType->translateOrNew($locale)->title = $title;
          $itemType->save();
        }
        
      }//End Foreach
    }//End Foreach
    
  }
}
