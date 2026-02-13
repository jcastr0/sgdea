<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Tenant;
use App\Services\Admin\TenantService;
use Illuminate\Support\Str;

class CreateTenantWizard extends Component
{
    use WithFileUploads;

    // ===========================
    // SERVICE
    // ===========================
    protected TenantService $tenantService;

    public function boot(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    // ===========================
    // CONTROL DEL WIZARD
    // ===========================
    public int $currentStep = 1;
    public int $totalSteps = 5;
    public bool $isSubmitting = false;
    public bool $showSuccessModal = false;

    // ===========================
    // PASO 1: INFORMACIÓN BÁSICA
    // ===========================
    public string $company_name = '';
    public string $slug = '';
    public string $domain = '';

    // ===========================
    // PASO 2: CONFIGURACIÓN
    // ===========================
    public string $plan = 'professional';
    public int $max_users = 50;
    public int $max_storage = 25;
    public string $status = 'active';

    // ===========================
    // PASO 3: BRANDING
    // ===========================
    public string $color_primary = '#2563eb';
    public string $color_secondary = '#0f172a';
    public string $color_accent = '#10b981';
    public bool $dark_mode_enabled = true;
    public $logo = null;

    // ===========================
    // PASO 4: USUARIO ADMIN
    // ===========================
    public string $admin_name = '';
    public string $admin_email = '';
    public string $admin_password = '';
    public bool $send_welcome_email = true;

    // ===========================
    // RESULTADO
    // ===========================
    public ?int $createdTenantId = null;
    public string $generatedPassword = '';

    // ===========================
    // OPCIONES
    // ===========================
    public array $plans = [
        'basic' => [
            'name' => 'Básico',
            'description' => 'Para empresas pequeñas',
            'limits' => '10 usuarios, 5GB de almacenamiento',
            'users' => 10,
            'storage' => 5,
        ],
        'professional' => [
            'name' => 'Profesional',
            'description' => 'Para empresas medianas',
            'limits' => '50 usuarios, 25GB de almacenamiento',
            'users' => 50,
            'storage' => 25,
        ],
        'enterprise' => [
            'name' => 'Empresarial',
            'description' => 'Para grandes empresas',
            'limits' => 'Usuarios y almacenamiento ilimitados',
            'users' => 0,
            'storage' => 0,
        ],
    ];

    public array $statusOptions = [
        'active' => ['label' => 'Activo', 'description' => 'El tenant puede usar el sistema inmediatamente', 'color' => 'green'],
        'trial' => ['label' => 'Período de Prueba', 'description' => 'Acceso temporal con todas las funciones', 'color' => 'amber'],
        'suspended' => ['label' => 'Suspendido', 'description' => 'El tenant no puede acceder al sistema', 'color' => 'red'],
    ];

    public array $colorPresets = [
        ['name' => 'Azul Profesional', 'primary' => '#2563eb', 'secondary' => '#0f172a', 'accent' => '#10b981'],
        ['name' => 'Verde Corporativo', 'primary' => '#059669', 'secondary' => '#1e293b', 'accent' => '#0ea5e9'],
        ['name' => 'Púrpura Moderno', 'primary' => '#7c3aed', 'secondary' => '#1e1b4b', 'accent' => '#f59e0b'],
        ['name' => 'Rojo Energético', 'primary' => '#dc2626', 'secondary' => '#18181b', 'accent' => '#22c55e'],
        ['name' => 'Naranja Cálido', 'primary' => '#ea580c', 'secondary' => '#292524', 'accent' => '#06b6d4'],
    ];

    // ===========================
    // REGLAS DE VALIDACIÓN
    // ===========================
    protected function rules()
    {
        return [
            // Paso 1
            'company_name' => 'required|string|max:255|unique:tenants,name',
            'slug' => 'required|string|max:255|unique:tenants,slug|regex:/^[a-z0-9-]+$/',
            'domain' => 'required|string|max:255|unique:tenants,domain',
            // Paso 2
            'plan' => 'required|in:basic,professional,enterprise',
            'max_users' => 'required|integer|min:0',
            'max_storage' => 'required|integer|min:0',
            'status' => 'required|in:active,trial,suspended',
            // Paso 3
            'color_primary' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_secondary' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_accent' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'dark_mode_enabled' => 'boolean',
            'logo' => 'nullable|image|max:2048',
            // Paso 4
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'nullable|string|min:8',
            'send_welcome_email' => 'boolean',
        ];
    }

    protected $messages = [
        'company_name.required' => 'El nombre de la empresa es obligatorio',
        'company_name.unique' => 'Ya existe una empresa con ese nombre',
        'slug.required' => 'El identificador es obligatorio',
        'slug.unique' => 'Ese identificador ya está en uso',
        'slug.regex' => 'Solo se permiten letras minúsculas, números y guiones',
        'domain.required' => 'El dominio es obligatorio',
        'domain.unique' => 'Ese dominio ya está registrado',
        'admin_name.required' => 'El nombre del administrador es obligatorio',
        'admin_email.required' => 'El email del administrador es obligatorio',
        'admin_email.unique' => 'Ese email ya está registrado',
        'admin_password.min' => 'La contraseña debe tener al menos 8 caracteres',
    ];

    // ===========================
    // MÉTODOS DE NAVEGACIÓN
    // ===========================
    public function nextStep()
    {
        $this->validateCurrentStep();

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function prevStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step)
    {
        if ($step >= 1 && $step <= $this->currentStep) {
            $this->currentStep = $step;
        }
    }

    protected function validateCurrentStep()
    {
        $rules = [];

        switch ($this->currentStep) {
            case 1:
                $rules = [
                    'company_name' => $this->rules()['company_name'],
                    'slug' => $this->rules()['slug'],
                    'domain' => $this->rules()['domain'],
                ];
                break;
            case 2:
                $rules = [
                    'plan' => $this->rules()['plan'],
                    'status' => $this->rules()['status'],
                ];
                break;
            case 3:
                $rules = [
                    'color_primary' => $this->rules()['color_primary'],
                    'color_secondary' => $this->rules()['color_secondary'],
                    'color_accent' => $this->rules()['color_accent'],
                ];
                break;
            case 4:
                $rules = [
                    'admin_name' => $this->rules()['admin_name'],
                    'admin_email' => $this->rules()['admin_email'],
                ];
                if (!empty($this->admin_password)) {
                    $rules['admin_password'] = $this->rules()['admin_password'];
                }
                break;
        }

        $this->validate($rules);
    }

    // ===========================
    // MÉTODOS AUXILIARES
    // ===========================
    public function updatedCompanyName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function updatedPlan($value)
    {
        if (isset($this->plans[$value])) {
            $this->max_users = $this->plans[$value]['users'];
            $this->max_storage = $this->plans[$value]['storage'];
        }
    }

    public function applyColorPreset(int $index)
    {
        if (isset($this->colorPresets[$index])) {
            $preset = $this->colorPresets[$index];
            $this->color_primary = $preset['primary'];
            $this->color_secondary = $preset['secondary'];
            $this->color_accent = $preset['accent'];
        }
    }

    public function generatePassword()
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
        $password = '';
        for ($i = 0; $i < 12; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        $this->admin_password = $password;
    }

    public function generateSlug()
    {
        $this->slug = Str::slug($this->company_name);
    }

    // ===========================
    // CREAR TENANT
    // ===========================
    public function createTenant()
    {
        $this->isSubmitting = true;

        try {
            // Validar todo
            $this->validate();

            // Preparar datos para el service
            $data = [
                'company_name' => $this->company_name,
                'slug' => $this->slug,
                'domain' => $this->domain,
                'status' => $this->status,
                'plan' => $this->plan,
                'max_users' => $this->max_users,
                'max_storage' => $this->max_storage,
                'color_primary' => $this->color_primary,
                'color_secondary' => $this->color_secondary,
                'color_accent' => $this->color_accent,
                'dark_mode_enabled' => $this->dark_mode_enabled,
                'admin_name' => $this->admin_name,
                'admin_email' => $this->admin_email,
                'admin_password' => $this->admin_password ?: null,
                'created_by' => auth()->id(),
            ];

            // Usar el service para crear el tenant
            $result = $this->tenantService->createTenant($data);

            if (!$result['success']) {
                $errorMsg = implode(', ', $result['errors']);
                $this->addError('general', 'Error al crear el tenant: ' . $errorMsg);
                return;
            }

            // Subir logo si existe (después de crear el tenant)
            if ($this->logo && $result['tenant']) {
                $logoPath = $this->logo->store("tenants/{$result['tenant']->id}/branding", 'public');
                $result['tenant']->update(['logo_path' => $logoPath]);
            }

            // Guardar datos para mostrar en modal
            $this->generatedPassword = $result['password'];
            $this->createdTenantId = $result['tenant']->id;
            $this->showSuccessModal = true;

            $this->dispatch('tenant-created', tenantId: $result['tenant']->id);

        } catch (\Exception $e) {
            $this->addError('general', 'Error al crear el tenant: ' . $e->getMessage());
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function resetWizard()
    {
        $this->reset();
        $this->currentStep = 1;
    }

    public function render()
    {
        return view('livewire.admin.create-tenant-wizard');
    }
}

