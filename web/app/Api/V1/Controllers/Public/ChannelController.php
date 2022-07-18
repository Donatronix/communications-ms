<?php

namespace App\Api\V1\Controllers\Public;

use App\Api\V1\Controllers\Controller;
use App\Models\Channel;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ChannelController extends Controller
{
    /**
     * Get auth channels list by platform name.
     *
     * @OA\Get(
     *     path="/channels/auth/{platform}",
     *     summary="Get auth channels list",
     *     description="NOTICE: Channels platform can be only: sumra, ultainfinity",
     *     tags={"Auth Channels"},
     *
     *     security={{
     *         "default": {
     *             "ManagerRead",
     *             "User",
     *             "ManagerWrite"
     *         }
     *     }},
     *
     *     @OA\Parameter(
     *         name="platform",
     *         in="path",
     *         required=true,
     *         description="Platform name. Available values: sumra | ultainfinity",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="The channels was successfully received."
     *     ),
     *     @OA\Response(
     *          response="422",
     *          description="Validation Error"
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Channels not found"
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Server got itself in trouble"
     *     )
     * )
     * @param String $platform
     * @return mixed
     */
    public function __invoke(string $platform): mixed
    {
        // Validate input
        $validator = Validator::make(
            ['platform' => $platform],
            [
                'platform' => [
                    'required',
                    'string',
                    Rule::in(Channel::$platforms),
                ],
            ]
        );

        if ($validator->fails()) {
            return response()->jsonApi([
                'title' => 'Get auth channels list',
                'message' => "Validation error: " . $validator->errors(),
            ], 422);
        }

        // Try update channel model
        try {
            $channels = Channel::query()
                ->where('type', 'auth')
                ->where('platform', $platform)
                ->where('status', true)
                ->get();

            $result = [];
            foreach ($channels as $key => $channel) {
                if($channel->messenger == 'twilio'){
                    continue;
                }

                $result[$key] = [
                    'title' => $channel->title,
                    'messenger' => $channel->messenger,
                ];

                switch ($channel->messenger) {
                    case 'whatsapp':
                        $result[$key] = array_merge($result[$key], [
                            'href' => "https://wa.me/{$channel->uri}?text=join", // text=join%20putting-itself
                            'hrefMobile' => "https://wa.me/{$channel->uri}?text=join"
                        ]);
                        //"https://web.whatsapp.com/send/?phone=14155238886&text=join%20stage-nine&app_absent=0",

                        break;
                    case 'telegram':
                        $uri = trim($channel->uri, '@');

                        $result[$key] = array_merge($result[$key], [
                            'href' => "https://t.me/{$uri}",
                            'hrefMobile' => "tg://resolve?domain={$uri}",
                        ]);

                        break;
                    case 'viber':
                        $result[$key] = array_merge($result[$key], [
                            'href' => "viber://pa?ChatURI={$channel->uri}",
                            'hrefMobile' => "viber://pa?ChatURI={$channel->uri}",
                        ]);

                        break;
                    case 'line':
                        $result[$key] = array_merge($result[$key], [
                            'href' => "https://line.me/R/ti/p/{$channel->uri}",
                            'hrefMobile' => "line://ti/p/{$channel->uri}",
                        ]);

                        break;
                    case 'discord':
                        $result[$key] = array_merge($result[$key], [
                            'href' => "https://discord.gg/{$channel->uri}",
                            'hrefMobile' => "https://discord.gg/{$channel->uri}"
                        ]);
                        //https://discord.com/invite/75xbhgmbvP

                        break;
                    case 'signal':
                        $result[$key] = array_merge($result[$key], [
                            'href' => $channel->uri,
                            'hrefMobile' => $channel->uri,
                        ]);

                        break;
                    case 'messenger':
                        $result[$key] = array_merge($result[$key], [
                            'href' => "https://m.me/$channel->uri",
                            'hrefMobile' => "https://m.me/$channel->uri",
                        ]);

                        break;
                    case 'facebook':
                        $access_token = env('FACEBOOK_MESSENGER_VERIFY_TOKEN');
                        $url = env('FACEBOOK_MESSENEGR_URL');
                        $result[$key] = array_merge($result[$key], [
                            'href' => "{$url}{$access_token}",
                            'hrefMobile' => $channel->uri,
                        ]);

                        break;
                    default:
                        break;
                }
            }

            return response()->jsonApi([
                'title' => 'Get auth channels list',
                'message' => 'Channels filtered by platform received successfully',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            return response()->jsonApi([
                'title' => 'Get auth channels list',
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }
}
