<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Event\MessageEvent\TextMessage;

class LineBotController extends Controller
{
    //
    public function index()
    {
        return view('linebot.index');
    }

    public function parrot(Request $request)
    {
        Log::debug($request->header());
        Log::debug($request->input());

        $httpClient = new CurlHTTPClient(env('LINE_ACCESS_TOKEN'));
        $lineBot = new LINEBot($httpClient, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);

        $signature = $request->header('x-line-signature');
        // validateSignatureメソッドは、メッセージボディと署名を引数として受け取り、署名の検証を行う
        // inputメソッドではなくgetContentメソッドを使うのはvalidSignatureメソッドの仕様でメッセージボディを配列ではなく文字列で渡す必要があるから
        if (!$lineBot->validateSignature($request->getContent(), $signature)) {
            abort(400, 'Invalid signature');
        }

        // parseEventRequestメソッドが、リクエストからイベント情報を取り出す
        $events = $lineBot->parseEventRequest($request->getContent(), $signature);

        Log::debug($events);

        foreach ($events as $event){
            //$event instanceof TextMessageで$eventがTextMessageクラスのインスタンスであるかどうかを判定
            //結果がfalseの場合ログファイルに表示
            if (!($event instanceof TextMessage)){
                Log::debug('Non text message has come');
                continue;
            }

            //$eventはTextMessageクラスのインスタンスである
            //Webhookとは友だち追加やメッセージの送信のようなイベントが発生すると、LINEプラットフォームからWebhook URL(ボットサーバー)にHTTPS POSTリクエストが送信される
            //応答メッセージを送るには、Webhookイベントオブジェクトに含まれる応答トークンが必要
            //イベントが発生するとWebhookを使って通知され、応答できるイベントには応答トークンが発行される
            //getReplyTokenメソッドで、応答トークン(replyToken)を取り出す
            $replyToken = $event->getReplyToken();

            //getTextメソッドは送られてきたメッセージのテキストを取り出す
            $replyText = $event->getText();

            //LINEBotクラスのreplyTextメソッドで、テキストメッセージでの返信が行われる
            //第一引数には応答トークンを、第二引数には返信内容のテキストを渡す
            $lineBot->replyText($replyToken, $replyText);
        }
    }
}
