<?php

namespace App\Http\Controllers;

use App\Models\BehaviouralRecord;
use App\Models\Child;
use App\Models\Milestone;
use App\Models\Prediction;
use App\Models\Recommendation;
use App\Models\TherapyGoal;
use App\Models\User;
use App\Services\ChildPredictionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdministratorDashboardController extends Controller
{
    /**
     * Ensure the authenticated user is an administrator.
     */
    private function authorizeAdmin(Request $request): void
    {
        abort_unless(
            $request->user() && $request->user()->role === 'administrator',
            403
        );
    }

    /**
     * Display the administrator dashboard with live platform, user governance,
     * clinician caseload management, and ML service oversight.
     */
    public function index(Request $request, ChildPredictionService $predictionService): View
    {
        $this->authorizeAdmin($request);

        $practitioners = User::query()
            ->withCount(['children', 'assignedChildren'])
            ->latest()
            ->get();

        $clinicians = $practitioners->where('role', 'clinician')->values();

        $children = Child::query()
            ->with(['caregiver', 'clinicians'])
            ->latest()
            ->get();

        $totalUsersCount = $practitioners->count();
        $clinicianCount = $clinicians->count();
        $caregiverCount = $practitioners->where('role', 'caregiver')->count();
        $adminCount = $practitioners->where('role', 'administrator')->count();

        $childrenCount = $children->count();
        $assignedChildrenCount = $children->filter(fn ($child) => $child->clinicians->isNotEmpty())->count();
        $unassignedChildrenCount = max(0, $childrenCount - $assignedChildrenCount);

        $observationsCount = BehaviouralRecord::count();
        $assessedObservationsCount = BehaviouralRecord::whereNotNull('ml_assessment_inputs')->count();

        $totalPredictionsCount = Prediction::count();
        $validatedPredictionsCount = Prediction::where('validated_by_clinician', true)->count();
        $pendingPredictionsCount = Prediction::where('validated_by_clinician', false)->count();

        $therapyGoalsCount = TherapyGoal::count();
        $milestonesCount = Milestone::count();
        $recommendationsCount = Recommendation::count();

        $mlHealth = $predictionService->checkHealth();

        return view('administrator.dashboard', compact(
            'practitioners',
            'clinicians',
            'children',
            'totalUsersCount',
            'clinicianCount',
            'caregiverCount',
            'adminCount',
            'childrenCount',
            'assignedChildrenCount',
            'unassignedChildrenCount',
            'observationsCount',
            'assessedObservationsCount',
            'totalPredictionsCount',
            'validatedPredictionsCount',
            'pendingPredictionsCount',
            'therapyGoalsCount',
            'milestonesCount',
            'recommendationsCount',
            'mlHealth'
        ));
    }

    /**
     * Onboard a new Clinician, Caregiver, or Administrator user account.
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', 'in:clinician,caregiver,administrator'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('administrator.dashboard')
            ->with('success', 'New ' . $validated['role'] . ' account (' . $validated['name'] . ') onboarded successfully.');
    }

    /**
     * Update a user's platform role with strict privilege safeguards.
     */
    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin($request);

        // Safeguard 1: Prevent self-role modification
        if ((int) $user->id === (int) $request->user()->id) {
            return redirect()
                ->route('administrator.dashboard')
                ->withErrors(['role' => 'Privilege safeguard: You cannot modify your own administrator role.']);
        }

        $validated = $request->validate([
            'role' => ['required', 'string', 'in:caregiver,clinician,administrator'],
        ]);

        // Safeguard 2: Prevent demoting the last remaining administrator
        if (
            $user->role === 'administrator'
            && $validated['role'] !== 'administrator'
            && User::where('role', 'administrator')->count() <= 1
        ) {
            return redirect()
                ->route('administrator.dashboard')
                ->withErrors(['role' => 'Privilege safeguard: Cannot demote the only remaining platform administrator.']);
        }

        // If a clinician is reassigned to caregiver, detach their clinical caseload
        if ($user->role === 'clinician' && $validated['role'] !== 'clinician') {
            $user->assignedChildren()->detach();
        }

        $user->update([
            'role' => $validated['role'],
        ]);

        return redirect()
            ->route('administrator.dashboard')
            ->with('success', 'Updated role for ' . $user->name . ' to ' . ucfirst($validated['role']) . '.');
    }

    /**
     * Assign a clinician to a child's caseload from the Administrator Dashboard.
     */
    public function assignClinician(Request $request, Child $child): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'clinician_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'clinician')),
            ],
        ]);

        $clinician = User::findOrFail((int) $validated['clinician_id']);
        $child->clinicians()->syncWithoutDetaching([$clinician->id]);

        return redirect()
            ->route('administrator.dashboard')
            ->with('success', 'Assigned ' . $clinician->name . ' to ' . $child->first_name . '\'s clinical care team.');
    }

    /**
     * Remove a clinician from a child's caseload from the Administrator Dashboard.
     */
    public function unassignClinician(Request $request, Child $child, User $clinician): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $child->clinicians()->detach($clinician->id);

        return redirect()
            ->route('administrator.dashboard')
            ->with('success', 'Removed ' . $clinician->name . ' from ' . $child->first_name . '\'s clinical care team.');
    }
}