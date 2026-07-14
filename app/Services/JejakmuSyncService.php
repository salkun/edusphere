<?php

namespace App\Services;

use App\Models\User;
use App\Support\RoleMapper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class JejakmuSyncService
{
    /**
     * @return array{created: int, updated: int, total: int, pages: int}
     */
    public function sync(): array
    {
        $baseUrl = rtrim(config('services.jejakmu.url'), '/');
        $defaultPassword = config('services.jejakmu.default_password', 'password123');

        $created = 0;
        $updated = 0;
        $page = 1;
        $lastPage = 1;

        do {
            $response = Http::timeout(30)
                ->acceptJson()
                ->get("{$baseUrl}/api/users", ['page' => $page])
                ->throw();

            $payload = $response->json('data');
            $lastPage = (int) ($payload['last_page'] ?? 1);

            foreach ($payload['data'] ?? [] as $remoteUser) {
                $externalId = (int) $remoteUser['id'];
                $name = trim((string) $remoteUser['username']);
                $role = RoleMapper::toDatabase((string) ($remoteUser['role'] ?? 'siswa'));

                $existing = User::query()->where('external_id', $externalId)->first();

                if ($existing) {
                    $existing->update([
                        'name' => $name,
                        'role' => $role,
                    ]);
                    $updated++;
                    continue;
                }

                User::create([
                    'external_id' => $externalId,
                    'name' => $name,
                    'email' => $this->generateEmail($externalId, $name),
                    'password' => Hash::make($defaultPassword),
                    'role' => $role,
                ]);
                $created++;
            }

            $page++;
        } while ($page <= $lastPage);

        return [
            'created' => $created,
            'updated' => $updated,
            'total' => $created + $updated,
            'pages' => $lastPage,
        ];
    }

    private function generateEmail(int $externalId, string $name): string
    {
        $slug = Str::slug($name);

        if ($slug === '') {
            $slug = 'user';
        }

        $email = "{$slug}.{$externalId}@jejakmu.imported";

        if (User::query()->where('email', $email)->exists()) {
            $email = "jejakmu.{$externalId}@imported.local";
        }

        return $email;
    }
}
