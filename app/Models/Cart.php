<?php

namespace App\Models;

class CartProduct
{

    public $items = null;
    public $totalQty = 0;
    public $totalPrice = 0;

    public function __construct($oldcart) {
        if ($oldcart) {
            $this->items = $oldcart->items;
            $this->totalQty = $oldcart->totalQty;
            $this->totalPrice = $oldcart->totalPrice;
        }
    }

    public function add($item) {
        $storedItem = [
          'code' => $item->code,
          'price' => $item->price,
          'item' => $item->name,
          
        ];

        if ($this->items) {
            if (array_key_exists($id, $this->items)) {
                $storedItem = $this->items[$id];
            }
        }
        $storedItem['qty']++;
        $storedItem['price'] = $item->price * $storedItem['qty'];
        $this->items[$id] = $storedItem;
        $this->totalQty++;
        $this->totalPrice += $item->price;
    }


    public function remove($id) {
        $this->items[$id]['qty']--;
        $this->items[$id]['price'] -= $this->items[$id]['item']['price'];
        $this->totalQty--;
        $this->totalPrice -= $this->items[$id]['item']['price'];
        if ($this->items[$id]['qty'] <= 0) {
          unset($this->items[$id]);
          return;
        }
    }



        public function destroy($id) {
          $this->totalQty -= $this->items[$id]['qty'];
          $this->totalPrice -= $this->items[$id]['price'];
          unset($this->items[$id]);
        }


}
