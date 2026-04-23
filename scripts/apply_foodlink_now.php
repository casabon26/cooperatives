<?php
// One-off script to update cooperative id 16 with provided Foodlink content.
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();


use App\Models\Cooperative;

$coopId = 16;
$coop = Cooperative::find($coopId);
if (!$coop) {
    echo "Cooperative id {$coopId} not found.\n";
    exit(1);
}

$mission = "To eradicate poverty by promoting inclusive and sustainable development in Philippine food systems through agribusiness expertise and collaboration.";
$vision = "A sustainable and inclusive Philippine food system where smallholder farmers are empowered and poverty is reduced through strong linkages and innovation.";

$about = "Foodlink Advocacy Co-operative (FAC) is a talent pool of agribusiness experts and practitioners with a common passion working towards poverty eradication through inclusive and sustainable development in Philippine food systems. FAC is a primary advocacy cooperative duly registered with the Cooperative Development Authority.";

$purpose = "The agricultural sector of the Philippines has long faced structural challenges. Despite vast arable land and a strong agrarian workforce, the country continues to struggle with food insecurity, inefficient supply chains, and persistent rural poverty. At the Foodlink Advocacy Cooperative (FAC), we believe this is not due to a lack of labor or resources, but to weak market linkages, unequal access to information, and imbalanced power structures. Our mission is to address these gaps by promoting inclusive and sustainable food systems. We recognize smallholder farmers as experts of their own context—valuing their knowledge and integrating them as equal partners in agribusiness, rather than treating them merely as beneficiaries.";

$services = "Research-for-Development (R4D): Leveraging the rich experience of our members, we quickly generate in-depth and holistic knowledge materials. Together with partners in development and academe, we form capable research teams across the Philippines and ensure research contributes to tangible developmental objectives. Technical Advisory & Consultancy: Expert advisory across all stages of project management—from planning to implementation and exit—applying rigorous R4D discipline for resilient, scalable solutions. Advocacy & Capacity Building: Resource speakers and trainers translating complex food system concepts into actionable insights for farmers, corporate partners, government and international organizations.";

$future = "We advance a systems-based approach to food security focusing on access, stability, agency, and sustainability. We drive innovation through cooperative competition ('co-opetition') and champion locally-led development and private-sector-driven investment models. We also explore the use of Artificial Intelligence to support institutional leverage points, guided by strict standards on transparency and authorship.";

// Update cooperative fields directly (some installs don't have a separate cooperative_profiles table)
$coop->mission = $mission;
$coop->vision = $vision;
$coop->objectives = $purpose;
$coop->services = $services . "\n\n" . $future;
$coop->description = $about;
$coop->save();

echo "Applied Foodlink content to cooperative id {$coopId} (updated main cooperative columns).\n";
echo "Mission: " . ($coop->mission ? 'set' : 'empty') . "\n";
echo "Vision: " . ($coop->vision ? 'set' : 'empty') . "\n";
exit(0);
