<script>
import { Link } from '@inertiajs/svelte';
import { externalLinks } from '@/lib/links';
import Logo from './Logo.svelte';
</script>

<footer class="bg-gradient-to-b from-[#060608] to-[#040405] px-5 pt-[60px] pb-[40px] border-t border-border-dark-faint">
	<div class="mx-auto max-w-[1100px]">
		<div class="mb-[50px] flex flex-wrap justify-between gap-10">
			<!-- Brand -->
			<div class="min-w-[250px] flex-1">
				<div class="mb-[15px]">
					<Logo size="lg" inline />
				</div>
				<p class="max-w-[300px] text-[14px] leading-[1.6] text-text-muted">
					Desbloquea tu mente. Elimina el ruido. Empieza a actuar.
				</p>
			</div>

			<!-- Links -->
			<div class="flex flex-wrap gap-[60px]">
				<div>
					<p class="mb-[20px] text-[12px] font-semibold tracking-[2px] text-text-secondary uppercase">Producto</p>
					<div class="flex flex-col gap-3">
						<a href="#features" class="text-[14px] text-text-primary no-underline hover:text-white">Características</a>
						<a href="#pricing" class="text-[14px] text-text-primary no-underline hover:text-white">Precios</a>
						<a href="#demo" class="text-[14px] text-text-primary no-underline hover:text-white">Demo</a>
					</div>
				</div>
				<div>
					<p class="mb-[20px] text-[12px] font-semibold tracking-[2px] text-text-secondary uppercase">Compañía</p>
					<div class="flex flex-col gap-3">
						<a href="#about" class="text-[14px] text-text-primary no-underline hover:text-white">Sobre nosotros</a>
						<a href="#blog" class="text-[14px] text-text-primary no-underline hover:text-white">Blog</a>
						<a href="#contact" class="text-[14px] text-text-primary no-underline hover:text-white">Contacto</a>
					</div>
				</div>
				<div>
					<p class="mb-[20px] text-[12px] font-semibold tracking-[2px] text-text-secondary uppercase">Legal</p>
					<div class="flex flex-col gap-3">
						<a href={externalLinks.privacy} class="text-[14px] text-text-primary no-underline hover:text-white">Privacidad</a>
						<a href={externalLinks.terms} class="text-[14px] text-text-primary no-underline hover:text-white">Términos</a>
					</div>
				</div>
			</div>
		</div>

		<!-- Bottom bar -->
		<div class="flex flex-wrap items-center justify-between gap-5 border-t border-border-dark-faint pt-[30px]">
			<p class="text-[13px] text-text-muted">2026 MIND-FOCUS. Todos los derechos reservados.</p>
			<div class="flex gap-5">
				<a href={externalLinks.social.twitter} class="text-[13px] text-text-muted no-underline hover:text-white">Twitter</a>
				<a href={externalLinks.social.linkedin} class="text-[13px] text-text-muted no-underline hover:text-white">LinkedIn</a>
				<a href={externalLinks.social.github} class="text-[13px] text-text-muted no-underline hover:text-white">GitHub</a>
			</div>
		</div>
	</div>
</footer>
