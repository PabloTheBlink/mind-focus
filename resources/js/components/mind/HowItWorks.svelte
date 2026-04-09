<script>
import Check from 'lucide-svelte/icons/check';
import Clock from 'lucide-svelte/icons/clock';
import Pen from 'lucide-svelte/icons/pen';
</script>

<section id="como-funciona" class="relative mx-auto max-w-[1200px] bg-gradient-to-[160deg] from-[#0E1015] via-[#131520] to-[#0F1118] px-4 py-[50px] sm:px-5 sm:py-[100px]">
	<!-- Top line -->
	<div class="absolute left-[5%] right-[5%] top-0 h-[1px] bg-gradient-to-r from-transparent via-purple/20 to-transparent" />
	<!-- Background glow - hidden on mobile -->
	<div class="pointer-events-none absolute top-1/2 right-[15%] hidden h-[500px] w-[500px] -translate-y-1/2 rounded-full bg-[radial-gradient(circle,rgba(167,139,250,0.05)_0%,transparent_60%)] sm:block" />

	<div class="relative z-2">
		<div class="mb-10 text-center sm:mb-[80px]">
			<p class="mb-3 text-[11px] font-semibold tracking-[3px] text-purple uppercase sm:mb-[20px] sm:text-[13px]">Cómo funciona</p>
			<h2 class="mb-4 text-[32px] font-bold tracking-[-1px] leading-[1.2] text-white sm:mb-[25px] sm:text-[48px]">Tres pasos. Cero fricción.</h2>
		</div>

		<div class="grid gap-8 sm:grid-cols-2 sm:gap-[50px] lg:grid-cols-3">
			<!-- Step 01: Escribir -->
			<div class="relative">
				<div class="absolute top-[-20px] left-[-10px] z-0 text-[64px] font-black leading-[1] text-cyan/8 sm:top-[-30px] sm:text-[96px]">01</div>
				<div class="relative z-1 p-5 sm:p-[30px]">
					<div class="mb-4 flex h-[40px] w-[40px] items-center justify-center rounded-xl bg-gradient-to-br from-cyan/15 to-cyan/3 sm:mb-[25px] sm:h-[50px] sm:w-[50px]">
						<Pen class="size-5 text-cyan sm:size-6" stroke-width="1.5" aria-hidden="true" />
					</div>
					<h3 class="mb-3 text-[20px] font-bold text-white sm:mb-[15px] sm:text-[24px]">Escribir</h3>
					<p class="text-[14px] leading-[1.7] text-text-secondary sm:text-[16px]">
						Vuelca todo lo que tienes en la cabeza. Ideas, tareas, preocupaciones, recordatorios. Sin orden. Sin estructura. Sin juicio. Solo escribe.
					</p>
				</div>
			</div>

			<!-- Step 02: Procesar -->
			<div class="relative">
				<div class="absolute top-[-20px] left-[-10px] z-0 text-[64px] font-black leading-[1] text-purple/8 sm:top-[-30px] sm:text-[96px]">02</div>
				<div class="relative z-1 p-5 sm:p-[30px]">
					<div class="mb-4 flex h-[40px] w-[40px] items-center justify-center rounded-xl bg-gradient-to-br from-purple/15 to-purple/3 sm:mb-[25px] sm:h-[50px] sm:w-[50px]">
						<Clock class="size-5 text-purple sm:size-6" stroke-width="1.5" aria-hidden="true" />
					</div>
					<h3 class="mb-3 text-[20px] font-bold text-white sm:mb-[15px] sm:text-[24px]">Procesar</h3>
					<p class="text-[14px] leading-[1.7] text-text-secondary sm:text-[16px]">
						La IA analiza tu texto, identifica patrones, prioriza por urgencia e impacto, y elimina el ruido. Tu cerebro no tiene que hacerlo solo.
					</p>
				</div>
			</div>

			<!-- Step 03: Actuar -->
			<div class="relative">
				<div class="absolute top-[-20px] left-[-10px] z-0 text-[64px] font-black leading-[1] text-cyan/8 sm:top-[-30px] sm:text-[96px]">03</div>
				<div class="relative z-1 p-5 sm:p-[30px]">
					<div class="mb-4 flex h-[40px] w-[40px] items-center justify-center rounded-xl bg-gradient-to-br from-cyan/15 to-cyan/3 sm:mb-[25px] sm:h-[50px] sm:w-[50px]">
						<Check class="size-5 text-cyan sm:size-6" stroke-width="1.5" aria-hidden="true" />
					</div>
					<h3 class="mb-3 text-[20px] font-bold text-white sm:mb-[15px] sm:text-[24px]">Actuar</h3>
					<p class="text-[14px] leading-[1.7] text-text-secondary sm:text-[16px]">
						Recibes una lista clara y accionable con una única recomendación: qué hacer ahora. Sin parálisis por análisis. Solo acción.
					</p>
				</div>
			</div>
		</div>
	</div>
</section>
