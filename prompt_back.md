# PROMPT DE CONTINUACIÓN - PROYECTO PLACEMY

## 📋 CONTEXTO DEL PROYECTO

**PlaceMy** es un sistema de gestión de establecimientos que permite a los meseros tomar pedidos mediante tablets. El sistema está dividido en:

- **Backend API REST**: Laravel 11 + MySQL
- **Frontend PWA**: Angular (por desarrollar)

---

## 🏗️ ARQUITECTURA DEL BACKEND

### **1. Patrón DDD (Domain-Driven Design)**

El proyecto sigue una arquitectura por dominios:
✅ Arquitectura DDD completa
✅ Service Providers modulares
✅ Validaciones con Requests
✅ Contratos para microservicios
✅ Sistema de permisos
✅ Catálogos con datos reales

Te adjunto la estructura en otro archivo 
---

## 🗄️ BASE DE DATOS - ESTRUCTURA ACTUAL

### **Tablas CORE (Implementadas)**

1. **core_tipos_documento** - Tipos de identificación (CC, CE, TI, PA, NIT, RUT)
2. **core_paises** - Países
3. **core_departamentos** - Departamentos/Estados
4. **core_ciudades** - Ciudades/Municipios
5. **core_personas_naturales** - Datos personales de usuarios
6. **core_usuarios** - Credenciales de acceso
7. **core_roles** - Roles del sistema
8. **core_permisos** - Permisos granulares
9. **core_usuarios_roles** - Relación Usuario-Rol
10. **core_roles_permisos** - Relación Rol-Permiso
11. **core_auditoria** - Log de todas las operaciones
12. **personal_access_tokens** - Tokens de Sanctum

Te adjunto la estructura completa en otro archivo

🔐 PERMISOS POR ESTABLECIMIENTO
Un usuario puede tener diferentes roles en diferentes establecimientos.
Tablas Clave

core_roles tiene establecimiento_id (rol pertenece a un establecimiento)
core_usuarios_roles tiene establecimiento_id (asignación es por establecimiento)

Respuesta del Login
json{
  "user": { "id": 1, "username": "..." },
  "establecimientos": [
    {
      "id": 1,
      "nombre": "Restaurante A",
      "roles": [
        { "id": 1, "nombre": "Admin", "permisos": ["mesas.ver", "mesas.crear"] }
      ]
    }
  ]
}
Métodos Usuario.php

getEstablecimientosIds() - IDs de establecimientos del usuario
rolesEnEstablecimiento($id) - Roles en un establecimiento
hasPermissionInEstablecimiento($permiso, $id) - Verificar permiso


👥 STAFF → ESTABLECIMIENTOSTAFF
La tabla staff fue eliminada. Ahora establecimiento_staff relaciona directamente usuarios con establecimientos.
Modelo: EstablecimientoStaff.php (ya no existe Staff.php)
FK en mesas: establecimiento_staff_id (antes era staff_asignado_id)

---
# 🏗️ GUÍA COMPLETA: CREAR NUEVO DOMINIO EN PLACEMY

## 📋 ARQUITECTURA DDD CON SERVICE PROVIDERS MODULARES

Esta guía describe el proceso completo para crear un nuevo dominio siguiendo Domain-Driven Design (DDD) con Service Providers separados, validaciones con Requests y contratos para escalabilidad.

---

## 🎯 EJEMPLO: DOMINIO PEDIDO

Vamos a crear el dominio **Pedido** paso a paso.

---

## PASO 1️⃣: CREAR TABLAS EN LA BASE DE DATOS

### **Script SQL:**

