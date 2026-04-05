<script>
import InputArea from '@/components/mind/InputArea.svelte';

let {
	text = `# 🚀 Trabajo y Proyectos

## Tareas Pendientes
- [ ] Terminar la presentación para el cliente (URGENTE) [1 hora]
  > El borrador final debe estar listo antes de las 18:00 para la reunión del lunes.
- [ ] Revisar el presupuesto del Q2 (IMPORTANTE) [30 min]
  > El margen operativo parece haber variado un 2%. Hay que ajustarlo para el equipo de ventas.

## Ideas Creativas
- Nueva estrategia de contenidos para el blog #idea #marketing
  > Podríamos usar testimonios de clientes en formato vídeo para generar más confianza.

# 🏠 Vida Personal y Hogar

## Pendientes de Casa
- [ ] Hacer la lista de la compra [15 min]
  > Faltan lácteos, fruta fresca y detergente para la ropa. Hay que pasar por el súper de la esquina.
- [ ] Llamar al seguro del coche [10 min]
  > El último recibo es sospechosamente alto. Quizás hay un error en la póliza.

## Bienestar y Salud
- Apuntarse a clases de Yoga #salud #personal #bienestar
  > He visto un estudio cerca de casa que tiene buenas reseñas y clases los martes por la tarde.`,
	currentText = '',
	structuredData = null,
} = $props();
</script>

<section id="demo" class="relative bg-gradient-to-[160deg] from-[#0D0E15] via-[#111320] to-[#0E0F18] px-5 py-[100px]">
	<div class="relative z-1 mx-auto w-full">
		<InputArea initialText={text} {currentText} {structuredData} />
	</div>
</section>
