<script>
/**
 * Reusable feature card for the features grid section.
 *
 * @param {string} title - Card title
 * @param {string} description - Card description
 */
let { title, description } = $props();
</script>

<div class="rounded-lg border border-border-dark-subtle bg-dark-surface p-[35px]">
	<h3 class="mb-[12px] text-[20px] font-bold text-white">{title}</h3>
	<p class="text-[15px] leading-[1.6] text-text-secondary">{description}</p>
</div>
