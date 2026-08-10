<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'source' => $this->source,
            'total_amount' => (int) $this->total_amount,
            'shipping_fee' => (int) $this->shipping_fee,
            'discount_amount' => (int) $this->discount_amount,
            'shipping_cost' => (int) $this->shipping_cost,
            'insurance_cost' => (int) $this->insurance_cost,
            'final_amount' => (int) $this->final_amount,
            'total_weight' => (float) $this->total_weight,
            'shipping_status' => $this->shipping_status,
            'shipping_status_label' => Order::shippingStatusLabel($this->shipping_status),
            'payment_status' => $this->payment_status,
            'payment_status_label' => Order::paymentStatusLabel($this->payment_status),
            'payment_method' => $this->payment_method,
            'shipping_method' => $this->shipping_method,
            'shipping_address' => $this->shipping_address,
            'shipping_city' => $this->shipping_city,
            'shipping_state' => $this->shipping_state,
            'shipping_postal_code' => $this->shipping_postal_code,
            'shipping_recipient_name' => $this->shipping_recipient_name,
            'shipping_phone' => $this->shipping_phone,
            'shipping_tracking_code' => $this->shipping_tracking_code,
            'notes' => $this->notes,
            'items' => OrderProductResource::collection($this->whenLoaded('items')),
            'histories' => OrderHistoryResource::collection($this->whenLoaded('histories')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
