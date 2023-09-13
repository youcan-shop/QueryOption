<p align="center">
<img width="150" height="150" src="https://github.com/youcan-shop/QueryOption/blob/main/assets/queryoptionlogo.svg" alt="Query Option package logo"/>
<br><b>QueryOption</b>
</p>

[![Tests](https://github.com/youcan-shop/QueryOption/actions/workflows/tests.yaml/badge.svg)](https://github.com/youcan-shop/QueryOption/actions/workflows/tests.yaml)
[![Total Downloads](https://img.shields.io/packagist/dt/youcanshop/queryoption.svg?style=flat-square)](https://packagist.org/packages/youcanshop/queryoption)
[![License](https://img.shields.io/github/license/youcan-shop/QueryOption?style=flat-square)](https://github.com/youcan-shop/QueryOption/blob/master/LICENSE.md)

This package helps you manipulate HTTP query data as an object instead of passing an array through different layers of your application.

## Usage

Inside a controller we tend to extract the params from the request and send them to our service and then to the repository to perform search, sort or filtering.

```php
class ListUsersController as Controller
{
    private UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function __invoke(Request $request) {
        $queryOption = QueryOptionFactory::createFromIlluminateRequest($request);

        $this->userService->paginate($queryOption);
    }
}
```

From the example above, the `QueryOptionFactory` helps create the `QueryOption` object that hold values needed for search, sort and filters.
Since in this example we have a Laravel application, we can use a specific factory method to crate directly from `Request` object.

| | |
| ------------- | ------------- |
| `createFromGlobals()` | create the `QueryOption` object from the global `$_REQUEST` object. |
| `createFromArray(array $attributes)` | create the `QueryOption` object from an array passed in param. |
| `createFromIlluminateRequest(Request $request)` | create the `QueryOption` object from a Laravel Illuminate request object. |
| `createFromSymfonyRequest(Request $request)` | create the `QueryOption` object from a Symfony HTTP foundation request object. |

## URL Params

Since the QueryOption package parse the URL parameters, we're going to explain the param names below:

| | |
| ------------- | ------------- |
| `q` | Used for search |
| `search_type` | (`like`: default, `equal`) |
| `page` | Current page (when working with pagination) |
| `limit` | Limit query result (when working with pagination) |
| `sort_field` | Name of the field when sorting (`created_at`: default) |
| `sort_order` | Sorting direction. (`asc`, `desc`: default) |
| `filters` | An array of filters (as described below) |

| | |
| ------------- | ------------- |
| `field` | Name of the field to filter by |
| `operator` | Comparison operator (`=`: default, `!=`, `>`, `<`, `=>`, `<=`, `is`, `is_not`, `in`) |
| `value` | The value to compare by |

## Query Option

The `QueryOption` is the glue that holds all the rest of the components.

```php
$queryOption->getSort();
$queryOption->getFilters();
$queryOption->getSearch();
```

For most applications, each controller has a set of filters that are allowed in certain context (admin vs normal user). 
In this case, you can use the `allowedFilters()` method inside the controller to limit passing filters in the wrong context.

An example would be listing blog posts. The admin can all the posts, while the normal user can only see the published ones.

```php
class AdminPostsController {
    private PostService $postService;

    public function __construct(PostService $postService) {
        $this->postService = $postService;
    }

    public function __invoke(Request $request) {
        $queryOption = QueryOptionFactory::createFromIlluminateRequest($request);

        $posts = $this->postService->paginate($queryOption);

        // return posts
    }
}
```

```php
class UserPostsController {
    private PostService $postService;

    public function __construct(PostService $postService) {
        $this->postService = $postService;
    }

    public function __invoke(Request $request) {
        $queryOption = QueryOptionFactory::createFromIlluminateRequest($request);

        // explicitly specify the filter names allowed. 
        $queryOption->allowedFilters(['published_date', 'author']);

        $posts = $this->postService->paginate($queryOption);

        // return posts
    }
}
```

## Laravel Bridge

For Laravel applications, you can add Query Option provider inside `config/app.php`

```php
<?php

use YouCanShop\QueryOption\Laravel\QueryOptionProvider;

return [
    // ...
    
    'providers' => [
        // ...
        QueryOptionProvider::class,    
    ],

    // ...
];
```

After that you can benefit from helpers like:

```
$queryOption = $request->queryOption();
```

Inside your repository you can use what we call **criterias**. Here's an example on how it works:

```php
<?php

use YouCanShop\QueryOption\Laravel\UsesQueryOption;

class PostRepository {
    use UsesQueryOption;

    public function paginated(QueryOption $queryOption)
    {
        $query = Post::query();

        [$query, $queryOption] = $this->pipeThroughCriterias($query, $queryOption);

        return $query->paginate(
            $queryOption->getLimit(),
            '*',
            'page',
            $queryOption->getPage()
        );
    }

    protected function getQueryOptionCriterias(): array
    {
        return [
            SearchCriteria::class,
            FilterByPublishedAtCriteria::class,
            SortByCriteria::class
        ];
    }
}
```

So calling the `paginate()` method will pass the query through the list of **criterias** to add the necessary logic for each defined query option.
Then, we return the modified query instance to continue the pagination.

Below is the code inside each criteria to illustrate how it works.

```php
class SearchCriteria
{
    public function handle(array $data, Closure $next)
    {
        [$query, $queryOption] = $data;

        $search = $queryOption->getSearch();
        if (!empty($search->getTerm())) {
            if ($search->getType() === 'like') {
                $query->where('title', 'like', "%" . $search->getTerm() . "%");
            }

            if ($search->getType() === 'equal') {
                $query->where('title', '=', $search->getTerm());
            }
        }
        
        return $next([$query, $queryOption]);
    }
}
```

The search criteria do a search using `like` or `equal` using the term when it's not empty, and return the `$query` and `$queryOption` for the next criteria. 

```php
class SortByCriteria
{
    public function handle(array $data, Closure $next)
    {
        [$query, $queryOption] = $data;

        $sort = $queryOption->getSort();

        // allow sorting only by publish date and title
        if(!in_array($sort->getField(), ['published_date','title'])) {
            return $next([$query, $queryOption]);
        }

        $query->orderBy($sort->getField(), $sort->getDirection());

        return $next([$query, $queryOption]);
    }
}
```

The sorting is pretty straightforward in this example. An important thing is to guard against sorting using not allowed fields. 

```php
class FilterByPublishedAtCriteria
{
    public function handle(array $data, Closure $next)
    {
        [$query, $queryOption] = $data;

        $filter = $queryOption->getFilters()->findByName('publish_date');
        
        $creationDate = Carbon::parse($filter->getValue());
        $query->whereDate('published_at', $filter->getOperator(), $creationDate);

        return $next([$query, $queryOption]);
    }
}
```

This is it, the pagination will take in consideration the fitering by publish date, searching by title and sorting by publish date.
