<?php

namespace App\Http\Controllers;

use App\Http\Requests\StructureTextRequest;
use App\Services\QwenMindFocusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MindFocusController extends Controller
{
    public function structure(StructureTextRequest $request, QwenMindFocusService $mindFocusService): Response|RedirectResponse
    {
        $text = $request->input('text', '');

        $structureResult = $mindFocusService->structure($text);
        $structuredMarkdown = $structureResult['markdown'] ?? $text;

        return Inertia::render('AppScreen', [
            'text' => $structuredMarkdown,
            'currentText' => $structuredMarkdown,
            'structuredData' => $structureResult['groups'] ?? [],
        ]);
    }

    public function structureApi(StructureTextRequest $request, QwenMindFocusService $mindFocusService): JsonResponse
    {
        $text = $request->input('text', '');

        $structureResult = $mindFocusService->structure($text);

        return response()->json([
            'markdown' => $structureResult['markdown'] ?? $text,
            'structuredData' => $structureResult['groups'] ?? [],
        ]);
    }
}
