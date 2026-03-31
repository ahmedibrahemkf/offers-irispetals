<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DataController extends Controller
{
    public function health(): JsonResponse
    {
        return response()->json(['ok' => true]);
    }

    public function getSettings(): JsonResponse
    {
        $row = SiteSetting::query()->find(1);

        return response()->json([
            'payload' => $row?->payload ?? new \stdClass(),
        ]);
    }

    public function saveSettings(Request $request): JsonResponse
    {
        $payload = $request->input('payload', []);
        if (! is_array($payload)) {
            $payload = [];
        }

        SiteSetting::query()->updateOrCreate(
            ['id' => 1],
            ['payload' => $payload, 'updated_at' => now()]
        );

        return response()->json(['ok' => true]);
    }

    public function listOrders(): JsonResponse
    {
        $rows = Order::query()
            ->orderByDesc('created_at')
            ->get(['id', 'created_at', 'payload']);

        return response()->json(['data' => $rows]);
    }

    public function upsertOrder(Request $request): JsonResponse
    {
        $id = trim((string) $request->input('id', ''));
        $payload = $request->input('payload', []);
        $createdAt = (string) $request->input('created_at', now()->toDateTimeString());

        if ($id === '' || ! is_array($payload)) {
            return response()->json(['ok' => false, 'message' => 'invalid payload'], 422);
        }

        Order::query()->updateOrCreate(
            ['id' => $id],
            ['payload' => $payload, 'created_at' => $createdAt]
        );

        return response()->json(['ok' => true]);
    }

    public function deleteOrder(string $id): JsonResponse
    {
        Order::query()->where('id', $id)->delete();

        return response()->json(['ok' => true]);
    }

    public function listExpenses(): JsonResponse
    {
        $rows = Expense::query()
            ->orderByDesc('created_at')
            ->get(['id', 'created_at', 'payload']);

        return response()->json(['data' => $rows]);
    }

    public function upsertExpense(Request $request): JsonResponse
    {
        $id = trim((string) $request->input('id', ''));
        $payload = $request->input('payload', []);
        $createdAt = (string) $request->input('created_at', now()->toDateTimeString());

        if ($id === '' || ! is_array($payload)) {
            return response()->json(['ok' => false, 'message' => 'invalid payload'], 422);
        }

        Expense::query()->updateOrCreate(
            ['id' => $id],
            ['payload' => $payload, 'created_at' => $createdAt]
        );

        return response()->json(['ok' => true]);
    }

    public function deleteExpense(string $id): JsonResponse
    {
        Expense::query()->where('id', $id)->delete();

        return response()->json(['ok' => true]);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        if (! $request->hasFile('file')) {
            return response()->json(['ok' => false, 'message' => 'missing file'], 422);
        }

        $folder = trim((string) $request->input('folder', 'uploads'));
        $folder = preg_replace('/[^a-zA-Z0-9_\/-]/', '-', $folder);
        $folder = trim((string) $folder, '/');
        $folder = $folder === '' ? 'uploads' : 'uploads/'.$folder;

        $targetDir = public_path($folder);
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $name = now()->format('YmdHis').'-'.Str::random(8).'.'.$ext;
        $file->move($targetDir, $name);

        $publicPath = $folder.'/'.$name;

        return response()->json([
            'ok' => true,
            'publicUrl' => url($publicPath),
            'path' => $publicPath,
        ]);
    }
}
