<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends BaseController
{
    public function doLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // dd($request->all());
            $response = Http::withoutVerifying()
                ->asForm()
                ->timeout(30)
                ->post('https://auth.srs-ssms.com/api/dev/login', [
                    'email' => $request->email,
                    'password' => $request->password,
                ]);

            return response()->json($response->json());
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function home()
    {
        return view('home');
    }

    function getListItem(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->timeout(30)
                ->get('https://auth.srs-ssms.com/api/dev/list-items');
            // dd([
            //     $response->status(),
            //     $response->body(),
            //     $token
            // ]);
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
