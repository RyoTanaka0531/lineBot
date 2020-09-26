<?php

namespace APP\Services;

use GuzzleHttp\Client;

class Gurunavi
{
    public function searchRestaurants($word)
    {
        //GuzzleのClientクラス（インスタンス）を生成
        $client = new Client();
        $response = $client
            ->get('https://api.gnavi.co.jp/RestSearchAPI/v3/', [
                //queryをキーとする連想配列でリクエストパラメータを指定
                'query' => [
                    'keyid' => env('GURUNAVI_ACCESS_KEY'),
                    //searchRestaurantsメソッドで渡された$wordをstr_replace関数で加工した上でセット
                    //半角スペースがあればそれを、カンマに置き換える
                    'freeword' => str_replace(' ', ',', $word),
                ],
            ]);

            //PHPで取り扱いやすくするため、json_decode関数を使って連想配列に変換している
            return json_decode($response->getBody()->getContents(), true);
    }
}