<script>
import { useForm, usePage } from '@inertiajs/svelte';
import DOMPurify from 'dompurify';
import { marked } from 'marked';

let { initialText = '', structuredData = null } = $props();
const page = usePage();

let isEditing = $state(false);

// Configure marked for custom task lists
marked.setOptions({
	breaks: true,
	gfm: true,
});

// Reactive structuredData from props or page props
let currentStructuredData = $derived(structuredData ?? page.props.structuredData ?? null);

// Markdown rendering
let renderedMarkdown = $derived.by(() => {
	if (!form.text) {
		return '';
	}

	const rawHtml = marked.parse(form.text);
	return DOMPurify.sanitize(rawHtml);
});

// Track which groups are expanded (all expanded by default)
let expandedGroups = $state(new Set());

function toggleGroup(groupId) {
	if (expandedGroups.has(groupId)) {
		expandedGroups.delete(groupId);
	} else {
		expandedGroups.add(groupId);
	}
	expandedGroups = new Set(expandedGroups);
}

const form = useForm({
	text: initialText,
});

$effect(() => {
	if (initialText) {
		form.text = initialText;
	}
});

function handleStructure() {
	if (!form.text || form.processing) return;

	form.post('/app/structure', {
		preserveState: true,
		preserveScroll: true,
	});
}

function handleReset() {
	form.reset('text');
}

function handleSave() {
	// Placeholder for save functionality
	console.log('Saving...', form.text);
}

