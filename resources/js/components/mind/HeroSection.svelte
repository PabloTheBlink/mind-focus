<script>
import { Link } from '@inertiajs/svelte';
import { app } from '@/routes';
import Section from '@/components/mind/Section.svelte';

let { subtitle = 'Tu mente no es el problema' } = $props();
</script>

<Section
	useDefaults={false}
	className="flex flex-col items-center justify-center min-h-[70vh] bg-gradient-to-[160deg] from-[#0F1117] via-dark-bg to-dark-bg-alt px-4 py-[50px] text-center sm:min-h-[90vh] sm:px-5 sm:py-[80px] md:px-5"
	innerClass="relative z-2 max-w-[900px]"
>
	<p class="mb-4 text-[12px] font-medium tracking-[3px] text-text-secondary uppercase sm:mb-[30px] sm:text-[14px] sm:tracking-[4px]">{subtitle}</p>
	<h1 class="mb-5 text-[40px] font-extrabold tracking-[-1.5px] leading-[1.1] text-white sm:mb-[30px] sm:text-[72px] sm:tracking-[-2px]">
		El ruido mental
		<span class="block text-cyan">te paraliza.</span>
	</h1>
	<p class="mx-auto mb-8 max-w-[650px] text-[16px] font-light leading-[1.6] text-text-primary sm:mb-[50px] sm:text-[22px]">
		Tienes cien ideas, cinco proyectos y una lista de tareas que no paras de posponer. Tu cabeza es un navegador con cuarenta pestañas abiertas. Y ninguna carga.
	</p>
	<div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:justify-center sm:gap-5">
		<Link
			href={app()}
			class="w-full rounded bg-cyan px-8 py-[16px] text-[15px] font-bold tracking-[1px] uppercase text-[#0A0A0A] transition-all hover:shadow-[0_0_40px_rgba(0,212,255,0.4)] sm:w-auto sm:px-12 sm:py-[18px] sm:text-[16px]"
		>
			Empieza ahora
		</Link>
		<Link
			href="#que-es"
			class="w-full rounded border border-white/20 bg-transparent px-8 py-[16px] text-[15px] font-semibold tracking-[1px] text-white transition-all hover:border-white/40 sm:w-auto sm:px-12 sm:py-[18px] sm:text-[16px]"
		>
			Descubrir más
		</Link>
	</div>
	<!-- Background glow -->
	<div class="pointer-events-none absolute top-[15%] left-[30%] z-1 hidden h-[700px] w-[700px] rounded-full bg-[radial-gradient(circle,rgba(0,212,255,0.08)_0%,transparent_60%)] sm:block" />
	<!-- Bottom fade -->
	<div class="pointer-events-none absolute bottom-0 left-0 right-0 z-1 h-[100px] bg-gradient-to-b from-transparent to-[#0B0D12] sm:h-[150px]" />
</Section>