```sql
-- Tabla principal de pedidos
CREATE TABLE pedido_pedidos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero_pedido VARCHAR(20) UNIQUE NOT NULL,
    establecimiento_id BIGINT UNSIGNED NOT NULL,
    mesa_id BIGINT UNSIGNED NOT NULL,
    mesero_id BIGINT UNSIGNED NOT NULL,
    cliente_nombre VARCHAR(255),
    cliente_telefono VARCHAR(20),
    estado_id BIGINT UNSIGNED NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    impuestos DECIMAL(10,2) NOT NULL DEFAULT 0,
    propina DECIMAL(10,2) NOT NULL DEFAULT 0,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    notas TEXT,
    fecha_pedido DATETIME NOT NULL,
    fecha_entrega DATETIME,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (establecimiento_id) REFERENCES establecimientos(id),
    FOREIGN KEY (mesa_id) REFERENCES mesas(id),
    FOREIGN KEY (mesero_id) REFERENCES staff(id),
    FOREIGN KEY (estado_id) REFERENCES pedido_estados(id),
    INDEX idx_numero_pedido (numero_pedido),
    INDEX idx_establecimiento (establecimiento_id),
    INDEX idx_estado (estado_id),
    INDEX idx_fecha (fecha_pedido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Detalle de pedidos
CREATE TABLE pedido_detalles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL,
    plato_id BIGINT UNSIGNED NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    notas_especiales TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedido_pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (plato_id) REFERENCES platos(id),
    INDEX idx_pedido (pedido_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Estados de pedido (catálogo)
CREATE TABLE pedido_estados (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    color VARCHAR(20),
    orden INT DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## PASO 2️⃣: CREAR ESTRUCTURA DE CARPETAS

```
app/Domain/Pedido/
├── Contracts/
│   ├── PedidoServiceInterface.php
│   └── PedidoDetalleServiceInterface.php
├── Controllers/
│   ├── PedidoController.php
│   └── CatalogoController.php
├── Models/
│   ├── Pedido.php
│   ├── PedidoDetalle.php
│   └── PedidoEstado.php
├── Repositories/
│   ├── PedidoRepository.php
│   └── PedidoDetalleRepository.php
├── Requests/
│   ├── CreatePedidoRequest.php
│   └── UpdatePedidoRequest.php
└── Services/
    ├── PedidoService.php
    └── PedidoDetalleService.php
```

---

## PASO 3️⃣: CREAR MODELS

### **app/Domain/Pedido/Models/Pedido.php**

```php
<?php

namespace App\Domain\Pedido\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Establecimiento\Models\Establecimiento;
use App\Domain\Establecimiento\Models\Mesa;
use App\Domain\Establecimiento\Models\Staff;

class Pedido extends Model
{
    protected $table = 'pedido_pedidos';
    
    protected $fillable = [
        'numero_pedido',
        'establecimiento_id',
        'mesa_id',
        'mesero_id',
        'cliente_nombre',
        'cliente_telefono',
        'estado_id',
        'subtotal',
        'impuestos',
        'propina',
        'total',
        'notas',
        'fecha_pedido',
        'fecha_entrega',
        'activo',
    ];
    
    protected $casts = [
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'propina' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_pedido' => 'datetime',
        'fecha_entrega' => 'datetime',
        'activo' => 'boolean',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class, 'establecimiento_id');
    }
    
    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }
    
    public function mesero()
    {
        return $this->belongsTo(Staff::class, 'mesero_id');
    }
    
    public function estado()
    {
        return $this->belongsTo(PedidoEstado::class, 'estado_id');
    }
    
    public function detalles()
    {
        return $this->hasMany(PedidoDetalle::class, 'pedido_id');
    }
    
    // =====================================================
    // MÉTODOS DE NEGOCIO
    // =====================================================
    
    /**
     * Calcular totales del pedido
     */
    public function calcularTotales(): void
    {
        $this->subtotal = $this->detalles->sum('subtotal');
        $this->impuestos = $this->subtotal * 0.19; // 19% IVA Colombia
        $this->total = $this->subtotal + $this->impuestos + $this->propina;
        $this->save();
    }
    
    /**
     * Verificar si el pedido puede ser editado
     */
    public function puedeEditarse(): bool
    {
        return in_array($this->estado->codigo, ['pendiente', 'en_preparacion']);
    }
    
    /**
     * Verificar si el pedido puede ser cancelado
     */
    public function puedeCancelarse(): bool
    {
        return in_array($this->estado->codigo, ['pendiente', 'en_preparacion']);
    }
}
```

### **app/Domain/Pedido/Models/PedidoDetalle.php**

```php
<?php

namespace App\Domain\Pedido\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Establecimiento\Models\Plato;

class PedidoDetalle extends Model
{
    protected $table = 'pedido_detalles';
    
    protected $fillable = [
        'pedido_id',
        'plato_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'notas_especiales',
    ];
    
    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];
    
    // Relaciones
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
    
    public function plato()
    {
        return $this->belongsTo(Plato::class, 'plato_id');
    }
    
    // Método de negocio
    public function calcularSubtotal(): void
    {
        $this->subtotal = $this->cantidad * $this->precio_unitario;
        $this->save();
    }
}
```

### **app/Domain/Pedido/Models/PedidoEstado.php**

```php
<?php

