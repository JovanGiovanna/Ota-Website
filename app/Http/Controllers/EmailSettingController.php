<?php

namespace App\Http\Controllers;

use App\Models\EmailSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailSettingController extends Controller
{
    public function index()
    {
        $emailSettings = EmailSetting::all();
        return view('super_admin.email_settings.index', compact('emailSettings'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Failed', 'Please check the form and try again');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            EmailSetting::create([
                'key' => 'headbase_' . time(),
                'label' => $request->label,
                'email' => $request->email,
                'is_active' => $request->has('is_active'),
                'description' => $request->description,
            ]);

            alert()->success('Success', 'Email notification added successfully');
            return redirect()->route('super_admin.email_settings');
        } catch (\Exception $e) {
            alert()->error('Error', 'Failed to add email: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $emailSetting = EmailSetting::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Failed', 'Please check the form and try again');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $emailSetting->update([
                'label' => $request->label,
                'email' => $request->email,
                'is_active' => $request->has('is_active'),
                'description' => $request->description,
            ]);

            alert()->success('Success', 'Email notification updated successfully');
            return redirect()->route('super_admin.email_settings');
        } catch (\Exception $e) {
            alert()->error('Error', 'Failed to update email: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function toggleStatus($id)
    {
        try {
            $emailSetting = EmailSetting::findOrFail($id);
            $emailSetting->update(['is_active' => !$emailSetting->is_active]);

            alert()->success('Success', 'Email notification status updated');
            return redirect()->back();
        } catch (\Exception $e) {
            alert()->error('Error', 'Failed to update status: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        try {
            $emailSetting = EmailSetting::findOrFail($id);
            $emailSetting->delete();

            alert()->success('Success', 'Email notification deleted successfully');
            return redirect()->route('super_admin.email_settings');
        } catch (\Exception $e) {
            alert()->error('Error', 'Failed to delete email: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}

