<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Faq;

class SimpleAdminFaqsController extends Controller
{
    protected function ensureAdmin()
    {
        if (!session('admin_authenticated')) {
            abort(403, 'Forbidden');
        }
    }

    protected function storagePath()
    {
        return 'faqs.json';
    }

    protected function loadAll()
    {
        try {
            if (!Storage::exists($this->storagePath())) return [];
            $json = Storage::get($this->storagePath());
            $data = json_decode($json, true);
            if (!is_array($data)) return [];
            return $data;
        } catch (\Throwable $e) {
            return [];
        }
    }

    protected function saveAll(array $items)
    {
        Storage::put($this->storagePath(), json_encode(array_values($items), JSON_PRETTY_PRINT));
    }

    public function index()
    {
        $this->ensureAdmin();
        // Prefer DB-backed FAQs; fall back to JSON file if table missing or empty
        try {
            $items = Faq::orderByDesc('created_at')->get()->map(function($m){ return $m->toArray(); })->toArray();
        } catch (\Throwable $e) {
            $items = $this->loadAll();
        }
        return view('admin.faqs.index', compact('items'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
        ]);
        // Save to DB
        try {
            $faq = Faq::create([
                'question' => $data['question'],
                'answer' => $data['answer'],
            ]);
        } catch (\Throwable $e) {
            // DB write failed; fall back to JSON
            $items = $this->loadAll();
            $nextId = 1;
            foreach ($items as $it) { $nextId = max($nextId, intval($it['id'] ?? 0) + 1); }
            $now = now()->toDateTimeString();
            $items[] = [
                'id' => $nextId,
                'question' => $data['question'],
                'answer' => $data['answer'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $this->saveAll($items);
            return redirect('/admin/manage-faqs')->with('success', 'FAQ added (saved to JSON)');
        }

        // Sync JSON backup
        try {
            $all = Faq::orderByDesc('created_at')->get()->map(function($m){ return $m->toArray(); })->toArray();
            $this->saveAll($all);
        } catch (\Throwable $e) { /* ignore backup failure */ }

        return redirect('/admin/manage-faqs')->with('success', 'FAQ added');
    }

    public function edit($id)
    {
        $this->ensureAdmin();
        try {
            $m = Faq::findOrFail($id);
            $found = $m->toArray();
        } catch (\Throwable $e) {
            $items = $this->loadAll();
            $found = null;
            foreach ($items as $it) if (intval($it['id']) === intval($id)) { $found = $it; break; }
            if (!$found) abort(404);
        }
        return view('admin.faqs.edit', ['item' => $found]);
    }

    public function update(Request $request, $id)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
        ]);
        try {
            $m = Faq::findOrFail($id);
            $m->question = $data['question'];
            $m->answer = $data['answer'];
            $m->save();
        } catch (\Throwable $e) {
            $items = $this->loadAll();
            $found = false;
            foreach ($items as &$it) {
                if (intval($it['id']) === intval($id)) {
                    $it['question'] = $data['question'];
                    $it['answer'] = $data['answer'];
                    $it['updated_at'] = now()->toDateTimeString();
                    $found = true; break;
                }
            }
            if (!$found) abort(404);
            $this->saveAll($items);
            return redirect('/admin/manage-faqs')->with('success', 'FAQ updated (JSON)');
        }

        // Sync JSON backup
        try {
            $all = Faq::orderByDesc('created_at')->get()->map(function($m){ return $m->toArray(); })->toArray();
            $this->saveAll($all);
        } catch (\Throwable $e) { /* ignore */ }

        return redirect('/admin/manage-faqs')->with('success', 'FAQ updated');
    }

    public function destroy($id)
    {
        $this->ensureAdmin();
        try {
            $m = Faq::findOrFail($id);
            $m->delete();
        } catch (\Throwable $e) {
            // fallback to JSON
            $items = $this->loadAll();
            $new = [];
            $deleted = false;
            foreach ($items as $it) {
                if (intval($it['id']) === intval($id)) { $deleted = true; continue; }
                $new[] = $it;
            }
            if ($deleted) $this->saveAll($new);
            return redirect('/admin/manage-faqs')->with('success', $deleted ? 'FAQ removed' : 'FAQ not found');
        }

        // Sync JSON backup
        try {
            $all = Faq::orderByDesc('created_at')->get()->map(function($m){ return $m->toArray(); })->toArray();
            $this->saveAll($all);
        } catch (\Throwable $e) { /* ignore */ }

        return redirect('/admin/manage-faqs')->with('success', 'FAQ removed');
    }
}
