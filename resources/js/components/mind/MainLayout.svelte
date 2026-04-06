<script>
import { Link } from '@inertiajs/svelte';
import { app } from '@/routes';
import Logo from './Logo.svelte';

let { children } = $props();
</script>

<div class="relative min-h-screen overflow-x-hidden bg-[#0A0A0A] font-sans text-white">
	<!-- Sticky nav -->
	<header class="sticky top-0 z-[1000] flex items-center justify-between border-b border-white/[0.06] bg-[rgba(10,10,10,0.95)] px-10 py-[25px] backdrop-blur-[10px]">
		<Logo size="xl" inline />
		<div class="flex items-center gap-[30px]">
			<a href="#que-es" class="text-[14px] font-medium text-[#D1D5DB] no-underline hover:text-white">Qué es</a>
			<a href="#demo" class="text-[14px] font-medium text-[#D1D5DB] no-underline hover:text-white">Demo</a>
			<a href="#como-funciona" class="text-[14px] font-medium text-[#D1D5DB] no-underline hover:text-white">Cómo funciona</a>
			<a href="#testimonios" class="text-[14px] font-medium text-[#D1D5DB] no-underline hover:text-white">Testimonios</a>
			<Link
				href={app()}
				class="rounded bg-[#00D4FF] px-6 py-[10px] text-[14px] font-bold tracking-[0.5px] text-[#0A0A0A] transition-all hover:shadow-[0_0_30px_rgba(0,212,255,0.4)]"
			>
				Empezar
			</Link>
		</div>
	</header>

	<!-- Main content -->
	{@render children()}
</div>
