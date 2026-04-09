<script>
import ArrowRight from 'lucide-svelte/icons/arrow-right';
</script>

<section id="que-es" class="relative overflow-hidden bg-gradient-to-[160deg] from-[#12141F] via-[#151725] to-[#101220] px-4 py-[60px] sm:px-5 sm:py-[120px]">
	<!-- Background glow -->
	<div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_70%_40%,rgba(167,139,250,0.06)_0%,transparent_50%)]" />

	<div class="relative z-2 mx-auto max-w-[1100px]">
		<div class="mb-10 text-center sm:mb-[80px]">
			<p class="mb-3 text-[11px] font-semibold tracking-[3px] text-cyan uppercase sm:mb-[20px] sm:text-[13px]">La solución</p>
			<h2 class="mb-5 text-[36px] font-extrabold tracking-[-1.5px] leading-[1.15] text-white sm:mb-[30px] sm:text-[56px]">
				Vuelca el caos.
				<span class="block text-cyan">Obtén claridad.</span>
			</h2>
			<p class="mx-auto max-w-[750px] text-[16px] font-light leading-[1.7] text-text-primary sm:text-[20px]">
				MIND-FOCUS no es un gestor de tareas. Es un desbloqueador mental. Escribes todo lo que tienes en la cabeza - sin orden, sin estructura, sin filtros - y una IA lo transforma en una lista priorizada con una única acción concreta para empezar ya.
			</p>
		</div>

		<div class="mx-auto grid max-w-[1000px] grid-cols-1 items-center gap-6 lg:grid-cols-[1fr_auto_1fr] lg:gap-10">
			<!-- Before: Tu mente ahora -->
			<div class="rounded-lg border border-white/[0.08] bg-white/[0.03] p-6 sm:p-10">
				<p class="mb-4 text-[11px] font-semibold tracking-[2px] text-text-secondary uppercase sm:mb-[25px] sm:text-[12px]">Tu mente ahora</p>
				<div class="flex flex-col gap-2 sm:gap-3">
					<p class="text-[13px] leading-[1.5] text-text-primary opacity-70 sm:text-[14px]">Revisar el diseño del cliente</p>
					<p class="text-[13px] leading-[1.5] text-text-primary opacity-50 sm:text-[14px]">Llamar al médico</p>
					<p class="text-[13px] leading-[1.5] text-text-primary opacity-80 sm:text-[14px]">Idea para el producto nuevo</p>
					<p class="text-[13px] leading-[1.5] text-text-primary opacity-40 sm:text-[14px]">Responder emails pendientes</p>
					<p class="text-[13px] leading-[1.5] text-text-primary opacity-60 sm:text-[14px]">Investigar sobre esa API</p>
					<p class="text-[13px] leading-[1.5] text-text-primary opacity-50 sm:text-[14px]">...</p>
				</div>
			</div>

			<!-- Arrow / IA -->
			<div class="flex flex-col items-center gap-2 sm:gap-[10px]">
				<div class="flex h-[50px] w-[50px] items-center justify-center rounded-full bg-gradient-to-br from-cyan to-purple sm:h-[60px] sm:w-[60px]">
					<ArrowRight class="size-5 text-[#0A0A0A] sm:size-6" stroke-width="2.5" aria-hidden="true" />
				</div>
				<p class="text-[10px] font-semibold tracking-[1px] text-cyan uppercase sm:text-[11px]">IA</p>
			</div>

			<!-- After: MIND-FOCUS -->
			<div class="rounded-lg border border-cyan/20 bg-cyan/6 p-6 sm:p-10">
				<p class="mb-4 text-[11px] font-semibold tracking-[2px] text-cyan uppercase sm:mb-[25px] sm:text-[12px]">MIND-FOCUS</p>
				<div class="flex flex-col gap-3 sm:gap-4">
					<div class="flex items-start gap-3">
						<div class="mt-1 h-2 w-2 shrink-0 rounded-full bg-cyan"></div>
						<p class="text-[14px] font-semibold text-white sm:text-[15px]">Revisa el diseño del cliente - Urgente para reunión de mañana</p>
					</div>
					<div class="flex items-start gap-3 opacity-60">
						<div class="mt-1 h-2 w-2 shrink-0 rounded-full bg-text-secondary"></div>
						<p class="text-[13px] text-text-primary sm:text-[14px]">Pide cita médica - 5 minutos</p>
					</div>
					<div class="flex items-start gap-3 opacity-40">
						<div class="mt-1 h-2 w-2 shrink-0 rounded-full bg-text-secondary"></div>
						<p class="text-[13px] text-text-primary sm:text-[14px]">Documenta la idea del producto en tu backlog</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
