<?php

namespace App\Domain\Shared\Repositories;

use App\Domain\Shared\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository
{
    protected $model;
    
    public function findById(int $id): ?Model
    {
        return $this->model::find($id);
    }
    
    public function findByIdOrFail(int $id): Model
    {
        $record = $this->findById($id);
        
        if (!$record) {
            $modelName = class_basename($this->model);
            throw new NotFoundException("{$modelName} con ID {$id} no encontrado");
        }
        
        return $record;
    }
    
    public function exists(int $id): bool
    {
        return $this->model::where('id', $id)->exists();
    }
    
    public function all(): Collection
    {
        return $this->model::all();
    }
    
    public function create(array $data): Model
    {
        return $this->model::create($data);
    }
    
    public function update(int $id, array $data): Model
    {
        $record = $this->findByIdOrFail($id);
        $record->update($data);
        return $record->fresh();
    }
    
    public function delete(int $id): bool
    {
        $record = $this->findByIdOrFail($id);
        return $record->delete();
    }
}