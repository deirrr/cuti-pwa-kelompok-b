<?php

namespace App\Http\Controllers;

use App\Http\Requests\HapusLanggananPushRequest;
use App\Http\Requests\SimpanLanggananPushRequest;
use App\Models\Pengguna;
use Illuminate\Http\JsonResponse;

class LanggananPushController extends Controller
{
    public function store(SimpanLanggananPushRequest $request): JsonResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();
        $data = $request->validated();

        $pengguna->updatePushSubscription(
            $data['endpoint'],
            $data['keys']['p256dh'],
            $data['keys']['auth'],
            $data['content_encoding'],
        );

        return response()->json([
            'message' => 'Notifikasi perangkat berhasil diaktifkan.',
        ]);
    }

    public function destroy(HapusLanggananPushRequest $request): JsonResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();
        $data = $request->validated();

        $pengguna->deletePushSubscription($data['endpoint']);

        return response()->json([
            'message' => 'Notifikasi perangkat berhasil dinonaktifkan.',
        ]);
    }
}
