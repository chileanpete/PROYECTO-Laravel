<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    /**
     * Obtener todos los usuarios
     */
    public function index(): JsonResponse
    {
        $usuarios = Usuario::with(['configuracion', 'historialPeso'])
            ->where('activo', true)
            ->paginate(15);

        $response = response()->json([
            'success' => true,
            'data' => $usuarios
        ]);

        // Aplicar headers personalizados
        return $this->setCustomHeaders($response, [
            'X-Total-Count' => $usuarios->total(),
            'X-Page-Current' => $usuarios->currentPage(),
            'X-Page-Last' => $usuarios->lastPage(),
        ]);
    }

    /**
     * Obtener un usuario específico
     */
    public function show(int $id): JsonResponse
    {
        $usuario = Usuario::with([
            'configuracion',
            'historialPeso',
            'registrosActividad.tipoEjercicio',
            'registrosConsumo.plato',
            'favoritosPlatos.plato',
            'objetivosAlimentacion',
            'usuarioDesafios.desafio',
            'usuarioEventos.eventoAcademico'
        ])->find($id);

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $usuario
        ]);
    }

    /**
     * Crear un nuevo usuario
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|min:6',
            'nombre' => 'required|string|max:50',
            'apellidos' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date|before:today',
            'genero' => 'required|in:M,F,O',
            'altura_cm' => 'required|integer|min:100|max:250',
            'peso_kg' => 'required|numeric|min:30|max:300',
            'nivel_actividad' => 'required|in:sedentario,ligero,moderado,activo,muy_activo',
            'objetivo_principal' => 'required|in:perder_peso,ganar_peso,mantener_peso,ganar_musculo',
            'preferencias_alimentarias' => 'nullable|string',
            'alergias' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $usuario = Usuario::create([
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'genero' => $request->genero,
            'altura_cm' => $request->altura_cm,
            'peso_kg' => $request->peso_kg,
            'nivel_actividad' => $request->nivel_actividad,
            'objetivo_principal' => $request->objetivo_principal,
            'fecha_registro' => now(),
            'preferencias_alimentarias' => $request->preferencias_alimentarias,
            'alergias' => $request->alergias
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'data' => $usuario
        ], 201);
    }

    /**
     * Actualizar un usuario
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'email' => ['email', Rule::unique('usuarios')->ignore($id, 'id_usuario')],
            'nombre' => 'string|max:50',
            'apellidos' => 'string|max:100',
            'fecha_nacimiento' => 'date|before:today',
            'genero' => 'in:M,F,O',
            'altura_cm' => 'integer|min:100|max:250',
            'peso_kg' => 'numeric|min:30|max:300',
            'nivel_actividad' => 'in:sedentario,ligero,moderado,activo,muy_activo',
            'objetivo_principal' => 'in:perder_peso,ganar_peso,mantener_peso,ganar_musculo',
            'preferencias_alimentarias' => 'nullable|string',
            'alergias' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $usuario->update($request->only([
            'email', 'nombre', 'apellidos', 'fecha_nacimiento', 'genero',
            'altura_cm', 'peso_kg', 'nivel_actividad', 'objetivo_principal',
            'preferencias_alimentarias', 'alergias'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente',
            'data' => $usuario
        ]);
    }

    /**
     * Eliminar un usuario (soft delete)
     */
    public function destroy(int $id): JsonResponse
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $usuario->update(['activo' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario desactivado exitosamente'
        ]);
    }

    /**
     * Login de usuario
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $usuario = Usuario::where('email', $request->email)
            ->where('activo', true)
            ->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // Actualizar último acceso
        $usuario->update(['fecha_ultimo_acceso' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'data' => [
                'usuario' => $usuario,
                'token' => $usuario->createToken('auth-token')->plainTextToken
            ]
        ]);
    }

    /**
     * Obtener estadísticas del usuario
     */
    public function estadisticas(int $id): JsonResponse
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $estadisticas = [
            'total_actividades' => $usuario->registrosActividad()->count(),
            'total_consumos' => $usuario->registrosConsumo()->count(),
            'puntos_totales' => $usuario->puntos_totales,
            'objetivos_completados' => $usuario->objetivosAlimentacion()->where('completado', true)->count(),
            'desafios_activos' => $usuario->usuarioDesafios()->where('estado', 'activo')->count(),
            'ultima_actividad' => $usuario->registrosActividad()->latest()->first(),
            'ultimo_consumo' => $usuario->registrosConsumo()->latest()->first()
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarPassword(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password_actual' => 'required',
            'password_nuevo' => 'required|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        if (!Hash::check($request->password_actual, $usuario->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Contraseña actual incorrecta'
            ], 401);
        }

        $usuario->update([
            'password_hash' => Hash::make($request->password_nuevo)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente'
        ]);
    }
}
