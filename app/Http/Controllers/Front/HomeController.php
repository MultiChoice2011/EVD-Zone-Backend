<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Prettus\Validator\Exceptions\ValidatorException;


class HomeController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        //return $this->brandService->getAllBrands($request);
    }



}
