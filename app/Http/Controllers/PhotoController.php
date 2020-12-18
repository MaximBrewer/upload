<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhotoController extends Controller
{
    public function patch(Request $request)
    {
        if (!$request->get("order") || !$request->file('photo')) return false;
        @mkdir(storage_path("tmp/orders/" . $request->get("order")), 0777, true);
        $photo_path = storage_path("tmp/orders/" . $request->get("order") . "/photo." . $request->photo->extension());
        $order_id = $request->get("order");

        file_put_contents($photo_path, file_get_contents($request->file('photo')));

        $ts_path = storage_path("tmp/orders/" . $request->get("order") . "/final.ts");

        if (!is_file($ts_path)) {
            exec("../makevideo.sh " . $photo_path . " " . $order_id . " " . storage_path());
        }

        if (is_file($ts_path)) {
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://auth.platformcraft.ru/token', [
                'form_params' => ['login' => 'montage', 'password' => 'fz7skpFa']
            ]);
            $array = json_decode($response->getBody()->getContents());
            $client->request('POST', "https://filespot.platformcraft.ru/2/fs/container/" . $array->user_id . "/object/photo/" . $order_id . ".ts", [
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => fopen($ts_path, "r"),
                        'filename' => $order_id . ".ts"
                    ],
                ],
                'headers' => [
                    "Authorization" => "Bearer " . $array->access_token
                ]
            ]);
            // @unlink(storage_path('app/public/orders/' . $order_id . '/') . "tmp.png");
            // @unlink(storage_path('app/public/orders/' . $order_id . '/') . "perspective.png");
            // @unlink(storage_path('app/public/orders/' . $order_id . '/') . "rotate.png");
            // @unlink(storage_path('app/public/orders/' . $order_id . '/') . "final.jpg");
            // @unlink(storage_path('app/public/orders/' . $order_id . '/') . "final.mp4");
            // @unlink(storage_path('app/public/orders/' . $order_id . '/') . "final.ts");
        }
    }
}
