<script>
import Pen from 'lucide-svelte/icons/pen';
import Clock from 'lucide-svelte/icons/clock';
import Check from 'lucide-svelte/icons/check';
</script>

<section id="como-funciona" class="relative mx-auto max-w-[1200px] bg-gradient-to-[160deg] from-[#0E1015] via-[#131520] to-[#0F1118] px-5 py-[100px]">
	<!-- Top line -->
	<div class="absolute left-[5%] right-[5%] top-0 h-[1px] bg-gradient-to-r from-transparent via-[rgba(167,139,250,0.2)] to-transparent" />
	<!-- Background glow -->
	<div class="pointer-events-none absolute top-1/2 right-[15%] h-[500px] w-[500px] -translate-y-1/2 rounded-full bg-[radial-gradient(circle,rgba(167,139,250,0.05)_0%,transparent_60%)]" />

	<div class="relative z-2">
		<div class="mb-[80px] text-center">
			<p class="mb-[20px] text-[13px] font-semibold tracking-[3px] text-[#A78BFA] uppercase">Cómo funciona</p>
			<h2 class="mb-[25px] text-[48px] font-bold tracking-[-1px] leading-[1.2] text-white">Tres pasos. Cero fricción.</h2>
		</div>

		<div class="grid gap-[50px] sm:grid-cols-2 lg:grid-cols-3">
			<!-- Step 01: Escribir -->
			<div class="relative">
				<div class="absolute top-[-30px] left-[-10px] z-0 text-[96px] font-black leading-[1] text-[rgba(0,212,255,0.08)]">01</div>
				<div class="relative z-1 p-[30px]">
					<div class="mb-[25px] flex h-[50px] w-[50px] items-center justify-center rounded-xl bg-gradient-to-br from-[#00D4FF]/[0.15] to-[#00D4FF]/[0.03]">
						<Pen class="size-6" color="#00D4FF" stroke-width="1.5" />
					</div>
					<h3 class="mb-[15px] text-[24px] font-bold text-white">Escribir</h3>
					<p class="text-[16px] leading-[1.7] text-[#9CA3AF]">
						Vuelca todo lo que tienes en la cabeza. Ideas, tareas, preocupaciones, recordatorios. Sin orden. Sin estructura. Sin juicio. Solo escribe.
					</p>
				</div>
			</div>

			<!-- Step 02: Procesar -->
			<div class="relative">
				<div class="absolute top-[-30px] left-[-10px] z-0 text-[96px] font-black leading-[1] text-[rgba(167,139,250,0.08)]">02</div>
				<div class="relative z-1 p-[30px]">
					<div class="mb-[25px] flex h-[50px] w-[50px] items-center justify-center rounded-xl bg-gradient-to-br from-[#A78BFA]/[0.15] to-[#A78BFA]/[0.03]">
						<Clock class="size-6" color="#A78BFA" stroke-width="1.5" />
					</div>
					<h3 class="mb-[15px] text-[24px] font-bold text-white">Procesar</h3>
					<p class="text-[16px] leading-[1.7] text-[#9CA3AF]">
						La IA analiza tu texto, identifica patrones, prioriza por urgencia e impacto, y elimina el ruido. Tu cerebro no tiene que hacerlo solo.
					</p>
				</div>
			</div>

			<!-- Step 03: Actuar -->
			<div class="relative">
				<div class="absolute top-[-30px] left-[-10px] z-0 text-[96px] font-black leading-[1] text-[rgba(0,212,255,0.08)]">03</div>
				<div class="relative z-1 p-[30px]">
					<div class="mb-[25px] flex h-[50px] w-[50px] items-center justify-center rounded-xl bg-gradient-to-br from-[#00D4FF]/[0.15] to-[#00D4FF]/[0.03]">
						<Check class="size-6" color="#00D4FF" stroke-width="1.5" />
					</div>
					<h3 class="mb-[15px] text-[24px] font-bold text-white">Actuar</h3>
					<p class="text-[16px] leading-[1.7] text-[#9CA3AF]">
						Recibes una lista clara y accionable con una única recomendación: qué hacer ahora. Sin parálisis por análisis. Solo acción.
					</p>
				</div>
			</div>
		</div>
	</div>
</section>
