<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Clan;
use App\Services\GedcomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class GedcomController extends Controller
{
    public function __construct(
        protected GedcomService $gedcomService
    ) {}

    public function index()
    {
        $clans = Clan::all();
        return view('gedcom.index', compact('clans'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'clan_id' => 'required|exists:clans,id',
        ]);

        $gedcomContent = $this->gedcomService->export($request->clan_id);
        $clan = Clan::find($request->clan_id);
        
        $filename = 'family_tree_' . \Str::slug($clan->name) . '_' . date('Y-m-d') . '.ged';

        return Response::make($gedcomContent, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'gedcom_file' => 'required|file',
            'clan_id' => 'required|exists:clans,id',
        ]);

        // Import logic would go here
        // $this->gedcomService->import($request->file('gedcom_file'), $request->clan_id);

        return back()->with('success', 'GEDCOM import feature is currently in beta. File uploaded successfully but not processed.');
    }
}
