<?php

namespace APP\Services;

use GuzzleHttp\Client;

class Gurunavi
{
    //ぐるなびのレストラン検索APIのURLということを変数名で明示
    //クラス定数の定義
    private const RESTAURANTS_SEARCH_API_URL = 'https://api.gnavi.co.jp/RestSearchAPI/v3/';

    //ユーザーから送られてくる$wordが文字列であることをstringをつけて宣言
    public function searchRestaurants(string $word): array
    {
        //GuzzleのClientクラス（インスタンス）を生成
        $client = new Client();
        //Guzzleのgetメソッドでgetリクエストが行える
        //第一引数にリクエスト先のURL
        //第二引数にオプションとなる情報を連想配列で渡す
        $response = $client
            //selfはGurunaviクラス自身を指している
            ->get(self::RESTAURANTS_SEARCH_API_URL, [
                //queryをキーとする連想配列でリクエストパラメータを指定
                'query' => [
                    'keyid' => env('GURUNAVI_ACCESS_KEY'),
                    //searchRestaurantsメソッドで渡された$wordをstr_replace関数で加工した上でセット
                    //半角スペースがあればそれを、カンマに置き換える
                    'freeword' => str_replace(' ', ',', $word),
                ],
                'http_errors' => false,
            ]);

            //PHPで取り扱いやすくするため、json_decode関数を使って連想配列に変換している
            return json_decode($response->getBody()->getContents(), true);
    }
}