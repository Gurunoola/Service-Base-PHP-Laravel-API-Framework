<?php

namespace App\Http\Controllers;

use App\Http\Resources\EnquiriesResource;
use App\Models\Enquiries;
use App\Http\Requests\StoreEnquiriesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Response;

class EnquiriesController extends Controller
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

        $enquiries = Enquiries::orderBy($sortBy, $sortOrder)->paginate($perPage);

        return EnquiriesResource::collection($enquiries);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEnquiriesRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreEnquiriesRequest $request)
    {
        $validated = $request->validated();

        $enquiry = Enquiries::create($validated);
        return (new EnquiriesResource($enquiry))
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
        $enquiry = Enquiries::findOrFail($id);
        return (new EnquiriesResource($enquiry))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreEnquiriesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoreEnquiriesRequest $request, $id)
    {
        if (Gate::denies('isAdmin')) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $enquiry = Enquiries::findOrFail($id);
        $enquiry->update($request->validated());

        return (new EnquiriesResource($enquiry))
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

        $enquiry = Enquiries::findOrFail($id);
        $enquiry->delete();

        return response()->json(['message' => 'Enquiries deleted successfully'], Response::HTTP_OK);
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
        $trashedEnquiries = Enquiries::onlyTrashed()->paginate($perPage);

        return EnquiriesResource::collection($trashedEnquiries)
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

        $enquiry = Enquiries::onlyTrashed()->findOrFail($id);
        $enquiry->restore();

        return response()->json(['message' => 'Enquiries restored successfully'], Response::HTTP_OK);
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

        $enquiry = Enquiries::onlyTrashed()->findOrFail($id);
        $enquiry->forceDelete();

        return response()->json(['message' => 'Enquiries permanently deleted'], Response::HTTP_OK);
    }
}