<?php

namespace App\Http\Controllers;

use App\Services\QwenMindFocusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MindFocusController extends Controller
{
    public function structure(Request $request, QwenMindFocusService $mindFocusService): Response|RedirectResponse
    {
        $text = $request->input('text', '');

        if (empty(trim($text))) {
            return back()->with('error', 'El texto no puede estar vacío.');
        }

        $result = $mindFocusService->structure($text);
        $structuredMarkdown = $result['markdown'] ?? $text;

        return Inertia::render('AppScreen', [
            'text' => $structuredMarkdown,
            'currentText' => $structuredMarkdown,
            'structuredData' => $result['groups'] ?? [],
        ]);
    }

    public function structureApi(Request $request, QwenMindFocusService $mindFocusService): JsonResponse
    {
        $text = $request->input('text', '');

        if (empty(trim($text))) {
            return response()->json([
                'error' => 'El texto no puede estar vacío.',
            ], 422);
        }

        $result = $mindFocusService->structure($text);

        return response()->json([
            'markdown' => $result['markdown'] ?? $text,
            'structuredData' => $result['groups'] ?? [],
        ]);
    }
}
