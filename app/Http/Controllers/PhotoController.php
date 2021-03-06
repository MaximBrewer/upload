<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhotoController extends Controller
{
    public function patch(Request $request)
    {
        if (!$request->get("order") || !$request->file('photo')) return false;
        @mkdir(storage_path("tmp/orders/" . $request->get("order")), 0777, true);

        touch(storage_path("tmp/orders/" . $request->get("order") . "/lock.txt"));

        $fp = fopen(storage_path("tmp/orders/" . $request->get("order") . "/lock.txt"), 'r+');
        if (flock($fp, LOCK_EX | LOCK_NB)) {

            $photo_path = storage_path("tmp/orders/" . $request->get("order") . "/photo." . $request->photo->extension());
            $order_id = $request->get("order");

            $url = "https://montage-cache.cdnvideo.ru/montage/photo/" . $order_id . ".ts";
            $headers = @get_headers($url);
            echo $headers[0] . PHP_EOL;
            if (strpos($headers[0], '200'))
                return false;

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
                @unlink(storage_path('app/public/orders/' . $order_id . '/') . "tmp.png");
                @unlink(storage_path('app/public/orders/' . $order_id . '/') . "perspective.png");
                @unlink(storage_path('app/public/orders/' . $order_id . '/') . "rotate.png");
                @unlink(storage_path('app/public/orders/' . $order_id . '/') . "final.jpg");
                @unlink(storage_path('app/public/orders/' . $order_id . '/') . "final.mp4");
                @unlink(storage_path('app/public/orders/' . $order_id . '/') . "final.ts");
            }
            fclose($fp);
            return 0;
        }
    }
}
