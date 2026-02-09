<?php

namespace App\Http\Controllers;

use App\Models\Cooperative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CooperativeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage,cooperative')->only(['edit','update','destroy']);
    }

    public function create()
    {
        return view('admin.cooperatives.create');
    }

    public function store(Request $request)
    {
        $this->authorize('access-admin');
        $data = $request->validate([
            'name'=>'required|string|max:191',
            'sector'=>'nullable|string',
            'region'=>'nullable|string',
            'description'=>'nullable|string',
            'link'=>'nullable|url',
            'status'=>'required|in:pending,active,suspended,archived',
        ]);
        Cooperative::create($data);
        return redirect()->route('admin.cooperatives.index')->with('success','Created');
    }

    public function importDefault()
    {
        $this->authorize('access-admin');

        $defaults = [
            ['name'=>'ATEC Employees Multi-Purpose Cooperative','description'=>'Provide financial services, merchandise, and employee welfare benefits.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao Athletes Basic School Vendors Consumers Cooperative','description'=>'Serve school vendors and consumers through retail services, savings, and support.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabueños Transport Cooperative (CabTransCo)','description'=>'Operate modern jeepney and shuttle transport services.','sector'=>'Transport','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao Agriculture and Fishery Multi-Purpose Cooperative (CAFMPC)','description'=>'Support farmers and fisherfolk with inputs, credit, and training.','sector'=>'Agriculture','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao Cityhall Vendors Producers Cooperative','description'=>'Assist city hall vendors and producers with savings and marketing support.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao Employees Multi-Purpose Cooperative (CEMPCO)','description'=>'Provide emergency loans, savings, and welfare services to city hall employees.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao Malasakit Producers Cooperative','description'=>'Support sustainable agricultural production such as mushrooms and aquaponics.','sector'=>'Agriculture','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao Market Vendors Multi-Purpose Cooperative (CAMAVEMCO)','description'=>'Provide MSME loans and business assistance to public market vendors.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao National High School Personnel Multi-Purpose Cooperative','description'=>'Offer credit, savings, and welfare services to school personnel.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao OFW Consumers Cooperative (CAOFWCOOP)','description'=>'Support overseas Filipino workers and their families through consumer goods and savings.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao School Personnel Multi-Purpose Cooperative','description'=>'Provide financial and welfare support to school personnel.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao Solo Parent Producers Cooperative','description'=>'Assist solo parents through livelihood and income-generating activities.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Casile–Guinting Upland Marketing Cooperative','description'=>'Market agricultural products of upland farmers.','sector'=>'Agriculture','region'=>'Region 1','status'=>'active'],
            ['name'=>'Driving N Safety Transport Cooperative','description'=>'Provide safe and reliable transport services with emphasis on road safety.','sector'=>'Transport','region'=>'Region 1','status'=>'active'],
            ['name'=>'Fastech Employees Multi-Purpose Cooperative (FEMCO)','description'=>'Provide credit, merchandise, calamity loans, and welfare services to employees.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Foodlink Advocacy Cooperative','description'=>'Conduct seminars, trainings, mentoring, and advocacy programs.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Go Ladies Producers Cooperative','description'=>'Empower women through production, marketing, and livelihood activities.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'J.A. Services Cooperative','description'=>'Provide labor and manpower services.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Kaagapay ng Pamilya Credit Cooperative','description'=>'Offer credit and savings services focused on family welfare.','sector'=>'Finance','region'=>'Region 1','status'=>'active'],
            ['name'=>'Kababaihan, Kaibigan ng Bigaa Multi-Purpose Cooperative (KABIG MPC)','description'=>'Provide diversified livelihood opportunities for women.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Lakas Kababaihan Credit Cooperative','description'=>'Provide credit and savings services for women.','sector'=>'Finance','region'=>'Region 1','status'=>'active'],
            ['name'=>'Mamatid–Festival Mall Transport and Multi-Purpose Cooperative (MAFESTCO)','description'=>'Operate van transport services and provide member benefits.','sector'=>'Transport','region'=>'Region 1','status'=>'active'],
            ['name'=>'Masigasig na Kababaihan ng Baclaran STK Credit Cooperative','description'=>'Provide credit and savings services to women members.','sector'=>'Finance','region'=>'Region 1','status'=>'active'],
            ['name'=>'Mr. Veggies Multi-Purpose Cooperative','description'=>'Promote health, nutrition, and livelihood through vegetable trading and programs.','sector'=>'Agriculture','region'=>'Region 1','status'=>'active'],
            ['name'=>'Nagkakaisang Samahan ng mga Kababaihan ng Brgy. Sala STK Credit Cooperative','description'=>'Provide credit and savings services to women in the community.','sector'=>'Finance','region'=>'Region 1','status'=>'active'],
            ['name'=>'Nestlé Employees Multi-Purpose Cooperative (NEMPC)','description'=>'Provide loans, appliances, and consumer goods to factory workers.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'NEXPEREON Multi-Purpose Cooperative','description'=>'Provide comprehensive financial and consumer services to employees.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Pamana Agriculture Cooperative (PAGCO)','description'=>'Support agricultural production and farming activities.','sector'=>'Agriculture','region'=>'Region 1','status'=>'active'],
            ['name'=>'Pamantasan ng Cabuyao Multi-Purpose Cooperative (PNC-MPC)','description'=>'Provide financial and consumer services to the university community.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Quality Labor Service Cooperative (QLSC)','description'=>'Provide manpower and employment services.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Samahan ng Nagkakaisang Kababaihan Purok 1 Credit Cooperative (SANAKAP)','description'=>'Provide credit and savings services to women members.','sector'=>'Finance','region'=>'Region 1','status'=>'active'],
            ['name'=>'Sumiden Employees’ Multi-Purpose Cooperative (SUMECO)','description'=>'Provide financial and welfare services to employees.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Una ang Malasakit Imahe ng Tunay na Samahan Credit Cooperative (UMITS)','description'=>'Provide compassionate credit and savings services.','sector'=>'Finance','region'=>'Region 1','status'=>'active'],
            ['name'=>'Wyeth Philippines Employees Multi-Purpose Cooperative','description'=>'Provide credit services and employee recognition benefits.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
        ];

        $created = 0;
        foreach ($defaults as $d) {
            $c = Cooperative::firstOrCreate(['name' => $d['name']], $d);
            if ($c->wasRecentlyCreated) $created++;
        }

        return redirect()->route('admin.cooperatives.index')->with('success', "Imported $created cooperatives.");
    }

    public function index()
    {
        // return all cooperatives (no pagination) for admin listing
        $cooperatives = Cooperative::orderBy('name')->get();
        return view('admin.cooperatives.index', compact('cooperatives'));
    }

    /**
     * Read-only overview for administrators (no edit/delete controls).
     */
    public function overview()
    {
        $this->authorize('access-admin');
        $cooperatives = Cooperative::orderBy('name')->get();
        return view('admin.cooperatives.view', compact('cooperatives'));
    }

    public function edit(Cooperative $cooperative)
    {
        return view('admin.cooperatives.edit', compact('cooperative'));
    }

    public function update(Request $request, Cooperative $cooperative)
    {
        $this->authorize('manage', $cooperative);
        $data = $request->validate([
            'name'=>'required|string|max:191',
            'sector'=>'nullable|string',
            'region'=>'nullable|string',
            'description'=>'nullable|string',
            'link'=>'nullable|url',
            'status'=>'required|in:pending,active,suspended,archived',
        ]);
        $cooperative->update($data);
        return redirect()->route('admin.cooperatives.index')->with('success','Updated');
    }

    public function destroy(Cooperative $cooperative)
    {
        $this->authorize('manage', $cooperative);
        $cooperative->delete();
        return redirect()->route('admin.cooperatives.index')->with('success', 'Deleted');
    }
}
