<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client; 
use App\Models\DomainIntersection;
use App\Services\RestClient;


class DomainIntersectionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'targets.*' => 'required|string',
            'exclude_targets.*' => 'nullable|string',
        ]);

        try {
            $validatedData = $request->json()->all();
            $client = new RestClient('https://api.dataforseo.com/',null,'challenger103@dataforseo.com', '4117eb89ac539a10'); // Створіть екземпляр класу RestClient
            $post_array = array();
            $targets = $validatedData['targets'];
            $targetsAssoc = [];
            foreach ($targets as $index => $value) {
                $targetsAssoc[$index + 1] = $value;
            }
            $post_array[] = [
                'targets' => $targetsAssoc,
                'exclude_targets' => $validatedData['exclude_targets'] ?? [],
                'limit' => 10,
                'include_subdomains' => false,
                'exclude_internal_backlinks' => true,
                'order_by' => [
                    '1.rank,desc' 
                    ]
                ];
           
            $result = $client->post('/v3/backlinks/domain_intersection/live', $post_array);

            $savedDomains = [];
            foreach ($result['tasks'][0]['result'][0]['items'] as $item) {
                foreach ($item['domain_intersection'] as $domain) {
                    DomainIntersection::create([
                        'excluded_target' => $domain['target'],
                        'target_domain' => $domain['target'],
                        'referring_domain' => $domain['referring_domains'],
                        'rank' => $domain['rank'],
                        'backlinks' => $domain['backlinks'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    // Зберігаємо дані у змінну
                    $savedDomains[] = [ 'excluded_target' => $domain['target'],
                    'target_domain' => $domain['target'],
                    'referring_domain' => $domain['referring_domains'],
                    'rank' => $domain['rank'],
                    'backlinks' => $domain['backlinks'],];
                }
            }
            
            return response()->json([
                'data' => $savedDomains,
                'message' => 'Success',
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to process request',
                'error' => $e->getMessage(),
                'type' => 'error'
            ], 500);
        }
    }
}
