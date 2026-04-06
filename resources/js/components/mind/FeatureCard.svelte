<script>
/**
 * Reusable feature card for the features grid section.
 *
 * @param {string} title - Card title
 * @param {string} description - Card description
 */
let { title, description } = $props();
</script>

<div class="rounded-lg border border-white/[0.06] bg-white/[0.02] p-[35px]">
	<h3 class="mb-[12px] text-[20px] font-bold text-white">{title}</h3>
	<p class="text-[15px] leading-[1.6] text-[#9CA3AF]">{description}</p>
</div>
