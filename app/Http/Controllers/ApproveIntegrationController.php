<?php

namespace App\Http\Controllers;

use App\Services\SelectorMatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Enums\UrlTypes;
use Illuminate\Validation\Rule;
use Validator;

class ApproveIntegrationController extends Controller
{
    private SelectorMatcher $matcher;

    private const TYPES = [
        'product' => 'analyzeProductPage',
        'cart' => 'analyzeCartPage',
        'gallery' => 'analyzeGalleryPage'
    ];

    public function __construct(SelectorMatcher $matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * Health check endpoint.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'approve-integration-tool',
        ]);
    }

    /**
     * Analyze product, cart, and optionally gallery pages for APPROVE selectors.
     */
    public function analyze(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'urls' => 'required|array',
            'urls.*.url' => 'required|url',
            'urls.*.type' => ['required', Rule::enum(UrlTypes::class)]
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        $urls = $data['urls'];

        $response = [];
        $results = null;

        foreach ($urls as $url) {
            $method = self::TYPES[$url['type']];
            $results = $this->matcher->$method($url['url']);
            $response[] = $this->formatPageResponse($results);
        }

        return response()->json($response);

    }

    /**
     * Analyze a single product page for APPROVE selectors.
     */
    public function analyzeProduct(Request $request): JsonResponse
    {
        $request->validate(['url' => 'required|url']);

        $results = $this->matcher->analyzeProductPage($request->input('url'));
        return response()->json($this->formatPageResponse($results));
    }

    /**
     * Analyze a single cart page for APPROVE selectors.
     */
    public function analyzeCart(Request $request): JsonResponse
    {
        $request->validate(['url' => 'required|url']);

        $results = $this->matcher->analyzeCartPage($request->input('url'));
        return response()->json($this->formatPageResponse($results));
    }

    /**
     * Analyze a single gallery/collection page for APPROVE selectors.
     */
    public function analyzeGallery(Request $request): JsonResponse
    {
        $request->validate(['url' => 'required|url']);

        $results = $this->matcher->analyzeGalleryPage($request->input('url'));
        return response()->json($this->formatPageResponse($results));
    }

    /**
     * List all available selector categories by page type.
     */
    public function listSelectors(): JsonResponse
    {
        $selectors = $this->matcher->getSelectors();

        $categoriesByType = [];
        foreach ($selectors as $pageType => $categories) {
            $categoriesByType[$pageType] = array_keys($categories);
        }

        return response()->json($categoriesByType);
    }

    /**
     * Format page match results for API response.
     */
    private function formatPageResponse($results): array
    {
        $response = [
            'url' => $results->url,
            'page_type' => $results->pageType,
            'success' => $results->success,
            'error' => $results->error,
        ];

        if ($results->success) {
            $response['selectors'] = $this->matcher->getBestMatchPerCategory($results);
        }

        return $response;
    }
}