namespace App\Domain\Pedido\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoEstado extends Model
{
    protected $table = 'pedido_estados';
    
    protected $fillable = [
        'nombre',
        'codigo',
        'color',
        'orden',
        'activo',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];
}
```

---

## PASO 4️⃣: CREAR REPOSITORIES

### **app/Domain/Pedido/Repositories/PedidoRepository.php**

```php
<?php

namespace App\Domain\Pedido\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Pedido\Models\Pedido;

class PedidoRepository extends BaseRepository
{
    public function __construct(Pedido $model)
    {
        $this->model = $model;
    }
    
    public function findByNumeroPedido(string $numero): ?Pedido
    {
        return $this->model::where('numero_pedido', $numero)
            ->with(['detalles.plato', 'mesero', 'mesa', 'estado'])
            ->first();
    }
    
    public function findByEstablecimiento(int $establecimientoId)
    {
        return $this->model::where('establecimiento_id', $establecimientoId)
            ->with(['detalles', 'mesero', 'mesa', 'estado'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    public function findPendientes(int $establecimientoId)
    {
        return $this->model::where('establecimiento_id', $establecimientoId)
            ->whereHas('estado', function($query) {
                $query->whereIn('codigo', ['pendiente', 'en_preparacion']);
            })
            ->with(['detalles', 'mesero', 'mesa'])
            ->orderBy('fecha_pedido', 'asc')
            ->get();
    }
}
```

---

## PASO 5️⃣: CREAR CONTRACTS (INTERFACES)

### **app/Domain/Pedido/Contracts/PedidoServiceInterface.php**

```php
<?php

namespace App\Domain\Pedido\Contracts;

use App\Domain\Pedido\Models\Pedido;

interface PedidoServiceInterface
{
    public function crear(array $data): Pedido;
    public function actualizar(int $id, array $data): Pedido;
    public function obtenerPorId(int $id): Pedido;
    public function obtenerPorNumeroPedido(string $numero): Pedido;
    public function listarPorEstablecimiento(int $establecimientoId);
    public function cambiarEstado(int $id, int $estadoId): Pedido;
    public function cancelar(int $id): Pedido;
}
```

---

## PASO 6️⃣: CREAR SERVICES

### **app/Domain/Pedido/Services/PedidoService.php**

```php
<?php

namespace App\Domain\Pedido\Services;

use App\Domain\Pedido\Contracts\PedidoServiceInterface;
use App\Domain\Pedido\Repositories\PedidoRepository;
use App\Domain\Pedido\Models\Pedido;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Exceptions\NotFoundException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;

class PedidoService implements PedidoServiceInterface
{
    protected $pedidoRepository;
    protected $auditoriaService;
    
    public function __construct(
        PedidoRepository $pedidoRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->pedidoRepository = $pedidoRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    public function crear(array $data): Pedido
    {
        DB::beginTransaction();
        try {
            // Generar número de pedido
            $data['numero_pedido'] = $this->generarNumeroPedido();
            $data['fecha_pedido'] = now();
            
            $pedido = $this->pedidoRepository->create($data);
            
            // Crear detalles
            if (isset($data['detalles']) && is_array($data['detalles'])) {
                foreach ($data['detalles'] as $detalle) {
                    $pedido->detalles()->create([
                        'plato_id' => $detalle['plato_id'],
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                        'subtotal' => $detalle['cantidad'] * $detalle['precio_unitario'],
                        'notas_especiales' => $detalle['notas_especiales'] ?? null,
                    ]);
                }
            }
            
            // Calcular totales
            $pedido->calcularTotales();
            
            // Auditoría
            $this->auditoriaService->registrar(
                'pedido_pedidos',
                $pedido->id,
                'INSERT',
                auth()->id(),
                null,
                $pedido->toArray()
            );
            
            DB::commit();
            return $pedido->load(['detalles.plato', 'mesero', 'mesa', 'estado']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function actualizar(int $id, array $data): Pedido
    {
        $pedido = $this->obtenerPorId($id);
        
        if (!$pedido->puedeEditarse()) {
            throw new BusinessException('El pedido no puede ser editado en este estado');
        }
        
        DB::beginTransaction();
        try {
            $pedidoAntes = $pedido->toArray();
            
            $pedido = $this->pedidoRepository->update($id, $data);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'pedido_pedidos',
                $pedido->id,
                'UPDATE',
                auth()->id(),
                $pedidoAntes,
                $pedido->toArray()
            );
            
            DB::commit();
            return $pedido->load(['detalles', 'mesero', 'mesa', 'estado']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function obtenerPorId(int $id): Pedido
    {
        $pedido = $this->pedidoRepository->findOrFail($id);
        return $pedido->load(['detalles.plato', 'mesero', 'mesa', 'estado']);
    }
    
    public function obtenerPorNumeroPedido(string $numero): Pedido
    {
        $pedido = $this->pedidoRepository->findByNumeroPedido($numero);
        
        if (!$pedido) {
            throw new NotFoundException('Pedido no encontrado');
        }
        
        return $pedido;
    }
    
    public function listarPorEstablecimiento(int $establecimientoId)
    {
        return $this->pedidoRepository->findByEstablecimiento($establecimientoId);
    }
    
    public function cambiarEstado(int $id, int $estadoId): Pedido
    {
        $pedido = $this->obtenerPorId($id);
        
        DB::beginTransaction();
        try {
            $estadoAnterior = $pedido->estado_id;
            
            $pedido->estado_id = $estadoId;
            $pedido->save();
            
            // Auditoría
            $this->auditoriaService->registrar(
                'pedido_pedidos',
                $pedido->id,
                'CAMBIO_ESTADO',
                auth()->id(),
                ['estado_id' => $estadoAnterior],
                ['estado_id' => $estadoId]
            );
            
            DB::commit();
            return $pedido->load(['estado']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function cancelar(int $id): Pedido
    {
        $pedido = $this->obtenerPorId($id);
        
        if (!$pedido->puedeCancelarse()) {
            throw new BusinessException('El pedido no puede ser cancelado en este estado');
        }
        
        // Buscar estado "cancelado"
        $estadoCancelado = \App\Domain\Pedido\Models\PedidoEstado::where('codigo', 'cancelado')->first();
        
        return $this->cambiarEstado($id, $estadoCancelado->id);
    }
    
    private function generarNumeroPedido(): string
    {
        $fecha = now()->format('Ymd');
        $ultimo = Pedido::whereDate('created_at', today())->count() + 1;
        return "PED-{$fecha}-" . str_pad($ultimo, 4, '0', STR_PAD_LEFT);
    }
}
```

---

## PASO 7️⃣: CREAR REQUESTS

### **app/Domain/Pedido/Requests/CreatePedidoRequest.php**

```php
<?php

namespace App\Domain\Pedido\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePedidoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'establecimiento_id' => 'required|integer',
            'mesa_id' => 'required|integer',
            'mesero_id' => 'required|integer',
            'estado_id' => 'required|integer',
            'cliente_nombre' => 'nullable|string|max:255',
            'cliente_telefono' => 'nullable|string|max:20',
            'notas' => 'nullable|string',
            'propina' => 'nullable|numeric|min:0',
            
            // Detalles del pedido
            'detalles' => 'nullable|array',
            'detalles.*.plato_id' => 'required|integer',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.notas_especiales' => 'nullable|string',
        ];
    }
    
    public function messages()
    {
        return [
            'establecimiento_id.required' => 'El establecimiento es obligatorio',
            'mesa_id.required' => 'La mesa es obligatoria',
            'mesero_id.required' => 'El mesero es obligatorio',
            'estado_id.required' => 'El estado es obligatorio',
        ];
    }
}
```

### **app/Domain/Pedido/Requests/UpdatePedidoRequest.php**

```php
<?php

namespace App\Domain\Pedido\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePedidoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'cliente_nombre' => 'nullable|string|max:255',
            'cliente_telefono' => 'nullable|string|max:20',
            'notas' => 'nullable|string',
            'propina' => 'nullable|numeric|min:0',
        ];
    }
}
```

---

## PASO 8️⃣: CREAR CONTROLLERS

### **app/Domain/Pedido/Controllers/PedidoController.php**

```php
<?php

namespace App\Domain\Pedido\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Pedido\Contracts\PedidoServiceInterface;
use App\Domain\Pedido\Requests\CreatePedidoRequest;
use App\Domain\Pedido\Requests\UpdatePedidoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    protected $pedidoService;
    
    public function __construct(PedidoServiceInterface $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }
    
    /**
     * Listar pedidos por establecimiento
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $establecimientoId = $request->input('establecimiento_id');
            
            if (!$establecimientoId) {
                return response()->json([
                    'success' => false,
                    'message' => 'El establecimiento es obligatorio'
                ], 400);
            }
            
            $pedidos = $this->pedidoService->listarPorEstablecimiento($establecimientoId);
            
            return response()->json([
                'success' => true,
                'data' => $pedidos
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Crear nuevo pedido
     */
    public function store(CreatePedidoRequest $request): JsonResponse
    {
        try {
            $pedido = $this->pedidoService->crear($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Pedido creado exitosamente',
                'data' => $pedido
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mostrar pedido específico
     */
    public function show(int $id): JsonResponse
    {
        try {
            $pedido = $this->pedidoService->obtenerPorId($id);
            
            return response()->json([
                'success' => true,
                'data' => $pedido
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Actualizar pedido
     */
    public function update(UpdatePedidoRequest $request, int $id): JsonResponse
    {
        try {
            $pedido = $this->pedidoService->actualizar($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Pedido actualizado exitosamente',
                'data' => $pedido
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Cambiar estado del pedido
     */
    public function cambiarEstado(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'estado_id' => 'required|integer'
            ]);
            
            $pedido = $this->pedidoService->cambiarEstado($id, $request->estado_id);
            
            return response()->json([
                'success' => true,
                'message' => 'Estado cambiado exitosamente',
                'data' => $pedido
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Cancelar pedido
     */
    public function cancelar(int $id): JsonResponse
    {
        try {
            $pedido = $this->pedidoService->cancelar($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Pedido cancelado exitosamente',
                'data' => $pedido
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
```

---

## PASO 9️⃣: CREAR SERVICE PROVIDER DEL DOMINIO

### **app/Providers/PedidoServiceProvider.php**

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repositories
use App\Domain\Pedido\Repositories\PedidoRepository;
use App\Domain\Pedido\Repositories\PedidoDetalleRepository;

// Contracts
use App\Domain\Pedido\Contracts\PedidoServiceInterface;

// Services
use App\Domain\Pedido\Services\PedidoService;

class PedidoServiceProvider extends ServiceProvider
{
    /**
     * Register Pedido domain services.
     */
    public function register(): void
    {
        // Repositories (Singleton)
        $this->app->singleton(PedidoRepository::class);
        $this->app->singleton(PedidoDetalleRepository::class);
        
        // Services con Interfaces
        $this->app->bind(PedidoServiceInterface::class, PedidoService::class);
    }
    
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
```

---

## PASO 🔟: REGISTRAR SERVICE PROVIDER

### **bootstrap/providers.php** (Laravel 11+)

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\SharedServiceProvider::class,
    App\Providers\CoreServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EstablecimientoServiceProvider::class,
    App\Providers\PedidoServiceProvider::class,  // ← AGREGAR ESTA LÍNEA
];
```

---

## PASO 1️⃣1️⃣: CREAR RUTAS

### **routes/domains/pedido.php**

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Pedido\Controllers\PedidoController;

Route::prefix('pedido')->name('pedido.')->middleware(['auth:sanctum'])->group(function () {
    
    // Pedidos
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/{id}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
    Route::put('/pedidos/{id}', [PedidoController::class, 'update'])->name('pedidos.update');
    Route::post('/pedidos/{id}/cambiar-estado', [PedidoController::class, 'cambiarEstado'])->name('pedidos.cambiar-estado');
    Route::post('/pedidos/{id}/cancelar', [PedidoController::class, 'cancelar'])->name('pedidos.cancelar');
    
});
```

---

## PASO 1️⃣2️⃣: CARGAR RUTAS EN api.php

### **routes/api.php**

```php
<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    
    // Dominios existentes
    require __DIR__.'/domains/establecimiento.php';
    
    // AGREGAR: Nuevo dominio Pedido
    require __DIR__.'/domains/pedido.php';
    
});
```

---

## PASO 1️⃣3️⃣: CREAR SEEDERS

### **database/seeders/Pedido/EstadosPedidoSeeder.php**

```php
<?php

namespace Database\Seeders\Pedido;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosPedidoSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['nombre' => 'Pendiente', 'codigo' => 'pendiente', 'color' => '#FFA500', 'orden' => 1],
            ['nombre' => 'En Preparación', 'codigo' => 'en_preparacion', 'color' => '#1E90FF', 'orden' => 2],
            ['nombre' => 'Listo para Servir', 'codigo' => 'listo', 'color' => '#32CD32', 'orden' => 3],
            ['nombre' => 'Entregado', 'codigo' => 'entregado', 'color' => '#228B22', 'orden' => 4],
            ['nombre' => 'Cancelado', 'codigo' => 'cancelado', 'color' => '#DC143C', 'orden' => 5],
        ];
        
        foreach ($estados as $estado) {
            DB::table('pedido_estados')->updateOrInsert(
                ['codigo' => $estado['codigo']],
                array_merge($estado, [
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
```

---

## PASO 1️⃣4️⃣: VERIFICAR INSTALACIÓN

### **Comandos de verificación:**

```powershell
# Limpiar cache
php artisan config:clear
php artisan route:clear

# Ver estado general
php artisan about

# Listar rutas del dominio
php artisan route:list --path=pedido

# Ejecutar seeders
php artisan db:seed --class=Database\\Seeders\\Pedido\\EstadosPedidoSeeder

# Verificar en Tinker
php artisan tinker
```

```php
// En Tinker
app(App\Domain\Pedido\Contracts\PedidoServiceInterface::class);
// Debe retornar: App\Domain\Pedido\Services\PedidoService

App\Domain\Pedido\Models\Pedido::count();
exit
```

---

## ✅ CHECKLIST COMPLETO

- [ ] Crear tablas en BD
- [ ] Crear estructura de carpetas
- [ ] Crear Models (con relaciones y métodos de negocio)
- [ ] Crear Repositories
- [ ] Crear Contracts (interfaces)
- [ ] Crear Services
- [ ] Crear Requests (validaciones)
- [ ] Crear Controllers
- [ ] Crear Service Provider del dominio
- [ ] Registrar Service Provider en bootstrap/providers.php
- [ ] Crear archivo de rutas
- [ ] Cargar rutas en api.php
- [ ] Crear Seeders
- [ ] Ejecutar seeders
- [ ] Verificar con `php artisan route:list`
- [ ] Probar en Postman

---

## 🎯 PATRÓN DE NOMENCLATURA

### **Tablas:**
- `{dominio}_{entidad_plural}` (ejemplo: `pedido_pedidos`, `pedido_detalles`)

### **Models:**
- Singular, PascalCase (ejemplo: `Pedido`, `PedidoDetalle`)

### **Repositories:**
- `{Entidad}Repository` (ejemplo: `PedidoRepository`)

### **Services:**
- `{Entidad}Service` (ejemplo: `PedidoService`)

### **Contracts:**
- `{Entidad}ServiceInterface` (ejemplo: `PedidoServiceInterface`)

### **Controllers:**
- `{Entidad}Controller` (ejemplo: `PedidoController`)

### **Requests:**
- `Create{Entidad}Request`, `Update{Entidad}Request`

### **Service Providers:**
- `{Dominio}ServiceProvider` (ejemplo: `PedidoServiceProvider`)

---

## 🚀 VENTAJAS DE ESTA ARQUITECTURA

1. **Escalabilidad:** Cada dominio es independiente
2. **Testeable:** Fácil hacer tests unitarios por dominio
3. **Mantenible:** Código organizado y fácil de encontrar
4. **Reutilizable:** Contratos permiten cambiar implementaciones
5. **Microservicios Ready:** Fácil migrar a microservicios
6. **Service Providers Modulares:** Solo cargas lo que necesitas

---

## 📚 DOCUMENTACIÓN RELACIONADA

- [Estructura del Proyecto](ESTRUCTURA_PROYECTO.md)
- [Sistema de Permisos](SISTEMA_PERMISOS.md)
- [Contratos y Escalabilidad](CONTRATOS_GUIA.md)
- [Service Providers Modulares](INSTALACION_PROVIDERS.md)

---

**¡Listo para crear tu siguiente dominio!** 🎉

---

## 🎯 PRÓXIMOS PASOS SUGERIDOS

### **Opción 1: Continuar con Backend**
1. Crear dominio CUENTA (Mesas, Meseros, Productos)
2. Crear dominio PEDIDO (Pedidos, Detalle)
3. Implementar endpoints CRUD para cada entidad
4. Agregar middleware de permisos

### **Opción 2: Crear PWA (Frontend)**
1. Crear proyecto Angular con soporte PWA
2. Implementar página de login
3. Implementar servicio de autenticación (guardar tokens)
4. Crear interceptor HTTP para agregar token a requests
5. Crear guard de autenticación
6. Crear módulo de pedidos básico

---

## 🔑 INFORMACIÓN IMPORTANTE

### **Credenciales Base de Datos:**
```
Host: 92.205.2.16
Port: 3306
Database: placemy-prod
User: (proporcionado por el usuario)
Password: (proporcionado por el usuario)
```

### **Servidor de desarrollo:**
```bash
php artisan serve
# Corre en: http://127.0.0.1:8000
```

### **Testing con Postman:**
```
Base URL: http://127.0.0.1:8000/api
```

---

## 🎓 LECCIONES APRENDIDAS Y CONFIGURACIONES CRÍTICAS

### **1. Configuración de Sanctum para API (IMPORTANTE)**

**Archivo: `bootstrap/app.php`**
```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // CRÍTICO: Configurar respuestas JSON para API
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated.'
                ], 401);
            }
        });
    })->create();
```

### **2. Configuración de Models con relaciones Many-to-Many**

**IMPORTANTE:** Para tablas intermedias SIN `created_at` y `updated_at`:

```php
public function roles()
{
    return $this->belongsToMany(
        Rol::class,
        'core_usuarios_roles',
        'usuario_id',
        'rol_id'
    )->withPivot('fecha_asignacion');  // ✅ NO usar withTimestamps()
}
```

**Para tablas intermedias CON campos personalizados:**
- Usar `->withPivot('campo_personalizado')` en lugar de `->withTimestamps()`

### **3. Configuración de Angular para consumir API Laravel**

**En Angular HttpClient:**
```typescript
const headers = new HttpHeaders({
  'Content-Type': 'application/json',
  'Accept': 'application/json'  // ← CRÍTICO para que Laravel devuelva JSON
});

return this.http.post(url, data, { headers });
```

**SIEMPRE incluir el header `Accept: application/json`** en todas las peticiones a Laravel.

### **4. Manejo de errores en Controllers**

```php
return response()->json([
    'success' => false,
    'message' => $e->getMessage()
], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);  // ← Convertir código a int
```

### **5. Estructura de carpetas del proyecto**

```
placemy-back/
├── app/
│   ├── Domain/
│   │   ├── Shared/
│   │   │   ├── Exceptions/
│   │   │   │   ├── BusinessException.php
│   │   │   │   └── NotFoundException.php
│   │   │   ├── Services/
│   │   │   │   └── AuditoriaService.php
│   │   │   └── Repositories/
│   │   │       └── BaseRepository.php
│   │   ├── Core/
│   │   │   ├── Models/
│   │   │   ├── Repositories/
│   │   │   └── Services/
│   │   └── Auth/
│   │       ├── Controllers/
│   │       ├── Services/
│   │       └── Requests/
│   └── Providers/
│       └── DomainServiceProvider.php
├── routes/
│   ├── api.php
│   └── domains/
│       └── auth.php
└── bootstrap/
    └── app.php
```

---

## ❓ INSTRUCCIONES PARA LA PRÓXIMA SESIÓN

**Indica al asistente:**

1. **Si quieres continuar con backend:**
   ```
   Quiero crear el dominio CUENTA con todas sus entidades (Cuenta, Mesas, Meseros, Categorias, Productos).
   Sigue el patrón DDD establecido y las configuraciones críticas documentadas.
   ```
   
2. **Si quieres crear otro dominio:**
   ```
   Quiero crear el dominio PEDIDO (Pedidos, PedidoDetalle).
   Sigue el patrón DDD establecido y las configuraciones críticas documentadas.
   ```

3. **Si quieres trabajar en el frontend:**
   ```
   Quiero trabajar en el frontend Angular para [descripción de funcionalidad].
   ```

4. **Recuerda SIEMPRE indicar:**
   - Que respete las preferencias de desarrollo (no asumir, preguntar antes de codificar)
   - Que siga el patrón de arquitectura DDD establecido
   - Que use validaciones explícitas en los Services
   - Que NO use `->withTimestamps()` en relaciones muchos-a-muchos sin verificar la estructura de la tabla

---

## 📚 ARQUITECTURA ESTABLECIDA

**Principios clave:**
1. ✅ Validaciones explícitas en Services (como si no existieran FKs)
2. ✅ Auditoría en todas las operaciones de escritura
3. ✅ Transacciones DB en operaciones críticas
4. ✅ Responses estandarizados en Controllers con manejo correcto de códigos de error
5. ✅ Separación clara de responsabilidades (Controller → Service → Repository → Model)
6. ✅ BaseRepository con métodos comunes reutilizables
7. ✅ Manejo de excepciones con BusinessException y NotFoundException
8. ✅ Configuración correcta de Sanctum para respuestas JSON en rutas API
9. ✅ Header `Accept: application/json` obligatorio en frontend
10. ✅ Relaciones many-to-many sin `withTimestamps()` cuando no aplica

---

## 🔑 CREDENCIALES Y CONFIGURACIÓN

### **Base de datos:**
```
Host: 92.205.2.16:3306
Database: placemy-prod
```

### **Usuario de prueba:**
```
Identifier: 42132501
Password: admin123
Rol: Super Administrador
```

### **Servidor backend:**
```bash
cd C:\xampp\htdocs\placemy-back
php artisan serve
# http://127.0.0.1:8000
```

### **Probar endpoints protegidos con Postman:**
```
Headers obligatorios:
  Accept: application/json
  Authorization: Bearer {access_token}
```

---
---

## ✅ ESTADO ACTUAL DEL PROYECTO

### **Backend completado:**

✅ Conexión a base de datos de producción MySQL
✅ Laravel Framework 12.37.0 instalado y configurado
✅ Laravel Sanctum configurado para autenticación con tokens
✅ Domain Shared completo (Exceptions, BaseRepository, AuditoriaService)
✅ Domain Core completo:
  - Models: PersonaNatural, Usuario, Rol, Permiso, TipoDocumento, Pais, Departamento, Ciudad
  - Repositories: PersonaNaturalRepository, UsuarioRepository, RolRepository
  - Services: PersonaNaturalService, UsuarioService (con validaciones explícitas)
✅ Domain Auth completo:
  - AuthController (login, logout, refresh, me)
  - AuthService (lógica de login con control de intentos fallidos)
  - LoginRequest (validación de credenciales)
✅ DomainServiceProvider registrando todas las dependencias
✅ Sistema de auditoría funcionando

### **Endpoints funcionando:**

```
POST   /api/auth/login      - Login con username/email + password
POST   /api/auth/refresh    - Renovar access token
POST   /api/auth/logout     - Cerrar sesión y revocar tokens
GET    /api/auth/me         - Obtener datos del usuario autenticado
```

### **Usuario de prueba creado:**

```
Username: 42132501
Email: mariarestrepo77@gmail.com
Password: admin123
Rol: Super Administrador
```



Reglas de oro:

✅ Cada dominio NO puede acceder directamente a otro dominio
✅ Comunicación SOLO a través de Services
✅ Cada dominio podría tener su propia BD (aunque ahora esté en una)
✅ APIs internas bien definidas




### **REGLA 1: Cada dominio es independiente**
```
✅ PERMITIDO:
Domain/Establecimiento → puede usar → Domain/Core (usuarios, ubicaciones)
Domain/Pedido → puede usar → Domain/Core
Domain/Pedido → puede usar → Domain/Establecimiento (a través de Service)

❌ PROHIBIDO:
Domain/Establecimiento → NO puede usar → Domain/Pedido
Domain/Core → NO puede usar → Domain/Establecimiento

REGLA 2: Comunicación SOLO a través de Services
php// ❌ PROHIBIDO
use App\Domain\Establecimiento\Models\Mesa;
$mesa = Mesa::find($id);

// ✅ PERMITIDO
use App\Domain\Establecimiento\Services\MesaService;
$mesa = $this->mesaService->obtenerPorId($id);


REGLA 3: Nunca exponer Models fuera del dominio
php// ❌ MAL
public function obtenerMesa(int $id): Mesa {
    return Mesa::find($id); // Retorna Model
}

// ✅ BIEN
public function obtenerMesa(int $id): array {
    $mesa = Mesa::find($id);
    return [
        'id' => $mesa->id,
        'identificacion' => $mesa->identificacion_mesa,
        'estado' => $mesa->estado->nombre,
        // Solo lo que necesitas
    ];
}
REGLA 4: Base de datos separadas lógicamente
sql-- Aunque estén en la misma BD ahora, NO uses JOINs entre dominios

-- ❌ PROHIBIDO
SELECT p.*, m.identificacion_mesa, e.nombre
FROM pedido_pedidos p
JOIN mesas m ON p.mesa_id = m.id           -- ← Cruzando dominios
JOIN establecimientos e ON m.establecimiento_id = e.id;

-- ✅ PERMITIDO
SELECT p.* FROM pedido_pedidos p WHERE mesa_id = ?;
-- Y luego llamas al Service de Establecimiento para obtener datos de mesa

**FIN DEL PROMPT DE CONTINUACIÓN**