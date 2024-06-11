<?php
namespace App\Http\Controllers;

use App\Http\Resources\EnquiriesResource;
use App\Models\Enquiries;
use App\Http\Requests\StoreEnquiriesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use App\Utils\ImageUtils;

class EnquiriesController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $filterableFields = [
            'name' => 'like',
            'phone_number' => 'like',
            'rescheduled_date' => 'like',
            'email' => 'like',
            'course' => 'like',
            'status' => '=',
            'dob' => 'like',
            'gender' => '=',
            'counsellor_id' => '=',
            'contact_preference' => '=',
        ];

        // $query = Enquiries::query();
        $query = Enquiries::with('counsellor');

        foreach ($request->all() as $key => $value) {
            if (array_key_exists($key, $filterableFields)) {
                $operator = $filterableFields[$key];
                $query->where($key, $operator, $operator === 'like' ? '%' . $value . '%' : $value);
            }
        }

        $enquiries = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

        return EnquiriesResource::collection($enquiries);
    }

    public function store(StoreEnquiriesRequest $request)
    {
        $validated = $request->validated();

        if ($request->has('dp_path')) {
            $optimizedImagePath = ImageUtils::optimizeAndStoreImage($request->input('dp_path'));
            try {
                $path = Storage::disk('public')->putFile('dps', new \Illuminate\Http\File($optimizedImagePath));
                if (file_exists($optimizedImagePath)) {
                    unlink($optimizedImagePath);
                }
            } catch (\Exception $e) {
                if (file_exists($optimizedImagePath)) {
                    unlink($optimizedImagePath);
                }
                return response()->json(['message' => 'Failed to save image', 'error' => $e->getMessage()], 500);
            }

            $validated['dp_path'] = $path;
        }

        $enquiry = Enquiries::create($validated);
        return (new EnquiriesResource($enquiry))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $enquiry = Enquiries::findOrFail($id);
        return (new EnquiriesResource($enquiry))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    // public function update(StoreEnquiriesRequest $request, $id)
    // {
    //     if (Gate::denies('isAdmin')) {
    //         return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
    //     }

    //     $enquiry = Enquiries::findOrFail($id);

    //     $validated = $request->validated();

    //     if ($request->has('dp_path')) {
    //         if ($enquiry->dp_path) {
    //             Storage::disk('public')->delete($enquiry->dp_path);
    //         }
    //         $optimizedImagePath = ImageUtils::optimizeAndStoreImage($request->input('dp_path'));
    //         try {
    //             $path = Storage::disk('public')->putFile('dps', new \Illuminate\Http\File($optimizedImagePath));
    //             if (file_exists($optimizedImagePath)) {
    //                 unlink($optimizedImagePath);
    //             }
    //         } catch (\Exception $e) {
    //             if (file_exists($optimizedImagePath)) {
    //                 unlink($optimizedImagePath);
    //             }
    //             return response()->json(['message' => 'Failed to save image', 'error' => $e->getMessage()], 500);
    //         }

    //         $validated['dp_path'] = $path;
    //     }

    //     $enquiry->update($validated);
    //     return (new EnquiriesResource($enquiry))
    //         ->response()
    //         ->setStatusCode(Response::HTTP_OK);
    // }

    public function update(StoreEnquiriesRequest $request, $id)
    {
        $user = Auth::user();
        $enquiry = Enquiries::findOrFail($id);
        $validated = $request->validated();

        if ($user->role === 'user') {
            // Allow users with 'user' role to update only specific fields
            $allowedFields = ['status', 'remarks', 'rescheduled_date', 'counsellor_id'];
            $filteredData = array_intersect_key($validated, array_flip($allowedFields));
            $enquiry->update($filteredData);
        } elseif ($user->role === 'admin') {
            // Allow admin to update all fields
            if ($request->has('dp_path')) {
                // Delete the old image if it exists
                if ($enquiry->dp_path) {
                    Storage::disk('public')->delete($enquiry->dp_path);
                }
                // Optimize and store the image temporarily
                $optimizedImagePath = ImageUtils::optimizeAndStoreImage($request->input('dp_path'));
                try {
                    $path = Storage::disk('public')->putFile('dps', new \Illuminate\Http\File($optimizedImagePath));
                    $validated['dp_path'] = $path;
                } catch (\Exception $e) {
                    // Handle any exceptions and clean up the temporary file
                    unlink($optimizedImagePath);
                    return response()->json(['message' => 'Failed to save image', 'error' => $e->getMessage()], 500);
                }
            }
            $enquiry->update($validated);
        } else {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        return (new EnquiriesResource($enquiry))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function destroy($id)
    {
        if (Gate::denies('isAdmin')) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $enquiry = Enquiries::findOrFail($id);
        if ($enquiry->dp_path) {
            Storage::disk('public')->delete($enquiry->dp_path);
        }
        $enquiry->delete();

        return response()->json(['message' => 'Enquiries deleted successfully'], Response::HTTP_NO_CONTENT);
    }

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

    public function restore($id)
    {
        if (Gate::denies('isAdmin')) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $enquiry = Enquiries::onlyTrashed()->findOrFail($id);
        $enquiry->restore();

        return response()->json(['message' => 'Enquiries restored successfully'], Response::HTTP_OK);
    }

    public function forceDelete($id)
    {
        if (Gate::denies('isAdmin')) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $enquiry = Enquiries::onlyTrashed()->findOrFail($id);
        if ($enquiry->dp_path) {
            Storage::disk('public')->delete($enquiry->dp_path);
        }
        $enquiry->forceDelete();

        return response()->json(['message' => 'Enquiries permanently deleted'], Response::HTTP_OK);
    }
}