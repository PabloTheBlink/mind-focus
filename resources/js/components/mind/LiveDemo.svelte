<script>
// Demo structured data matching the QwenMindFocusService format
const demoGroups = [
	{
		id: 1,
		name: 'Proyecto Dashboard Cliente',
		icon: 'briefcase',
		color: 'cyan',
		subgroups: [
			{
				id: 1,
				name: 'Tareas',
				type: 'tasks',
				items: [
					{ id: 1, title: 'Termina el gráfico de conversiones y envía el dashboard', description: 'Llevas 2 días parado. Deadline jueves con Carlos. Es lo que más presión genera.', priority: 'urgente', isPrimary: true, estimatedTime: '2 horas' },
					{ id: 2, title: 'Pide a Ana los datos del informe mensual', description: 'Presentas el lunes. Necesitas los datos para empezar.', priority: 'importante', isPrimary: false, estimatedTime: '5 min' },
				],
			},
			{
				id: 2,
				name: 'Notas e ideas',
				type: 'notes',
				items: [
					{ id: 3, title: 'Preocupación por no llegar al objetivo mensual del equipo', description: 'Tendrías que hablar con el equipo pero no sabes cómo plantearlo sin sonar pesimista.', tags: ['preocupación', 'equipo'] },
				],
			},
		],
	},
	{
		id: 2,
		name: 'Proyecto Personal',
		icon: 'lightbulb',
		color: 'purple',
		subgroups: [
			{
				id: 3,
				name: 'Tareas',
				type: 'tasks',
				items: [
					{ id: 4, title: 'Compra el dominio para la herramienta de email marketing', description: 'Dominio pendiente para el proyecto personal.', priority: 'normal', isPrimary: false, estimatedTime: '10 min' },
					{ id: 5, title: 'Revisa la factura del diseñador web', description: 'Creo que está mal. Revisar antes de pagar.', priority: 'importante', isPrimary: false, estimatedTime: '5 min' },
				],
			},
			{
				id: 4,
				name: 'Notas e ideas',
				type: 'notes',
				items: [
					{ id: 6, title: 'Extensión Chrome para extraer leads de LinkedIn', description: 'Idea de automatización que puede generar valor.', tags: ['idea', 'backlog'] },
					{ id: 7, title: 'Canal de YouTube sobre productividad', description: 'Te gustaría empezar pero no sabes por dónde y da vergüenza grabarse.', tags: ['aspiración', 'contenido'] },
				],
			},
		],
	},
	{
		id: 3,
		name: 'Personal',
		icon: 'user',
		color: 'green',
		subgroups: [
			{
				id: 5,
				name: 'Tareas',
				type: 'tasks',
				items: [
					{ id: 8, title: 'Llama al banco por la cuenta de empresa', description: 'Trámite pendiente que necesitas resolver.', priority: 'normal', isPrimary: false, estimatedTime: '20 min' },
				],
			},
			{
				id: 6,
				name: 'Notas e ideas',
				type: 'notes',
				items: [
					{ id: 9, title: 'Pedir cita médica', description: 'Pendiente hace semanas.', tags: ['salud', 'pendiente'] },
				],
			},
		],
	},
];

const groupColorMap = {
	cyan: { bg: 'rgba(0,212,255,0.15)', text: '#00D4FF' },
	purple: { bg: 'rgba(167,139,250,0.15)', text: '#A78BFA' },
	green: { bg: 'rgba(34,197,94,0.15)', text: '#22C55E' },
};

function getGroupBgColor(color) {
	return groupColorMap[color]?.bg || 'rgba(0,212,255,0.15)';
}

function getGroupTextColor(color) {
	return groupColorMap[color]?.text || '#00D4FF';
}

function countGroupTasks(group) {
	let total = 0;
	for (const sg of group.subgroups ?? []) {
		total += (sg.items ?? []).length;
	}
	return total;
}

function findPrimaryTask(groups) {
	for (const group of groups) {
		for (const subgroup of group.subgroups ?? []) {
			for (const item of subgroup.items ?? []) {
				if (item.isPrimary) {
					return { ...item, groupName: group.name, groupColor: group.color };
				}
			}
		}
	}
	return null;
}

const primaryTask = findPrimaryTask(demoGroups);

// For the demo, all groups start expanded
const expandedGroups = new Set(demoGroups.map(g => g.id));
</script>

