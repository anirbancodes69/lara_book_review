<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;

    // THE MAIN RELATIONSHIP STARTS
    public function reviews(){
        return $this->hasMany(Review::class);
    }
    // THE MAIN RELATIONSHIP ENDS



    // ALL THE SCOPE FILTERS STARTS
    public function scopeTitle(Builder $query, $title):Builder {
        return $query->where("title","like","%". $title ."%");
    }

    public function scopePopular(Builder $query, $from = null, $to = null): Builder {
        return $query->withCount([
        'reviews' => function (Builder $q) use ($from, $to){
           $this->dateFilter($q, $from, $to);
        }])
        ->having('reviews_count','>', 0)
        ->orderBy('reviews_count', 'desc');
    }

    public function ScopeHighestRated(Builder $query,  $from = null, $to = null): Builder{
        return $query
        ->withAvg([
            'reviews' => function (Builder $q) use ($from, $to){
               $this->dateFilter($q, $from, $to);
            }], 'rating')
        ->orderBy('reviews_avg_rating', 'desc');
    }
    // ALL THE SCOPE FILTERS ENDS



    // IN HOUSE FUNCTION STARTS
    private function dateFilter(Builder $query, $from, $to) {

        if($from && !$to){
            $query->where('created_at' ,'>=', $from);
        }
        elseif(!$from && $to){
            $query->where('created_at' ,'<', $to);
        }
        elseif($from && $to){
            $query->whereBetween('created_at', [$from, $to]);
        }

    }
    // IN HOUSE FUNCTION ENDS


}
