<?php

namespace App\Api\V1\Controllers\Application;

use App\Api\V1\Controllers\Controller;
use App\Models\BotDetail;
use App\Models\Channel;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Sumra\SDK\JsonApiResponse;

/**
 * Class BotDetailController
 *
 * @package App\Api\V1\Controllers\Application
 */
class BotDetailController extends Controller
{
    private BotDetail $botdetail;

    /**
     * BotDetailController constructor.
     *
     * @param BotDetail $botdetail
     */
    public function __construct(BotDetail $botdetail)
    {
        $this->botdetail = $botdetail;
        $this->user_id = auth()->user()->getAuthIdentifier();
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/bot-details",
     *     summary="Load bot details list",
     *     description="Load bot details list",
     *     tags={"Bot Details"},
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
     *         name="limit",
     *         in="query",
     *         description="Limit botdetails",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Count botdetails",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search keywords",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort-by",
     *         in="query",
     *         description="Sort by field ()",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort-order",
     *         in="query",
     *         description="Sort order (asc, desc)",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Success send data"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function index()
    {
        try {
            // Get botdetails list
            $botdetails = $this->botdetail
                ->where('user_id', $this->user_id)
                ->get();

            // Return response
            return response()->jsonApi([
                'title' => "botdetails list",
                'message' => 'List of botdetails successfully received',
                'data' => $botdetails->toArray()
            ]);
        } catch (Exception $e) {
            return response()->jsonApi([
                'title' => "bot details list",
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Save bot detail
     *
     * @OA\Post(
     *     path="/bot-details",
     *     summary="Save bot detail",
     *     description="Note: bot type can be only: 'telegram', 'viber', 'whatsapp'",
     *     tags={"Bot Details"},
     *
     *     security={{
     *         "default": {
     *             "ManagerRead",
     *             "User",
     *             "ManagerWrite"
     *         }
     *     }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BotDetailSchema")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successfully save"
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Bot detail created"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation failed"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'type' => ["required", "string", Rule::in(Channel::$messengers)],
            'token' => 'required|string',
            'name' => 'required|string',
            'username' => 'required|string',
        ]);
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        // Try to add new botdetail
        try {
            // check if same bot detail has already been created
            $botdetail = $this->botdetail->where(['user_id' => $this->user_id, 'type' => $request->get('type')])->first();

            if ($botdetail) {
                return response()->jsonApi([
                    'title' => 'New botdetail registration',
                    'message' => "User already created a {$request->get('type')} bot. Try update it instead"
                ], 400);
            }

            // create new botdetail
            $botdetail = $this->botdetail->create([
                'user_id' => $this->user_id,
                'type' => $request->get('type'),
                'token' => $request->get('token'),
                'name' => $request->get('name'),
                'username' => $request->get('username')
            ]);

            // setwebhook for bot
            $response = $this->setWebHookUrl($request->get('type'));
            if ($response instanceof JsonApiResponse) {
                return $response;
            }

            // Return response to client
            return response()->jsonApi([
                'title' => 'New bot detail created registration',
                'message' => "Bot detail successfully added",
                'data' => $botdetail->toArray()
            ]);
        } catch (Exception $e) {
            return response()->jsonApi([
                'title' => 'New botdetail registration',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Set Webhook Url
     *
     * @param $type
     * @return mixed
     */
    private function setWebHookUrl($type): mixed
    {
        try {
            $app_url = env("APP_URL");
            $app_version = env("APP_API_VERSION");
            // get token
            $token = $this->botdetail->where(['user_id' => $this->user_id, 'type' => $type])->first()->token;
            // webhook url
            $webhook_url = "{$app_url}/{$app_version}/saveUpdates/{$type}/{$token}";
            if ($type == "telegram") {
                // call api to set webhook url
                $client = new Client();
                $response = $client->request('GET', "https://api.telegram.org/bot{$token}/setWebhook?url={$webhook_url}");
            } else if ($type == "viber") {
                // call api to set webhook url
                $client = new Client();
                $response = $client->request('POST', "https://chatapi.viber.com/pa/set_webhook", [
                    'headers' => [
                        'X-Viber-Auth-Token' => $token
                    ],
                    'json' => [
                        'url' => $webhook_url,
                    ]
                ]);
            }

            return json_decode($response->getBody(), true);
        } catch (ModelNotFoundException $e) {
            return response()->jsonApi([
                'title' => "Set webhook url",
                'message' => "{$e->getMessage()}"
            ], 404);
        }
    }

    /**
     * Update a botdetail
     *
     * @OA\Put(
     *     path="/bot-details/{id}",
     *     summary="Update a bot detail",
     *     description="Update a bot detail",
     *     tags={"Bot Details"},
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
     *         name="botdetail_id",
     *         in="path",
     *         description="botdetail Id",
     *         example="0aa06e6b-35de-3235-b925-b0c43f8f7c75",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         description="Name of bot",
     *         example="MyBot"
     *     ),
     *     @OA\Property(
     *         property="username",
     *         type="string",
     *         description="Username of bot",
     *         example="my_bot"
     *     ),
     *     @OA\Property(
     *         property="token",
     *         type="string",
     *         description="Access token for bot",
     *         example="36374819605:GSF4oK7H50GFSg4*uTPT9puKrAd6TKBFF6Ks"
     *     ),
     *          )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successfully save"
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Bot detail created"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation failed"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'name' => 'required|string',
            'username' => 'required|string',
        ]);
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        // Try to update botdetail
        try {

            // find botdetail with id
            $botdetail = $this->getObject($id);

            if ($botdetail instanceof JsonApiResponse) {
                return $botdetail;
            }

            $botdetail->update([
                'username' => $request->get('username'),
                'token' => $request->get('token'),
                'name' => $request->get('name'),
                'username' => $request->get('username')
            ]);

            // Return response to client
            return response()->jsonApi([
                'title' => 'Bot detail updation',
                'message' => "Bot detail successfully updated",
                'data' => $botdetail->toArray()
            ]);
        } catch (Exception $e) {
            return response()->jsonApi([
                'title' => 'Bot detail updation',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get botdetail object
     *
     * @param $id
     * @return mixed
     */
    private function getObject($id): mixed
    {
        try {
            return $this->botdetail::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->jsonApi([
                'title' => "Get botdetail",
                'message' => "Bot detail with id #{$id} not found: {$e->getMessage()}",
            ], 404);
        }
    }

    /**
     * Delete botdetail from storage
     *
     * @OA\Delete(
     *     path="/bot-details/{id}",
     *     summary="Delete botdetail",
     *     description="Delete botdetail",
     *     tags={"Bot Details"},
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
     *         name="id",
     *         in="path",
     *         description="bot detail Id",
     *         example="0aa06e6b-35de-3235-b925-b0c43f8f7c75",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successfully delete"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function destroy($id)
    {
        // Read botdetail model
        $botdetail = $this->getObject($id);
        if ($botdetail instanceof JsonApiResponse) {
            return $botdetail;
        }

        // Try to delete botdetail
        try {
            $botdetail->delete();

            return response()->jsonApi([
                'title' => "Delete botdetail",
                'message' => 'botdetail is successfully deleted',
            ]);
        } catch (Exception $e) {
            return response()->jsonApi([
                'title' => "Delete of botdetail",
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Getting bot detail
     *
     * @OA\Get(
     *     path="/bot-details/{id}",
     *     summary="Getting bot details",
     *     description="Getting bot details",
     *     tags={"Bot Details"},
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
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Bot detail Id",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok"
     *     )
     * )
     * @param $id
     */
    public function show($id)
    {
        // Read botdetail model
        $botdetail = $this->getObject($id);
        if ($botdetail instanceof JsonApiResponse) {
            return $botdetail;
        }

        try {
            return response()->jsonApi([
                'title' => 'Bot detail',
                'message' => "Bot detail been received",
                'data' => $botdetail->toArray()
            ]);
        } catch (Exception $e) {
            return response()->jsonApi([
                'title' => 'Bot detail',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Post(
     *     path="/bot-details/setwebhookurl",
     *     summary="Set webhook url",
     *     description="Set webhook url",
     *     tags={"Bot Details"},
     *
     *     security={{
     *         "default": {
     *             "ManagerRead",
     *             "User",
     *             "ManagerWrite"
     *         }
     *     }},
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *     @OA\Property(
     *         property="type",
     *         type="string",
     *         description="Type of bot",
     *         example="telegram"
     *     ),
     *          )
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Success send data"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function setBotWebHookUrl(Request $request)
    {
        try {
            // setwebhook for bot
            $response = $this->setWebHookUrl($request->get('type'));
            if ($response instanceof JsonApiResponse) {
                return $response;
            }

            return $response;

        } catch (Exception $e) {
            return response()->jsonApi([
                'title' => "bot details list",
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
