<?php

namespace App\Api\V1\Controllers\Admin;

use App\Api\V1\Controllers\Controller;
use App\Models\Channel;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class BotController
 *
 * Bots Administration
 *
 * @package App\Api\V1\Controllers\Admin
 */
class ChannelController extends Controller
{
    /**
     * Receiving list of Bots. Can get all bots, if type=None and platform=None
     * else receive bots by filtering type or platform fields.
     *
     * @OA\Get(
     *     path="/admin/bots",
     *     summary="Load bots list",
     *     description="Load bots list",
     *     tags={"Admin / Bots"},
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
     *         name="limit",
     *         in="query",
     *         description="Limit bots of page",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Amount bots by page",
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
     *         name="status",
     *         in="query",
     *         description="Show bots by status",
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Show bots by type",
     *         @OA\Schema(
     *             type="enum"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="platform",
     *         in="query",
     *         description="Show bots by platform",
     *         @OA\Schema(
     *             type="enum"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort[by]",
     *         in="query",
     *         description="Sort by field (name, type, platform)",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort[order]",
     *         in="query",
     *         description="Sort order (asc, desc)",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="All bots received successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server got itself in trouble"
     *     )
     * )
     *
     * @return JsonResponse|mixed
     */
    public function index(Request $request)
    {
        try {
            // Get bots list
            $bots = Channel::select('id', 'name', 'uri', 'token', 'type', 'status')
                ->when($request->has('type'), function ($q) use ($request) {
                    return $q->where('type', $request->get('type'));
                })
                ->when($request->has('platform'), function ($q) use ($request) {
                    return $q->where('platform', $request->get('platform'));
                })
                ->when($request->has('status'), function ($q) use ($request) {
                    return $q->where('status', $request->boolean('status'));
                })
                ->get();

            // Return response
            return response()->jsonApi([
                'type' => 'success',
                'title' => "Bots list",
                'message' => 'All bots received successfully',
                'data' => $bots->toArray(),
            ], 200);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => "Bots list",
                'message' => 'Failed to get all bots' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Create a new bot
     *
     * @OA\Post(
     *     path="/admin/bots",
     *     summary="Create a new bot",
     *     description="NOTICE. Bot type can be only: 'telegram', 'viber', 'line', 'discord', 'signal', 'whatsapp', 'twilio', 'nexmo'. Bot platform can be only: 'sumra', 'ultainfinity'.",
     *     tags={"Admin / Bots"},
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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BotSchema")
     *     ),
     *
     *     @OA\Response(
     *         response="201",
     *         description="New bot was successfully created"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request syntax or unsupported method"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Server got itself in trouble"
     *     )
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        // Validate input
        $this->validate($request, Channel::validationRules());

        // Checks if there is a bot in the database with the given token.
        $bot = Channel::where('token', $request->get('token', null))->first();
        if ($bot) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => 'Adding a bot',
                'message' => 'Bot with this token already exists.',
                'data' => null,
            ], 400);
        }

        try {
            $bot = Channel::create([
                'name' => $request->get('name', null),
                'uri',
                'token',
                'platform',
                'type',
            ]);

            return response()->jsonApi([
                'type' => 'success',
                'title' => 'Adding a bot',
                'message' => "New bot {$bot->name} was successfully added",
                'data' => $bot->toArray(),
            ], 201);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => 'Adding a bot',
                'message' => 'New bot was not created: ' . $e->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    /**
     * Get detail info about Bot
     *
     * Handles HTTP requests to URL: /api/v1/admin/bots/<string:bot_id>.
     * Show a single bot and lets you update and delete them.
     *
     * @OA\Get(
     *     path="/admin/bots/{id}",
     *     summary="Get detail data about bot",
     *     description="Get detail data about Bot",
     *     tags={"Admin / Bots"},
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
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Bot ID",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="The bot was successfully received"
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Bot not found"
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Server got itself in trouble"
     *     )
     * )
     *
     * @param $id
     *
     * @return mixed
     * @throws GuzzleException
     */
    public function show($id, Request $request)
    {
        // Validate input
        $this->validate($request, [
            'id' => 'string|min:36|max:36',
        ]);

        // Get object
        $bot = $this->getObject($id);

        if ($bot instanceof JsonApiResponse) {
            return $bot;
        }

        return response()->jsonApi([
            'type' => 'success',
            'title' => 'Bot details',
            'message' => "The bot was successfully received",
            'data' => $bot->toArray(),
        ], 200);
    }

    /**
     * Get Bot object
     *
     * @param $id
     *
     * @return mixed
     */
    private function getObject($id)
    {
        try {
            return Channel::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => "Get bot",
                'message' => "Bot #{$id} not found or empty",
                'data' => null,
            ], 404);
        }
    }

    /**
     * Update a bot given its identifier
     *
     * @OA\Put(
     *     path="/admin/bots/{id}",
     *     summary="Update a bot given its identifier",
     *     description="Update a bot given its identifier",
     *     tags={"Admin / Bots"},
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
     *         name="id",
     *         in="path",
     *         description="Bot Id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BotSchema")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="The bot was successfully updated."
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Validation Error"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Bot not found"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Server got itself in trouble"
     *     )
     * )
     *
     * @param Request                  $request
     * @param                          $id
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        // Validate input
        $this->validate($request, Channel::validationRules());

        // Get object
        $bot = $this->getObject($id);

        if ($bot instanceof JsonApiResponse) {
            return $bot;
        }

        // Try update bot model
        try {
            $bot->name = $request->get('name', null);
            $bot->save();

            return response()->jsonApi([
                'type' => 'success',
                'title' => 'Update a bot',
                'message' => "The bot was {$bot->name} successfully updated",
                'data' => $bot->toArray(),
            ], 200);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => 'Change a bot',
                'message' => $e->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    /**
     * Delete a bot given its identifier
     *
     * @OA\Delete(
     *     path="/admin/bots/{id}",
     *     summary="Delete a bot given its identifier",
     *     description="Delete a bot given its identifier",
     *     tags={"Admin / Bots"},
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
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Bot ID uuid4",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="204",
     *         description="The bot was successfully deleted."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Bot not found"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Server got itself in trouble"
     *     )
     * )
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function destroy($id, Request $request): JsonResponse
    {
        // Validate input
        $this->validate($request, [
            'id' => 'string|min:36|max:36',
        ]);

        // Get object
        $bot = $this->getObject($id);

        if ($bot instanceof JsonApiResponse) {
            return $bot;
        }

        // Try to detach Bots and delete bot
        try {
            $bot->delete();

            return response()->jsonApi([
                'type' => 'success',
                'title' => "Delete of bot",
                'message' => 'The bot was successfully deleted',
                'data' => null,
            ], 204);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => "Delete of bot",
                'message' => 'Cannot delete bot' . $e->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    /**
     * Update status of bots
     *
     * @OA\Post(
     *     path="/admin/bots/{id}/update-status",
     *     summary="Update status of bots",
     *     description="Update status of bots",
     *     tags={"Admin / Bots"},
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
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Bot ID",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successfully updated"
     *     )
     * )
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function updateStatus($id): JsonResponse
    {
        // Get object
        $bot = $this->getObject($id);

        if ($bot instanceof JsonApiResponse) {
            return $bot;
        }

        // Try to detach Bots and delete bot
        try {
            $bot->update([
                'status' => !$bot->status,
            ]);

            // Load bot
            $bot->load('bots');

            return response()->jsonApi([
                'type' => 'success',
                'title' => 'Favorites list',
                'message' => sprintf("%s was successfully %s favorites", $bot->display_name, $bot->is_favorite ? 'added to' : 'removed from'),
                'data' => $bot->toArray(),
            ], 200);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => "Favorites list",
                'message' => "Can't change status for Bots {$bot->display_name}",
            ], 404);
        }
    }
}
