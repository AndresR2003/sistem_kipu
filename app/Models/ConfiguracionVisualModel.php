<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfiguracionVisualModel extends Model
{
    protected $table      = 'configuracion_visual';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $createdField  = null;
    protected $updatedField  = null;

    protected $allowedFields = [
        'sidebar_bg', 'sidebar_text', 'sidebar_active_bg',
        'topbar_bg', 'topbar_text', 'primary_color',
    ];

    public function Obtener(): array
    {
        $data = $this->find(1);
        if (!$data) {
            $this->db->table($this->table)->insert(['id' => 1]);
            $data = $this->find(1);
        }
        return $data ?? [
            'sidebar_bg'        => '#13131f',
            'sidebar_text'      => 'rgba(255,255,255,0.55)',
            'sidebar_active_bg' => '#4669FA',
            'topbar_bg'         => 'rgba(15,15,26,0.92)',
            'topbar_text'       => '#e2e8f0',
            'primary_color'     => '#4669FA',
        ];
    }

    public function Guardar(array $datos): bool
    {
        $datos['id'] = 1;
        return $this->replace($datos) ? true : false;
    }
}
