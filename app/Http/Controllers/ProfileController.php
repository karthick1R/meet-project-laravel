<?php

namespace App\Http\Controllers;

use App\Mail\ProfileUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:25',
            'password' => 'nullable|string|min:8|confirmed',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $originalEmail = $user->email;

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('logo')) {
            if ($user->logo) {
                Storage::disk('public')->delete($user->logo);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
            $updateData['logo'] = $logoPath;
        }

        $user->update($updateData);

        Mail::to($user->email)->send(new ProfileUpdated($user, $originalEmail));

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }
}


