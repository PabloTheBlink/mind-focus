<script>
/**
 * Reusable section wrapper for mind/ landing page sections.
 * Provides consistent gradient background, padding, and max-width container.
 *
 * @param {string} [id] - Section id attribute
 * @param {string} [class:className=''] - Additional classes for the section element (will override defaults if provided)
 * @param {string} [innerClass=''] - Additional classes for the inner container
 * @param {boolean} [useDefaults=true] - Whether to use default section styling
 */
let { id, class: className = '', innerClass = '', useDefaults = true, children } = $props();

const sectionBase = 'relative bg-gradient-to-[160deg] from-dark-bg via-[#0B0C12] to-dark-bg-alt px-5 py-[100px]';
const sectionClasses = $derived(useDefaults ? `${sectionBase} ${className}` : `relative ${className}`);
</script>

<section {id} class={sectionClasses}>
	<div class="mx-auto max-w-[1100px] {innerClass}">
		{@render children()}
	</div>
</section>
