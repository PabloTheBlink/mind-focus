<script>
import DOMPurify from 'dompurify';
import AlertCircle from 'lucide-svelte/icons/alert-circle';
import Book from 'lucide-svelte/icons/book';
import Briefcase from 'lucide-svelte/icons/briefcase';
import Check from 'lucide-svelte/icons/check';
import ChevronDown from 'lucide-svelte/icons/chevron-down';
import Circle from 'lucide-svelte/icons/circle';
import DollarSign from 'lucide-svelte/icons/dollar-sign';
import Heart from 'lucide-svelte/icons/heart';
import Home from 'lucide-svelte/icons/home';
import Lightbulb from 'lucide-svelte/icons/lightbulb';
import Loader2 from 'lucide-svelte/icons/loader-2';
import User from 'lucide-svelte/icons/user';
import Users from 'lucide-svelte/icons/users';
import X from 'lucide-svelte/icons/x';
import { marked } from 'marked';
import { SvelteSet } from 'svelte/reactivity';

let { initialText = '', currentText = '', structuredData = null } = $props();

let isEditing = $state(false);
let isProcessing = $state(false);
let apiError = $state(null);
let errorDismissTimer = $state(null);

// Configure marked for custom task lists
marked.setOptions({
	breaks: true,
	gfm: true,
});

// Internal structured data derived from the markdown text
let parsedStructuredData = $derived.by(() => {
	if (!textareaValue) {
		return [];
	}

	return parseMarkdownToStructure(textareaValue);
});

// Local state for AJAX response
let ajaxStructuredData = $state(null);

// Use either AJAX structured data, passed structuredData, or parsed from text
let currentStructuredData = $derived(
	ajaxStructuredData
		? ajaxStructuredData
		: parsedStructuredData.length > 0
			? parsedStructuredData
			: (structuredData ?? [])
);

