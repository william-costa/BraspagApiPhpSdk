<?php
namespace Braspag\API;

class Cart implements \JsonSerializable
{

    private $isGift;

    private $returnsAccepted;

    private $items = [];

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function populate(\stdClass $data)
    {
      $this->isGift = isset($data->IsGift)? $data->IsGift: null;
      $this->returnsAccepted = isset($data->ReturnsAccepted)? $data->ReturnsAccepted: null;

      if (isset($data->Items) and is_array($data->Items)) {
         foreach($data->Items as $item){
           $cartItem = $this->item();
           $cartItem->populate($item);
         }
      }
    }

    public function getIsGift()
    {
        return $this->isGift;
    }

    public function setIsGift($isGift)
    {
        $this->isGift = $isGift;
        return $this;
    }

    public function getReturnsAccepted()
    {
        return $this->returnsAccepted;
    }

    public function setReturnsAccepted($returnsAccepted)
    {
        $this->returnsAccepted = $returnsAccepted;
        return $this;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    public function item()
    {
        $item = new Item();

        $this->items[] = $item;

        return $item;
    }

}
