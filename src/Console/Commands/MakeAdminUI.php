<?php

namespace Laramina\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MakeAdminUI extends Command
{
    /**
     * Command signature
     */
    protected $signature = 'laramina:make-ui {model} {--force}';

    /**
     * Command description
     */
    protected $description = 'Generate Laramina UI module (table, form, actions, module, blade)';

    /**
     * Entry point
     */
    public function handle()
    {
        $modelInput = $this->argument('model');
        $modelClass = $this->normalizeModel($modelInput);

        if (!class_exists($modelClass)) {
            $this->error("Model not found: {$modelClass}");
            return;
        }

        $slug = $this->buildSlugFromClass($modelClass);

        $this->generateModuleFiles($modelClass, $slug);
        $this->generateBladeView($slug);
        $this->registerModule($slug);

        $this->info("✅ Admin UI generated for {$modelClass}");
    }

    /**
     * Resolve model namespace
     */
    protected function normalizeModel($model)
    {
        if (class_exists($model)) {
            return $model;
        }

        $model = str_replace('/', '\\', $model);

        $candidate = "App\\Models\\{$model}";
        return class_exists($candidate) ? $candidate : $model;
    }

    /**
     * Build file slug from model class
     *
     * مثال:
     *   App\Models\Sms\Credential => sms/credentials
     */
    protected function buildSlugFromClass($class)
    {
        $class = str_replace('App\\Models\\', '', $class);
        $segments = explode('\\', $class);

        $segments = array_map(function ($segment) {
            return Str::kebab(Str::pluralStudly($segment));
        }, $segments);

        return implode('/', $segments);
    }

    /**
     * Extract module + resource names for endpoints & lang keys
     *
     * sms/credentials => ['module' => 'sms', 'resource' => 'credentials']
     */
    protected function parseSlugParts(string $slug): array
    {
        $segments = explode('/', $slug);

        if (count($segments) === 1) {
            return [
                'module'   => $segments[0],
                'resource' => $segments[0],
                'slug'     => $segments[0],
            ];
        }

        $module   = $segments[0];
        $resource = end($segments);

        return [
            'module'   => $module,
            'resource' => $resource,
            'slug'     => "{$module}.{$resource}",
        ];
    }

    /**
     * Generate JS module files
     */
    protected function generateModuleFiles($modelClass, $slug)
    {
        $base = public_path("js/modules/{$slug}");

        if (!File::exists($base)) {
            File::makeDirectory($base, 0755, true);
        }

        if (!File::exists("{$base}/forms")) {
            File::makeDirectory("{$base}/forms", 0755, true);
        }

        $table      = $this->buildTable($modelClass, $slug);
        $createForm = $this->buildCreateForm($modelClass, $slug);
        $actions    = $this->buildActions($modelClass, $slug);
        $module     = $this->buildModule($slug);

        $this->writeFile("{$base}/table.js", $table);
        $this->writeFile("{$base}/forms/create-form.js", $createForm);
        $this->writeFile("{$base}/actions.js", $actions);
        $this->writeFile("{$base}/module.js", $module);
    }

    /**
     * Generate Blade view file
     */
    protected function generateBladeView($slug)
    {
        $viewPath = resource_path("views/{$slug}");

        if (!File::exists($viewPath)) {
            File::makeDirectory($viewPath, 0755, true);
        }

        $blade = <<<BLADE
@extends('layouts.app')

@section('content')
    <div id="admin-module" data-module="{$slug}"></div>
@endsection
BLADE;

        $this->writeFile("{$viewPath}/index.blade.php", $blade);
    }

