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
        'topbar_bg', 'topbar_text', 'primary_color', 'content_bg', 'card_bg',
        'marca_activa', 'marca_nombre', 'marca_logo',
        'anuncio',
    ];

    public function Obtener(): array
    {
        $cache = service('cache');
        $cached = $cache->get('config_visual');
        if (is_array($cached)) {
            return $cached;
        }

        $data = $this->find(1);
        if (!$data) {
            $this->db->table($this->table)->insert(['id' => 1]);
            $data = $this->find(1);
        }
        $data = $data ?? [
            'sidebar_bg'        => '#13131f',
            'sidebar_text'      => 'rgba(255,255,255,0.55)',
            'sidebar_active_bg' => '#4669FA',
            'topbar_bg'         => 'rgba(15,15,26,0.92)',
            'topbar_text'       => '#e2e8f0',
            'primary_color'     => '#4669FA',
            'content_bg'        => '#0f0f1a',
            'card_bg'           => '#1a1a2e',
            'marca_activa'      => 0,
            'marca_nombre'      => '',
            'marca_logo'        => '',
        ];

        $cache->save('config_visual', $data, 60);
        return $data;
    }

    public function Guardar(array $datos): bool
    {
        service('cache')->delete('config_visual');
        $datos['id'] = 1;
        return $this->replace($datos) ? true : false;
    }
}
