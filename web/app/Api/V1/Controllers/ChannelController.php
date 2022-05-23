<?php

namespace App\Api\V1\Controllers;

use App\Models\Channel;
use Exception;
use Illuminate\Http\Request;
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
     *     x={
     *         "auth-type": "Application & Application User",
     *         "throttling-tier": "Unlimited",
     *         "wso2-application-security": {
     *             "security-types": {"oauth2"},
     *             "optional": "false"
     *         }
     *     },
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
     *          response="400",
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
     */
    public function __invoke($platform, Request $request)
    {
        // Validate input
        $validator = Validator::make(
            [
                'platform' => $platform,
            ],
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
                'type' => 'warning',
                'title' => '',
                'message' => "Validation error",
                'data' => $validator->errors(),
            ], 400);
        }

        // Try update channel model
        try {
            $channels = Channel::where('platform', $platform)
                ->where('status', true)
                ->get();

            $result = [];
            foreach ($channels as $key => $channel) {
                $result[$key] = [
                    'type' => $channel->type,
                    'title' => strtolower($channel->type),
                ];

                switch ($channel->type) {
                    case 'telegram':
                        $uri = trim($channel->type, '@');

                        $result[$key] = array_merge($result[$key], [
                            'href' => "https://t.me/{$uri}",
                            'hrefMobile' => "tg://resolve?domain={$uri}",
                        ]);

                        break;
                    case 'viber':
                        $result[$key] = array_merge($result[$key], [
                            //'href' => "https://chats.viber.com/{$channel->uri}",
                            'href' => "viber://pa?ChatURI={$channel->uri}",
                            'hrefMobile' => "viber://pa?ChatURI={$channel->uri}",
                        ]);
                        break;
                    case 'line':
                        $result[$key] = array_merge($result[$key], [
                            //'href' => "https://page.line.me/?accountId=772dmcwu",
                            'href' => "https://line.me/R/ti/p/{$channel->uri}",
                            'hrefMobile' => "line://ti/p/{$channel->uri}",
                        ]);

                        break;
                    case 'discord':
                        $result[$key] = array_merge($result[$key], [
                            'href' => $channel->uri,
                            'hrefMobile' => $channel->uri,
                        ]);
                        //https://discord.com/oauth2/authorize?client_id=843810660324474890&permissions=210944&scope=channel
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
                            'href' => $channel->uri,
                            'hrefMobile' => $channel->uri,
                        ]);
                        //href: "https://m.me/SumraChannel",
                        //hrefMobile: "https://m.me/SumraChannel",

                        break;
                    case 'whatsapp':
                        $result[$key] = array_merge($result[$key], [
                            'href' => $channel->uri,
                            'hrefMobile' => $channel->uri,
                        ]);

                        // https://wa.me/14155238886
                        //"https://web.whatsapp.com/send/?phone=14155238886&text=join%20stage-nine&app_absent=0",

                        break;
                    case 'twilio':
                    case 'nexmo':
                        $result[$key] = array_merge($result[$key], [
                            'href_send_phone' => "https://{$request->getHost()}/api/v1/sms/send-phone?channel_id={$channel->id}",
                            'href_send_sms' => "https://{$request->getHost()}/api/v1/sms/send-sms?channel_id={$channel->id}",
                        ]);
                        break;

                    case 'facebook':
                        $access_token = env('FACEBOOK_BOT_PAGE_ACCESS_TOKEN');
                        $result[$key] = array_merge($result[$key], [
                            //'href' = "https://graph.facebook.com/v2.6/me/messages?access_token={$access_token}",
                            'href' => $channel->uri,
                            'hrefMobile' => $channel->uri,
                        ]);
                        break;
                    default:
                        break;
                }
            }

            return response()->jsonApi([
                'type' => 'success',
                'title' => 'Get auth channels list',
                'message' => 'Channels filtered by platform received successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => 'Get auth channels list',
                'message' => $e->getMessage(),
                'data' => null,
            ], 400);
        }
    }
}
