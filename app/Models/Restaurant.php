<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Restaurant extends Model
{
    use HasFactory,Sortable;

    protected $fillable = [
        'name',
        'description',
        'lowest_price',
        'highest_price',
        'postal_code',
        'address',
        'opening_time',
        'closing_time',
        'seating_capacity',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function regular_holidays()
    {
        return $this->belongsToMany(RegularHoliday::class)->withTimestamps();
    }
}