function parseMarkdownToStructure(md) {
	const lines = md.split('\n');
	const groups = [];
	let currentGroup = null;
	let currentSubgroup = null;
	let currentItem = null;

	let groupId = 1;
	let subgroupId = 1;
	let itemId = 1;

	const iconByKeywords = {
		trabajo: 'briefcase',
		proyecto: 'briefcase',
		personal: 'user',
		salud: 'heart',
		bienestar: 'heart',
		idea: 'lightbulb',
		creatividad: 'lightbulb',
		dinero: 'dollar-sign',
		finanzas: 'dollar-sign',
		estudio: 'book',
		aprendizaje: 'book',
		hogar: 'home',
		casa: 'home',
		equipo: 'users',
		familia: 'users',
	};

	const colorByIcon = {
		briefcase: 'cyan',
		user: 'green',
		heart: 'pink',
		lightbulb: 'purple',
		'dollar-sign': 'orange',
		book: 'blue',
		home: 'blue',
		users: 'cyan',
	};

	for (let i = 0; i < lines.length; i++) {
		const line = lines[i].trim();

		if (!line) {
			continue;
		}

		// Group: # Name
		if (line.startsWith('# ')) {
			const name = line.substring(2).trim();

			let icon = 'briefcase';

			for (const [kw, ic] of Object.entries(iconByKeywords)) {
				if (name.toLowerCase().includes(kw)) {
					icon = ic;
					break;
				}
			}

			currentGroup = {
				id: groupId++,
				name,
				icon,
				color: colorByIcon[icon] || 'cyan',
				subgroups: [],
			};
			groups.push(currentGroup);
			currentSubgroup = null;
			currentItem = null;
		}

		// Subgroup: ## Name
		else if (line.startsWith('## ')) {
			if (!currentGroup) {
				currentGroup = {
					id: groupId++,
					name: 'General',
					icon: 'briefcase',
					color: 'cyan',
					subgroups: [],
				};
				groups.push(currentGroup);
			}

			const name = line.substring(3).trim();
			currentSubgroup = {
				id: subgroupId++,
				name,
				type: name.toLowerCase().includes('tarea') ? 'tasks' : 'notes',
				items: [],
			};
			currentGroup.subgroups.push(currentSubgroup);
			currentItem = null;
		}

		// Task: - [ ] Title (PRIORITY) [TIME]
		else if (line.startsWith('- [ ] ')) {
			if (!currentSubgroup) {
				if (!currentGroup) {
					currentGroup = {
						id: groupId++,
						name: 'General',
						icon: 'briefcase',
						color: 'cyan',
						subgroups: [],
					};
					groups.push(currentGroup);
				}

				currentSubgroup = {
					id: subgroupId++,
					name: 'Tareas',
					type: 'tasks',
					items: [],
				};
				currentGroup.subgroups.push(currentSubgroup);
			}

			let title = line.substring(6).trim();
			let priority = 'normal';
			let estimatedTime = '';

			// Extract Priority
			if (title.includes('(URGENTE)')) {
				priority = 'urgente';
				title = title.replace('(URGENTE)', '').trim();
			} else if (title.includes('(IMPORTANTE)')) {
				priority = 'importante';
				title = title.replace('(IMPORTANTE)', '').trim();
			} else if (title.includes('(BAJA)')) {
				priority = 'baja';
				title = title.replace('(BAJA)', '').trim();
			}

			// Extract Time
			const timeMatch = title.match(/\[(.*?)\]/);

			if (timeMatch) {
				estimatedTime = timeMatch[1];
				title = title.replace(`[${estimatedTime}]`, '').trim();
			}

			currentItem = {
				id: itemId++,
				title,
				priority,
				isPrimary: false, // We'll set primary later if none
				estimatedTime,
				description: '',
			};
			currentSubgroup.items.push(currentItem);
		}

		// Note: - Title #tags
		else if (line.startsWith('- ')) {
			if (!currentSubgroup) {
				if (!currentGroup) {
					currentGroup = {
						id: groupId++,
						name: 'General',
						icon: 'briefcase',
						color: 'cyan',
						subgroups: [],
					};
					groups.push(currentGroup);
				}

				currentSubgroup = {
					id: subgroupId++,
					name: 'Notas e ideas',
					type: 'notes',
					items: [],
				};
				currentGroup.subgroups.push(currentSubgroup);
			}

			let title = line.substring(2).trim();
			const tags = [];
			const tagMatches = title.match(/#(\w+)/g);

			if (tagMatches) {
				tagMatches.forEach((tag) => {
					tags.push(tag.substring(1));
					title = title.replace(tag, '').trim();
				});
			}

			currentItem = {
				id: itemId++,
				title,
				tags,
				description: '',
			};
			currentSubgroup.items.push(currentItem);
		}

		// Description: > text
		else if (line.startsWith('>') || line.startsWith('  >')) {
			if (currentItem) {
				const desc = line.replace(/^\s*>\s*/, '').trim();
				currentItem.description += (currentItem.description ? '\n' : '') + desc;
			}
		}
	}

	// Post-process to ensure at least one primary task if needed
	let hasPrimary = false;

	for (const g of groups) {
		for (const sg of g.subgroups) {
			for (const it of sg.items) {
				if (it.isPrimary) {
					hasPrimary = true;
				}
			}
		}
	}

	if (!hasPrimary && groups.length > 0) {
		for (const g of groups) {
			for (const sg of g.subgroups) {
				if (sg.type === 'tasks' && sg.items.length > 0) {
					// Make the first urgent or first task primary
					const urgent = sg.items.find((it) => it.priority === 'urgente');

					if (urgent) {
						urgent.isPrimary = true;
					} else {
						sg.items[0].isPrimary = true;
					}

					hasPrimary = true;
					break;
				}
			}

			if (hasPrimary) {
				break;
			}
		}
	}

	return groups;
}

// Markdown rendering
let renderedMarkdown = $derived.by(() => {
	if (!textareaValue) {
		return '';
	}

	const rawHtml = marked.parse(textareaValue);

	// Only sanitize in browser environment (not during SSR)
	if (typeof window !== 'undefined') {
		return DOMPurify.sanitize(rawHtml);
	}
	
	return rawHtml;
});

// Track which groups are expanded (all expanded by default)
let expandedGroups = new SvelteSet();

function toggleGroup(groupId) {
	if (expandedGroups.has(groupId)) {
		expandedGroups.delete(groupId);
	} else {
		expandedGroups.add(groupId);
	}
}

let textareaValue = $state(initialText || currentText || '');

$effect(() => {
	if (initialText) {
		textareaValue = initialText;
	}
});

$effect(() => {
	if (currentText) {
		textareaValue = currentText;
	}
});

async function handleStructure() {
	if (!textareaValue || isProcessing) {
		return;
	}

	// Clear any previous error
	clearError();

	isProcessing = true;

	try {
		const response = await fetch('/api/structure', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
				'X-Requested-With': 'XMLHttpRequest',
			},
			body: JSON.stringify({ text: textareaValue }),
		});

		if (!response.ok) {
			let errorMessage = 'Error desconocido del servidor';
			
			try {
				const error = await response.json();
				errorMessage = error.error || error.message || errorMessage;
			} catch {
				// If we can't parse the error response, use HTTP status
				errorMessage = `Error del servidor: ${response.status} ${response.statusText}`;
			}
			
			setError(errorMessage);

			return;
		}

		const data = await response.json();

		// Update the textarea with the structured markdown
		textareaValue = data.markdown;

		// Update local structured data state
		ajaxStructuredData = data.structuredData;
	} catch (error) {
		// Network errors, timeouts, etc.
		if (error.name === 'AbortError') {
			setError('La petición fue cancelada. Inténtalo de nuevo.');
		} else if (error.name === 'TypeError') {
			setError('No se pudo conectar con el servidor. Verifica tu conexión e inténtalo de nuevo.');
		} else {
			setError('No se pudo estructurar el texto. Inténtalo de nuevo.');
		}
	} finally {
		isProcessing = false;
	}
}