const groupColorMap = {
	cyan: { bg: 'rgba(0,212,255,0.15)', text: '#00D4FF' },
	purple: { bg: 'rgba(167,139,250,0.15)', text: '#A78BFA' },
	green: { bg: 'rgba(34,197,94,0.15)', text: '#22C55E' },
	orange: { bg: 'rgba(249,115,22,0.15)', text: '#F97316' },
	pink: { bg: 'rgba(236,72,153,0.15)', text: '#EC4899' },
	blue: { bg: 'rgba(59,130,246,0.15)', text: '#3B82F6' },
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

// Find the primary task across all groups
let primaryTask = $derived(currentStructuredData ? findPrimaryTask(currentStructuredData) : null);

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
</script>

<div class="flex min-h-[calc(100vh-73px)] w-full flex-col">
	<!-- Top bar -->
	<div class="flex items-center justify-between border-b border-white/[0.04] bg-black/20 px-[30px] py-[15px]">
		<div class="flex gap-[30px]">
			<div class="flex items-center gap-2">
				<div class="h-[6px] w-[6px] rounded-full bg-[#00D4FF]"></div>
				<span class="text-[11px] font-semibold tracking-[2px] text-[#6B7280] uppercase">Tu mente</span>
			</div>
			<div class="flex items-center gap-2">
				<div class="h-[6px] w-[6px] rounded-full bg-[#A78BFA]"></div>
				<span class="text-[11px] font-semibold tracking-[2px] text-[#6B7280] uppercase">Estructurado</span>
			</div>
		</div>
		<div class="flex gap-[10px]">
			<button
				type="button"
				class="rounded border border-white/[0.06] bg-transparent px-[14px] py-[6px] text-[11px] font-semibold tracking-[0.5px] uppercase text-[#6B7280] transition-all hover:border-white/[0.12] hover:text-white"
				onclick={handleReset}
			>
				Limpiar
			</button>
			<button
				type="button"
				class="rounded border border-white/[0.06] bg-transparent px-[14px] py-[6px] text-[11px] font-semibold tracking-[0.5px] uppercase text-[#6B7280] transition-all hover:border-white/[0.12] hover:text-white"
				onclick={handleSave}
			>
				Guardar
			</button>
		</div>
	</div>

	<!-- Main grid -->
	<div class="grid min-h-[500px] flex-1 grid-cols-2">
		<!-- Left: Input -->
		<div class="border-r border-white/[0.04] bg-black/10 p-[25px]">
			{#if isEditing || !form.text}
				<textarea
					bind:value={form.text}
					onblur={() => (isEditing = false)}
					placeholder="Escribe aquí todo lo que tienes en la cabeza...

Sin orden. Sin estructura. Sin filtros.
Solo escribe."
					class="h-full min-h-[380px] w-full rounded-lg border border-dashed border-white/[0.08] bg-transparent px-5 py-[20px] text-[14px] leading-[1.9] text-[#B0B8C4] outline-none resize-none font-sans"
				></textarea>
			{:else}
				<div 
					role="button"
					tabindex="0"
					onclick={() => (isEditing = true)}
					onkeydown={(e) => e.key === 'Enter' && (isEditing = true)}
					class="prose prose-invert h-full min-h-[380px] w-full cursor-text overflow-y-auto rounded-lg border border-white/[0.04] bg-white/[0.02] px-5 py-[20px]"
				>
					{@html renderedMarkdown}
				</div>
			{/if}
		</div>

		<!-- Right: Structured output -->
		<div class="overflow-y-auto bg-[#00D4FF]/[0.015] p-[25px]">
			{#if currentStructuredData && currentStructuredData.length > 0}
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
					<p class="text-[10px] text-[#6B7280]">{currentStructuredData.length} grupos</p>
				</div>

				<!-- Primary task - always visible at top -->
				{#if primaryTask}
					<div class="mb-4 rounded-lg border border-[#00D4FF]/[0.25] bg-gradient-to-br from-[#00D4FF]/[0.1] to-[#00D4FF]/[0.02] p-[16px_18px]">
						<div class="mb-2 flex items-center gap-2">
							<div class="h-2 w-2 rounded-full bg-[#00D4FF] shadow-[0_0_8px_rgba(0,212,255,0.5)]"></div>
							<p class="text-[9px] font-extrabold tracking-[2px] text-[#00D4FF] uppercase">Empieza por aquí</p>
							{#if primaryTask.priority === 'urgente'}
								<span class="rounded bg-red-500/20 px-2 py-[2px] text-[9px] font-bold text-red-400">URGENTE</span>
							{:else if primaryTask.priority === 'importante'}
								<span class="rounded bg-amber-500/20 px-2 py-[2px] text-[9px] font-bold text-amber-400">IMPORTANTE</span>
							{/if}
						</div>
						<p class="mb-1 text-[14px] font-semibold leading-[1.4] text-white">{primaryTask.title}</p>
						{#if primaryTask.description}
							<p class="text-[11px] text-[#9CA3AF]">{primaryTask.description}</p>
						{/if}
						<div class="mt-2 flex items-center gap-3">
							{#if primaryTask.estimatedTime}
								<p class="text-[10px] text-[#6B7280]">{primaryTask.estimatedTime}</p>
							{/if}
							<p class="text-[10px] text-[#4B5563]">{primaryTask.groupName}</p>
						</div>
					</div>
				{/if}

				<!-- Groups list -->
				<div class="flex flex-col gap-3">
					{#each currentStructuredData as group (group.id)}
						<!-- Collapsible group card -->
						<div class="overflow-hidden rounded-lg border border-white/[0.06] bg-white/[0.02]">
							<!-- Group header - clickable -->
							<button
								type="button"
								class="flex w-full items-center justify-between px-4 py-3 transition-colors hover:bg-white/[0.02]"
								onclick={() => toggleGroup(group.id)}
							>
								<div class="flex items-center gap-2">
									<!-- Group icon circle -->
									<div
										class="flex h-7 w-7 items-center justify-center rounded-full text-[10px] font-bold"
										style="background: {getGroupBgColor(group.color)}; color: {getGroupTextColor(group.color)};"
									>
										{#if group.icon === 'briefcase'}
											<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
										{:else if group.icon === 'user'}
											<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
										{:else if group.icon === 'lightbulb'}
											<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a7 7 0 0 0-4 12.7V17h8v-2.3A7 7 0 0 0 12 2z"/><path d="M9 21h6"/><path d="M10 17v4"/></svg>
										{:else if group.icon === 'heart'}
											<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
										{:else if group.icon === 'dollar-sign'}
											<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
										{:else if group.icon === 'book'}
											<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
										{:else if group.icon === 'home'}
											<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
										{:else if group.icon === 'users'}
											<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
										{:else}
											<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
										{/if}
									</div>
									<h3 class="text-[14px] font-semibold text-white">{group.name}</h3>
								</div>
								<div class="flex items-center gap-2">
									<span class="text-[10px] text-[#6B7280]">{countGroupTasks(group)} items</span>
									<!-- Chevron -->
									<svg
										class="transition-transform duration-200"
										class:rotate-180={expandedGroups.has(group.id)}
										width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
									>
										<polyline points="6 9 12 15 18 9" />
									</svg>
								</div>
							</button>

							<!-- Expandable content -->
							{#if expandedGroups.has(group.id)}
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
														<!-- Primary task - skip (already shown at top) -->
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
							{/if}
						</div>
					{/each}
				</div>
			{:else}
				<div class="flex h-full items-center justify-center text-[14px] text-[#6B7280]">
					<p>Estructura tu texto para ver los resultados aquí</p>
				</div>
			{/if}
		</div>
	</div>

	<!-- Bottom bar -->
	<div class="flex items-center justify-center gap-3 border-t border-white/[0.04] bg-black/15 px-[30px] py-[18px]">
		<p class="text-[10px] italic text-[#4B5563]">Escribe a la izquierda. Estructura cuando quieras.</p>
		<button
			type="button"
			class="ml-[10px] rounded bg-gradient-to-r from-[#00D4FF] to-[#00B8E6] px-9 py-[10px] text-[13px] font-bold tracking-[1px] uppercase text-[#0A0A0A] shadow-[0_0_25px_rgba(0,212,255,0.2)] transition-all hover:shadow-[0_0_35px_rgba(0,212,255,0.4)]"
			onclick={handleStructure}
			disabled={form.processing || !form.text}
		>
			{#if form.processing}
				Procesando...
			{:else}
				Estructurar
			{/if}
		</button>
	</div>
</div>

<style>
	/* Markdown Preview Styles */
	.prose :global(h1) {
		font-size: 1.5rem;
		font-weight: 700;
		color: white;
		margin-top: 1.5rem;
		margin-bottom: 1rem;
		border-bottom: 1px solid rgba(255, 255, 255, 0.05);
		padding-bottom: 0.5rem;
	}

	.prose :global(h2) {
		font-size: 1.25rem;
		font-weight: 600;
		color: #e2e8f0;
		margin-top: 1.25rem;
		margin-bottom: 0.75rem;
	}

	.prose :global(h3) {
		font-size: 1.1rem;
		font-weight: 600;
		color: #cbd5e1;
		margin-top: 1rem;
		margin-bottom: 0.5rem;
	}

	.prose :global(p) {
		margin-bottom: 1rem;
		line-height: 1.7;
		color: #94a3b8;
	}

	.prose :global(ul), .prose :global(ol) {
		margin-bottom: 1rem;
		padding-left: 1.5rem;
	}

	.prose :global(li) {
		margin-bottom: 0.5rem;
		color: #94a3b8;
	}

	.prose :global(li > p) {
		margin-bottom: 0.25rem;
	}

	.prose :global(blockquote) {
		border-left: 3px solid #00D4FF;
		padding-left: 1rem;
		font-style: italic;
		color: #64748b;
		margin: 1rem 0;
	}

	.prose :global(code) {
		background: rgba(255, 255, 255, 0.05);
		padding: 0.2rem 0.4rem;
		border-radius: 4px;
		font-family: ui-monospace, monospace;
		font-size: 0.9em;
		color: #f1f5f9;
	}

	.prose :global(pre) {
		background: #0f172a;
		padding: 1rem;
		border-radius: 8px;
		overflow-x: auto;
		margin: 1rem 0;
		border: 1px solid rgba(255, 255, 255, 0.05);
	}

	.prose :global(a) {
		color: #00D4FF;
		text-decoration: underline;
	}

	.prose :global(strong) {
		color: white;
		font-weight: 600;
	}

	/* Simple scrollbar for the preview */
	.prose::-webkit-scrollbar {
		width: 6px;
	}

	.prose::-webkit-scrollbar-track {
		background: transparent;
	}

	.prose::-webkit-scrollbar-thumb {
		background: rgba(255, 255, 255, 0.05);
		border-radius: 3px;
	}

	.prose::-webkit-scrollbar-thumb:hover {
		background: rgba(255, 255, 255, 0.1);
	}
</style>
