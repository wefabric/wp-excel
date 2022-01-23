# WordPress Excel export
Implementation to use Excel export as a bulk action in the WordPress backend.
To use with the Wefabric themosis implementation. It uses the https://github.com/SpartnerNL/Laravel-Excel (v3.0) package.

## Installation
Run the following command
```shell
composer require wefabric/wp-excel
```

Publish the config file
```shell
php console vendor:publish --tag=wp-excel
```

## Usage
Every post type needs to be registered for export before usage. 
In the following example we will register the export class for a default WordPress post

1. First you need to create the class which converts the posts data to an Illuminate Collection.
To do this run the following command
```shell
php console make:export PostsExport
```

2. Add the logic to export the WordPress posts to the Illuminate collection
for example:
```php
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Wefabric\WPExcel\Concerns\WpPostsExcellable;

class PostsExport implements FromCollection, WithHeadings, WpPostsExcellable
{

    protected array $postIds = [];

    public function headings(): array
    {
        return [
            'Id',
            'Title',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $args = [
            'post_type' => 'post'
        ];

        if($this->postIds) {
            $args['post__in'] = $this->postIds;
        }

        $posts = get_posts($args);
        $collection = new Collection();
        foreach ($posts as $post) {
            $collection->push([
                'Id' => $post->ID,
                'Title' => $post->post_title,
            ]);
        }
        return $collection;
    }

    public function setPostIds(array $postIds)
    {
        $this->postIds = $postIds;
    }
}
```

3. Add the class to the wp-excel config file (config/wp-excel.php). Where 'post' is the post type name and the second parameter is the export class

```php
return [
  'post_types' => [
    'post' => \App\Exports\PostsExport::class,
  ]
]
```

4. Go to the WordPress backend (wp-admin/edit.php), select a post and run the bulk action.

## Export formats
The default export format is '.xlsx'. To use a different export change the 'default_format' in the wp-excel config file (config/wp-excel.php) to the correct format.
The allowed export formats are located here: https://docs.laravel-excel.com/3.0/exports/export-formats.html