function setError(message) {
	apiError = message;
	
	// Auto-dismiss after 8 seconds
	if (errorDismissTimer) {
		clearTimeout(errorDismissTimer);
	}
	
	errorDismissTimer = setTimeout(() => {
		apiError = null;
		errorDismissTimer = null;
	}, 8000);
}

function clearError() {
	apiError = null;
	
	if (errorDismissTimer) {
		clearTimeout(errorDismissTimer);
		errorDismissTimer = null;
	}
}

function dismissError() {
	clearError();
}

function handleRetry() {
	clearError();
	handleStructure();
}

function handleReset() {
	textareaValue = '';
}

function handleSave() {
	// Placeholder for save functionality
	console.log('Saving...', textareaValue);
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

<div class="flex h-full w-full flex-col">
	<!-- Top bar -->
	<div class="flex shrink-0 flex-wrap items-center justify-between gap-3 border-b border-white/[0.04] bg-black/20 px-4 py-3 sm:px-6 sm:py-[15px] md:px-[30px]">
		<div class="flex flex-wrap gap-4 sm:gap-[30px]">
			<div class="flex items-center gap-2">
				<div class="h-[6px] w-[6px] rounded-full bg-[#00D4FF]"></div>
				<span class="text-[10px] font-semibold tracking-[2px] text-[#6B7280] uppercase sm:text-[11px]">Tu mente</span>
			</div>
			<div class="flex items-center gap-2">
				<div class="h-[6px] w-[6px] rounded-full bg-[#A78BFA]"></div>
				<span class="text-[10px] font-semibold tracking-[2px] text-[#6B7280] uppercase sm:text-[11px]">Estructurado</span>
			</div>
		</div>
		<div class="flex gap-[10px]">
			<button
				type="button"
				class="rounded border border-white/[0.06] bg-transparent px-3 py-1.5 text-[10px] font-semibold tracking-[0.5px] uppercase text-[#6B7280] transition-all hover:border-white/[0.12] hover:text-white sm:px-[14px] sm:py-[6px] sm:text-[11px]"
				onclick={handleReset}
			>
				Limpiar
			</button>
			<button
				type="button"
				class="rounded border border-white/[0.06] bg-transparent px-3 py-1.5 text-[10px] font-semibold tracking-[0.5px] uppercase text-[#6B7280] transition-all hover:border-white/[0.12] hover:text-white sm:px-[14px] sm:py-[6px] sm:text-[11px]"
				onclick={handleSave}
			>
				Guardar
			</button>
		</div>
	</div>

	<!-- Main grid -->
	<div class="grid min-h-0 flex-1 grid-cols-1 lg:grid-cols-2">
		<!-- Left: Input -->
		<div class="overflow-y-auto border-b border-white/[0.04] bg-black/10 p-4 sm:p-5 lg:border-b-0 lg:border-r lg:p-[25px]">
			{#if isEditing || !textareaValue}
				<textarea
					bind:value={textareaValue}
					onblur={() => (isEditing = false)}
					placeholder="Escribe aquí todo lo que tienes en la cabeza...

Sin orden. Sin estructura. Sin filtros.
Solo escribe."
					class="h-full min-h-[250px] w-full rounded-lg border border-dashed border-white/[0.08] bg-transparent px-4 py-[15px] text-[13px] leading-[1.8] text-[#B0B8C4] outline-none resize-none font-sans sm:min-h-[380px] sm:px-5 sm:py-[20px] sm:text-[14px] sm:leading-[1.9]"
				></textarea>
			{:else}
				<div
					role="button"
					tabindex="0"
					onclick={() => (isEditing = true)}
					onkeydown={(e) => e.key === 'Enter' && (isEditing = true)}
					class="prose prose-sm prose-invert h-full min-h-[250px] w-full cursor-text overflow-y-auto rounded-lg border border-white/[0.04] bg-white/[0.02] px-4 py-[15px] sm:min-h-[380px] sm:px-5 sm:py-[20px]"
				>
					{@html renderedMarkdown}
				</div>
			{/if}
		</div>

		<!-- Right: Structured output -->
		<div class="overflow-y-auto bg-[#00D4FF]/[0.015] p-4 sm:p-[25px]">
			{#if currentStructuredData && currentStructuredData.length > 0}
				<div class="mb-3 flex items-center justify-between sm:mb-[18px]">
					<div class="flex items-center gap-2">
						<div class="flex h-[20px] w-[20px] items-center justify-center rounded-lg bg-gradient-to-br from-[#00D4FF]/[0.2] to-[#00D4FF]/[0.05] sm:h-[22px] sm:w-[22px]">
							<Check class="size-2.5 sm:size-3" color="#00D4FF" stroke-width="2.5" aria-hidden="true" />
						</div>
						<p class="text-[10px] font-semibold tracking-[2px] text-[#00D4FF] uppercase sm:text-[11px]">Estructurado</p>
					</div>
					<p class="text-[9px] text-[#6B7280] sm:text-[10px]">{currentStructuredData.length} grupos</p>
				</div>

				<!-- Primary task - always visible at top -->
				{#if primaryTask}
					<div class="mb-3 rounded-lg border border-[#00D4FF]/[0.25] bg-gradient-to-br from-[#00D4FF]/[0.1] to-[#00D4FF]/[0.02] p-3 sm:mb-4 sm:p-[16px_18px]">
						<div class="mb-1.5 flex flex-wrap items-center gap-2 sm:mb-2">
							<div class="h-1.5 w-1.5 rounded-full bg-[#00D4FF] shadow-[0_0_8px_rgba(0,212,255,0.5)] sm:h-2 sm:w-2"></div>
							<p class="text-[8px] font-extrabold tracking-[2px] text-[#00D4FF] uppercase sm:text-[9px]">Empieza por aquí</p>
							{#if primaryTask.priority === 'urgente'}
								<span class="rounded bg-red-500/20 px-1.5 py-[2px] text-[8px] font-bold text-red-400 sm:px-2 sm:text-[9px]">URGENTE</span>
							{:else if primaryTask.priority === 'importante'}
								<span class="rounded bg-amber-500/20 px-1.5 py-[2px] text-[8px] font-bold text-amber-400 sm:px-2 sm:text-[9px]">IMPORTANTE</span>
							{/if}
						</div>
						<p class="mb-1 text-[13px] font-semibold leading-[1.4] text-white sm:text-[14px]">{primaryTask.title}</p>
						{#if primaryTask.description}
							<p class="text-[10px] text-[#9CA3AF] sm:text-[11px]">{primaryTask.description}</p>
						{/if}
						<div class="mt-1.5 flex flex-wrap items-center gap-2 sm:mt-2 sm:gap-3">
							{#if primaryTask.estimatedTime}
								<p class="text-[9px] text-[#6B7280] sm:text-[10px]">{primaryTask.estimatedTime}</p>
							{/if}
							<p class="text-[9px] text-[#4B5563] sm:text-[10px]">{primaryTask.groupName}</p>
						</div>
					</div>
				{/if}

				<!-- Groups list -->
				<div class="flex flex-col gap-2 sm:gap-3">
					{#each currentStructuredData as group (group.id)}
						<!-- Collapsible group card -->
						<div class="overflow-hidden rounded-lg border border-white/[0.06] bg-white/[0.02]">
							<!-- Group header - clickable -->
							<button
								type="button"
								class="flex w-full items-center justify-between px-3 py-2.5 transition-colors hover:bg-white/[0.02] sm:px-4 sm:py-3"
								onclick={() => toggleGroup(group.id)}
							>
								<div class="flex items-center gap-2">
									<!-- Group icon circle -->
									<div
										class="flex h-6 w-6 items-center justify-center rounded-full text-[9px] font-bold sm:h-7 sm:w-7 sm:text-[10px]"
										style="background: {getGroupBgColor(group.color)}; color: {getGroupTextColor(group.color)};"
									>
										{#if group.icon === 'briefcase'}
											<Briefcase class="size-3 sm:size-3.5" aria-hidden="true" />
										{:else if group.icon === 'user'}
											<User class="size-3 sm:size-3.5" aria-hidden="true" />
										{:else if group.icon === 'lightbulb'}
											<Lightbulb class="size-3 sm:size-3.5" aria-hidden="true" />
										{:else if group.icon === 'heart'}
											<Heart class="size-3 sm:size-3.5" aria-hidden="true" />
										{:else if group.icon === 'dollar-sign'}
											<DollarSign class="size-3 sm:size-3.5" aria-hidden="true" />
										{:else if group.icon === 'book'}
											<Book class="size-3 sm:size-3.5" aria-hidden="true" />
										{:else if group.icon === 'home'}
											<Home class="size-3 sm:size-3.5" aria-hidden="true" />
										{:else if group.icon === 'users'}
											<Users class="size-3 sm:size-3.5" aria-hidden="true" />
										{:else}
											<Circle class="size-3 sm:size-3.5" aria-hidden="true" />
										{/if}
									</div>
									<h3 class="text-[13px] font-semibold text-white sm:text-[14px]">{group.name}</h3>
								</div>
								<div class="flex items-center gap-1.5 sm:gap-2">
									<span class="text-[9px] text-[#6B7280] sm:text-[10px]">{countGroupTasks(group)} items</span>
									<!-- Chevron -->
									<div class={expandedGroups.has(group.id) ? 'rotate-180' : ''}>
										<ChevronDown
											class="size-3.5 transition-transform duration-200 sm:size-4"
											color="#6B7280"
											aria-hidden="true"
										/>
									</div>
								</div>
							</button>

							<!-- Expandable content -->
							{#if expandedGroups.has(group.id)}
								<div class="border-t border-white/[0.05] px-3 pb-3 pt-2.5 sm:px-4 sm:pb-4 sm:pt-3">
									{#each group.subgroups as subgroup (subgroup.id)}
										<!-- Subgroup header -->
										<div class="mb-1.5 mt-1 flex items-center gap-2 sm:mb-2 sm:mt-1">
											{#if subgroup.type === 'tasks'}
												<div class="h-[3px] w-[3px] rounded-full bg-[#00D4FF] sm:h-[4px] sm:w-[4px]"></div>
												<p class="text-[9px] font-semibold tracking-[1px] text-[#00D4FF] uppercase sm:text-[10px]">{subgroup.name}</p>
											{:else}
												<div class="h-[3px] w-[3px] rounded-full bg-[#A78BFA] sm:h-[4px] sm:w-[4px]"></div>
												<p class="text-[9px] font-semibold tracking-[1px] text-[#A78BFA] uppercase sm:text-[10px]">{subgroup.name}</p>
											{/if}
										</div>

										<!-- Subgroup items -->
										<div class="mb-2 flex flex-col gap-1.5 sm:mb-3 sm:gap-2">
											{#each subgroup.items as item (item.id)}
												{#if subgroup.type === 'tasks'}
													{#if item.isPrimary}
														<!-- Primary task - skip (already shown at top) -->
														<div class="flex items-start gap-2 rounded-lg border border-white/[0.05] bg-white/[0.02] border-l-[3px] border-l-[#00D4FF] p-2.5 sm:gap-3 sm:p-[12px_14px]">
															<div class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-[#00D4FF] sm:mt-[5px] sm:h-[6px] sm:w-[6px]"></div>
															<div class="flex-1">
																<p class="mb-0.5 text-[12px] font-medium text-[#D1D5DB] sm:text-[13px]">{item.title}</p>
																<p class="text-[9px] text-[#4B5563] italic sm:text-[10px]">↑ Ya mostrada arriba</p>
															</div>
														</div>
													{:else}
														<!-- Regular task -->
														<div class="flex items-start gap-2 rounded-lg border border-white/[0.05] bg-white/[0.02] border-l-[3px] border-l-[#00D4FF] p-2.5 sm:gap-3 sm:p-[12px_14px]">
															<div class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-[#00D4FF] sm:mt-[5px] sm:h-[6px] sm:w-[6px]"></div>
															<div class="flex-1">
																<div class="mb-0.5 flex flex-wrap items-center gap-1.5 sm:mb-1 sm:gap-2">
																	<p class="text-[12px] font-medium text-[#D1D5DB] sm:text-[13px]">{item.title}</p>
																	{#if item.priority === 'urgente'}
																		<span class="rounded bg-red-500/20 px-1 py-[1px] text-[7px] font-bold text-red-400 sm:px-1.5 sm:text-[8px]">URG</span>
																	{:else if item.priority === 'importante'}
																		<span class="rounded bg-amber-500/20 px-1 py-[1px] text-[7px] font-bold text-amber-400 sm:px-1.5 sm:text-[8px]">IMP</span>
																	{:else if item.priority === 'baja'}
																		<span class="rounded bg-gray-500/20 px-1 py-[1px] text-[7px] font-bold text-gray-400 sm:px-1.5 sm:text-[8px]">LOW</span>
																	{/if}
																</div>
																{#if item.description}
																	<p class="text-[9px] text-[#6B7280] sm:text-[10px]">{item.description}</p>
																{/if}
																{#if item.estimatedTime}
																	<p class="mt-0.5 text-[9px] text-[#4B5563] sm:mt-1 sm:text-[10px]">{item.estimatedTime}</p>
																{/if}
															</div>
														</div>
													{/if}
												{:else}
													<!-- Note item -->
													<div class="flex items-start gap-2 rounded-lg border border-[#A78BFA]/[0.08] bg-[#A78BFA]/[0.03] border-l-[3px] border-l-[#A78BFA] p-2.5 sm:gap-3 sm:p-[12px_14px]">
														<div class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-[#A78BFA] sm:mt-[5px] sm:h-[6px] sm:w-[6px]"></div>
														<div class="flex-1">
															<p class="mb-0.5 text-[12px] font-medium text-[#D1D5DB] sm:text-[13px]">{item.title}</p>
															{#if item.description}
																<p class="text-[9px] text-[#6B7280] sm:text-[10px]">{item.description}</p>
															{/if}
															{#if item.tags && item.tags.length > 0}
																<div class="mt-1.5 flex flex-wrap gap-1 sm:mt-2">
																	{#each item.tags as tag}
																		<span class="rounded bg-[#A78BFA]/[0.15] px-1.5 py-[2px] text-[8px] font-medium text-[#A78BFA] sm:px-2 sm:text-[9px]">{tag}</span>
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
	<div class="flex shrink-0 flex-col gap-3 border-t border-white/[0.04] bg-black/15 px-4 py-4 sm:flex-row sm:items-center sm:justify-center sm:gap-3 sm:px-[30px] sm:py-[18px]">
		<!-- Error banner -->
		{#if apiError}
			<div class="flex items-start gap-3 rounded-lg border border-red-500/20 bg-red-500/10 p-3">
				<AlertCircle class="mt-0.5 size-4 shrink-0 text-red-400" aria-hidden="true" />
				<div class="flex-1">
					<p class="mb-2 text-[13px] text-red-200">{apiError}</p>
					<button
						type="button"
						class="rounded bg-red-500/20 px-3 py-1 text-[11px] font-semibold text-red-300 transition-colors hover:bg-red-500/30"
						onclick={handleRetry}
					>
						Reintentar
					</button>
				</div>
				<button
					type="button"
					class="rounded p-1 text-red-400 transition-colors hover:bg-red-500/20 hover:text-red-300"
					onclick={dismissError}
					aria-label="Cerrar mensaje de error"
				>
					<X class="size-4" aria-hidden="true" />
				</button>
			</div>
		{/if}

		<div class="flex flex-col items-center justify-center gap-3 sm:flex-row sm:gap-3">
			<p class="text-[10px] italic text-[#4B5563]">Escribe a la izquierda. Estructura cuando quieras.</p>
			<button
				type="button"
				class="flex w-full items-center justify-center gap-2 rounded bg-gradient-to-r from-[#00D4FF] to-[#00B8E6] px-6 py-[12px] text-[13px] font-bold tracking-[1px] uppercase text-[#0A0A0A] shadow-[0_0_25px_rgba(0,212,255,0.2)] transition-all hover:shadow-[0_0_35px_rgba(0,212,255,0.4)] disabled:cursor-not-allowed disabled:opacity-50 sm:ml-[10px] sm:w-auto sm:px-9 sm:py-[10px]"
				onclick={handleStructure}
				disabled={isProcessing || !textareaValue}
			>
				{#if isProcessing}
					<Loader2 class="size-4 animate-spin" aria-hidden="true" />
					<span>Procesando...</span>
				{:else}
					<span>Estructurar</span>
				{/if}
			</button>
		</div>
	</div>
</div>
