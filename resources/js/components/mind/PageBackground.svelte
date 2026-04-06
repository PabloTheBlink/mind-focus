<script>
let { children } = $props();
</script>

<!-- Fixed background that covers the body -->
<div class="pointer-events-none fixed inset-0 z-[-1] bg-[#0A0D14]" />
<!-- Gradient glow backgrounds -->
<div class="pointer-events-none fixed top-[-20%] right-[-10%] z-0 h-[600px] w-[600px] rounded-full bg-[radial-gradient(circle,rgba(0,212,255,0.04)_0%,transparent_60%)]" />
<div class="pointer-events-none fixed bottom-[-30%] left-[-15%] z-0 h-[800px] w-[800px] rounded-full bg-[radial-gradient(circle,rgba(167,139,250,0.03)_0%,transparent_55%)]" />

<!-- Main content wrapper -->
<div style="background: linear-gradient(160deg, #0A0D14 0%, #080B12 30%, #060810 100%); color: #FFFFFF; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;" class="relative min-h-screen overflow-x-hidden">
	{@render children()}
</div>
