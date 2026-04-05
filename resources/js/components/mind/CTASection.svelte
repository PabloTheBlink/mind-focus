<script>
import { Link } from '@inertiajs/svelte';
import { app } from '@/routes';
</script>
<section class="relative overflow-hidden bg-gradient-to-[160deg] from-[#0A0C14] via-[#0E1020] to-[#080A12] px-5 py-[120px]">
	<!-- Background glow -->
	<div class="pointer-events-none absolute inset-x-0 inset-0 bg-[radial-gradient(ellipse_at_50%_50%,rgba(0,212,255,0.07)_0%,transparent_50%)]" />
	<!-- Top line -->
	<div class="absolute left-[15%] right-[15%] top-0 h-[1px] bg-gradient-to-r from-transparent via-[#00D4FF]/[0.25] to-transparent" />

	<div class="relative z-2 mx-auto max-w-[800px] text-center">
		<h2 class="mb-[25px] text-[52px] font-extrabold tracking-[-1.5px] leading-[1.15] text-white">
			Tu mente merece
			<span class="block text-[#00D4FF]">empezar ya.</span>
		</h2>
		<p class="mb-[50px] text-[20px] font-light leading-[1.7] text-[#9CA3AF]">
			Deja de organizar el caos. Elimínalo.
		</p>
		<Link
			href={app()}
			class="inline-block rounded bg-[#00D4FF] px-[60px] py-[20px] text-[17px] font-bold tracking-[1px] uppercase text-[#0A0A0A] shadow-[0_0_60px_rgba(0,212,255,0.3)] transition-all hover:shadow-[0_0_80px_rgba(0,212,255,0.5)] no-underline"
		>
			Desbloquea tu mente
		</Link>
		<p class="mt-[25px] text-[13px] text-[#6B7280]">Sin tarjeta. Sin registro obligatorio. Sin compromisos.</p>
	</div>
</section>
