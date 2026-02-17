<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Guest;
use App\Models\Agent;
use App\Models\Company;

class CompanyController extends Controller
{
    // Companies
    public function create()
    {
        return view('admin.companies.create');
    }

    public function store_company(Request $request)
    {
        $company = Company::create([
            'name'          => $request->name,
            'legal_name'    => $request->legal_name,
            'slug'          => $request->slug,
            'currency'      => $request->currency,
            'timezone'      => $request->timezone,
            'billing_email' => $request->billing_email,
            'address'       => $request->address,
            'vat_tax_id'    => $request->vat_tax_id,
        ]);

        return redirect()
            ->route('company.index')
            ->with('success', 'Company created successfully.');
    }



    public function index()
    {
        $companies = Company::all();
        return view('admin.companies.index', compact('companies'));
    }

     public function show($id)
    {

        $company = Company::findOrFail($id);
        return view('admin.companies.detail', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $company->update([
            'name'        => $request->name,
            'legal_name'  => $request->legal_name,
            'slug'        => $request->slug,
            'currency'    => $request->currency,
            'timezone'    => $request->timezone,
            'billing_email' => $request->billing_email,
            'address'     => $request->address,
            'vat_tax_id'  => $request->vat_tax_id,
        ]);

        return redirect()
            ->route('company.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return redirect()->route('company.index')->with('success', 'Company deleted successfully.');
    }
}
