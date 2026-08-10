<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'total_amount' => (int) $this->total_amount,
            'shipping_fee' => (int) $this->shipping_fee,
            'discount_amount' => (int) $this->discount_amount,
            'final_amount' => (int) $this->final_amount,
            'total_weight' => (float) $this->total_weight,
            'shipping_status' => $this->shipping_status,
            'shipping_status_label' => Order::shippingStatusLabel($this->shipping_status),
            'payment_status' => $this->payment_status,
            'payment_status_label' => Order::paymentStatusLabel($this->payment_status),
            'items_count' => $this->when($this->items_count !== null, $this->items_count),
            'created_at' => $this->created_at,
        ];
    }
}
