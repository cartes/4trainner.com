<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    /**
     * Show the main settings SPA page
     */
    public function index()
    {
        return view('admin.vue_settings');
    }

    /**
     * Get all settings as JSON for the Vue component
     */
    public function getData()
    {
        $settings = SystemSetting::all()->pluck('value', 'key');

        // Cast known booleans back to true boolean types for frontend convenience
        $booleanKeys = [
            'public_registration_enabled',
            'maintenance_mode',
            'notify_admin_on_user_register'
        ];

        $formattedSettings = [];
        foreach ($settings as $key => $value) {
            if (in_array($key, $booleanKeys)) {
                $formattedSettings[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            } else {
                $formattedSettings[$key] = $value;
            }
        }

        return response()->json($formattedSettings);
    }

    /**
     * Update settings in batch from Vue
     */
    public function updateBatch(Request $request)
    {
        $data = $request->all();

        // Categorize settings to decide types
        $booleanKeys = [
            'public_registration_enabled',
            'maintenance_mode',
            'notify_admin_on_user_register'
        ];

        foreach ($data as $key => $value) {
            $type = in_array($key, $booleanKeys) ? 'boolean' : 'string';
            $group = 'general';

            SystemSetting::setVal($key, $value, $type, $group);
        }

        return response()->json(['success' => true, 'message' => 'Configuraciones actualizadas exitosamente']);
    }
}
