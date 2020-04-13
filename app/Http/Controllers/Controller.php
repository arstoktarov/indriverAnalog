<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public const PAGINATE_COUNT = 20;

    protected function uploadFile($file, $dir = ''){
        if (isset($file)){
            File::isDirectory($dir) or File::makeDirectory($dir, 0777, true, true);

            $file_type = File::extension($file->getClientOriginalName());
            $file_name = time().Str::random(5).'.'.$file_type;
            $file->move('uploads/'.$dir, $file_name);
            return 'uploads/'.$dir.'/'.$file_name;
        }
    }

    protected function deleteFile(string $path){
        if (File::exists($path)) {
            File::delete($path);
            return true;
        }
        else{
            return false;
        }
    }

    protected function Paginator($page, $limit, $model, $resource = null){
        if (!$page) $page = 1;
        $offset = ($page <= 1) ? 0:($page - 1) * $limit;
        $count = $model->count();

        $data = $model->offset($offset)->limit($limit)->get();
        $result['current_page'] = (int)$page;
        $result['count_pages'] = (int)ceil($count/$limit);
        $result['count_date'] = $count;
        $result['offset'] =  $offset;
        $result['limit'] = $limit;
        $result['data']  =  $resource != null ? $resource::collection($data) : $data;
        return $this->Result(200,$result);
    }

    protected function Result(int $statusCode, $data = null, $message = null){
        switch ($statusCode){
            case 200:
                $result['statusCode'] = $statusCode;
                $result['message'] = ($message) ? $message : trans('Успешно');
                $result['result'] = $data;
                break;

            case 404:
                $result['statusCode'] = $statusCode;
                $result['message'] = ($message) ? $message : trans('Не найдено');
                $result['result'] = $data;
                break;
            case 401:
                $result['statusCode'] = 401;
                $result['message'] = ($message) ? $message : trans('Не авторизован');
                $result['result'] = null;
                break;
            case 400:
                $result['statusCode'] = 400;
                $result['message'] = ($message) ? $message : 'Данные неверны';
                $result['result'] = $data;
                break;
            default:
                $result['statusCode'] = $statusCode;
                $result['message'] = $message;
                $result['result'] = $data;
                break;
        }

        return response()->json($result, $result['statusCode'], []);

    }

    public function paginatedToResource($paginated, $resource) {
        $collection = $paginated->getCollection();
        $collection = $resource::collection($collection)->collection;
        $paginated->setCollection($collection);
        return $paginated;
    }
}
