<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Helpers\FileHelper;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Create a new UserController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:sanctum', ['except' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return UserResource::collection(
            $this->pageableRequest(
                $request,
                User::class,
                'name'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response()->json('/api/auth/register', 308);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return UserResource::make($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            $validator = Validator::make($request->all(), [
                'name' => 'string|between:2,100|nullable',
                'image' => 'image|nullable'
            ]);

            if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }

            if(isset($request->name)) {
                $user->name = $request->name;
            }

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                if(Storage::exists($user->image_location . '/' . FileHelper::getModelImage($user))) {
                    Storage::delete($user->image_location . '/' . FileHelper::getModelImage($user));
                }
                $user->image_extension = $request->image->extension();
                $request->image->storeAs($user->image_location, FileHelper::getModelImage($user));
            }

            $user->save();

            return response()->json(UserResource::make($user), 201);
        } else {
            Gate::authorize(PermissionEnum::ChangeRoleUser, $user);

            $validator = Validator::make($request->all(), [
                'role' => 'required|integer|exists:role,id|max:' . $request->user()->role->id
            ]);

            if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }

            $user->role()->associate(Role::find($request->role));
            $user->save();

            return response()->json('User role successfully updated.', 201);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        Gate::authorize(PermissionEnum::RemoveUser, $user);

        $user->delete();
    }
}
