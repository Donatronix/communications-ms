<?php

namespace App\Api\V1\Controllers\Admin;

use App\Api\V1\Controllers\Controller;
use App\Models\Channel;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Sumra\SDK\JsonApiResponse;

/**
 * Class ChannelController
 *
 * Channels Administration
 *
 * @package App\Api\V1\Controllers\Admin
 */
class ChannelController extends Controller
{
    /**
     * Receiving list of Channels. Can get all channels, if type=None and platform=None
     * else receive channels by filtering type or platform fields.
     *
     * @OA\Get(
     *     path="/admin/channels",
     *     summary="Load channels list",
     *     description="Load channels list",
     *     tags={"Admin / Channels"},
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
     *         description="Limit channels of page",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Amount channels by page",
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
     *         description="Show channels by status",
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Show channels by type",
     *         @OA\Schema(
     *             type="enum"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="platform",
     *         in="query",
     *         description="Show channels by platform",
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
     *         description="All channels received successfully"
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
     *         response="500",
     *         description="Server got itself in trouble"
     *     )
     * )
     *
     * @return JsonResponse|mixed
     */
    public function index(Request $request)
    {
        try {
            // Get channels list
            $channels = Channel::select('id', 'name', 'uri', 'token', 'sid', 'secret', 'number', 'type', 'status')
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
                'title' => "Channels list",
                'message' => 'All channels received successfully',
                'data' => $channels->toArray(),
            ], 200);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => "Channels list",
                'message' => 'Failed to get all channels' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Create a new channel
     *
     * @OA\Post(
     *     path="/admin/channels",
     *     summary="Create a new channel",
     *     description="NOTICE. Channel type can be only: 'telegram', 'viber', 'line', 'discord', 'signal', 'whatsapp', 'twilio', 'nexmo', 'facebook'. Channel platform can be only: 'sumra', 'ultainfinity'.",
     *     tags={"Admin / Channels"},
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
     *         @OA\JsonContent(ref="#/components/schemas/ChannelSchema")
     *     ),
     *
     *     @OA\Response(
     *         response="201",
     *         description="New channel was successfully created"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad request syntax or unsupported method"
     *     ),
     *     @OA\Response(
     *         response="401",
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

        // Checks if there is a channel in the database with the given token.
        $channel = Channel::where('token', $request->get('token', null))->first();
        if ($channel) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => 'Adding a channel',
                'message' => 'Channel with this token already exists.',
                'data' => null,
            ], 400);
        }

        try {
            $channel = Channel::create([
                'name' => $request->get('name', null),
                'uri',
                'token',
                'platform',
                'type',
                'number',
                'sid',
                'secret',
            ]);

            return response()->jsonApi([
                'type' => 'success',
                'title' => 'Adding a channel',
                'message' => "New channel {$channel->name} was successfully added",
                'data' => $channel->toArray(),
            ], 201);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => 'Adding a channel',
                'message' => 'New channel was not created: ' . $e->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    /**
     * Get detail info about Channel
     *
     * Handles HTTP requests to URL: /v1/admin/channels/<string:channel_id>.
     * Show a single channel and lets you update and delete them.
     *
     * @OA\Get(
     *     path="/admin/channels/{id}",
     *     summary="Get detail data about channel",
     *     description="Get detail data about Channel",
     *     tags={"Admin / Channels"},
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
     *         description="Channel ID",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="The channel was successfully received"
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Channel not found"
     *     ),
     *     @OA\Response(
     *          response="500",
     *          description="Server got itself in trouble"
     *     )
     * )
     *
     * @param         $id
     * @param Request $request
     *
     * @return mixed
     * @throws ValidationException
     */
    public function show($id, Request $request)
    {
        // Validate input
        $this->validate($request, [
            'id' => 'string|min:36|max:36',
        ]);

        // Get object
        $channel = $this->getObject($id);

        if ($channel instanceof JsonApiResponse) {
            return $channel;
        }

        return response()->jsonApi([
            'type' => 'success',
            'title' => 'Channel details',
            'message' => "The channel was successfully received",
            'data' => $channel->toArray(),
        ], 200);
    }

    /**
     * Get Channel object
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
                'title' => "Get channel",
                'message' => "Channel #{$id} not found or empty",
                'data' => null,
            ], 404);
        }
    }

    /**
     * Update a channel given its identifier
     *
     * @OA\Put(
     *     path="/admin/channels/{id}",
     *     summary="Update a channel given its identifier",
     *     description="Update a channel given its identifier",
     *     tags={"Admin / Channels"},
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
     *         description="Channel Id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ChannelSchema")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="The channel was successfully updated."
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Validation Error"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Channel not found"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Server got itself in trouble"
     *     )
     * )
     *
     * @param Request $request
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
        $channel = $this->getObject($id);

        if ($channel instanceof JsonApiResponse) {
            return $channel;
        }

        // Try update channel model
        try {
            $channel->name = $request->get('name', null);
            $channel->save();

            return response()->jsonApi([
                'type' => 'success',
                'title' => 'Update a channel',
                'message' => "The channel was {$channel->name} successfully updated",
                'data' => $channel->toArray(),
            ], 200);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => 'Change a channel',
                'message' => $e->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    /**
     * Delete a channel given its identifier
     *
     * @OA\Delete(
     *     path="/admin/channels/{id}",
     *     summary="Delete a channel given its identifier",
     *     description="Delete a channel given its identifier",
     *     tags={"Admin / Channels"},
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
     *         description="Channel ID uuid4",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="204",
     *         description="The channel was successfully deleted."
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Channel not found"
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
        $channel = $this->getObject($id);

        if ($channel instanceof JsonApiResponse) {
            return $channel;
        }

        // Try to detach Channels and delete channel
        try {
            $channel->delete();

            return response()->jsonApi([
                'type' => 'success',
                'title' => "Delete of channel",
                'message' => 'The channel was successfully deleted',
                'data' => null,
            ], 204);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => "Delete of channel",
                'message' => 'Cannot delete channel' . $e->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    /**
     * Update status of channels
     *
     * @OA\Post(
     *     path="/admin/channels/{id}/update-status",
     *     summary="Update status of channels",
     *     description="Update status of channels",
     *     tags={"Admin / Channels"},
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
     *         description="Channel ID",
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
        $channel = $this->getObject($id);

        if ($channel instanceof JsonApiResponse) {
            return $channel;
        }

        // Try to detach Channels and delete channel
        try {
            $channel->update([
                'status' => !$channel->status,
            ]);

            // Load channel
            $channel->load('channels');

            return response()->jsonApi([
                'type' => 'success',
                'title' => 'Favorites list',
                'message' => sprintf("%s was successfully %s favorites", $channel->display_name, $channel->is_favorite ? 'added to' : 'removed from'),
                'data' => $channel->toArray(),
            ], 200);
        } catch (Exception $e) {
            return response()->jsonApi([
                'type' => 'danger',
                'title' => "Favorites list",
                'message' => "Can't change status for Channels {$channel->display_name}",
            ], 404);
        }
    }
}
