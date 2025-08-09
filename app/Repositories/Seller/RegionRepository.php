<?php
namespace App\Repositories\Seller;

use App\Models\Region;
use Prettus\Repository\Eloquent\BaseRepository;

class RegionRepository extends BaseRepository
{
    public function getRegions($request)
    {
        return $this->model()::with('translations')
        ->when($request->name,function($q) use($request){
            $q->whereHas('translations', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        })
        ->orderBy('id','desc')
        ->get();
    }
    public function show($id)
    {
        return $this->getModel()::where('id',$id)->with('translations')->first();
    }
    public function getRegionsByCountry($countryId)
    {
        return $this->getModel()::with('translations')->where('country_id',$countryId)->get();
    }
    public function model() : String
    {
        return Region::class;
    }
}
