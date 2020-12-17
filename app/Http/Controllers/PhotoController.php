<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhotoController extends Controller
{
    public function patch(Request $request)
    {
        if(!$request->get("order") || !$request->file('photo')) return false;
        @mkdir(storage_path("/tpm/" . $request->get("order")), 0777, true);
        file_put_contents(storage_path("/tpm/" . $request->get("order") . "/photo." . $request->photo->extension()), file_get_contents($request->file('photo')));
        // try {
        //     $filepath = storage_path(('app/public/orders/' . $order->id) . "/final.ts");

        //     if (!is_file($filepath)) {
        //         exec("./makevideo.sh " . storage_path(('app/public/') . $order->photo . " " . storage_path(('app/public/') . $order->id)));
        //     }

        //     var_dump([$filepath => is_file($filepath)]);


        //     if (is_file($filepath)) {

        //         $client = new \GuzzleHttp\Client();

        //         $response = $client->post('https://auth.platformcraft.ru/token', [
        //             'form_params' => ['login' => 'montage', 'password' => 'fz7skpFa']
        //         ]);
        //         $array = json_decode($response->getBody()->getContents());


        //         $client->request('POST', "https://filespot.platformcraft.ru/2/fs/container/" . $array->user_id . "/object/photo/" . $order->id . ".ts", [
        //             'multipart' => [
        //                 [
        //                     'name'     => 'file',
        //                     'contents' => fopen($filepath, "r"),
        //                     'filename' => $order->id . ".ts"
        //                 ],
        //             ],
        //             'headers' => [
        //                 "Authorization" => "Bearer " . $array->access_token
        //             ]
        //         ]);

        //         @unlink(storage_path('app/public/orders/' . $order->id . '/') . "tmp.png");
        //         @unlink(storage_path('app/public/orders/' . $order->id . '/') . "perspective.png");
        //         @unlink(storage_path('app/public/orders/' . $order->id . '/') . "rotate.png");
        //         @unlink(storage_path('app/public/orders/' . $order->id . '/') . "final.jpg");
        //         @unlink(storage_path('app/public/orders/' . $order->id . '/') . "final.mp4");
        //         @unlink(storage_path('app/public/orders/' . $order->id . '/') . "final.ts");

        //         $order->update([
        //             'video' => 1
        //         ]);
        //     }
        // } catch (Throwable $e) {
        //     report($e);
        // }
    }
}
