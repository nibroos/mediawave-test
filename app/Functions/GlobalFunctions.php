<?php

use Carbon\Carbon;
use App\Models\UserTimeline;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as PaginationLengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;


function addNumberEachRowOnGet(Collection $rows)
{
  $rows = $rows->map(function ($row, $index) {
    if (gettype($row) == 'object') {
      $row->row_number = $index + 1;
    } else {
      $row['row_number'] = $index + 1;
    }

    return $row;
  });
  return $rows;
}

function addNumberEachRowOnPagination(PaginationLengthAwarePaginator $rows)
{
  $rows->getCollection()->transform(function ($row, $key) use ($rows) {
    $row->row_number = ($rows->currentPage() - 1) * $rows->perPage() + $key + 1;
    return $row;
  });
  return $rows;
}

function formatNumberRupiah(float $number, int $decimal = 0, $decimal_separator = ',', $thousand_separator = '.'): string
{
  return number_format($number, $decimal, $decimal_separator, $thousand_separator);
}

function formatTranslatedDateDMY(string $date): string
{
  return Carbon::parse($date)->translatedFormat('d M Y');
}

function getIndexModule(Builder $moduleBuilder): object
{
  return $moduleBuilder->get();
}

function paginateIndexModule(Builder $moduleBuilder): PaginationLengthAwarePaginator
{
  return $moduleBuilder->paginate(request('per_page', 10));
}

function formatDateYMD(string $date): string
{
  return Carbon::parse($date)->format('Y-m-d');
}

function getPaginatedData(Collection $data): PaginationLengthAwarePaginator
{
  $perPage = request('per_page', 10);
  $currentPage = LengthAwarePaginator::resolveCurrentPage();
  $pagedData = $data->values()->slice(($currentPage - 1) * $perPage, $perPage)->all();
  return new LengthAwarePaginator($pagedData, count($data), $perPage);
}

function apiSuccessGetResponse(LengthAwarePaginator|Collection  $data, ?bool $isPaginated = false, ?string $message, ?array $opt = [])
{
  if (request('per_page') != null && request('per_page') != -1) {
    $data = addNumberEachRowOnPagination($data);
  } else {
    $data = addNumberEachRowOnGet($data);
  }

  if (!$isPaginated) {
    $data = [
      'data' => $data,
      'status' => Response::HTTP_OK,
      'message' => $message ?? 'Success retrieve data',
      'meta' => [
        'current_page' => 1,
        'from' => 1,
        'last_page' => 1,
        'path' => url()->current(),
        'per_page' => count($data),
        'to' => count($data),
        'total' => count($data),
      ],
    ];
  } else {
    $data = [
      'data' => $data->items(),
      'status' => Response::HTTP_OK,
      'message' => $message ?? 'Success retrieve data',
      'meta' => [
        'current_page' => $data->currentPage(),
        'from' => $data->firstItem(),
        'last_page' => $data->lastPage(),
        'path' => $data->url(1),
        'per_page' => $data->perPage(),
        'to' => $data->lastItem(),
        'total' => $data->total(),
      ],
    ];
  }

  return response()->json($data);
}

function apiErrorGetResponse(\Exception|ValidationException $e, ?string $message = 'Failed retrieve data', ?array $opt = [])
{
  DB::rollBack();
  Log::error($e->getMessage() ?? 'Failed retrieve data');

  $errors = [];
  if ($e instanceof ValidationException) {
    $errors = $e->errors();
    $opt['status'] = Response::HTTP_UNPROCESSABLE_ENTITY;
  }

  return response()->json([
    'data' => [],
    'status' => isset($opt['status']) ? $opt['status'] : Response::HTTP_INTERNAL_SERVER_ERROR,
    'message' => $e->getMessage() ?? $message,
    'errors' => $errors,
    'meta' => [
      'current_page' => 1,
      'from' => 1,
      'last_page' => 1,
      'path' => url()->current(),
      'per_page' => 0,
      'to' => 0,
      'total' => 0,
    ],
  ], isset($opt['status']) ? $opt['status'] : Response::HTTP_INTERNAL_SERVER_ERROR);
}
