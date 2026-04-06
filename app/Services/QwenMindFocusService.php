<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class QwenMindFocusService
{
    private const ALLOWED_ICONS = [
        'briefcase',
        'user',
        'heart',
        'lightbulb',
        'dollar-sign',
        'book',
        'home',
        'users',
    ];

    private int $timeout;

    private string $binaryPath;

    public function __construct()
    {
        $this->binaryPath = config('services.qwen.binary_path');
        $this->timeout = config('services.qwen.timeout', 120);
    }

    /**
     * Structure a brain dump text into prioritized tasks/categories.
     *
     * Returns an array with structured data ready for display.
     *
     * @return array{
     *     groups: array<int, array{
     *         id: int,
     *         name: string,
     *         icon: string,
     *         color: string,
     *         subgroups: array<int, array{
     *             id: int,
     *             name: string,
     *             type: string,
     *             items: array<int, array{
     *                 id: int,
     *                 title: string,
     *                 description: string,
     *                 priority?: string,
     *                 isPrimary: bool,
     *                 estimatedTime?: string,
     *                 tags?: array<int, string>
     *             }>
     *         }>
     *     }>,
     *     markdown: string
     * }
     */
    public function structure(string $text): array
    {
        $systemPrompt = $this->getSystemPrompt();
        $userMessage = "Texto del usuario:\n\n{$text}";

        $response = $this->callQwen($systemPrompt, $userMessage);

        if ($response === null) {
            return $this->fallbackStructure($text);
        }

        return $this->parseResponse($response);
    }

    private function callQwen(string $systemPrompt, string $userMessage): ?string
    {
        // Escape arguments properly
        $escapedSystem = escapeshellarg($systemPrompt);
        $escapedUser = escapeshellarg($userMessage);

        $command = "{$this->binaryPath} -y --system-prompt {$escapedSystem} -p {$escapedUser}";

        $process = Process::fromShellCommandline($command, null, [
            'PATH' => '/opt/homebrew/bin:/usr/local/bin:/usr/bin:/bin',
        ]);
        $process->setTimeout($this->timeout);

        try {
            $process->run();

            if (! $process->isSuccessful()) {
                logger()->error('Qwen CLI error (MindFocus)', [
                    'exit_code' => $process->getExitCode(),
                    'error_output' => $process->getErrorOutput(),
                ]);

                return null;
            }

            return trim($process->getOutput());
        } catch (\Exception $e) {
            logger()->error('Qwen CLI exception (MindFocus)', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function getSystemPrompt(): string
    {
        $promptPath = resource_path('prompts/mindfocus_system_prompt.md');

        if (! file_exists($promptPath)) {
            throw new \RuntimeException("System prompt file not found: {$promptPath}");
        }

        $prompt = file_get_contents($promptPath);

        if ($prompt === false) {
            throw new \RuntimeException("Failed to read system prompt file: {$promptPath}");
        }

        return $prompt;
    }

    private function parseResponse(string $response): array
    {
        // Try to extract JSON from the response
        // Remove markdown code blocks if present
        $response = preg_replace('/^```json\s*/m', '', $response);
        $response = preg_replace('/^```\s*/m', '', $response);
        $response = trim($response);

        // Try to find JSON object
        $startPos = strpos($response, '{');
        $endPos = strrpos($response, '}');

        if ($startPos === false || $endPos === false) {
            return $this->fallbackStructure($response);
        }

        $jsonStr = substr($response, $startPos, $endPos - $startPos + 1);
        $decoded = json_decode($jsonStr, true);

        if (! is_array($decoded) || ! isset($decoded['groups'])) {
            return $this->fallbackStructure($response);
        }

        // Ensure all groups, subgroups, and items have required fields and defaults
        $groups = [];
        $hasPrimary = false;

        foreach ($decoded['groups'] as $groupIndex => $group) {
            $subgroups = [];
            foreach ($group['subgroups'] ?? [] as $subgroupIndex => $subgroup) {
                $items = [];
                foreach ($subgroup['items'] ?? [] as $itemIndex => $item) {
                    $isTask = ($subgroup['type'] ?? 'tasks') === 'tasks';
                    $itemData = [
                        'id' => $item['id'] ?? ($groupIndex + 1) * 100 + ($subgroupIndex + 1) * 10 + $itemIndex + 1,
                        'title' => $item['title'] ?? 'Elemento sin título',
                    ];

                    if ($isTask) {
                        $itemData['description'] = $item['description'] ?? '';
                        $itemData['priority'] = $item['priority'] ?? 'normal';
                        $itemData['isPrimary'] = $item['isPrimary'] ?? false;
                        $itemData['estimatedTime'] = $item['estimatedTime'] ?? '';

                        if ($itemData['isPrimary']) {
                            $hasPrimary = true;
                        }
                    } else {
                        $itemData['description'] = $item['description'] ?? '';
                        $itemData['tags'] = is_array($item['tags'] ?? null) ? $item['tags'] : [];
                    }

                    $items[] = $itemData;
                }

                if (count($items) > 0) {
                    $subgroups[] = [
                        'id' => $subgroup['id'] ?? $subgroupIndex + 1,
                        'name' => $subgroup['name'] ?? ($isTask ? 'Tareas' : 'Notas e ideas'),
                        'type' => $subgroup['type'] ?? 'tasks',
                        'items' => $items,
                    ];
                }
            }

            if (count($subgroups) > 0) {
                $icon = in_array($group['icon'] ?? null, self::ALLOWED_ICONS, true)
                    ? $group['icon']
                    : 'briefcase';

                $groups[] = [
                    'id' => $group['id'] ?? $groupIndex + 1,
                    'name' => $group['name'] ?? 'Sin nombre',
                    'icon' => $icon,
                    'color' => $group['color'] ?? 'cyan',
                    'subgroups' => $subgroups,
                ];
            }
        }

        // Set primary item for each subgroup's items
        foreach ($groups as &$group) {
            foreach ($group['subgroups'] as &$subgroup) {
                $this->ensurePrimaryItem($subgroup['items']);
            }
        }

        $markdown = $this->generateMarkdown($groups);

        return [
            'groups' => $groups,
            'markdown' => $markdown,
        ];
    }

    /**
     * Generate a clean markdown from the structured data.
     */
    private function generateMarkdown(array $groups): string
    {
        $markdown = '';

        foreach ($groups as $group) {
            $markdown .= '# '.$group['name']."\n\n";

            foreach ($group['subgroups'] as $subgroup) {
                if ($subgroup['type'] === 'tasks') {
                    $markdown .= '## '.$subgroup['name']."\n";
                    foreach ($subgroup['items'] as $item) {
                        $line = '- [ ] '.$item['title'];
                        if (! empty($item['priority']) && $item['priority'] !== 'normal') {
                            $line .= ' ('.strtoupper($item['priority']).')';
                        }
                        if (! empty($item['estimatedTime'])) {
                            $line .= ' ['.$item['estimatedTime'].']';
                        }
                        $markdown .= $line."\n";
                        if (! empty($item['description'])) {
                            $markdown .= '  > '.$item['description']."\n";
                        }
                    }
                    $markdown .= "\n";
                } else {
                    $markdown .= '## '.$subgroup['name']."\n";
                    foreach ($subgroup['items'] as $item) {
                        $line = '- '.$item['title'];
                        if (! empty($item['tags'])) {
                            foreach ($item['tags'] as $tag) {
                                $line .= ' #'.$tag;
                            }
                        }
                        $markdown .= $line."\n";
                        if (! empty($item['description'])) {
                            $markdown .= '  > '.$item['description']."\n";
                        }
                    }
                    $markdown .= "\n";
                }
            }
        }

        return trim($markdown);
    }

    /**
     * Ensure at least one item in the collection is marked as primary.
     */
    private function ensurePrimaryItem(array &$items): void
    {
        if (empty($items)) {
            return;
        }

        $hasPrimary = collect($items)->contains('isPrimary', true);

        if (! $hasPrimary) {
            $items[0]['isPrimary'] = true;
        }
    }

    /**
     * Fallback: create a simple structure from text without AI.
     */
    private function fallbackStructure(string $text): array
    {
        $lines = array_filter(array_map('trim', explode("\n", $text)));
        $lines = array_values($lines);

        $taskItems = [];
        $noteItems = [];

        foreach ($lines as $index => $line) {
            if (empty($line)) {
                continue;
            }

            $taskItems[] = [
                'id' => $index + 1,
                'title' => ucfirst($line),
                'description' => 'Elemento extraído del brain dump.',
                'priority' => $index === 0 ? 'urgente' : 'normal',
                'isPrimary' => $index === 0,
                'estimatedTime' => '',
            ];
        }

        if (empty($taskItems)) {
            $taskItems[] = [
                'id' => 1,
                'title' => 'Revisar el texto introducido',
                'description' => 'No se pudieron extraer elementos claros del texto.',
                'priority' => 'normal',
                'isPrimary' => true,
                'estimatedTime' => '',
            ];
        }

        $groups = [
            [
                'id' => 1,
                'name' => 'General',
                'icon' => 'briefcase',
                'color' => 'cyan',
                'subgroups' => [
                    [
                        'id' => 1,
                        'name' => 'Tareas',
                        'type' => 'tasks',
                        'items' => $taskItems,
                    ],
                ],
            ],
        ];

        return [
            'groups' => $groups,
            'markdown' => $this->generateMarkdown($groups),
        ];
    }
}
