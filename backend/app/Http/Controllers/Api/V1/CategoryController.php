<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Requests\UpdateCategoryStatusRequest;
use App\Http\Requests\DeleteCategoryRequest;
use App\Models\Category;



class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = $request->user()
            ->categories()
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }


    public function store(StoreCategoryRequest $request)
    {
        $category = new Category();

        $category->name = $request->validated('name');
        $category->is_system = false;
        $category->is_active = true;

        $request->user()->categories()->save($category);

        return response()->json([
            'message' => 'Categoria criada com sucesso.',
            'data' => new CategoryResource($category),
        ], 201);
    }


    public function update(UpdateCategoryRequest $request, string $id)
    {
        $category = $request->user()
            ->categories()
            ->findOrFail($id);

        if ($category->is_system) {
            return response()->json([
                'message' => 'Categorias do sistema não podem ser renomeadas.',
            ], 403);
        }

        $category->update($request->validated());

        return response()->json([
            'message' => 'Categoria atualizada com sucesso.',
            'data' => new CategoryResource($category),
        ]);
    }


    public function updateStatus(UpdateCategoryStatusRequest $request, string $id)
    {
        $category = $request->user()
            ->categories()
            ->findOrFail($id);

        if (!$category->is_system) {
            return response()->json([
                'message' => 'Somente categorias do sistema podem ser ativadas ou desativadas.',
            ], 403);
        }

        $category->update($request->validated());

        return response()->json([
            'message' => 'Estado da categoria atualizado com sucesso.',
            'data' => new CategoryResource($category),
        ]);
    }

        public function destroy(DeleteCategoryRequest $request, string $id)
    {
        return DB::transaction(function () use ($request, $id) {

            $category = $request->user()
                ->categories()
                ->lockForUpdate()
                ->findOrFail($id);

            if ($category->is_system) {
                return response()->json([
                    'message' => 'Categorias do sistema não podem ser excluídas.',
                ], 403);
            }

            $newCategoryId = $request->validated('new_category_id');

            if ((int) $newCategoryId === (int) $category->id) {
                return response()->json([
                    'message' => 'A categoria de destino deve ser diferente da categoria excluída.',
                ], 422);
            }

            $newCategory = $request->user()
                ->categories()
                ->where('is_active', true)
                ->lockForUpdate()
                ->findOrFail($newCategoryId);

            $hasFinishedActivities = $category->activities()
                ->whereNotIn('status', ['pending', 'active'])
                ->exists();

            if ($hasFinishedActivities) {
                return response()->json([
                    'message' => 'Não é possível excluir esta categoria porque existem atividades finalizadas vinculadas a ela.',
                ], 409);
            }

            $category->activities()
                ->whereIn('status', ['pending', 'active'])
                ->update([
                    'category_id' => $newCategory->id,
                ]);

            $category->delete();

            return response()->json([
                'message' => 'Categoria excluída e atividades reclassificadas com sucesso.',
            ]);
        });
    }
}
