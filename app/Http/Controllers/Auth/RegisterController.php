<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ProductKeyIssued;
use App\Models\ProductKey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{

    /**
     * Where to redirect users after registration.
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     */
    public function showRegistrationForm(Request $request)
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = $this->create($request->all());

        // Issue product key & notify user
        $productKey = $this->issueProductKey($user, $request->all());
        Mail::to($user->email)->send(
            new ProductKeyIssued(
                $productKey,
                route('register'),
                route('login')
            )
        );

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $user->update(['logo' => $logoPath]);
        }

        Auth::login($user);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'user' => $user
            ], 201);
        }

        return redirect($this->redirectTo);
    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function validator(array $data)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:25'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'role' => 'user', // Default role
        ]);
    }

    /**
     * Create & activate a product key for the newly registered user.
     */
    protected function issueProductKey(User $user, array $data): ProductKey
    {
        return ProductKey::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'phone' => $data['phone'] ?? '',
            'product_key' => ProductKey::generateKey(),
            'registration_token' => ProductKey::generateRegistrationToken(),
            'is_active' => true,
            'payment_status' => 'completed',
        ]);
    }
}