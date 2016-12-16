<?php

namespace App\Http\Controllers\Auth;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
//use Illuminate\Support\Facades\Redis;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';
    protected $auth;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * We are going to override the AuthenticatesUsers method
     * so that we can return our Javascript Web Token 
     * 
     */

    public function login(Request $request)
    {
        $this->validateLogin($request);
        
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->credentials($request);

        try {
            // attempt to verify the credentials and create a token for the user
            $token = $this->auth->attempt($credentials);
            if (!$token) {
                $this->incrementLoginAttempts($request);
                return response()->json(['error' => 'Invalid Credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'Could Not Create Token'], 500);
        }

        // no errors so we can loggin the user
        // and send back the token to vue

        //$user = User::where('email', $request->input('email'))->first();
        //auth()->login($user);

        $this->clearLoginAttempts($request);
        //Redis::publish('public-message', auth()->user()->full_name.' has logged in');
        return response()->json(compact('token'));

    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        auth()->logout();
        return response()->json([
            'success' => 'logout'
        ]);
    }


}
