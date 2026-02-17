<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audit;

class AuditController extends Controller
{
    public function index()
    {
        // Fetch all audits with the related user
        $audits = Audit::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.audit.index', compact('audits'));
    }
}
