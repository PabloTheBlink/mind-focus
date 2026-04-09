<script>
import { Link } from '@inertiajs/svelte';
import { app } from '@/routes';
import Section from '@/components/mind/Section.svelte';
</script>

<Section
	useDefaults={false}
	className="overflow-hidden bg-gradient-to-[160deg] from-[#0A0C14] via-[#0E1020] to-[#080A12] px-4 py-[60px] sm:px-5 sm:py-[120px]"
	innerClass="relative z-2 mx-auto max-w-[800px] text-center"
>
	<!-- Background glow -->
	<div class="pointer-events-none absolute inset-x-0 inset-0 bg-[radial-gradient(ellipse_at_50%_50%,rgba(0,212,255,0.07)_0%,transparent_50%)]" />
	<!-- Top line -->
	<div class="absolute left-[15%] right-[15%] top-0 h-[1px] bg-gradient-to-r from-transparent via-cyan/25 to-transparent" />

	<h2 class="mb-4 text-[36px] font-extrabold tracking-[-1.5px] leading-[1.15] text-white sm:mb-[25px] sm:text-[52px]">
		Tu mente merece
		<span class="block text-cyan">empezar ya.</span>
	</h2>
	<p class="mb-8 text-[16px] font-light leading-[1.7] text-text-secondary sm:mb-[50px] sm:text-[20px]">
		Deja de organizar el caos. Elimínalo.
	</p>
	<Link
		href={app()}
		class="inline-block w-full rounded bg-cyan px-8 py-[16px] text-[15px] font-bold tracking-[1px] uppercase text-[#0A0A0A] shadow-[0_0_60px_rgba(0,212,255,0.3)] transition-all hover:shadow-[0_0_80px_rgba(0,212,255,0.5)] no-underline sm:w-auto sm:px-[60px] sm:py-[20px] sm:text-[17px]"
	>
		Desbloquea tu mente
	</Link>
	<p class="mt-4 text-[12px] text-text-muted sm:mt-[25px] sm:text-[13px]">Sin tarjeta. Sin registro obligatorio. Sin compromisos.</p>
</Section>
