<script>
import { Link } from '@inertiajs/svelte';
</script>

<header class="sticky top-0 z-[100] flex items-center justify-between border-b border-white/[0.05] bg-[rgba(8,10,16,0.8)] px-10 py-[18px] backdrop-blur-[12px]">
	<div class="flex items-baseline gap-2">
		<span class="text-[28px] text-[#00D4FF]" style="font-family: 'Brush Script MT', 'Great Vibes', cursive;">MIND</span>
		<span class="text-[20px] font-extrabold tracking-[1px] text-white" style="font-family: -apple-system, BlinkMacSystemFont, sans-serif;">FOCUS</span>
	</div>
	<div class="flex items-center gap-[25px]">
		<Link
			href="/"
			class="flex items-center gap-[5px] text-[13px] font-medium text-[#6B7280] no-underline hover:text-white"
		>
			Volver a la web
			<svg fill="none" height="14" viewBox="0 0 24 24" width="14">
				<path d="M19 12H5M12 19l-7-7 7-7" stroke="#6B7280" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
			</svg>
		</Link>
	</div>
</header>
