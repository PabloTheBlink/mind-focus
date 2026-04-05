<script>
import { Link } from '@inertiajs/svelte';
import Logo from './Logo.svelte';
</script>

<header class="sticky top-0 z-[100] flex items-center justify-between border-b border-white/[0.05] bg-[rgba(8,10,16,0.8)] px-10 py-[18px] backdrop-blur-[12px]">
	<Logo size="lg" inline />
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
