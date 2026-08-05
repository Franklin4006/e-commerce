<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StateController extends Controller
{
    public function index(Request $request): View
    {
        $states = State::query()
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->input('search').'%'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.states.index', compact('states'));
    }

    public function create(): View
    {
        return view('admin.states.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateState($request);

        State::create($validated);

        return redirect()->route('admin.states.index')->with('status', 'State created successfully.');
    }

    public function edit(State $state): View
    {
        return view('admin.states.edit', compact('state'));
    }

    public function update(Request $request, State $state): RedirectResponse
    {
        $validated = $this->validateState($request, $state);

        $state->update($validated);

        return redirect()->route('admin.states.index')->with('status', 'State updated successfully.');
    }

    public function destroy(State $state): RedirectResponse
    {
        $state->delete();

        return redirect()->route('admin.states.index')->with('status', 'State deleted successfully.');
    }

    private function validateState(Request $request, ?State $state = null): array
    {
        return Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('states', 'name')->ignore($state)],
            'shipping_charge' => ['required', 'numeric', 'min:0'],
        ])->validate();
    }
}
