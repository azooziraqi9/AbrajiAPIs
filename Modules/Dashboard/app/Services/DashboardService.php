<?php

namespace Modules\Dashboard\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Modules\Dashboard\Abstract\DashboardServiceInterface;

class DashboardService implements DashboardServiceInterface
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

// Fetch the initial dashboard data
    private function fetchDashboardData($authorization)
    {
        $dashboardUrl = config('app.api_domain') . '/admin/api/index.php/api/dashboard';

        try {
            $response = $this->client->get($dashboardUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => $authorization,
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 500;
            return [
                'error' => 'Failed to fetch dashboard data',
                'status' => $statusCode
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }

// Fetch data for an individual widget
    private function fetchWidgetData($internalDataSource, $authorization)
    {
        $widgetUrl = config('app.api_domain') . '/admin/api/index.php/api/widgetData/internal/' . $internalDataSource;

        try {
            $response = $this->client->get($widgetUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => $authorization,
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true); // Assuming the API returns the value in a 'value' key

        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 500;
            return [
                'error' => 'Failed to fetch widget data',
                'status' => $statusCode
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }

// Transform widget name into the desired format
    private function transformWidgetName($name)
    {
        return strtolower(str_replace(' ', '_', $name));
    }

// Main function to get the dashboard data with widget values
    public function GetDashboard($authorization)
    {
        $dashboardData = $this->fetchDashboardData($authorization);

        if (isset($dashboardData['error'])) {
            return response()->json($dashboardData, $dashboardData['status']);
        }

        // Initialize an empty array to store the widget values
        $widgetValues = [];

        // Iterate over each widget and fetch its value
        foreach ($dashboardData['data']['widgets'] as $row) {
            foreach ($row['widgets'] as $widget) {
                $internalDataSource = $widget['internal_data_source'];
                $widgetName = $this->transformWidgetName($widget['name']);
                $widgetData = $this->fetchWidgetData($internalDataSource, $authorization);

                if (isset($widgetData['error'])) {
                    $widgetValues[$widgetName] = $widgetData['error'];
                } else {
                    $widgetValues[$widgetName] = $widgetData['data']; // Assuming the API response has 'data' key
                }
            }
        }

        // Return the final response with widget values
        return response()->json($widgetValues);
    }


}
