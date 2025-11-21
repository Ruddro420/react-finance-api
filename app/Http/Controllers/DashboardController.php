<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\AboutPage;
use App\Models\ContactPage;
use App\Models\HomePage;
use App\Models\ProductApPage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getDashboardData()
    {
        try {
            // Get total pages created (count of all models)
            $totalPages = $this->getTotalPages();
            
            // Get total images across all pages
            $totalImages = $this->getTotalImages();
            
            // Get latest update timestamp
            $lastUpdated = $this->getLastUpdated();
            
            // Get storage usage (optional - if you want to show disk usage)
            $storageUsage = $this->getStorageUsage();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'totalPages' => $totalPages,
                    'totalImages' => $totalImages,
                    'lastUpdated' => $lastUpdated,
                    'storageUsage' => $storageUsage,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get total number of pages created across all models
     */
    private function getTotalPages(): array
    {
        $homePages = HomePage::count();
        $aboutPages = AboutPage::count();
        $contactPages = ContactPage::count();
        $productApPages = ProductApPage::count();

        return [
            'total' => $homePages + $aboutPages + $contactPages + $productApPages,
            'breakdown' => [
                'home' => $homePages,
                'about' => $aboutPages,
                'contact' => $contactPages,
                'product_ap' => $productApPages,
            ]
        ];
    }

    /**
     * Get total number of images across all pages
     */
    private function getTotalImages(): array
    {
        $totalImages = 0;
        $imagesByType = [];

        // Count images from HomePage
        $homePage = HomePage::first();
        if ($homePage) {
            $homeImages = $this->countImagesInArray($homePage->images ?? []) +
                         $this->countImagesInArray($homePage->invoice_images ?? []) +
                         $this->countArrayImages($homePage->accounts_receivable ?? []) +
                         $this->countArrayImages($homePage->accounts_payable ?? []) +
                         $this->countArrayImages($homePage->capabilities ?? []) +
                         $this->countArrayImages($homePage->integrations ?? []) +
                         $this->countArrayImages($homePage->erp_logos ?? []) +
                         $this->countArrayImages($homePage->bank_methods ?? []) +
                         $this->countArrayImages($homePage->testimonials ?? []);

            $totalImages += $homeImages;
            $imagesByType['home'] = $homeImages;
        }

        // Count images from AboutPage
        $aboutPage = AboutPage::first();
        if ($aboutPage) {
            $aboutImages = ($aboutPage->hero && isset($aboutPage->hero['image']) ? 1 : 0) +
                          $this->countArrayImages($aboutPage->leadership ?? []) +
                          $this->countArrayImages($aboutPage->investors ?? []);

            $totalImages += $aboutImages;
            $imagesByType['about'] = $aboutImages;
        }

        // Count images from ContactPage
        $contactPage = ContactPage::first();
        if ($contactPage) {
            $contactImages = $this->countArrayImages($contactPage->logos ?? []);
            $totalImages += $contactImages;
            $imagesByType['contact'] = $contactImages;
        }

        // Count images from ProductApPage
        $productApPage = ProductApPage::first();
        if ($productApPage) {
            $productApImages = $this->countImagesInArray($productApPage->hero ?? []) +
                              $this->countArrayImages($productApPage->invoice_processes ?? []) +
                              $this->countArrayImages($productApPage->capabilities ?? []) +
                              $this->countImagesInArray($productApPage->invoice_section ?? []);

            $totalImages += $productApImages;
            $imagesByType['product_ap'] = $productApImages;
        }

        return [
            'total' => $totalImages,
            'breakdown' => $imagesByType
        ];
    }

    /**
     * Count images in a simple array structure
     */
    private function countImagesInArray(array $data): int
    {
        $count = 0;
        foreach ($data as $value) {
            if (is_string($value) && $value !== null && $value !== '') {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Count images in array of items (like leadership, capabilities, etc.)
     */
    private function countArrayImages(array $items): int
    {
        $count = 0;
        foreach ($items as $item) {
            if (isset($item['image']) && $item['image'] !== null && $item['image'] !== '') {
                $count++;
            }
            if (isset($item['logo']) && $item['logo'] !== null && $item['logo'] !== '') {
                $count++;
            }
            if (isset($item['bigImage']) && $item['bigImage'] !== null && $item['bigImage'] !== '') {
                $count++;
            }
            if (isset($item['smallImage']) && $item['smallImage'] !== null && $item['smallImage'] !== '') {
                $count++;
            }
            if (isset($item['image1']) && $item['image1'] !== null && $item['image1'] !== '') {
                $count++;
            }
            if (isset($item['image2']) && $item['image2'] !== null && $item['image2'] !== '') {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get the last updated timestamp across all pages
     */
    private function getLastUpdated(): array
    {
        $timestamps = [];

        $homePage = HomePage::latest('updated_at')->first();
        if ($homePage) {
            $timestamps[] = $homePage->updated_at;
        }

        $aboutPage = AboutPage::latest('updated_at')->first();
        if ($aboutPage) {
            $timestamps[] = $aboutPage->updated_at;
        }

        $contactPage = ContactPage::latest('updated_at')->first();
        if ($contactPage) {
            $timestamps[] = $contactPage->updated_at;
        }

        $productApPage = ProductApPage::latest('updated_at')->first();
        if ($productApPage) {
            $timestamps[] = $productApPage->updated_at;
        }

        if (empty($timestamps)) {
            return [
                'timestamp' => null,
                'formatted' => 'Never',
                'page' => null
            ];
        }

        $latest = max($timestamps);
        
        // Find which page was last updated
        $lastUpdatedPage = null;
        if ($homePage && $homePage->updated_at == $latest) $lastUpdatedPage = 'Home Page';
        if ($aboutPage && $aboutPage->updated_at == $latest) $lastUpdatedPage = 'About Page';
        if ($contactPage && $contactPage->updated_at == $latest) $lastUpdatedPage = 'Contact Page';
        if ($productApPage && $productApPage->updated_at == $latest) $lastUpdatedPage = 'Product AP Page';

        return [
            'timestamp' => $latest,
            'formatted' => $latest->diffForHumans(),
            'page' => $lastUpdatedPage
        ];
    }

    /**
     * Get storage usage statistics
     */
    private function getStorageUsage(): array
    {
        $imageDirectories = [
            'home/images',
            'home/invoices',
            'home/ar',
            'home/ap', 
            'home/capabilities',
            'home/integrations',
            'home/erp',
            'home/banks',
            'home/testimonials',
            'about/hero',
            'about/leadership',
            'about/investors',
            'contact/logos',
            'product-ap/hero',
            'product-ap/processes',
            'product-ap/capabilities',
            'product-ap/invoice'
        ];

        $totalSize = 0;
        $totalFiles = 0;

        foreach ($imageDirectories as $directory) {
            if (Storage::disk('public')->exists($directory)) {
                $files = Storage::disk('public')->allFiles($directory);
                $totalFiles += count($files);
                
                foreach ($files as $file) {
                    $totalSize += Storage::disk('public')->size($file);
                }
            }
        }

        return [
            'total_size' => $totalSize,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'total_files' => $totalFiles,
            'formatted_size' => $this->formatBytes($totalSize)
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}