<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Favorite extends BaseModel
{
    use HasFactory;
    protected $fillable = ['product_id','category_id'];

    // Define the polymorphic relationship
    public function favoritable() : MorphTo
    {
        return $this->morphTo();
    }

    // Define the relationship to the product
    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
