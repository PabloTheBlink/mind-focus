<?php

namespace App\Http\Controllers;

use App\Services\QwenMindFocusService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MindFocusController extends Controller
{
    public function structure(Request $request, QwenMindFocusService $mindFocusService)
    {
        $text = $request->input('text', '');

        if (empty(trim($text))) {
            return back()->with('error', 'El texto no puede estar vacío.');
        }

        $result = $mindFocusService->structure($text);
        $structuredMarkdown = $result['markdown'] ?? $text;

        return Inertia::render('AppScreen', [
            'text' => $structuredMarkdown,
            'structuredData' => $result['groups'] ?? [],
        ]);
    }
}
