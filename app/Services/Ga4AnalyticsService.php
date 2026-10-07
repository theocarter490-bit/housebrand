<?php

namespace App\Services;

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\OrderBy\DimensionOrderBy;
use Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy;
use Google\Analytics\Data\V1beta\OrderBy;

use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\StringFilter;


class Ga4AnalyticsService
{
    protected $client;
    protected $propertyId;

    public function __construct()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path('app/analytics/service-account-credentials.json'));
        $this->propertyId = env('GA4_PROPERTY_ID');
        $this->client = new BetaAnalyticsDataClient();
    }

    public function getUserAndPageViews($dateRange)
    {
        $request = new RunReportRequest([
            'property' => 'properties/' . $this->propertyId,
            'date_ranges' => [
                new DateRange([
                    'start_date' => $dateRange,
                    'end_date' => 'today',
                ]),
            ],
            'metrics' => [
                new Metric(['name' => 'activeUsers']),
                new Metric(['name' => 'newUsers']),
                new Metric(['name' => 'totalUsers']),
                new Metric(['name' => 'averageSessionDuration']),
                new Metric(['name' => 'bounceRate']),
                new Metric(['name' => 'engagedSessions']),
            ],
        ]);

        $response = $this->client->runReport($request);


        foreach ($response->getRows() as $row) {
            $activeUsers = $row->getMetricValues()[0]->getValue();
            $newUser = $row->getMetricValues()[1]->getValue();
            $totalUsers = $row->getMetricValues()[2]->getValue();
            $averageSessionDuration = $row->getMetricValues()[3]->getValue();
            $bounceRate = $row->getMetricValues()[4]->getValue();
            $engagedSessions = $row->getMetricValues()[5]->getValue();
        }


        return [
            'active_user' => $activeUsers ?? 0,
            'new_user' => $newUser ?? 0,
            'total_users' => $totalUsers ?? 0,
            'average_session_duration' => $averageSessionDuration ?? 0,
            'bounce_rate' => $bounceRate ?? 0,
            'engaged_sessions' => $engagedSessions ?? 0,
        ];
    }


    public function getPagesViews($dateRange, $dataLimit = 6)
    {
        $request = new RunReportRequest([
            'property' => 'properties/' . $this->propertyId,
            'date_ranges' => [
                new DateRange([
                    'start_date' => $dateRange,
                    'end_date' => 'today',
                ]),
            ],
            'dimensions' => [
                new Dimension(['name' => 'pagePath']),
            ],
            'metrics' => [
                new Metric(['name' => 'screenPageViews']),
            ],
            'order_bys' => [
                new OrderBy([
                    'metric' => new MetricOrderBy(['metric_name' => 'screenPageViews']),
                    'desc' => true,
                ]),
            ],
            'limit' => $dataLimit,
        ]);

        $response = $this->client->runReport($request);

        $results = [];
        foreach ($response->getRows() as $row) {
            $results[] = [
                'path' => $row->getDimensionValues()[0]->getValue(),
                'views' => $row->getMetricValues()[0]->getValue(),
            ];
        }

        return $results;
    }

    function getTopCountries($dateRange, $dataLimit = 7)
    {

        $filterExpression = new FilterExpression([
            'not_expression' => new FilterExpression([
                'filter' => new Filter([
                    'field_name' => 'country',
                    'string_filter' => new StringFilter([
                        'match_type' => StringFilter\MatchType::EXACT,
                        'value' => 'Bangladesh',
                    ]),
                ]),
            ]),
        ]);

        $request = new RunReportRequest([
            'property' => 'properties/' . $this->propertyId,
            'date_ranges' => [
                new DateRange([
                    'start_date' => $dateRange,
                    'end_date' => 'today',
                ]),
            ],
            'dimensions' => [
                new Dimension(['name' => 'country']),
            ],
            'metrics' => [
                new Metric(['name' => 'screenPageViews']),
            ],
            'dimension_filter' => $filterExpression,
            'order_bys' => [
                new OrderBy([
                    'metric' => new MetricOrderBy(['metric_name' => 'screenPageViews']),
                    'desc' => true,
                ]),
            ],
            'limit' => $dataLimit,
        ]);

        $response = $this->client->runReport($request);

        $results = [];
        foreach ($response->getRows() as $row) {
            $results[] = [
                'country' => $row->getDimensionValues()[0]->getValue(),
                'views' => $row->getMetricValues()[0]->getValue(),
            ];
        }

        return $results;
    }
}
