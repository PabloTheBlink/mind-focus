<script>
let { size = 'default' } = $props();

const sizes = {
	sm: { mind: 22, focus: 16 },
	default: { mind: 42, focus: 32 },
	lg: { mind: 28, focus: 20 },
	xl: { mind: 32, focus: 24 }
};

const current = $derived(sizes[size] || sizes.default);
</script>

<div class="flex flex-col items-center gap-1 p-5">
	<div class="flex items-baseline gap-2">
		<span
			class="text-[#00D4FF]"
			style="font-family: 'Brush Script MT', 'Great Vibes', cursive; font-size: {current.mind}px; font-weight: 400; letter-spacing: 1px;"
		>
			MIND
		</span>
		<span
			class="text-white"
			style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: {current.focus}px; font-weight: 800; letter-spacing: 2px;"
		>
			FOCUS
		</span>
	</div>
	<span class="text-[#9CA3AF] text-[12px] tracking-[3px] uppercase">desbloquea tu mente</span>
</div>
