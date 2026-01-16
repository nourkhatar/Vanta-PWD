<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Entry;
use App\Services\EncryptionService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $encryption = new EncryptionService();

        $users = [
            [
                'email' => 'admin@gmail.com',
                'username' => 'admin',
                'password' => 'AdminSecure2025!',
                'pin' => '7890',
                'force_update' => true,
                'entries' => [
                    [
                        'title' => 'Admin Panel',
                        'username' => 'admin',
                        'password' => 'SuperSecureAdmin!2025',
                    ],
                    [
                        'title' => 'DGI Tax Portal',
                        'username' => 'admin.dgi',
                        'password' => 'Admin-DGI-2025#',
                    ],
                    [
                        'title' => 'CNSS Portal',
                        'username' => 'admin.cnss',
                        'password' => 'Admin-CNSS-2025$',
                    ],
                    [
                        'title' => 'CIH Bank',
                        'username' => 'admin.cih',
                        'password' => 'Admin-CIH-2025!',
                    ],
                    [
                        'title' => 'IAM (Maroc Telecom)',
                        'username' => 'admin.iam',
                        'password' => 'Admin-IAM-2025&',
                    ],
                ],
            ],
            [
                'email' => 'youssef.elmansouri@agadir.ma',
                'username' => 'youssef_el',
                'password' => 'YoussefPwd2025!',
                'pin' => '4321',
                'entries' => [
                    [
                        'title' => 'CIH Bank',
                        'username' => 'y.elmansouri',
                        'password' => 'CiH-Bank-Agadir-2025!',
                    ],
                    [
                        'title' => 'Attijariwafa Bank',
                        'username' => 'y.elmansouri',
                        'password' => 'Attijari-Agadir-2025!',
                    ],
                    [
                        'title' => 'ONCF',
                        'username' => 'y.elmansouri',
                        'password' => 'ONCF-Agadir-2025!',
                    ],
                    [
                        'title' => 'DGI Tax Portal',
                        'username' => 'y.elmansouri',
                        'password' => 'DGI-Agadir-2025#',
                    ],
                    [
                        'title' => 'IAM (Maroc Telecom)',
                        'username' => 'y.elmansouri',
                        'password' => 'IAM-Agadir-2025&',
                    ],
                ],
            ],
            [
                'email' => 'fatima.zahra.benali@casablanca.ma',
                'username' => 'fatima_z_b',
                'password' => 'FatimaPwd2025!',
                'pin' => '2468',
                'entries' => [
                    [
                        'title' => 'CNSS Portal',
                        'username' => 'f.benali',
                        'password' => 'Cnss-Portal-2025$',
                    ],
                    [
                        'title' => 'CIH Bank',
                        'username' => 'f.benali',
                        'password' => 'CIH-Casa-2025!',
                    ],
                    [
                        'title' => 'ONCF',
                        'username' => 'f.benali',
                        'password' => 'ONCF-Casa-2025!',
                    ],
                    [
                        'title' => 'IAM (Maroc Telecom)',
                        'username' => 'f.benali',
                        'password' => 'IAM-Casa-2025&',
                    ],
                    [
                        'title' => 'DGI Tax Portal',
                        'username' => 'f.benali',
                        'password' => 'DGI-Casa-2025#',
                    ],
                ],
            ],
            [
                'email' => 'rachid.bouazza@rabat.ma',
                'username' => 'rachid_b',
                'password' => 'RachidPwd2025!',
                'pin' => '1357',
                'entries' => [
                    [
                        'title' => 'Tax Portal DGI',
                        'username' => 'r.bouazza',
                        'password' => 'Dgi-Tax-Portal-2025#',
                    ],
                    [
                        'title' => 'CNSS Portal',
                        'username' => 'r.bouazza',
                        'password' => 'CNSS-Rabat-2025$',
                    ],
                    [
                        'title' => 'CIH Bank',
                        'username' => 'r.bouazza',
                        'password' => 'CIH-Rabat-2025!',
                    ],
                    [
                        'title' => 'ONCF',
                        'username' => 'r.bouazza',
                        'password' => 'ONCF-Rabat-2025!',
                    ],
                    [
                        'title' => 'IAM (Maroc Telecom)',
                        'username' => 'r.bouazza',
                        'password' => 'IAM-Rabat-2025&',
                    ],
                ],
            ],
            [
                'email' => 'salma.elhaddad@marrakech.ma',
                'username' => 'salma_eh',
                'password' => 'SalmaPwd2025!',
                'pin' => '9090',
                'entries' => [
                    [
                        'title' => 'Inwi Account',
                        'username' => 'salma.mrk',
                        'password' => 'Inwi-Account-2025&',
                    ],
                    [
                        'title' => 'IAM (Maroc Telecom)',
                        'username' => 'salma.mrk',
                        'password' => 'IAM-Marrakech-2025&',
                    ],
                    [
                        'title' => 'CIH Bank',
                        'username' => 'salma.mrk',
                        'password' => 'CIH-Marrakech-2025!',
                    ],
                    [
                        'title' => 'CNSS Portal',
                        'username' => 'salma.mrk',
                        'password' => 'CNSS-Marrakech-2025$',
                    ],
                    [
                        'title' => 'ONCF',
                        'username' => 'salma.mrk',
                        'password' => 'ONCF-Marrakech-2025!',
                    ],
                ],
            ],
        ];

        foreach ($users as $definition) {
            $user = User::where('email', $definition['email'])->first();

            if (!$user) {
                $encSalt = bin2hex(random_bytes(32));

                $user = User::create([
                    'email' => $definition['email'],
                    'username' => $definition['username'],
                    'password' => Hash::make($definition['password']),
                    'enc_salt' => $encSalt,
                    'user_pin' => Hash::make($definition['pin']),
                ]);
            } elseif (!empty($definition['force_update'])) {
                $encSalt = bin2hex(random_bytes(32));
                $user->update([
                    'password' => Hash::make($definition['password']),
                    'user_pin' => Hash::make($definition['pin']),
                    'enc_salt' => $encSalt,
                ]);
            }

            $key = hash_pbkdf2('sha256', $definition['password'], $user->enc_salt, 1000, 32, true);

            foreach ($definition['entries'] as $entryDef) {
                $existing = Entry::where('user_id', $user->id)
                    ->where('title', $entryDef['title'])
                    ->first();

                if ($existing) {
                    continue;
                }

                $encPassword = $encryption->encrypt($entryDef['password'], $key);
                $encUsername = $encryption->encrypt($entryDef['username'], $key);

                $finalUsernameEnc = $encUsername['iv'] . ':' . $encUsername['encrypted'];

                Entry::create([
                    'user_id' => $user->id,
                    'title' => $entryDef['title'],
                    'username_enc' => $finalUsernameEnc,
                    'password_enc' => $encPassword['encrypted'],
                    'iv' => $encPassword['iv'],
                ]);
            }
        }
    }
}
