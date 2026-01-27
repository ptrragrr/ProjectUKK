<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Get all users (optionally filter by role_id).
     */
    public function get(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => User::when($request->role_id, function (Builder $query, string $role_id) {
                $query->role($role_id);
            })->get()
        ]);
    }

    /**
     * Display a paginated list of users.
     */
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);

        $data = User::when($request->search, function (Builder $query, string $search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%");
        })
            ->oldest()
            ->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        return response()->json($data);
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('photo')) {
            $validatedData['photo'] = $request->file('photo')->store('photo', 'public');
        }

        $user = User::create($validatedData);

        $role = Role::findById($validatedData['role_id']);
        $user->assignRole($role);

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

//     public function show($id)
// {
//     $user = User::findOrFail($id);
//     return response()->json(['user' => $user]);
// }

    /**
     * Show profile of currently authenticated user.
     */
    public function me(Request $request)
    {
        $user = $request->user();
        
        // Load roles relationship
        $user->load('roles');
        
        // Get role information
        $userRole = $user->roles->first();
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'photo' => $user->photo,
                'photo_url' => $user->photo ? asset('storage/' . $user->photo) : null,
                'role' => $userRole ? $userRole->name : null,
                'role_id' => $userRole ? $userRole->id : null,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ]);
    }

    /**
     * Get all roles for dropdown.
     */
    public function getRoles()
    {
        $roles = Role::select('id', 'name')->get();
        
        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    /**
     * Update profile of currently authenticated user.
     */
    // public function updateMe(Request $request)
    // {
    //     Log::info('UpdateMe request data: ', $request->all());
    
    // $request->validate([
    //     'name' => 'required|string|max:255',
    //     'phone' => 'nullable|string|max:20',
    //     'tahun' => 'nullable|integer', // Tambahkan ini
    //     'role_id' => 'nullable|exists:roles,id',
    //     'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    // ]);

    //     $user = $request->user();

    //     $user->name = $request->name;
    //     $user->phone = $request->phone;

    //     if ($request->hasFile('photo')) {
    //         if ($user->photo) {
    //             Storage::disk('public')->delete($user->photo);
    //         }
    //         $user->photo = $request->file('photo')->store('photo', 'public');
    //     }

    //     $user->save();

    //     if ($request->role_id) {
    //         $role = Role::findById($request->role_id);
    //         $user->syncRoles($role);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'data' => $user
    //     ]);
    // }

    /**
 * Update profile of currently authenticated user.
 */
public function updateMe(Request $request)
{
    // Enhanced logging
    Log::info('UpdateMe called');
    Log::info('Request method: ' . $request->method());
    Log::info('Content-Type: ' . $request->header('Content-Type'));
    Log::info('All request data: ', $request->all());
    Log::info('Request input name: ', [$request->input('name')]);
    Log::info('Request has name: ', [$request->has('name')]);
    Log::info('Files: ', $request->allFiles());

    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'role_id' => 'nullable|exists:roles,id',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        Log::info('Validation passed');
        Log::info('Validated data: ', $validated);

        $user = $request->user();
        Log::info('Current user ID: ' . $user->id);

        // Update basic fields
        $user->name = $request->input('name');
        $user->phone = $request->input('phone');

        // Handle photo upload
        if ($request->hasFile('photo')) {
            Log::info('Photo file detected');
            // Delete old photo if exists
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('photo', 'public');
            Log::info('New photo saved: ' . $user->photo);
        }

        $user->save();
        Log::info('User saved successfully');

        // Handle role update
        if ($request->filled('role_id')) {
            Log::info('Updating role to: ' . $request->input('role_id'));
            $role = Role::findById($request->input('role_id'));
            $user->syncRoles($role);
            Log::info('Role updated successfully');
        }

        // Reload user with relationships
        $user->load('roles');

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed: ', $e->errors());
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        Log::error('Update error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Update a user by admin.
     */
    // public function update(UpdateUserRequest $request, User $user)
    // {
    //     $validatedData = $request->validated();

    //     if ($request->hasFile('photo')) {
    //         if ($user->photo) {
    //             Storage::disk('public')->delete($user->photo);
    //         }
    //         $validatedData['photo'] = $request->file('photo')->store('photo', 'public');
    //     } else {
    //         if (isset($validatedData['photo']) && $validatedData['photo'] === null && $user->photo) {
    //             Storage::disk('public')->delete($user->photo);
    //         }
    //     }

    //     $user->update($validatedData);

    //     $role = Role::findById($validatedData['role_id']);
    //     $user->syncRoles($role);

    //     return response()->json([
    //         'success' => true,
    //         'user' => $user
    //     ]);
    // }

    /**
     * Remove a user.
     */
public function destroy($uuid)
{
    $user = User::where('uuid', $uuid)->first();

    if (!$user) {
        return response()->json(['message' => 'Resource Not Found'], 404);
    }

    $user->delete();
    return response()->json(['message' => 'User berhasil dihapus']);
}
}
