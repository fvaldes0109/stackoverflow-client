<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class QuestionController extends Controller
{
    public function get(Request $request)
    {
        $request->validate([
            'tagged' => 'required|string',
            'fromdate' => 'numeric',
            'todate' => 'numeric|gte:fromdate',
        ]);

        $response = Http::get('https://api.stackexchange.com/2.3/questions', [
            'site' => 'stackoverflow.com',
            'tagged' => $request->input('tagged'),
            'fromdate' => $request->input('fromdate'),
            'todate' => $request->input('todate'),
        ]);

        return $response->json();
    }
}
