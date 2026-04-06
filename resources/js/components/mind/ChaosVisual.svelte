<script>
let { showGlow = true } = $props();
</script>

<div class="relative flex items-center justify-center h-[400px] w-full my-[60px] bg-gradient-to-b from-[#080808] via-[#0A0A0C] to-[#0C0C0E]">
	<svg viewBox="0 0 1200 400" class="absolute w-full h-full opacity-15">
		<line x1="100" y1="50" x2="350" y2="180" stroke="var(--color-cyan)" stroke-width="1" opacity="0.6" />
		<line x1="200" y1="300" x2="450" y2="100" stroke="var(--color-purple)" stroke-width="0.8" opacity="0.5" />
		<line x1="800" y1="80" x2="950" y2="250" stroke="var(--color-cyan)" stroke-width="1.2" opacity="0.4" />
		<line x1="600" y1="350" x2="750" y2="120" stroke="var(--color-purple)" stroke-width="0.6" opacity="0.7" />
		<line x1="50" y1="200" x2="280" y2="320" stroke="var(--color-cyan)" stroke-width="0.9" opacity="0.5" />
		<line x1="1050" y1="150" x2="1150" y2="50" stroke="var(--color-purple)" stroke-width="1" opacity="0.6" />
		<line x1="400" y1="280" x2="650" y2="320" stroke="var(--color-cyan)" stroke-width="0.7" opacity="0.4" />
		<line x1="700" y1="60" x2="880" y2="280" stroke="var(--color-purple)" stroke-width="1.1" opacity="0.5" />
		<circle cx="250" cy="150" r="3" fill="var(--color-cyan)" opacity="0.8" />
		<circle cx="450" cy="280" r="2" fill="var(--color-purple)" opacity="0.6" />
		<circle cx="750" cy="100" r="3" fill="var(--color-cyan)" opacity="0.7" />
		<circle cx="900" cy="320" r="2.5" fill="var(--color-purple)" opacity="0.8" />
		<circle cx="150" cy="80" r="2" fill="var(--color-cyan)" opacity="0.5" />
		<circle cx="1000" cy="200" r="3" fill="var(--color-purple)" opacity="0.6" />
		<line x1="500" y1="200" x2="700" y2="200" stroke="var(--color-cyan)" stroke-width="2" opacity="0.9" />
		<line x1="550" y1="180" x2="650" y2="180" stroke="var(--color-cyan)" stroke-width="1.5" opacity="0.7" />
		<line x1="530" y1="220" x2="670" y2="220" stroke="var(--color-cyan)" stroke-width="1.5" opacity="0.7" />
	</svg>
	{#if showGlow}
		<div class="relative z-2 flex items-center justify-center w-[120px] h-[120px] rounded-full bg-[radial-gradient(circle,rgba(0,212,255,0.3)_0%,rgba(0,212,255,0)_70%)]">
			<div class="flex items-center justify-center w-[60px] h-[60px] rounded-full border-2 border-cyan">
				<div class="w-2 h-2 rounded-full bg-cyan" />
			</div>
		</div>
	{/if}
</div>
