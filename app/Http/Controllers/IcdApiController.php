<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IcdApiController extends Controller
{
    private const ICD10_URL = 'https://clinicaltables.nlm.nih.gov/api/icd10cm/v3/search';
    private const ICD9CM_SG_URL = 'https://clinicaltables.nlm.nih.gov/api/icd9cm_sg/v3/search';

    private const MAX_RESULTS = 15;

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:icd10,icd9'],
        ]);

        $query = $validated['q'];

        if ($validated['type'] === 'icd10') {
            $response = Http::get(self::ICD10_URL, [
                'sf' => 'code,name',
                'terms' => $query,
                'maxList' => self::MAX_RESULTS,
            ]);
        } else {
            $response = Http::get(self::ICD9CM_SG_URL, [
                'terms' => $query,
                'maxList' => self::MAX_RESULTS,
            ]);
        }

        if ($response->failed()) {
            return response()->json(['results' => []], 200);
        }

        $data = $response->json();

        // Format: [total, [codes], null, [[code, name], ...]]
        $total = $data[0] ?? 0;
        $displayRows = $data[3] ?? [];

        $results = array_map(fn (array $row) => [
            'code' => $row[0],
            'name' => $row[1],
        ], $displayRows);

        return response()->json([
            'results' => $results,
            'total' => $total,
        ]);
    }
}
