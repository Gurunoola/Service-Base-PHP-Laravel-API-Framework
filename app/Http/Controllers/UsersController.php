<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $filterableFields = [
            'name' => 'like',
            'email' => 'like',
            'role' => '='
        ];

        $query = User::query();

        foreach ($request->all() as $key => $value) {
            if (array_key_exists($key, $filterableFields)) {
                $operator = $filterableFields[$key];
                $query->where($key, $operator, $operator === 'like' ? '%' . $value . '%' : $value);
            }
        }

        $users = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreUserRequest $request)
    {
        if (Gate::denies('isAdmin')) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

         // Get the maximum number of users allowed from the configuration
         $maxUsers = Config::get('custom.max_users');

         // Check the current number of users
         $currentUsersCount = User::count();
 
         if ($currentUsersCount >= $maxUsers) {
             return response()->json(['message' => 'Maximum number of users reached'], Response::HTTP_FORBIDDEN);
         }

        $validated = $request->validated();
        
        $enquiry = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password)
        ]);
        return (new UserResource($enquiry))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $enquiry = User::findOrFail($id);
        return (new UserResource($enquiry))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoreUserRequest $request, $id)
    {
        if (Gate::denies('isAdmin')) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $enquiry = User::findOrFail($id);
        $enquiry->update($request->validated());

        return (new UserResource($enquiry))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        if (Gate::denies('isAdmin')) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $enquiry = User::findOrFail($id);
        $enquiry->delete();

        return response()->json(['message' => 'User deleted successfully'], Response::HTTP_NO_CONTENT);
    }

    /**
     * Method to retrieve soft deleted enquiries.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function trashed(Request $request)
    {
        if (Gate::denies('isAdmin')) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $perPage = $request->get('per_page', 15);
        $trashedUser = User::onlyTrashed()->paginate($perPage);

        return UserResource::collection($trashedUser)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Method to restore a soft deleted enquiry.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        if (Gate::denies('isAdmin')) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $enquiry = User::onlyTrashed()->findOrFail($id);
        $enquiry->restore();

        return response()->json(['message' => 'User restored successfully'], Response::HTTP_OK);
    }

    /**
     * Method to permanently delete a soft deleted enquiry.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete($id)
    {
        if (Gate::denies('isAdmin')) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $enquiry = User::onlyTrashed()->findOrFail($id);
        $enquiry->forceDelete();

        return response()->json(['message' => 'User permanently deleted'], Response::HTTP_OK);
    }
}