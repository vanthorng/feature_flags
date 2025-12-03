<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Pennant\Feature;

class FeatureController extends Controller
{
    /**
     * List ALL dynamic feature names in the system.
     * Example: ["new-dashboard", "print_invoice"]
     */
    public function index()
    {
        return DB::table('features')
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');
    }

    /**
     * Create a NEW dynamic feature name.
     * We insert a "global" placeholder row so it appears in the dropdown.
     */
    public function createFeature(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $exists = DB::table('features')->where('name', $data['name'])->exists();

        if (! $exists) {
            DB::table('features')->insert([
                'name'       => $data['name'],
                'scope'      => 'global',   // 🔥 was null, now a non-null placeholder
                'value'      => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return [
            'message'  => 'Feature created',
            'features' => DB::table('features')
                ->select('name')
                ->distinct()
                ->orderBy('name')
                ->pluck('name'),
        ];
    }

    /**
     * Activate a feature for a specific user.
     */
    public function activate(Request $request)
    {
        $data = $request->validate([
            'feature' => ['required', 'string'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $user = User::findOrFail($data['user_id']);

        // ✅ correct way: scope to the user, then activate
        Feature::for($user)->activate($data['feature']);

        return ['message' => 'Feature activated'];
    }

    /**
     * Deactivate a feature for a specific user.
     */
    public function deactivate(Request $request)
    {
        $data = $request->validate([
            'feature' => ['required', 'string'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $user = User::findOrFail($data['user_id']);

        // ✅ correct way: scope to the user, then deactivate
        Feature::for($user)->deactivate($data['feature']);

        return ['message' => 'Feature deactivated'];
    }
}
