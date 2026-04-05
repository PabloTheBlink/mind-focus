<script>
import AppLayout from '@/components/mind/AppLayout.svelte';
import AppHeader from '@/components/mind/AppHeader.svelte';
import AppFooter from '@/components/mind/AppFooter.svelte';
import InputArea from '@/components/mind/InputArea.svelte';

let { text = '', structuredData = null } = $props();
let initialText = $derived(text);
</script>

<AppLayout>
	<AppHeader />
	<div class="relative z-1 mx-auto w-full max-w-[1100px]">
		<InputArea {initialText} {structuredData} />
	</div>
	<AppFooter />
</AppLayout>
