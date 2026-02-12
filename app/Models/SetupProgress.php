<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetupProgress extends Model
{
    protected $table = 'setup_progress';

    protected $fillable = [
        'current_step',
        'total_steps',
        'percentage',
        'last_completed_phase',
        'progress_data',
    ];

    protected $casts = [
        'progress_data' => 'array',
    ];

    /**
     * Actualizar progreso en porcentaje
     */
    public function updatePercentage()
    {
        $completed = SetupCheckpoint::completed()->count();
        $total = SetupCheckpoint::count();

        $this->percentage = $total > 0 ? (int)(($completed / $total) * 100) : 0;
        $this->current_step = $completed + 1;
        $this->save();

        return $this;
    }

    /**
     * Obtener datos de progreso
     */
    public function getProgressData()
    {
        return $this->progress_data ?? [];
    }

    /**
     * Guardar dato en progress_data JSON
     */
    public function setProgressData($key, $value)
    {
        $data = $this->progress_data ?? [];
        $data[$key] = $value;
        $this->progress_data = $data;
        $this->save();

        return $this;
    }

    /**
     * Obtener dato específico
     */
    public function getProgressValue($key, $default = null)
    {
        $data = $this->progress_data ?? [];
        return $data[$key] ?? $default;
    }
}

