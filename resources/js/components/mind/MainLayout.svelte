<script>
import { Link } from '@inertiajs/svelte';
import { app } from '@/routes';
import Logo from './Logo.svelte';
import Menu from 'lucide-svelte/icons/menu';
import X from 'lucide-svelte/icons/x';

let { children } = $props();
let mobileMenuOpen = $state(false);
</script>

<div class="relative min-h-screen overflow-x-hidden bg-[#0A0A0A] font-sans text-white">
	<!-- Sticky nav -->
	<header class="sticky top-0 z-[1000] flex items-center justify-between border-b border-border-dark-subtle bg-[#0A0A0A]/95 px-4 py-[15px] backdrop-blur-[10px] sm:px-6 sm:py-[20px] md:px-10 md:py-[25px]">
		<Logo size="xl" inline />
		
		<!-- Desktop navigation -->
		<div class="hidden items-center gap-[30px] md:flex">
			<a href="#que-es" class="text-[14px] font-medium text-text-primary no-underline hover:text-white">Qué es</a>
			<a href="#demo" class="text-[14px] font-medium text-text-primary no-underline hover:text-white">Demo</a>
			<a href="#como-funciona" class="text-[14px] font-medium text-text-primary no-underline hover:text-white">Cómo funciona</a>
			<a href="#testimonios" class="text-[14px] font-medium text-text-primary no-underline hover:text-white">Testimonios</a>
			<Link
				href={app()}
				class="rounded bg-cyan px-6 py-[10px] text-[14px] font-bold tracking-[0.5px] text-[#0A0A0A] transition-all hover:shadow-[0_0_30px_rgba(0,212,255,0.4)]"
			>
				Empezar
			</Link>
		</div>
		
		<!-- Mobile menu button -->
		<button
			type="button"
			class="rounded p-2 text-white md:hidden"
			onclick={() => (mobileMenuOpen = !mobileMenuOpen)}
			aria-label={mobileMenuOpen ? 'Cerrar menú' : 'Abrir menú'}
		>
			{#if mobileMenuOpen}
				<X class="size-6" aria-hidden="true" />
			{:else}
				<Menu class="size-6" aria-hidden="true" />
			{/if}
		</button>
	</header>
	
	<!-- Mobile navigation -->
	{#if mobileMenuOpen}
		<div class="fixed inset-0 z-[999] top-[60px] bg-[#0A0A0A]/98 backdrop-blur-[10px] md:hidden">
			<div class="flex flex-col items-center justify-center gap-8 pt-20">
				<a
					href="#que-es"
					class="text-[18px] font-medium text-text-primary no-underline hover:text-white"
					onclick={() => (mobileMenuOpen = false)}
				>
					Qué es
				</a>
				<a
					href="#demo"
					class="text-[18px] font-medium text-text-primary no-underline hover:text-white"
					onclick={() => (mobileMenuOpen = false)}
				>
					Demo
				</a>
				<a
					href="#como-funciona"
					class="text-[18px] font-medium text-text-primary no-underline hover:text-white"
					onclick={() => (mobileMenuOpen = false)}
				>
					Cómo funciona
				</a>
				<a
					href="#testimonios"
					class="text-[18px] font-medium text-text-primary no-underline hover:text-white"
					onclick={() => (mobileMenuOpen = false)}
				>
					Testimonios
				</a>
				<Link
					href={app()}
					class="mt-4 rounded bg-cyan px-8 py-[14px] text-[16px] font-bold tracking-[0.5px] text-[#0A0A0A] transition-all hover:shadow-[0_0_30px_rgba(0,212,255,0.4)]"
					onclick={() => (mobileMenuOpen = false)}
				>
					Empezar
				</Link>
			</div>
		</div>
	{/if}

	<!-- Main content -->
	{@render children()}
</div>
