<script>
import FeatureCard from './FeatureCard.svelte';
</script>

<section class="relative bg-gradient-to-[160deg] from-dark-bg via-[#0B0C12] to-dark-bg-alt px-4 py-[50px] sm:px-5 sm:py-[100px]">
	<!-- Top line -->
	<div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-cyan/15 to-transparent" />

	<div class="mx-auto max-w-[1100px]">
		<div class="mb-10 text-center sm:mb-[70px]">
			<p class="mb-3 text-[11px] font-semibold tracking-[3px] text-cyan uppercase sm:mb-[20px] sm:text-[13px]">Por qué es diferente</p>
			<h2 class="mb-3 text-[32px] font-bold tracking-[-1px] leading-[1.2] text-white sm:mb-[20px] sm:text-[44px]">
				No organiza tu caos. Lo elimina.
			</h2>
		</div>

		<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 sm:gap-[30px]">
			<FeatureCard
				title="Entrada libre"
				description="Sin categorías, sin etiquetas, sin estructura previa. Escribe como piensas. La IA se encarga del resto."
			/>
			<FeatureCard
				title="Priorización inteligente"
				description="No todas las tareas pesan igual. MIND-FOCUS identifica qué es urgente, qué es importante y qué puede esperar."
			/>
			<FeatureCard
				title="Una acción a la vez"
				description="Te da una única recomendación clara. Sin listas interminables. Sin abrumación. Solo el siguiente paso."
			/>
			<FeatureCard
				title="Aprende de ti"
				description="Cuantos más brain dumps haces, mejor entiende tus patrones, plazos y qué te genera más ansiedad."
			/>
			<FeatureCard
				title="Sin curva de aprendizaje"
				description="No hay tutorial que estudiar. No hay configuración compleja. Abres, escribes, recibes claridad."
			/>
			<FeatureCard
				title="Privado y local"
				description="Tus pensamientos no se comparten. Todo se procesa de forma segura y puedes usarlo sin cuenta."
			/>
		</div>
	</div>
</section>
