<script>
import { Link } from '@inertiajs/svelte';
import ArrowLeft from 'lucide-svelte/icons/arrow-left';
import { home } from '@/routes';
import Logo from './Logo.svelte';
</script>

<header class="sticky top-0 z-[100] flex items-center justify-between border-b border-border-dark-medium bg-dark-bg/80 px-10 py-[18px] backdrop-blur-[12px]">
	<Logo size="lg" inline />
	<div class="flex items-center gap-[25px]">
		<Link
			href={home()}
			class="flex items-center gap-[5px] text-[13px] font-medium text-text-muted no-underline hover:text-white"
		>
			Volver a la web
			<ArrowLeft class="size-3.5" aria-hidden="true" />
		</Link>
	</div>
</header>
