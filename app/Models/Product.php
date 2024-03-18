<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_name',
        'product_description',
        'product_price',
        'product_tag',
    ];

    public static array $product_name = [
        'iPhone',
        'Make Up',
        'Charger',
        'Corndog',
        '48 Laws of Power',
        'Polo Shirt',
        'Electric Fan',
        'Laptop',
        'Dinner Set',
        'Remote Control Car',
    ];

    public static array $product_tag = [
        'Electronics',
        'Fashion',
        'Home',
        'Kitchen',
        'Beauty',
        'Toys',
        'Food',
        'Books',
    ];

    protected $casts = [
        'product_tag' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
