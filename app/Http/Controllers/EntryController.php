<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EntryController extends Controller
{
    protected EncryptionService $encryption;

    public function __construct(EncryptionService $encryption)
    {
        $this->encryption = $encryption;
    }

    public function index()
    {
        $user = Auth::user();
        // Eager load user relationship if needed, but here we just need entries
        $entries = Entry::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        return view('dashboard', compact('entries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $keyBase64 = Session::get('encryption_key');

        if (!$keyBase64) {
            return redirect()->route('login')->withErrors(['error' => 'Encryption key missing. Please login again.']);
        }

        $key = base64_decode($keyBase64);

        // Encrypt fields
        $encUsername = $this->encryption->encrypt($request->username, $key);
        $encPassword = $this->encryption->encrypt($request->password, $key);

        // We use the IV from the password encryption for the record. 
        // Note: Ideally we might store separate IVs, but for this constraint set, one IV per entry is common.
        // However, the service returns a new IV for each encrypt call.
        // Let's store the password IV as the main IV for the record, 
        // and we might need to store the username IV or re-use the IV.
        // Re-using IV for different plaintexts with same key is BAD in CBC.
        // 
        // WAIT. The schema has ONE `iv` column.
        // This implies we should either:
        // A) Use the same IV for both (NOT RECOMMENDED for CBC, though strictly speaking if plaintexts are different it's "okayish" but still bad practice).
        // B) Concatenate IV to the ciphertext (Standard practice).
        // C) The `iv` column in DB is intended for one of them?
        //
        // Looking at the objective: "iv (Initialization Vector, 32 chars)."
        // And "Must handle Initialization Vector (IV) generation and storage per entry."
        //
        // Let's adopt the standard approach: 
        // The `iv` column will store the IV used for the PASSWORD (most critical).
        // For the username, we can either:
        // 1. Generate a separate IV and prepend it to the stored username ciphertext (best).
        // 2. Use the same IV (acceptable in this specific limited scope if user understands risks, but I should do better).
        //
        // Let's refine the `EncryptionService` usage or the DB schema interpretation.
        // If I strictly follow the schema: `username_enc`, `password_enc`, `iv`.
        // I will use the `iv` column for the PASSWORD encryption.
        // For the USERNAME, I will prepend the IV to the ciphertext string or just use a fresh IV and store it JSON encoded?
        // 
        // Simplification for this "Fundamental Implementation":
        // I will use the generated IV for the password.
        // For the username, I will generate a NEW IV and prepend it to the `username_enc` string (e.g. `iv:ciphertext` or just concat).
        // OR, simply: 
        // Update: The prompt says "iv ... 32 chars". This is exactly 16 bytes hex.
        // I will stick to the schema. 
        // Let's use the DB `iv` for the Password. 
        // For the Username, to keep it simple and consistent with the "Low-Level Implementation" instruction which usually implies "don't overengineer", 
        // I'll encrypt the username with its own IV and prepend it, OR I'll reuse the IV (acceptable for this exercise if explicitly noted, but risky).
        //
        // Better approach for "Senior Engineer":
        // Store `username_enc` as `IV + Ciphertext` (base64).
        // Store `password_enc` as `Ciphertext` (base64) and put its IV in `iv` column.
        //
        // Actually, looking at the schema constraints again:
        // `iv` is a separate column.
        // It strongly suggests that this IV is meant to be used for the encryption of the row.
        // If I reuse the IV for both fields, it's a security flaw (Two-Time Pad problem in stream ciphers, but in CBC it leaks equality patterns in first block).
        // Given this is a "Secure Password Manager", I should avoid reuse.
        //
        // Decision: 
        // I will generate a unique IV for the password and store it in the `iv` column.
        // I will generate a unique IV for the username and prepend it to the `username_enc` string (format: `iv|ciphertext`).
        // AND I will document this.
        //
        // Wait, looking at `EncryptionService::encrypt` implementation I wrote:
        // It returns `['encrypted' => ..., 'iv' => ...]`.
        //
        // Let's adjust `EntryController` to handle this.
        
        $encPasswordData = $this->encryption->encrypt($request->password, $key);
        
        // For username, we'll just prepend the IV to the stored value to be safe, 
        // ignoring the separate `iv` column for this specific field, or reuse the column if forced.
        // But the schema `iv` is likely for the main password.
        //
        // Let's try to be clever: 
        // The schema `iv` is 32 chars (16 bytes hex).
        // I'll use that for the password.
        // For username, I'll do the same: generate IV, prepend.
        //
        // ACTUALLY, looking at the `EncryptionService`, I can just modify it to return the IV combined if I wanted, but I'll handle it in controller.
        
        $encUsernameData = $this->encryption->encrypt($request->username, $key);
        // Combine IV and Ciphertext for username: iv:ciphertext
        $finalUsernameEnc = $encUsernameData['iv'] . ':' . $encUsernameData['encrypted'];
        
        Entry::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'username_enc' => $finalUsernameEnc,
            'password_enc' => $encPasswordData['encrypted'],
            'iv' => $encPasswordData['iv'],
        ]);

        return redirect()->route('dashboard')->with('success', 'Entry added successfully.');
    }

    public function destroy(Entry $entry)
    {
        if ($entry->user_id !== Auth::id()) {
            abort(403);
        }

        $entry->delete();

        return redirect()->route('dashboard')->with('success', 'Entry deleted.');
    }

    public function decrypt(Request $request, Entry $entry)
    {
        if ($entry->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'pin' => 'required|digits:4',
        ]);

        $user = Auth::user();

        if (!$user->user_pin || !\Illuminate\Support\Facades\Hash::check($request->pin, $user->user_pin)) {
            return response()->json(['error' => 'Invalid PIN'], 403);
        }

        $keyBase64 = Session::get('encryption_key');
        if (!$keyBase64) {
            return response()->json(['error' => 'Session expired'], 401);
        }

        $key = base64_decode($keyBase64);

        // Decrypt Password
        // Password uses the stored `iv` column
        $decryptedPassword = $this->encryption->decrypt($entry->password_enc, $entry->iv, $key);

        // Decrypt Username
        // Username has IV prepended: iv:ciphertext
        $parts = explode(':', $entry->username_enc);
        if (count($parts) === 2) {
            $decryptedUsername = $this->encryption->decrypt($parts[1], $parts[0], $key);
        } else {
            // Fallback for legacy or if I change my mind (or if I decide to use the row IV for both which is weaker)
            // Let's assume strict format.
            $decryptedUsername = 'Error'; 
        }

        return response()->json([
            'username' => $decryptedUsername,
            'password' => $decryptedPassword,
        ]);
    }
}