    /**
     * Build table.js content (سبک جدید، متکی بر AdminLang)
     */
    protected function buildTable($modelClass, $slug)
    {
        $model      = new $modelClass;
        $table      = $model->getTable();
        $columns    = Schema::getColumnListing($table);
        $actionsExport = $this->inferActionsExportName($slug);

        // مثال: sms/credentials => endpoint json: sms.credentials.json
        $parts    = $this->parseSlugParts($slug);
        $endpoint = $parts['slug'];
        $name     = $parts['slug'];

        // کلیدهای ترجمه
        $moduleFieldsNs  = "modules.{$parts['resource']}.fields";
        $moduleActionsNs = "modules.{$parts['resource']}.actions";

        // عنوان را به ترجمه می‌سپاریم؛ fallback client-side است
        $titleKey       = "{$moduleActionsNs}.list_title";
        $headerTitleKey = "{$moduleActionsNs}.header_title";
        $addButtonKey   = "{$moduleActionsNs}.create";

        $columnLines = [];

        foreach ($columns as $column) {
            if (in_array($column, ['updated_at', 'password', 'remember_token', 'deleted_at'])) {
                continue;
            }

            // labelKey فقط expression جاوااسکریپتی است (publicLang.* یا moduleFields.*)
            $labelKey = $this->makeLabelKey($parts['resource'], $column);

            // ستون‌های toggle
            if ($column === 'is_default') {
                $columnLines[] = <<<JS
        {
            key: 'is_default',
            label: {$labelKey},
            type: 'action-toggle',
            confirmTitle: moduleActions.confirm_set_default || publicLang.confirm_set_default,
            endpoint: '{$endpoint}.set-default',
            map: {
                true: { label: publicLang.is_default, color: 'green' },
                false: { label: publicLang.not_default || moduleActions.select_default, color: 'gray' }
            }
        }
JS;
                continue;
            }

            if ($column === 'is_active') {
                $columnLines[] = <<<JS
        {
            key: 'is_active',
            label: {$labelKey},
            type: 'action-toggle',
            endpoint: '{$endpoint}.toggle-status',
            confirmTitle: publicLang.confirm_toggle_status || moduleActions.confirm_toggle_status,
            map: {
                true: { label: publicLang.active, color: 'green' },
                false: { label: publicLang.inactive, color: 'gray' }
            },
            icons: {
                true: { html: '<i class="fa-solid fa-toggle-on text-lg"></i>', color: 'green' },
                false: { html: '<i class="fa-solid fa-toggle-off text-lg"></i>', color: 'red' }
            }
        }
JS;
                continue;
            }

            $sortable = in_array($column, ['id', 'name', 'created_at', 'updated_at']) ? 'true' : 'false';

            $columnLines[] =
                "        { key: '{$column}', label: {$labelKey}" .
                ($sortable === 'true' ? ", sortable: true" : "") .
                " }";
        }

        // ستون عملیات
        $columnLines[] = <<<JS
        {
            label: publicLang.actions,
            type: 'actions',
            actions: ['edit', 'delete']
        }
JS;

        $columnsJs = implode(",\n", $columnLines);

        // فیلترها فقط کلید ترجمه دارند (expression)
        $filtersJs = [];
        if (in_array('is_active', $columns)) {
            $filtersJs[] = <<<JS
        {
            key: 'is_active',
            label: publicLang.status,
            type: 'select',
            options: {
                1: publicLang.active,
                0: publicLang.inactive
            }
        }
JS;
        }
        if (in_array('is_default', $columns)) {
            $filtersJs[] = <<<JS
        {
            key: 'is_default',
            label: publicLang.is_default,
            type: 'select',
            options: {
                1: publicLang.is_default,
                0: publicLang.not_default
            }
        }
JS;
        }
        $filtersBlock = $filtersJs ? implode(",\n", $filtersJs) : '';

        return <<<JS
import { createForm, editForm } from './forms/create-form.js'
import { {$actionsExport} } from './actions.js'

const publicLang   = AdminLang.getNamespace('common');
const moduleFields = AdminLang.getNamespace('{$moduleFieldsNs}');
const moduleActions = AdminLang.getNamespace('{$moduleActionsNs}');

export default {

    endpoint: '{$endpoint}.json',
    search: true,

    headerTitle: moduleFields.header_title || (publicLang.manage + ' ' + (moduleFields.title || '{$name}')),
    addButtonLabel: moduleActions.create || (publicLang.create + ' ' + (moduleActions.item || publicLang.item)),
    displayButton: true,

    actions: {$actionsExport},

    perPage: 10,

    modalTheme: 'light',

    modals: {
        create: {
            title: moduleActions.create || publicLang.create,
            width: '500px',
            form: createForm
        },
        edit: {
            title: moduleActions.edit || publicLang.edit,
            width: '500px',
            form: editForm
        }
    },

    filters: [
{$filtersBlock}
    ],

    columns: [
{$columnsJs}
    ],

}
JS;
    }

