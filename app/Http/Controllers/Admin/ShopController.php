<?php

namespace App\Http\Controllers\Admin;

use App\Models\Webserver\ShopCategory;
use App\Models\Webserver\ShopItem;
use App\Models\Webserver\ShopSubCategory;

use Illuminate\Http\Request;

class ShopController extends Controller
{
  /**
   * GET/POST /admin/shop-category
   */
  public function shopCategory(Request $request)
  {
      // When try to add Category
      if($request->isMethod('post')) {
          ShopCategory::create([
              'category_name' => $request->input('category_name')
          ]);
      }

      $categories = ShopCategory::get();

      return view('admin.shop.category', [
          'categories' => $categories
      ]);
  }

  /**
   * GET/POST /admin/shop-subcategory
   */
  public function shopSubCategory(Request $request)
  {
      // When try to add SubCategory
      if($request->isMethod('post')) {
          ShopSubCategory::create([
              'id_category' => $request->input('category_id'),
              'name' => $request->input('sub_category_name')
          ]);
      }

      $categories             = ShopCategory::get();
      $categoriesSelectInput  = [];
      $subCategories          = ShopSubCategory::get();

      // Create beautiful array for select Input
      foreach($categories as $category){
          $categoriesSelectInput[$category->category_name] = [
            $category->id => $category->category_name
          ];
      }

      return view('admin.shop.subcategory', [
          'categories'    => $categoriesSelectInput,
          'subCategories' => $subCategories
      ]);
  }

  /**
   * GET /admin/shop-add
   */
  public function shopAdd(Request $request)
  {

      // Success message
      $success = null;

      // When try to add item
      if($request->isMethod('post')) {
          $itemAdded = ShopItem::create([
              'id_sub_category' => $request->input('id_sub_category'),
              'id_item'         => $request->input('id_item'),
              'quality_item'    => $request->input('quality_item'),
              'name'            => $request->input('name'),
              'price'           => $request->input('price'),
              'quantity'        => $request->input('quantity'),
              'level'           => $request->input('level'),
              'purchased'       => 0
          ]);

          if($itemAdded !== null){
              $success = $request->input('name')." a été ajouté avec succès";
          }
      }

      // List all Sub-Categories group by Category
      $categories = ShopCategory::all()->reduce(function($acc, $cat) {
          $subCategories = ShopSubCategory::where('id_category', $cat->id)->get();

          $acc[$cat->category_name] = $subCategories->reduce(function($ac, $sub) {
              $ac[$sub->id] = $sub->name;

              return $ac;
          }, []);

          return $acc;
      }, []);

      return view('admin.shop.add', [
          'categories' => $categories,
          'success'    => $success
      ]);
  }

  /**
   * GET /admin/shop-edit/{id}
   */
  public function shopEdit(Request $request, $id)
  {
      // Success message
      $success = null;

      // When try to edit item
      if($request->isMethod('post')){
          $itemSaved = ShopItem::where('id_item', '=', $id)->update([
              'id_sub_category' => $request->input('id_sub_category'),
              'id_item'         => $request->input('id_item'),
              'name'            => $request->input('name'),
              'price'           => $request->input('price'),
              'quantity'        => $request->input('quantity'),
              'level'           => $request->input('level'),
          ]);

          if($itemSaved !== null){
              $success = $request->input('name')." a été modifié avec succès";
          }

      }

      $subCategories      = ShopSubCategory::get();
      $item               = ShopItem::where('id_item', '=', $id)->first();
      $subCategoriesInput = [];

      // Create beautiful array for select Input
      foreach($subCategories as $subCategory){
          $subCategoriesInput[$subCategory->name] = [
              $subCategory->id => $subCategory->name
          ];
      }

      return view('admin.shop.edit', [
          'item'          => $item,
          'subCategories' => $subCategoriesInput,
          'success'       => $success
      ]);
  }

  /**
   * GET/POST /admin/shop/subcategory/{id}
   */
  public function ItemsInSubCategory($id)
  {
      $subCategory = ShopSubCategory::find($id);
      $items = ShopItem::where('id_sub_category', '=', $id)->paginate(20);

      return view('admin.shop.items_in_subcategory', [
          'results'     => $items,
          'subCategory' => $subCategory
      ]);
  }
}
