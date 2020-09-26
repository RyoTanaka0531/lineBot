<?php

namespace App\Services;

use Illuminate\Support\Arr;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder;

class RestaurantBubbleBuilder implements ContainerBuilder
{
    private const GOOGLE_MAP_URL = 'https://www.google.com/maps';

    private $imageUrl;
    private $name;
    private $closestStation;
    private $minutesByFoot;
    private $category;
    private $budget;
    private $latitude;
    private $logitude;
    private $phoneNumber;
    private $restaurantUrl;

    public static function builder(): RestaurantBubbleBuilder
    {
        return new self();
    }

    public function setContents(array $restaurant): void
    {
        // Arr::getは第一引数に連想配列、第二引数にキーを受け取り、そのキーの値を返す
        $this->imageUrl = Arr::get($restaurant, 'image_url.shop_image1', null);
        $this->name = Arr::get($restaurant, 'name', null);
        $this->closestStation = Arr::get($restaurant, 'access.station', null);
        $this->minutesByFoot = Arr::get($restaurant, 'access.walk', null);
        $this->category = Arr::get($restaurant, 'category', null);
        $this->budget = Arr::get($restaurant, 'budget', null);
        $this->latitude = Arr::get($restaurant, 'latitude', null);
        $this->logitude = Arr::get($restaurant, 'logitude', null);
        $this->phoneNumber = Arr::get($restaurant, 'tel', null);
        $this->restaurantUrl = Arr::get($restaurant, 'url', null);
    }

    public function build(): array
    {
        $array = [
            'type' => 'bubble',
            'hero' => [
                'type' => 'image',
                'url' => $this->imageUrl,
                'size' => 'full',
                'aspectRatio' => '20:13',
                'aspectMode' => 'cover',
            ],
            'body' => [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => $this->name,
                        'wrap' => true,
                        'weight' => 'bold',
                        'size' => 'md',
                    ],
                    [
                        'type' => 'box',
                        'layout' => 'vertical',
                        'margin' => 'lg',
                        'spacing' => 'sm',
                        'contents' => [
                            [
                                'type' => 'box',
                                'layout' => 'baseline',
                                'spacing' => 'xs',
                                'contents' => [
                                    [
                                        'type' => 'text',
                                        'text' => 'エリア',
                                        'color' => '#aaaaaa',
                                        'size' => 'xs',
                                        'flex' => 4
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => $this->closestStation . '徒歩' . $this->minutesByFoot . '分',
                                        'wrap' => true,
                                        'color' => '#666666',
                                        'size' => 'xs',
                                        'flex' => 12
                                    ]
                                ]
                            ],
                            [
                                'type' => 'box',
                                'layout' => 'baseline',
                                'spacing' => 'xs',
                                'contents' => [
                                    [
                                        'type' => 'text',
                                        'text' => 'ジャンル',
                                        'color' => '#aaaaaa',
                                        'size' => 'xs',
                                        'flex' => 4
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => $this->category,
                                        'wrap' => true,
                                        'color' => '#666666',
                                        'size' => 'xs',
                                        'flex' => 12
                                    ]
                                ]
                            ],
                            [
                                'type' => 'box',
                                'layout' => 'baseline',
                                'spacing' => 'xs',
                                'contents' => [
                                    [
                                        'type' => 'text',
                                        'text' => '予算',
                                        'wrap' => true,
                                        'color' => '#aaaaaa',
                                        'size' => 'sm',
                                        'flex' => 4
                                    ],
                                    [
                                        'type' => 'text',
                                        //三項演算子 式1 ? 式2 : 式3
                                        //式1がtrueの場合、式2が値となる。式1がfalseの場合、式3が値となる
                                        'text' => is_numeric($this->budget) ? '¥' . number_format($this->budget) . '円' : '不明',
                                        'wrap' => true,
                                        'maxLines' => 1,
                                        'color' => '#666666',
                                        'size' => 'xs',
                                        'flex' => 12
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'footer' => [
                'type' => 'box',
                'layout' => 'vertical',
                'spacing' => 'xs',
                'contents' => [
                    [
                        'type' => 'button',
                        'style' => 'link',
                        'height' => 'sm',
                        'action' => [
                            'type' => 'uri',
                            'label' => '地図を見る',
                            'uri' => self::GOOGLE_MAP_URL . '?q=' . $this->latitude . ',' . $this->longitude,
                        ]
                    ],
                    [
                        'type' => 'button',
                        'style' => 'link',
                        'height' => 'sm',
                        'action' => [
                            'type' => 'uri',
                            'label' => '電話する',
                            'uri' => 'tel:' . $this->phoneNumber,
                        ]
                    ],
                    [
                        'type' => 'button',
                        'style' => 'link',
                        'height' => 'sm',
                        'action' => [
                            'type' => 'uri',
                            'label' => '詳しく見る',
                            'uri' => $this->restaurantUrl,
                        ]
                    ],
                    [
                        'type' => 'spacer',
                        'size' => 'xs'
                    ]
                ],
                'flex' => 0
            ]
        ];

        if ($this->imageUrl == '') {
            //Arr::forget関数は第一引数に連想配列、第二引数にキーを受け取り、連想配列からそのキーを削除する
            //バブルコンテナに各ブロックの有無は任意なので削除してもLINEチャネルはユーザーにFlex Messageを送ってくれる。
            Arr::forget($array, 'hero');
        }

        return $array;
    }
}