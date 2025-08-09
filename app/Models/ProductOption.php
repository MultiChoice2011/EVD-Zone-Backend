<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOption extends BaseModel
{
    use HasFactory;

    protected $table = 'product_options';

    protected $fillable = [
        'product_id',
        'option_id',
        'value',
        'required',
    ];

    // Define relationships if needed
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function product_option_value()
    {
        return $this->hasMany(ProductOptionValue::class);
    }
    public function option()
    {
        return $this->belongsTo(Option::class);
    }
}
