<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'vendor_id', 'product_id', 'qty', 'price', 'orderId', 'phone'];

    //===================================== Relationship ======================================//

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->with('shipping', 'billing', 'payment', 'order');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class)->with('product');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function subDistrict()
    {
        return $this->belongsTo(SubDistrict::class, 'sub_district_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'employee_id', 'id');
    }

    public function invoiceNumber()
    {
        $prefix = strtoupper(substr(config('app.name'), 0, 3)); // First 3 letters of APP_NAME in uppercase

        $orderLastId = Order::orderBy('id', 'desc')->first();

        if (! $orderLastId) {
            return $prefix . '0001';
        } else {
            $lastInvoice = $orderLastId->orderId ?? null;

            if ($lastInvoice) {
                // Extract the numeric part from previous invoice number
                $number = (int) filter_var($lastInvoice, FILTER_SANITIZE_NUMBER_INT);
                return $prefix . sprintf('%04d', $number + 1);
            }

            return $prefix . '0001';
        }
    }

    public function notification()
    {
        return $this->morphOne(Notification::class, 'notifiable');
    }
}
