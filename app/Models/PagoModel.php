<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo de Pagos
 * Maneja todas las operaciones de pagos y generacion automatica de deudas
 */
class PagoModel extends Model
{
    protected $table            = 'pagos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'id_usuario',
        'mes',
        'anio',
        'monto',
        'estado',
        'captura',
        'observacion',
        'fecha_envio',
        'fecha_aprobacion',
    ];

    protected $validationRules = [
        'id_usuario' => 'required|integer',
        'mes'        => 'required|integer|in_list[1,2,3,4,5,6,7,8,9,10,11,12]',
        'anio'       => 'required|integer',
        'monto'      => 'required|decimal',
        'estado'     => 'required|in_list[NO_PAGADO,PENDIENTE,PAGADO,RECHAZADO]',
    ];

    /**
     * Generar deudas automaticas para el mes actual
     * Se ejecuta en cada peticion para asegurar que todos tengan registro del mes vigente
     */
    public function GenerarDeudasAutomaticas(): void
    {
        $usuarioModel = new \App\Models\UsuarioModel();
        $usuarios = $usuarioModel->ObtenerActivos();

        $mesActual = (int) date('m');
        $anioActual = (int) date('Y');

        foreach ($usuarios as $usuario) {
            // Verificar si ya existe registro para este mes
            $existe = $this->where('id_usuario', $usuario['id'])
                            ->where('mes', $mesActual)
                            ->where('anio', $anioActual)
                            ->countAllResults();

            // Si no existe, crear registro NO_PAGADO
            if ($existe === 0) {
                $this->insert([
                    'id_usuario' => $usuario['id'],
                    'mes'        => $mesActual,
                    'anio'       => $anioActual,
                    'monto'      => $usuario['monto'] ?? 12.00,
                    'estado'     => 'NO_PAGADO',
                ]);
            }
        }
    }

    /**
     * Obtener el estado del mes actual para un usuario
     */
    public function EstadoMesActual(int $idUsuario): ?array
    {
        $mesActual = (int) date('m');
        $anioActual = (int) date('Y');

        return $this->where('id_usuario', $idUsuario)
                     ->where('mes', $mesActual)
                     ->where('anio', $anioActual)
                     ->first();
    }

    /**
     * Obtener historial completo de un usuario
     */
    public function HistorialUsuario(int $idUsuario): array
    {
        return $this->where('id_usuario', $idUsuario)
                     ->orderBy('anio', 'DESC')
                     ->orderBy('mes', 'DESC')
                     ->findAll();
    }

    /**
     * Obtener todos los pagos con datos del usuario (para admin)
     */
    public function ObtenerPagosConUsuario(): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pagos p');
        $builder->select('p.*, u.nombre, u.telefono, u.token');
        $builder->join('usuarios u', 'u.id = p.id_usuario', 'left');
        $builder->orderBy('p.anio', 'DESC');
        $builder->orderBy('p.mes', 'DESC');
        $builder->orderBy('u.nombre', 'ASC');
        $query = $builder->get();
        return $query->getResultArray();
    }

    /**
     * Obtener pagos del mes actual con usuario
     */
    public function PagosMesActual(): array
    {
        $mesActual = (int) date('m');
        $anioActual = (int) date('Y');

        $db = \Config\Database::connect();
        $builder = $db->table('pagos p');
        $builder->select('p.*, u.nombre, u.telefono, u.token');
        $builder->join('usuarios u', 'u.id = p.id_usuario', 'left');
        $builder->where('p.mes', $mesActual);
        $builder->where('p.anio', $anioActual);
        $builder->orderBy('u.nombre', 'ASC');
        $query = $builder->get();
        return $query->getResultArray();
    }

    /**
     * Contar pagos por estado en el mes actual
     */
    public function ContarPorEstadoMesActual(string $estado): int
    {
        $mesActual = (int) date('m');
        $anioActual = (int) date('Y');

        return $this->where('mes', $mesActual)
                     ->where('anio', $anioActual)
                     ->where('estado', $estado)
                     ->countAllResults();
    }

    /**
     * Contar usuarios con al menos un NO_PAGADO
     */
    public function ContarConDeuda(): int
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pagos');
        $builder->select('COUNT(DISTINCT id_usuario) as total');
        $builder->where('estado', 'NO_PAGADO');
        $query = $builder->get();
        $row = $query->getRowArray();
        return $row['total'] ?? 0;
    }

    /**
     * Sumar montos cobrados en el mes actual
     */
    public function TotalRecaudadoMesActual(): float
    {
        $mesActual = (int) date('m');
        $anioActual = (int) date('Y');

        $result = $this->selectSum('monto')
                        ->where('mes', $mesActual)
                        ->where('anio', $anioActual)
                        ->where('estado', 'PAGADO')
                        ->first();

        return (float) ($result['monto'] ?? 0);
    }

    /**
     * Sumar montos cobrados historico
     */
    public function TotalRecaudadoHistorico(): float
    {
        $result = $this->selectSum('monto')
                        ->where('estado', 'PAGADO')
                        ->first();

        return (float) ($result['monto'] ?? 0);
    }

    /**
     * Aprobar un pago
     */
    public function AprobarPago(int $idPago): bool
    {
        return $this->update($idPago, [
            'estado'          => 'PAGADO',
            'fecha_aprobacion' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Rechazar un pago
     */
    public function RechazarPago(int $idPago, string $observacion = ''): bool
    {
        return $this->update($idPago, [
            'estado'          => 'RECHAZADO',
            'observacion'     => $observacion,
            'fecha_aprobacion' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Subir comprobante - actualizar registro con imagen
     */
    public function SubirComprobante(int $idPago, string $rutaImagen): bool
    {
        return $this->update($idPago, [
            'captura'    => $rutaImagen,
            'fecha_envio' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Enviar comprobante para un usuario en un mes especifico
     */
    public function EnviarComprobante(int $idUsuario, int $mes, int $anio, string $rutaImagen): bool
    {
        $pago = $this->where('id_usuario', $idUsuario)
                      ->where('mes', $mes)
                      ->where('anio', $anio)
                      ->first();

        if (!$pago) {
            return false;
        }

        return $this->update($pago['id'], [
            'estado'     => 'PENDIENTE',
            'captura'    => $rutaImagen,
            'fecha_envio' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Obtener meses adeudados por un usuario
     */
    public function MesesAdeudados(int $idUsuario): array
    {
        return $this->where('id_usuario', $idUsuario)
                     ->whereNotIn('estado', ['PAGADO'])
                     ->orderBy('anio', 'ASC')
                     ->orderBy('mes', 'ASC')
                     ->findAll();
    }

    /**
     * Total adeudado por un usuario
     */
    public function TotalAdeudado(int $idUsuario): float
    {
        $result = $this->selectSum('monto')
                        ->where('id_usuario', $idUsuario)
                        ->whereNotIn('estado', ['PAGADO'])
                        ->first();

        return (float) ($result['monto'] ?? 0);
    }

    /**
     * Contar usuarios con pagos pendientes de aprobacion
     */
    public function ContarPendientes(): int
    {
        $mesActual = (int) date('m');
        $anioActual = (int) date('Y');

        return $this->where('mes', $mesActual)
                     ->where('anio', $anioActual)
                     ->where('estado', 'PENDIENTE')
                     ->countAllResults();
    }

    /**
     * Verificar si un usuario ya pago el mes actual
     */
    public function YaPagoMesActual(int $idUsuario): bool
    {
        $mesActual = (int) date('m');
        $anioActual = (int) date('Y');

        return $this->where('id_usuario', $idUsuario)
                     ->where('mes', $mesActual)
                     ->where('anio', $anioActual)
                     ->where('estado', 'PAGADO')
                     ->countAllResults() > 0;
    }

    /**
     * Obtener el ultimo pago de un usuario
     */
    public function UltimoPagoUsuario(int $idUsuario): ?array
    {
        return $this->where('id_usuario', $idUsuario)
                     ->where('estado', 'PAGADO')
                     ->orderBy('anio', 'DESC')
                     ->orderBy('mes', 'DESC')
                     ->first();
    }

    /**
     * Eliminar un registro de pago
     */
    public function EliminarPago(int $idPago): bool
    {
        return $this->delete($idPago);
    }

    /**
     * Obtener datos para el DataTable de pagos admin
     */
    public function ParaDataTable(): array
    {
        $mesActual = (int) date('m');
        $anioActual = (int) date('Y');

        $db = \Config\Database::connect();
        $builder = $db->table('pagos p');
        $builder->select('
            p.id,
            p.mes,
            p.anio,
            p.monto,
            p.estado,
            p.captura,
            p.observacion,
            p.fecha_envio,
            p.fecha_aprobacion,
            u.nombre,
            u.telefono,
            u.token,
            u.id as id_usuario
        ');
        $builder->join('usuarios u', 'u.id = p.id_usuario', 'left');
        $builder->where('p.mes', $mesActual);
        $builder->where('p.anio', $anioActual);
        $builder->orderBy('p.estado', 'ASC');
        $builder->orderBy('u.nombre', 'ASC');
        $query = $builder->get();
        return $query->getResultArray();
    }

    /**
     * Obtener pagos con filtros opcionales (para dashboard y admin)
     */
    public function ConFiltros(?int $idUsuario = null, ?int $mes = null, ?int $anio = null, ?string $estado = null): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pagos p');
        $builder->select('
            p.id,
            p.mes,
            p.anio,
            p.monto,
            p.estado,
            p.captura,
            p.observacion,
            p.fecha_envio,
            p.fecha_aprobacion,
            u.nombre,
            u.telefono,
            u.token,
            u.id as id_usuario
        ');
        $builder->join('usuarios u', 'u.id = p.id_usuario', 'left');

        if ($idUsuario !== null) {
            $builder->where('p.id_usuario', $idUsuario);
        }
        if ($mes !== null) {
            $builder->where('p.mes', $mes);
        }
        if ($anio !== null) {
            $builder->where('p.anio', $anio);
        }
        if ($estado !== null) {
            $builder->where('p.estado', $estado);
        }

        $builder->orderBy('p.anio', 'DESC');
        $builder->orderBy('p.mes', 'DESC');
        $builder->orderBy('u.nombre', 'ASC');

        $query = $builder->get();
        return $query->getResultArray();
    }

    /**
     * Estadisticas con filtros opcionales
     */
    public function EstadisticasConFiltros(?int $idUsuario = null, ?int $mes = null, ?int $anio = null, ?string $estado = null): array
    {
        $db = \Config\Database::connect();

        // Query principal con filtros
        $builder = $db->table('pagos p');
        $builder->join('usuarios u', 'u.id = p.id_usuario', 'left');
        if ($idUsuario !== null) {
            $builder->where('p.id_usuario', $idUsuario);
        }
        if ($mes !== null) {
            $builder->where('p.mes', $mes);
        }
        if ($anio !== null) {
            $builder->where('p.anio', $anio);
        }
        if ($estado !== null) {
            $builder->where('p.estado', $estado);
        }

        $builder->select('
            COUNT(DISTINCT p.id_usuario) as totalConRegistro,
            SUM(CASE WHEN p.estado = "PAGADO" THEN 1 ELSE 0 END) as pagados,
            SUM(CASE WHEN p.estado = "PENDIENTE" THEN 1 ELSE 0 END) as pendientes,
            SUM(CASE WHEN p.estado = "NO_PAGADO" THEN 1 ELSE 0 END) as noPagados,
            SUM(CASE WHEN p.estado = "RECHAZADO" THEN 1 ELSE 0 END) as rechazados,
            SUM(CASE WHEN p.estado = "PAGADO" THEN p.monto ELSE 0 END) as recaudado
        ');
        $result = $builder->get()->getRowArray();

        // Con deuda: meses con deuda aplicando filtros activos
        $builderDeuda = $db->table('pagos');
        $builderDeuda->select('COUNT(*) as total');
        if ($idUsuario !== null) {
            $builderDeuda->where('id_usuario', $idUsuario);
        }
        if ($mes !== null) {
            $builderDeuda->where('mes', $mes);
        }
        if ($anio !== null) {
            $builderDeuda->where('anio', $anio);
        }
        $builderDeuda->groupStart();
        $builderDeuda->where('estado', 'NO_PAGADO');
        $builderDeuda->orWhere('estado', 'RECHAZADO');
        $builderDeuda->groupEnd();
        if ($estado !== null) {
            // Si el usuario filtra por un estado específico, ese sobreescribe la deuda
            $builderDeuda->where('estado', $estado);
        }
        $conDeudaResult = $builderDeuda->get()->getRowArray();

        // Deuda total: SUMA de montos de meses con deuda (NO_PAGADO o RECHAZADO)
        $builderDeudaMonto = $db->table('pagos');
        $builderDeudaMonto->select('COALESCE(SUM(monto), 0) as total');
        if ($idUsuario !== null) {
            $builderDeudaMonto->where('id_usuario', $idUsuario);
        }
        if ($mes !== null) {
            $builderDeudaMonto->where('mes', $mes);
        }
        if ($anio !== null) {
            $builderDeudaMonto->where('anio', $anio);
        }
        $builderDeudaMonto->groupStart();
        $builderDeudaMonto->where('estado', 'NO_PAGADO');
        $builderDeudaMonto->orWhere('estado', 'RECHAZADO');
        $builderDeudaMonto->groupEnd();
        if ($estado !== null) {
            $builderDeudaMonto->where('estado', $estado);
        }
        $deudaMontoResult = $builderDeudaMonto->get()->getRowArray();

        $usuarioModel = new UsuarioModel();
        $totalUsuarios = $usuarioModel->ContarActivos();

        return [
            'totalUsuarios' => $totalUsuarios,
            'pagaronMes'    => (int) ($result['pagados'] ?? 0),
            'pendientes'    => (int) ($result['pendientes'] ?? 0),
            'conDeuda'      => (int) ($conDeudaResult['total'] ?? 0),
            'recaudadoMes'  => number_format((float) ($result['recaudado'] ?? 0), 2),
            'deuda'         => number_format((float) ($deudaMontoResult['total'] ?? 0), 2),
        ];
    }

    /**
     * Obtener meses/distintos con registros
     */
    public function MesesConRegistros(): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pagos');
        $builder->select('mes, anio');
        $builder->distinct(true);
        $builder->orderBy('anio', 'DESC');
        $builder->orderBy('mes', 'DESC');
        $query = $builder->get();
        return $query->getResultArray();
    }
}
