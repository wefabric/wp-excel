<?php


namespace Wefabric\WPExcel\Hooks;


use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Themosis\Hook\Hookable;
use Wefabric\WPExcel\WpPostsExcelExport;
use Wefabric\WPExcel\Concerns\WpPostsExcellable;

class BulkActions extends Hookable
{

    public $hook = 'init';

    public function register(): void
    {
        $this->registerActions();
    }

    public function registerActions(): void
    {
        foreach (WpPostsExcelExport::get() as $postType => $excelCollection) {
            add_filter('bulk_actions-edit-'.$postType, function($bulk_actions) {
                $bulk_actions['export-to-excel'] = config('wp-excel.export_title', __('Exporteer naar excel', 'wefabric'));
                return $bulk_actions;
            });

            add_filter('handle_bulk_actions-edit-'.$postType, function($redirectUrl, $action, $postIds) use($postType, $excelCollection){
                if ($action == 'export-to-excel') {
                    $excelCollectionClass = new $excelCollection;
                    if($excelCollectionClass instanceof WpPostsExcellable) {
                        $excelCollectionClass->setPostIds($postIds);
                    }
                    $this->saveAndDownload($postType, $excelCollectionClass);
                }
                return $redirectUrl;
            }, 10, 3);
        }
    }

    /**
     * @param string $postType
     * @param $excelCollectionClass
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    private function saveAndDownload(string $postType, $excelCollectionClass)
    {
        $fileName = $postType.'.'.config('wp-excel.default_format', 'xlsx');
        $path = 'wp-excel/'.md5($fileName);
        $fullPath = $path.'/'.$fileName;

        Excel::store($excelCollectionClass, $fullPath, 'public');

        $storageDisk = config('wp-excel.storage', 'local');

        $storagePath = Storage::disk($storageDisk)->path($fullPath);
        $response = BinaryFileResponse::create($storagePath);
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($storagePath));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($storagePath) . '";');
        $response->headers->set('Content-length', filesize($storagePath));
        $response->sendHeaders();
        $response->send();
        Storage::disk($storageDisk)->delete($fullPath);
        exit;
    }
}