<section id="demo" class="relative bg-gradient-to-[160deg] from-[#0D0E15] via-[#111320] to-[#0E0F18] px-5 py-[100px]">
	<!-- Top line -->
	<div class="absolute left-0 right-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-[rgba(0,212,255,0.15)] to-transparent" />
	<!-- Background glow -->
	<div class="pointer-events-none absolute top-[10%] left-1/2 h-[700px] w-[700px] -translate-x-1/2 rounded-full bg-[radial-gradient(circle,rgba(0,212,255,0.04)_0%,transparent_55%)]" />

	<div class="relative z-2 mx-auto max-w-[1200px]">
		<div class="mb-[60px] text-center">
			<p class="mb-[20px] text-[13px] font-semibold tracking-[3px] text-[#00D4FF] uppercase">Demostración</p>
			<h2 class="mb-[20px] text-[44px] font-bold tracking-[-1px] leading-[1.2] text-white">
				Así es como se siente
				<br />
				<span class="text-[#00D4FF]">vaciar tu mente.</span>
			</h2>
			<p class="mx-auto max-w-[600px] text-[18px] font-light leading-[1.6] text-[#9CA3AF]">
				Esto es lo que escribió alguien real. Sin editar. Sin filtrar. Así salen las cosas cuando dejas de pensar y empiezas a escribir.
			</p>
		</div>

		<!-- Demo container -->
		<div class="overflow-hidden rounded-xl border border-white/[0.06] bg-white/[0.015]">
			<!-- Header -->
			<div class="flex items-center justify-between border-b border-white/[0.05] bg-[rgba(8,10,16,0.6)] px-[25px] py-[15px]">
				<div class="flex items-center gap-2">
					<span class="text-[18px] text-[#00D4FF]" style="font-family: 'Brush Script MT', 'Great Vibes', cursive;">MIND</span>
					<span class="text-[14px] font-extrabold tracking-[1px] text-white" style="font-family: -apple-system, BlinkMacSystemFont, sans-serif;">FOCUS</span>
				</div>
				<div class="flex items-center gap-[6px]">
					<div class="h-[5px] w-[5px] rounded-full bg-[#00D4FF]"></div>
					<span class="text-[11px] tracking-[1px] text-[#6B7280]">Sesión de ejemplo</span>
				</div>
			</div>

			<!-- Content grid -->
			<div class="grid grid-cols-2 min-h-[520px]">
				<!-- Left: Brain dump -->
				<div class="border-r border-white/[0.05] bg-black/15 px-[25px] py-[30px]">
					<div class="mb-[18px] flex items-center justify-between">
						<p class="text-[11px] font-semibold tracking-[2px] text-[#6B7280] uppercase">Brain dump</p>
						<p class="text-[10px] italic text-[#4B5563]">Sin editar</p>
					</div>
					<div class="min-h-[420px] rounded-lg border border-white/[0.06] bg-white/[0.02] p-5">
						<p class="text-[14px] leading-[1.9] text-[#B0B8C4]" style="font-family: -apple-system, BlinkMacSystemFont, sans-serif;">
							tengo que enviar el dashboard a carlos antes del jueves pero todavía no me gusta como queda el gráfico de conversiones y llevo dos días con eso parado<br /><br />
							también necesito pedirle a ana los datos del informe mensual que tengo que presentar el lunes<br /><br />
							ah y comprar el dominio para el proyecto personal ese de la herramienta de email marketing<br /><br />
							me preocupa que no llegamos al objetivo del mes en el trabajo y tendría que hablar con el equipo pero no sé cómo plantearlo sin sonar pesimista<br /><br />
							idea: y si hacemos una extensión de chrome que extraiga los leads de linkedin automáticamente<br /><br />
							llamar al banco por lo de la cuenta de la empresa<br /><br />
							revisar la factura que me mandó el diseñador de la web que creo que está mal<br /><br />
							quiero empezar a hacer contenido en youtube sobre productividad pero no sé por dónde empezar y me da vergüenza grabarme
						</p>
					</div>
				</div>

				<!-- Right: Structured output with new grouped layout -->
				<div class="overflow-y-auto bg-[#00D4FF]/[0.015] px-[25px] py-[30px]">
					<div class="mb-[18px] flex items-center justify-between">
						<div class="flex items-center gap-2">
							<div class="flex h-[22px] w-[22px] items-center justify-center rounded-lg bg-gradient-to-br from-[#00D4FF]/[0.2] to-[#00D4FF]/[0.05]">
								<svg width="12" height="12" viewBox="0 0 24 24" fill="none">
									<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke="#00D4FF" stroke-width="2.5" stroke-linecap="round" />
									<path d="M9 11l3 3L22 4" stroke="#00D4FF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</div>
							<p class="text-[11px] font-semibold tracking-[2px] text-[#00D4FF] uppercase">Estructurado</p>
						</div>
						<p class="text-[10px] text-[#6B7280]">{demoGroups.length} grupos</p>
					</div>

					<!-- Primary task - always visible at top -->
					{#if primaryTask}
						<div class="mb-4 rounded-lg border border-[#00D4FF]/[0.25] bg-gradient-to-br from-[#00D4FF]/[0.1] to-[#00D4FF]/[0.02] p-[16px_18px]">
							<div class="mb-2 flex items-center gap-2">
								<div class="h-2 w-2 rounded-full bg-[#00D4FF] shadow-[0_0_8px_rgba(0,212,255,0.5)]"></div>
								<p class="text-[9px] font-extrabold tracking-[2px] text-[#00D4FF] uppercase">Empieza por aquí</p>
								<span class="rounded bg-red-500/20 px-2 py-[2px] text-[9px] font-bold text-red-400">URGENTE</span>
							</div>
							<p class="mb-1 text-[14px] font-semibold leading-[1.4] text-white">{primaryTask.title}</p>
							{#if primaryTask.description}
								<p class="text-[11px] text-[#9CA3AF]">{primaryTask.description}</p>
							{/if}
							<div class="mt-2 flex items-center gap-3">
								<p class="text-[10px] text-[#6B7280]">{primaryTask.estimatedTime}</p>
								<p class="text-[10px] text-[#4B5563]">{primaryTask.groupName}</p>
							</div>
						</div>
					{/if}

					<!-- Groups list -->
					<div class="flex flex-col gap-3">
						{#each demoGroups as group (group.id)}
							<!-- Group card -->
							<div class="overflow-hidden rounded-lg border border-white/[0.06] bg-white/[0.02]">
								<!-- Group header -->
								<div class="flex items-center justify-between px-4 py-3">
									<div class="flex items-center gap-2">
										<!-- Group icon circle -->
										<div
											class="flex h-7 w-7 items-center justify-center rounded-full text-[10px] font-bold"
											style="background: {getGroupBgColor(group.color)}; color: {getGroupTextColor(group.color)};"
										>
											{#if group.icon === 'briefcase'}
												<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
											{:else if group.icon === 'lightbulb'}
												<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a7 7 0 0 0-4 12.7V17h8v-2.3A7 7 0 0 0 12 2z"/><path d="M9 21h6"/><path d="M10 17v4"/></svg>
											{:else if group.icon === 'user'}
												<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
											{:else}
												<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
											{/if}
										</div>
										<h3 class="text-[14px] font-semibold text-white">{group.name}</h3>
									</div>
									<span class="text-[10px] text-[#6B7280]">{countGroupTasks(group)} items</span>
								</div>

								<!-- Expanded content -->
								<div class="border-t border-white/[0.05] px-4 pb-4 pt-3">
									{#each group.subgroups as subgroup (subgroup.id)}
										<!-- Subgroup header -->
										<div class="mb-2 mt-1 flex items-center gap-2">
											{#if subgroup.type === 'tasks'}
												<div class="h-[4px] w-[4px] rounded-full bg-[#00D4FF]"></div>
												<p class="text-[10px] font-semibold tracking-[1px] text-[#00D4FF] uppercase">{subgroup.name}</p>
											{:else}
												<div class="h-[4px] w-[4px] rounded-full bg-[#A78BFA]"></div>
												<p class="text-[10px] font-semibold tracking-[1px] text-[#A78BFA] uppercase">{subgroup.name}</p>
											{/if}
										</div>

										<!-- Subgroup items -->
										<div class="mb-3 flex flex-col gap-2">
											{#each subgroup.items as item (item.id)}
												{#if subgroup.type === 'tasks'}
													{#if item.isPrimary}
														<!-- Primary task shown at top, placeholder here -->
														<div class="flex items-start gap-3 rounded-lg border border-white/[0.05] bg-white/[0.02] border-l-[3px] border-l-[#00D4FF] p-[12px_14px] opacity-70">
															<div class="mt-[5px] h-[6px] w-[6px] shrink-0 rounded-full bg-[#00D4FF]"></div>
															<div class="flex-1">
																<p class="mb-1 text-[13px] font-medium text-[#D1D5DB]">{item.title}</p>
																<p class="text-[10px] text-[#4B5563] italic">↑ Ya mostrada arriba</p>
															</div>
														</div>
													{:else}
														<!-- Regular task -->
														<div class="flex items-start gap-3 rounded-lg border border-white/[0.05] bg-white/[0.02] border-l-[3px] border-l-[#00D4FF] p-[12px_14px]">
															<div class="mt-[5px] h-[6px] w-[6px] shrink-0 rounded-full bg-[#00D4FF]"></div>
															<div class="flex-1">
																<div class="mb-1 flex items-center gap-2">
																	<p class="text-[13px] font-medium text-[#D1D5DB]">{item.title}</p>
																	{#if item.priority === 'urgente'}
																		<span class="rounded bg-red-500/20 px-1.5 py-[1px] text-[8px] font-bold text-red-400">URG</span>
																	{:else if item.priority === 'importante'}
																		<span class="rounded bg-amber-500/20 px-1.5 py-[1px] text-[8px] font-bold text-amber-400">IMP</span>
																	{:else if item.priority === 'baja'}
																		<span class="rounded bg-gray-500/20 px-1.5 py-[1px] text-[8px] font-bold text-gray-400">LOW</span>
																	{/if}
																</div>
																{#if item.description}
																	<p class="text-[10px] text-[#6B7280]">{item.description}</p>
																{/if}
																{#if item.estimatedTime}
																	<p class="mt-1 text-[10px] text-[#4B5563]">{item.estimatedTime}</p>
																{/if}
															</div>
														</div>
													{/if}
												{:else}
													<!-- Note item -->
													<div class="flex items-start gap-3 rounded-lg border border-[#A78BFA]/[0.08] bg-[#A78BFA]/[0.03] border-l-[3px] border-l-[#A78BFA] p-[12px_14px]">
														<div class="mt-[5px] h-[6px] w-[6px] shrink-0 rounded-full bg-[#A78BFA]"></div>
														<div class="flex-1">
															<p class="mb-1 text-[13px] font-medium text-[#D1D5DB]">{item.title}</p>
															{#if item.description}
																<p class="text-[10px] text-[#6B7280]">{item.description}</p>
															{/if}
															{#if item.tags && item.tags.length > 0}
																<div class="mt-2 flex flex-wrap gap-1">
																	{#each item.tags as tag}
																		<span class="rounded bg-[#A78BFA]/[0.15] px-2 py-[2px] text-[9px] font-medium text-[#A78BFA]">{tag}</span>
																	{/each}
																</div>
															{/if}
														</div>
													</div>
												{/if}
											{/each}
										</div>
									{/each}
								</div>
							</div>
						{/each}
					</div>
				</div>
			</div>

			<!-- Footer legend -->
			<div class="flex items-center justify-between border-t border-white/[0.04] bg-[rgba(8,10,16,0.4)] px-[25px] py-[15px]">
				<div class="flex items-center gap-[15px]">
					<div class="flex items-center gap-[5px]">
						<div class="h-[6px] w-[6px] rounded-full bg-[#00D4FF]"></div>
						<span class="text-[10px] text-[#6B7280]">Tareas</span>
					</div>
					<div class="flex items-center gap-[5px]">
						<div class="h-[6px] w-[6px] rounded-full bg-[#A78BFA]"></div>
						<span class="text-[10px] text-[#6B7280]">Ideas / Preocupaciones</span>
					</div>
					<div class="flex items-center gap-[5px]">
						<div class="h-[6px] w-[6px] rounded-full bg-[#6B7280]"></div>
						<span class="text-[10px] text-[#6B7280]">Pendientes / Aspiraciones</span>
					</div>
				</div>
				<p class="text-[10px] italic text-[#4B5563]">De 8 líneas de caos a 3 grupos organizados con 9 acciones claras</p>
			</div>
		</div>
	</div>
</section>
