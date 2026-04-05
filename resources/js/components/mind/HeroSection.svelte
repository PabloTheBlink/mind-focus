<script>
import { Link } from '@inertiajs/svelte';
import { app } from '@/routes';

let { subtitle = 'Tu mente no es el problema' } = $props();
</script>

<section class="relative flex flex-col items-center justify-center min-h-[90vh] bg-gradient-to-[160deg] from-[#0F1117] via-[#0A0D14] to-[#060810] px-5 py-[80px] text-center">
	<div class="relative z-2 max-w-[900px]">
		<p class="mb-[30px] text-[14px] font-medium tracking-[4px] text-[#9CA3AF] uppercase">{subtitle}</p>
		<h1 class="mb-[30px] text-[72px] font-extrabold tracking-[-2px] leading-[1.1] text-white">
			El ruido mental
			<span class="block text-[#00D4FF]">te paraliza.</span>
		</h1>
		<p class="mx-auto mb-[50px] max-w-[650px] text-[22px] font-light leading-[1.6] text-[#D1D5DB]">
			Tienes cien ideas, cinco proyectos y una lista de tareas que no paras de posponer. Tu cabeza es un navegador con cuarenta pestañas abiertas. Y ninguna carga.
		</p>
		<div class="flex flex-wrap justify-center gap-5">
			<Link
				href={app()}
				class="bg-[#00D4FF] px-12 py-[18px] text-[16px] font-bold tracking-[1px] uppercase text-[#0A0A0A] transition-all hover:shadow-[0_0_40px_rgba(0,212,255,0.4)]"
			>
				Empieza ahora
			</Link>
			<Link
				href="#que-es"
				class="border border-white/20 bg-transparent px-12 py-[18px] text-[16px] font-semibold tracking-[1px] text-white transition-all hover:border-white/40"
			>
				Descubrir más
			</Link>
		</div>
	</div>
	<!-- Background glow -->
	<div class="pointer-events-none absolute top-[15%] left-[30%] z-1 h-[700px] w-[700px] rounded-full bg-[radial-gradient(circle,rgba(0,212,255,0.08)_0%,transparent_60%)]" />
	<!-- Bottom fade -->
	<div class="pointer-events-none absolute bottom-0 left-0 right-0 z-1 h-[150px] bg-gradient-to-b from-transparent to-[#0B0D12]" />
</section>
