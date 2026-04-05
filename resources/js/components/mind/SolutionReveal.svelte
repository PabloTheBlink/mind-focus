<script>
import ArrowRight from 'lucide-svelte/icons/arrow-right';
</script>

<section id="que-es" class="relative overflow-hidden bg-gradient-to-[160deg] from-[#12141F] via-[#151725] to-[#101220] px-5 py-[120px]">
	<!-- Background glow -->
	<div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_70%_40%,rgba(167,139,250,0.06)_0%,transparent_50%)]" />

	<div class="relative z-2 mx-auto max-w-[1100px]">
		<div class="mb-[80px] text-center">
			<p class="mb-[20px] text-[13px] font-semibold tracking-[3px] text-[#00D4FF] uppercase">La solución</p>
			<h2 class="mb-[30px] text-[56px] font-extrabold tracking-[-1.5px] leading-[1.15] text-white">
				Vuelca el caos.
				<span class="block text-[#00D4FF]">Obtén claridad.</span>
			</h2>
			<p class="mx-auto max-w-[750px] text-[20px] font-light leading-[1.7] text-[#D1D5DB]">
				MIND-FOCUS no es un gestor de tareas. Es un desbloqueador mental. Escribes todo lo que tienes en la cabeza - sin orden, sin estructura, sin filtros - y una IA lo transforma en una lista priorizada con una única acción concreta para empezar ya.
			</p>
		</div>

		<div class="mx-auto grid max-w-[1000px] grid-cols-[1fr_auto_1fr] items-center gap-10">
			<!-- Before: Tu mente ahora -->
			<div class="rounded-lg border border-white/[0.08] bg-white/[0.03] p-10">
				<p class="mb-[25px] text-[12px] font-semibold tracking-[2px] text-[#9CA3AF] uppercase">Tu mente ahora</p>
				<div class="flex flex-col gap-3">
					<p class="text-[14px] leading-[1.5] text-[#D1D5DB] opacity-70">Revisar el diseño del cliente</p>
					<p class="text-[14px] leading-[1.5] text-[#D1D5DB] opacity-50">Llamar al médico</p>
					<p class="text-[14px] leading-[1.5] text-[#D1D5DB] opacity-80">Idea para el producto nuevo</p>
					<p class="text-[14px] leading-[1.5] text-[#D1D5DB] opacity-40">Responder emails pendientes</p>
					<p class="text-[14px] leading-[1.5] text-[#D1D5DB] opacity-60">Investigar sobre esa API</p>
					<p class="text-[14px] leading-[1.5] text-[#D1D5DB] opacity-50">...</p>
				</div>
			</div>

			<!-- Arrow / IA -->
			<div class="flex flex-col items-center gap-[10px]">
				<div class="flex h-[60px] w-[60px] items-center justify-center rounded-full bg-gradient-to-br from-[#00D4FF] to-[#A78BFA]">
					<ArrowRight class="size-6" color="#0A0A0A" stroke-width="2.5" />
				</div>
				<p class="text-[11px] font-semibold tracking-[1px] text-[#00D4FF] uppercase">IA</p>
			</div>

			<!-- After: MIND-FOCUS -->
			<div class="rounded-lg border border-[#00D4FF]/[0.2] bg-[#00D4FF]/[0.06] p-10">
				<p class="mb-[25px] text-[12px] font-semibold tracking-[2px] text-[#00D4FF] uppercase">MIND-FOCUS</p>
				<div class="flex flex-col gap-4">
					<div class="flex items-center gap-3">
						<div class="h-2 w-2 rounded-full bg-[#00D4FF]" />
						<p class="text-[15px] font-semibold text-white">Revisa el diseño del cliente - Urgente para reunión de mañana</p>
					</div>
					<div class="flex items-center gap-3 opacity-60">
						<div class="h-2 w-2 rounded-full bg-[#9CA3AF]" />
						<p class="text-[14px] text-[#D1D5DB]">Pide cita médica - 5 minutos</p>
					</div>
					<div class="flex items-center gap-3 opacity-40">
						<div class="h-2 w-2 rounded-full bg-[#9CA3AF]" />
						<p class="text-[14px] text-[#D1D5DB]">Documenta la idea del producto en tu backlog</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
