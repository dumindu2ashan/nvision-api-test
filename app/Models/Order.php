<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['customer_name', 'order_value', 'process_id', 'status'];

    public function getStatusStringAttribute()
    {
        switch ($this->status) {
            case 1:
                return "Processing";
                break;
            case 2:
                return "Shipping";
                break;
            case 3:
                return "Delivered";
                break;
        }
    }

    public function getOrderIdAttribute()
    {
        return str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
}
