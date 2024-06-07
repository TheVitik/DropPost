<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class TimezoneController extends Controller
{
    public function index(): JsonResponse
    {
        $timezones = collect(timezone_identifiers_list())->map(function ($timezone) {
            return ['name' => $timezone];
        });

        return response()->json($timezones);
    }
}