    /**
     * Build forms/create-form.js (شامل createForm + editForm)
     */
    protected function buildCreateForm($modelClass, $slug)
    {
        $model   = new $modelClass;
        $table   = $model->getTable();
        $columns = Schema::getColumnListing($table);

        $parts = $this->parseSlugParts($slug);

        $moduleFieldsNs  = "modules.{$parts['resource']}.fields";
        $moduleActionsNs = "modules.{$parts['resource']}.actions";

        // ─── فیلدهای مشترک ───
        $commonFields = [];
        $passwordColumn = null;

        foreach ($columns as $column) {
            if (in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            // ستون password را جداگانه مدیریت می‌کنیم
            if (Str::contains($column, 'password')) {
                $passwordColumn = $column;
                continue;
            }

            $type = 'text';
            if (Str::contains($column, 'email')) {
                $type = 'email';
            } elseif (Str::contains($column, ['_at', 'date'])) {
                $type = 'date';
            } elseif (Str::startsWith($column, 'is_')) {
                $type = 'checkbox';
            }

            $labelKey = $this->makeLabelKey($parts['resource'], $column);

            $commonFields[] = "        {\n"
                . "            name: '{$column}',\n"
                . "            label: {$labelKey},\n"
                . "            type: '{$type}'\n"
                . "        }";
        }

        $commonFieldsJs = implode(",\n", $commonFields);
        $baseRoute = $parts['slug'];

        // ─── فیلد password برای create (اجباری) ───
        $createPasswordJs = '';
        $editPasswordJs = '';
        if ($passwordColumn) {
            $createPasswordJs = <<<JS
        {
            name: '{$passwordColumn}',
            label: publicLang.password,
            type: 'password',
            required: true,
            min: 6,
            placeholder: 'حداقل ۶ کاراکتر',
            helper: 'گذرواژه مناسب باید حداقل ۸ کاراکتر و شامل حروف، اعداد و نمادها باشد',
        },
JS;
            $editPasswordJs = <<<JS
        {
            name: '{$passwordColumn}',
            label: 'رمز عبور جدید',
            type: 'password',
            placeholder: 'در صورت تمایل تغییر دهید',
            hideValue: true,
            value: '',
            helper: 'در صورت تمایل رمز عبور جدید وارد کنید در غیر این صورت خالی بگذارید',
        },
JS;
        }

        return <<<JS
const publicLang   = AdminLang.getNamespace('common');
const moduleFields = AdminLang.getNamespace('{$moduleFieldsNs}');
const moduleActions = AdminLang.getNamespace('{$moduleActionsNs}');

// ─── فیلدهای مشترک بین هر دو فرم ───
const commonFields = [
{$commonFieldsJs}
];

// ─── فرم ایجاد (با رمز عبور اجباری) ───
export const createForm = {

    endpoint: '{$baseRoute}.store',
    updateEndpoint: '{$baseRoute}.update',
    deleteEndpoint: '{$baseRoute}.destroy',

    title: moduleActions.create || publicLang.create,

    fields: [
        ...commonFields,
{$createPasswordJs}
    ],

    buttons: {
        submit: publicLang.save,
        cancel: publicLang.cancel,
    }
};

// ─── فرم ویرایش (با رمز عبور اختیاری) ───
export const editForm = {

    title: moduleActions.edit || publicLang.edit,

    fields: [
        ...commonFields,
{$editPasswordJs}
    ],

    buttons: {
        submit: publicLang.save,
        cancel: publicLang.cancel,
    }
};
JS;
    }

    /**
     * Build actions.js content (ترجمه‌محور + view + setToggle)
     */
    protected function buildActions($modelClass, $slug)
    {
        $exportName = $this->inferActionsExportName($slug);
        $parts      = $this->parseSlugParts($slug);
        $moduleActionsNs = "modules.{$parts['resource']}.actions";

        return <<<JS
import ModalPlugin from '/js/laramina/plugins/ui/modal/modal-plugin.js'
import FormEngine from '/js/laramina/engines/form-engine.js'
import { createForm, editForm } from './forms/create-form.js'
import { action } from '/js/laramina/core/action.js'

const publicLang    = AdminLang.getNamespace('common');
const moduleActions = AdminLang.getNamespace('{$moduleActionsNs}');

export const {$exportName} = {

    view: (row) => {
        console.log('view {$exportName}', row)
    },

    edit: action({
        icon: 'fas fa-edit',
        color: 'text-blue-600',
        size: 'text-lg',
        tooltip: publicLang.edit,
    }, (row, table, event) => {
        const url = AppAlert.route(editForm.updateEndpoint, { id: row.id });
        event?.preventDefault()

        ModalPlugin.open({
            title: moduleActions.edit || publicLang.edit,
            width: '500px',

            content: (container) => {

                const config = {
                    ...editForm,
                    endpoint: url,
                    method: 'POST'
                }

                FormEngine.render(config, container, row)
            }
        })
    }),

    delete: action({
        icon: 'fas fa-trash',
        color: 'text-red-600',
        size: 'text-lg',
        tooltip: publicLang.delete,
    }, async (row, table, event) => {

        event?.preventDefault()

        const url = AppAlert.route(createForm.deleteEndpoint, { id: row.id });

        const res = await AppAlert.confirmDelete(url, {
            title: moduleActions.delete_title || (publicLang.delete + ' ' + (moduleActions.item || publicLang.item)),
        })

        if (res) {
            document.dispatchEvent(
                new CustomEvent('admin:table:remove-row', {
                    detail: { id: row.id }
                })
            )
        }
    }),

    setToggle(id, table, endpoint) {
        const url = AppAlert.route(endpoint, { id });

        return AppAlert.post(url, {}, {
            loading: true,
            successAlert: true
        }).done((res) => {

            // اگر update شامل 'table' بود، کل جدول را رفرش کن
            if (res.update && res.update == 'table') {
                if (typeof table.loadData === 'function') {
                    table.loadData();  // رفرش با حفظ page/filter
                }
                else {
                    console.warn('table.loadData is not a function');
                }
                return;
            }

            // در غیر اینصورت، فقط همان ردیف را آپدیت کن

            // ۱) اگر سرور ردیف کامل را برگرداند
            if (res.provider && typeof table.updateRow === 'function') {
                table.updateRow(res.provider);
                return;
            }

            // ۲) fallback: ردیف قدیمی را بگیر و patch کن (در مواقعی که فقط چند فیلد ساده برگشتند)

            const oldRow = table.getRowById
                ? table.getRowById(id)
                : (table.currentRows || []).find(r => r.id == id);

            if (!oldRow) {
                console.warn("Row not found:", id);
                return;
            }

            // برای جلوگیری از خراب‌کاری، همه res را merge نکن
            // فقط فیلدهای مجاز را patch کن
            const allowedKeys = ['is_active', 'is_default'];
            const patch = {};
            allowedKeys.forEach(k => {
                if (k in res) patch[k] = res[k];
            });

            const newRow = { ...oldRow, ...patch };

            if (typeof table.updateRow === 'function') {
                table.updateRow(newRow);
            } else {
                console.warn('table.updateRow is not a function');
            }
        });
    },
};
JS;
    }

    /**
     * Build module.js content
     */
    protected function buildModule($slug)
    {
        $parts = $this->parseSlugParts($slug);
        $name  = $parts['slug'];

        return <<<JS
import tableConfig from './table.js'

export default {
    name: '{$name}',

    table: tableConfig,

    init() {
        console.log('{$name} module loaded')
    }
}
JS;
    }

    /**
     * Auto-register module inside config/laramina.php
     */
    protected function registerModule($slug)
    {
        $configFile = config_path('laramina.php');

        if (!File::exists($configFile)) {
            $this->warn("⚠️ Config file not found: {$configFile}");
            return;
        }

        $config = include $configFile;
        $config['modules'] = $config['modules'] ?? [];

        // ساختار جدید: کلید-مقدار
        if (!isset($config['modules'][$slug])) {
            $parts = $this->parseSlugParts($slug);
            $label = Str::title(str_replace('-', ' ', $parts['resource']));
            
            $config['modules'][$slug] = [
                'label' => $label,
                'icon'  => 'fas fa-cube',
                'route' => "{$slug}.index",
            ];
            
            $content = "<?php\n\nreturn " . var_export($config, true) . ";\n";
            File::put($configFile, $content);

            $this->info("✅ Module registered in config/laramina.php");
        }
    }

    /**
     * File writing logic with --force support
     */
    protected function writeFile($file, $content)
    {
        if (File::exists($file) && !$this->option('force')) {
            $this->warn("⚠️ File exists: {$file}");
            return;
        }

        File::put($file, $content);
    }

    /**
     * به‌جای برگرداندن متن یا key خام، expression جاوااسکریپتی برمی‌گردانیم
     * - برای ستون‌های عمومی مثل id, name → publicLang.id
     * - برای بقیه → moduleFields.username
     */
    protected function makeLabelKey(string $resource, string $column): string
    {
        $commonKeys = [
            'user_id',
            'deleted_at',
            'url',
            'address',
            'email',
            'mail',
            'status',
            'id',
            'name',
            'username',
            'save',
            'cancel',
            'active',
            'inactive',
            'password',
            'is_active',
            'create',
            'edit',
            'site',
            'website',
            'created_at',
            'updated_at',
            'created_by',
            'is_default',
            'indefault',
            'not_default',
            'title',
            'user',
            'item',
            'delete',
            'actions',
            'allowed_ip',
            'manage',
        ];

        if (in_array($column, $commonKeys)) {
            // publicLang از بالای table.js و create-form.js تعریف می‌شود
            return "publicLang.{$column}";
        }

        // moduleFields از بالای table.js و create-form.js تعریف می‌شود
        return "publicLang.{$column} || moduleFields.{$column}";
    }

    /**
     * برای سازگاری، اگر جایی در آینده نیاز به متن PHP-side باشد
     * (در حال حاضر در JS استفاده نمی‌کنیم)
     */
    protected function makeLabel($column)
    {
        $translation = __("adminUI.common.{$column}");

        if ($translation !== "adminUI.common.{$column}") {
            return $translation;
        }

        return Str::title(str_replace('_', ' ', $column));
    }

    protected function makeModuleTitle(string $slug): string
    {
        $parts = explode('/', $slug);
        $parts = array_map(fn($p) => Str::title(str_replace('-', ' ', $p)), $parts);
        return implode(' ', $parts);
    }

    /**
     * نام export اکشن‌ها مثل providerActions بر اساس slug
     */
    protected function inferActionsExportName(string $slug): string
    {
        $segments = explode('/', $slug);
        $last = end($segments); // e.g. credentials
        $baseName = Str::camel(Str::singular($last)); // credential
        return $baseName . 'Actions'; // credentialActions
    }
}
