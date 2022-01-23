<?php

namespace Wefabric\WPExcel;

use Illuminate\Support\Collection;

class WpPostsExcelExport
{
    public static ?Collection $postTypesExcelCollections = null;

    /**
     * @param array|string $postTypes
     * @param string|null $excelCollection
     */
    public static function register($postTypes, string $excelCollection = null)
    {
        if(is_array($postTypes)) {
            foreach ($postTypes as $postType => $postTypeExcelCollection) {
                self::add($postType, $postTypeExcelCollection);
            }
            return;
        }

        self::add($postTypes, $excelCollection);
    }

    /**
     * @param string $postType
     * @param string $excelCollection
     */
    public static function add(string $postType, string $excelCollection): void
    {
        self::get()->put($postType, $excelCollection);
    }

    /**
     * @return Collection
     */
    public static function get(): Collection
    {
        if(!self::$postTypesExcelCollections) {
            self::$postTypesExcelCollections = new Collection();
        }
        return self::$postTypesExcelCollections;
    }

}