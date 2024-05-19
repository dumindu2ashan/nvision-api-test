<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(StoreUserRequest $request)
    {
        DB::beginTransaction();
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ];
        try {
            $user = $this->userRepository->register($data);
            $resp['token'] = $user->createToken('nvisionapp')->plainTextToken;
            $resp['name'] = $user->name;
            DB::commit();
            return ApiResponseClass::sendResponse(new UserResource($resp), 'User Create Successful', 201);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e);
        }
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $resp['token'] = $user->createToken('nvisionapp')->plainTextToken;
            $resp['name'] = $user->name;

            return ApiResponseClass::sendResponse(new UserResource($resp), 'User login Successful', 201);
        } else {
            return ApiResponseClass::rollback('Unauthorised', 'Invalid credentials');
        }
    }
}
