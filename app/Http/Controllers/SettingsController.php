<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class SettingsController extends Controller
{
    protected EncryptionService $encryption;

    public function __construct(EncryptionService $encryption)
    {
        $this->encryption = $encryption;
    }

    public function index()
    {
        return view('settings');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->username = $validated['username'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $keyBase64 = Session::get('encryption_key');

            if (!$keyBase64) {
                return redirect()->route('settings')->withErrors(['password' => 'Encryption key missing. Please log out and log in again before changing password.']);
            }

            $oldKey = base64_decode($keyBase64);

            $entries = Entry::where('user_id', $user->id)->get();

            $decrypted = [];

            foreach ($entries as $entry) {
                $currentPassword = $this->encryption->decrypt($entry->password_enc, $entry->iv, $oldKey);

                $usernameParts = explode(':', $entry->username_enc);
                $currentUsername = null;

                if (count($usernameParts) === 2) {
                    $currentUsername = $this->encryption->decrypt($usernameParts[1], $usernameParts[0], $oldKey);
                }

                if ($currentPassword === false || $currentUsername === false) {
                    return redirect()->route('settings')->withErrors(['password' => 'Failed to decrypt existing entries. Password was not changed.']);
                }

                $decrypted[] = [
                    'model' => $entry,
                    'username' => $currentUsername,
                    'password' => $currentPassword,
                ];
            }

            $newKey = hash_pbkdf2('sha256', $validated['password'], $user->enc_salt, 1000, 32, true);

            foreach ($decrypted as $item) {
                $encPassword = $this->encryption->encrypt($item['password'], $newKey);
                $encUsername = $this->encryption->encrypt($item['username'], $newKey);

                $item['model']->update([
                    'username_enc' => $encUsername['iv'] . ':' . $encUsername['encrypted'],
                    'password_enc' => $encPassword['encrypted'],
                    'iv' => $encPassword['iv'],
                ]);
            }

            $user->password = Hash::make($validated['password']);

            Session::put('encryption_key', base64_encode($newKey));
        }

        $user->save();

        return redirect()->route('settings')->with('success', 'Profile updated successfully.');
    }
}
