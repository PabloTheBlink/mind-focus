<script>
import ChaosVisual from '@/components/mind/ChaosVisual.svelte';
import CTASection from '@/components/mind/CTASection.svelte';
import FeaturesGrid from '@/components/mind/FeaturesGrid.svelte';
import HeroSection from '@/components/mind/HeroSection.svelte';
import HowItWorks from '@/components/mind/HowItWorks.svelte';
import LandingFooter from '@/components/mind/LandingFooter.svelte';
import LiveDemo from '@/components/mind/LiveDemo.svelte';
import MainLayout from '@/components/mind/MainLayout.svelte';
import ProblemStatement from '@/components/mind/ProblemStatement.svelte';
import SolutionReveal from '@/components/mind/SolutionReveal.svelte';
import Testimonials from '@/components/mind/Testimonials.svelte';
</script>

<MainLayout>
	<HeroSection />
	<ChaosVisual />
	<ProblemStatement />
	<SolutionReveal />
	<LiveDemo />
	<HowItWorks />
	<FeaturesGrid />
	<Testimonials />
	<CTASection />
	<LandingFooter />
</MainLayout>
