<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SelectListItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class SelectListController extends Controller
{
    public function index(Request $request)
    {
        $group = $request->input('group');
        // If the migration hasn't been run yet, avoid a hard DB error and show a helpful message
        if (!Schema::hasTable('select_list_items')) {
            $items = new LengthAwarePaginator([], 0, 40, 1, [
                'path' => url()->current(),
                'query' => $request->query(),
            ]);
            $missingTable = true;
            return view('admin.select_lists.index', compact('items','group','missingTable'));
        }

        $query = SelectListItem::query();
        if (!empty($group)) {
            $query->where('group', $group);
        }
        $items = $query->orderBy('group')->orderBy('sort_order')->paginate(40)->withQueryString();
        return view('admin.select_lists.index', compact('items','group'));
    }

    public function create(Request $request)
    {
        $group = $request->input('group');
        // Show the select-list creation form. For the `cabstop` group this
        // represents a CabStop place choice (not a store location), so the
        // view will render wording appropriate for places.
        return view('admin.select_lists.create', compact('group'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'group' => 'required|string|max:100',
            'key' => 'nullable|string|max:150',
            'label' => 'required|string|max:191',
            'sort_order' => 'nullable|integer',
            'active' => 'nullable|boolean',
        ]);

        $data['active'] = !empty($data['active']);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        SelectListItem::create($data);
        return redirect()->route('admin.select_lists.index')->with('success', 'List item added');
    }

    public function edit(SelectListItem $select_list)
    {
        return view('admin.select_lists.edit', ['item' => $select_list, 'group' => $select_list->group]);
    }

    public function update(Request $request, SelectListItem $select_list)
    {
        $data = $request->validate([
            'group' => 'required|string|max:100',
            'key' => 'nullable|string|max:150',
            'label' => 'required|string|max:191',
            'sort_order' => 'nullable|integer',
            'active' => 'nullable|boolean',
        ]);

        $data['active'] = !empty($data['active']);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $select_list->update($data);
        return redirect()->route('admin.select_lists.index')->with('success', 'List item updated');
    }

    public function destroy(SelectListItem $select_list)
    {
        $select_list->delete();
        return redirect()->route('admin.select_lists.index')->with('success', 'List item removed');
    }
}
