<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class QwenMindFocusService
{
    private int $timeout = 120;

    private string $binaryPath;

    public function __construct()
    {
        $this->binaryPath = env('QWEN_BIN_PATH', '/opt/homebrew/bin/qwen');
    }

    /**
     * Structure a brain dump text into prioritized tasks/categories.
     *
     * Returns an array with structured data ready for display.
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
                \Log::error('Qwen CLI error (MindFocus)', [
                    'exit_code' => $process->getExitCode(),
                    'error_output' => $process->getErrorOutput(),
                ]);

                return null;
            }

            return trim($process->getOutput());
        } catch (\Exception $e) {
            \Log::error('Qwen CLI exception (MindFocus)', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function getSystemPrompt(): string
    {
        return <<<'PROMPT'
Eres MIND-FOCUS, un asistente de IA que transforma caos mental en acciones claras organizadas por contexto.

El usuario te dará un "brain dump" - un texto sin estructura con ideas, tareas, preocupaciones, recordatorios, etc.

Tu tarea es ANALIZAR el texto, IDENTIFICAR los contextos/grupos naturales (proyectos, áreas de vida, temas) y organizar CADA elemento dentro del grupo y subgrupo correcto.

Devuelve un JSON estrictamente con el siguiente formato:

```json
{
  "groups": [
    {
      "id": 1,
      "name": "Nombre del grupo o contexto",
      "icon": "briefcase",
      "color": "cyan",
      "subgroups": [
        {
          "id": 1,
          "name": "Tareas",
          "type": "tasks",
          "items": [
            {
              "id": 1,
              "title": "Texto claro y accionable",
              "description": "Contexto adicional o por qué importa",
              "priority": "urgente|importante|normal|baja",
              "isPrimary": true,
              "estimatedTime": "15 min"
            }
          ]
        },
        {
          "id": 2,
          "name": "Notas e ideas",
          "type": "notes",
          "items": [
            {
              "id": 5,
              "title": "Idea de automatización de reportes",
              "description": "Podría ahorrar tiempo semanal",
              "tags": ["idea", "backlog"]
            }
          ]
        }
      ]
    }
  ]
}
```

REGLAS DE CLASIFICACIÓN:

**GRUPOS (contextos):**
- Identifica los contextos naturales del texto: proyectos de trabajo, vida personal, temas de dinero, salud, ideas creativas, etc.
- Cada grupo representa un área coherente de la vida del usuario.
- icon: usa "briefcase" para trabajo/proyectos, "user" para personal, "heart" para salud/bienestar, "lightbulb" para ideas/creatividad, "dollar-sign" para finanzas, "book" para aprendizaje, "home" para hogar, "users" para equipo/familia.
- color: "cyan" para trabajo urgente, "purple" para ideas/creatividad, "green" para personal, "orange" para finanzas, "pink" para salud, "blue" para aprendizaje.

**SUBGRUPOS:**
- Cada grupo tiene subgrupos de "Tareas" (acciones concretas) y "Notas e ideas" (información, ideas, referencias).
- type: "tasks" para acciones que requieren hacer algo, "notes" para información, ideas, preocupaciones, aspiraciones.
- SOLO crea subgrupos que tengan items. No crees subgrupos vacíos.
- Si un grupo solo tiene tareas, solo crea el subgrupo "Tareas". Si solo tiene notas, solo "Notas e ideas".

**ITEMS EN TAREAS:**
- title: frase accionable, empieza con verbo en imperativo o presente. Clara y directa.
- description: contexto breve o por qué importa.
- priority: "urgente" si tiene deadline o presión temporal, "importante" si impacta significativamente, "normal" para estándar, "baja" para lo que puede esperar.
- isPrimary: SOLO UN item en TODA la respuesta debe tener true. Es el que combina más urgencia + importancia. Es por donde el usuario debe empezar.
- estimatedTime: estimación breve ("15 min", "5 min", "1 hora", "pendiente").

**ITEMS EN NOTAS:**
- title: idea, preocupación, referencia o aspiración expresada claramente.
- description: contexto o detalle relevante.
- tags: array de 1-3 tags como ["idea", "backlog"], ["preocupación"], ["referencia"], ["aspiración"], ["personal"], ["urgente"].

REGLAS GENERALES:
- Extrae CADA idea/tarea/preocupación como un item separado.
- No combines items distintos a menos que sean literalmente lo mismo.
- Si hay algo que lleva días bloqueado, sube su prioridad.
- Identifica deadlines o urgencias implícitas en el texto.
- El JSON debe ser VÁLIDO. Sin comentarios, sin markdown extra.
- Devuelve SOLO el JSON válido, sin texto adicional.

EJEMPLO de respuesta:
{"groups":[{"id":1,"name":"Proyecto Dashboard Cliente","icon":"briefcase","color":"cyan","subgroups":[{"id":1,"name":"Tareas","type":"tasks","items":[{"id":1,"title":"Termina el gráfico de conversiones y envía el dashboard","description":"Llevas 2 días parado. Deadline jueves con Carlos. Es lo que más presión genera.","priority":"urgente","isPrimary":true,"estimatedTime":"2 horas"},{"id":2,"title":"Pide a Ana los datos del informe mensual","description":"Presentas el informe el lunes. Necesitas los datos para empezar.","priority":"importante","isPrimary":false,"estimatedTime":"5 min"}]},{"id":2,"name":"Notas e ideas","type":"notes","items":[{"id":3,"title":"Preocupación por no llegar al objetivo mensual del equipo","description":"Tendrías que hablar con el equipo pero no sabes cómo plantearlo sin sonar pesimista.","tags":["preocupación","equipo"]}]}]},{"id":2,"name":"Proyecto Personal","icon":"lightbulb","color":"purple","subgroups":[{"id":3,"name":"Tareas","type":"tasks","items":[{"id":4,"title":"Compra el dominio para la herramienta de email marketing","description":"Dominio pendiente para el proyecto personal.","priority":"normal","isPrimary":false,"estimatedTime":"10 min"},{"id":5,"title":"Revisa la factura del diseñador web","description":"Creo que está mal. Revisar antes de pagar.","priority":"importante","isPrimary":false,"estimatedTime":"5 min"}]},{"id":4,"name":"Notas e ideas","type":"notes","items":[{"id":6,"title":"Extensión Chrome para extraer leads de LinkedIn automáticamente","description":"Idea de automatización que puede generar valor.","tags":["idea","backlog","automatización"]},{"id":7,"title":"Canal de YouTube sobre productividad","description":"Te gustaría empezar pero no sabes por dónde y da vergüenza grabarse.","tags":["aspiración","contenido"]}]}]},{"id":3,"name":"Personal","icon":"user","color":"green","subgroups":[{"id":5,"name":"Tareas","type":"tasks","items":[{"id":8,"title":"Llama al banco por la cuenta de empresa","description":"Trámite pendiente que necesitas resolver.","priority":"normal","isPrimary":false,"estimatedTime":"20 min"}]},{"id":6,"name":"Notas e ideas","type":"notes","items":[{"id":9,"title":"Pedir cita médica","description":"Pendiente hace semanas.","tags":["salud","pendiente"]}]}]}]}
PROMPT;
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
                $iconMap = [
                    'briefcase' => 'briefcase',
                    'user' => 'user',
                    'heart' => 'heart',
                    'lightbulb' => 'lightbulb',
                    'dollar-sign' => 'dollar-sign',
                    'book' => 'book',
                    'home' => 'home',
                    'users' => 'users',
                ];
                $icon = $group['icon'] ?? 'briefcase';
                if (! isset($iconMap[$icon])) {
                    $icon = 'briefcase';
                }

                $groups[] = [
                    'id' => $group['id'] ?? $groupIndex + 1,
                    'name' => $group['name'] ?? 'Sin nombre',
                    'icon' => $icon,
                    'color' => $group['color'] ?? 'cyan',
                    'subgroups' => $subgroups,
                ];
            }
        }

        // If no primary was set but there are tasks, set the first urgent one as primary
        if (! $hasPrimary) {
            foreach ($groups as $groupIndex => $group) {
                foreach ($group['subgroups'] as $subgroupIndex => $subgroup) {
                    if ($subgroup['type'] !== 'tasks') {
                        continue;
                    }
                    foreach ($subgroup['items'] as $itemIndex => $item) {
                        if (($item['priority'] ?? '') === 'urgente') {
                            $groups[$groupIndex]['subgroups'][$subgroupIndex]['items'][$itemIndex]['isPrimary'] = true;

                            return ['groups' => $groups];
                        }
                    }
                }
            }
        }

        // If still no primary, make the first task primary
        if (! $hasPrimary && count($groups) > 0) {
            foreach ($groups as $groupIndex => $group) {
                foreach ($group['subgroups'] as $subgroupIndex => $subgroup) {
                    if ($subgroup['type'] === 'tasks' && count($subgroup['items']) > 0) {
                        $groups[$groupIndex]['subgroups'][$subgroupIndex]['items'][0]['isPrimary'] = true;

                        return ['groups' => $groups];
                    }
                }
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
