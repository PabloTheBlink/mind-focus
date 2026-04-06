<script>
import FeatureCard from './FeatureCard.svelte';
</script>

<section class="relative bg-gradient-to-[160deg] from-dark-bg via-[#0B0C12] to-dark-bg-alt px-5 py-[100px]">
	<!-- Top line -->
	<div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-cyan/15 to-transparent" />

	<div class="mx-auto max-w-[1100px]">
		<div class="mb-[70px] text-center">
			<p class="mb-[20px] text-[13px] font-semibold tracking-[3px] text-cyan uppercase">Por qué es diferente</p>
			<h2 class="mb-[20px] text-[44px] font-bold tracking-[-1px] leading-[1.2] text-white">
				No organiza tu caos. Lo elimina.
			</h2>
		</div>

		<div class="grid gap-[30px] sm:grid-cols-2 lg:grid-cols-3">
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
