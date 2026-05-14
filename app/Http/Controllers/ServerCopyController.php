<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchServerCopyRequest;
use App\Services\ServerCopyService;
use App\Services\PdfGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServerCopyController extends Controller
{
    public function __construct(
        private readonly ServerCopyService   $service,
        private readonly PdfGeneratorService $pdfGenerator,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/server-copy/search?nid=xxx&dob=xxx
    // ─────────────────────────────────────────────────────────────────────────

    public function search(SearchServerCopyRequest $request): JsonResponse
    {
        try {
            $data = $this->service->search(
                $request->validated('nid'),
                $request->validated('dob'),
            );

            return response()->json([
                'success' => true,
                'data'    => $data->toArray(),
            ]);

        } catch (\RuntimeException $e) {
            return match ($e->getMessage()) {
                'NID_NOT_FOUND' => response()->json([
                    'success' => false,
                    'message' => 'তথ্য পাওয়া যায়নি। NID নম্বর বা তারিখ সঠিক নয়।',
                    'code'    => 'NID_NOT_FOUND',
                ], 404),

                'NID_API_ERROR' => response()->json([
                    'success' => false,
                    'message' => 'সরকারি সার্ভার ডাউন হয়েছে, অনুগ্রহ করে কিছুক্ষণ পরে আবার চেষ্টা করুন।',
                    'code'    => 'NID_API_ERROR',
                ], 502),

                default => response()->json([
                    'success' => false,
                    'message' => 'সার্ভারে সাময়িক সমস্যা হয়েছে। অনুগ্রহ করে কিছুক্ষণ পরে আবার চেষ্টা করুন।',
                ], 500),
            };
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/server-copy/pdf?nid=xxx&dob=xxx
    // ─────────────────────────────────────────────────────────────────────────

    public function downloadPdf(Request $request): Response|JsonResponse
    {
        $nid = trim($request->query('nid', ''));
        $dob = trim($request->query('dob', ''));

        if (! $nid || ! $dob) {
            return response()->json([
                'success' => false,
                'message' => 'NID এবং DOB দিন',
            ], 400);
        }

        $data = $this->service->getForPdf($nid, $dob);

        if (! $data) {
            return response()->json([
                'success' => false,
                'message' => 'তথ্য পাওয়া যায়নি। আগে সার্চ করুন।',
            ], 404);
        }

        try {
            $pdf = $this->pdfGenerator->generate($data);

            return response($pdf, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="serverCopy_' . $nid . '.pdf"',
                'Content-Length'      => strlen($pdf),
            ]);

        } catch (\Exception $e) {
            \Log::error('serverCopy PDF error', ['nid' => $nid, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'PDF তৈরিতে সমস্যা।',
            ], 500);
        }
    }
}
