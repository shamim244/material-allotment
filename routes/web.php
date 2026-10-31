<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Stock Management Routes
    Route::get('/stock', [App\Http\Controllers\StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/import', [App\Http\Controllers\StockImportController::class, 'index'])->name('stock.import');
    Route::post('/stock/import/box', [App\Http\Controllers\StockImportController::class, 'storeBox'])->name('stock.import.box');
    Route::post('/stock/import/box/preview', [App\Http\Controllers\StockImportController::class, 'previewBox'])->name('stock.import.box.preview');
    Route::post('/stock/import/normal', [App\Http\Controllers\StockImportController::class, 'storeNormal'])->name('stock.import.normal');
    Route::post('/stock/import/large', [App\Http\Controllers\StockImportController::class, 'storeLarge'])->name('stock.import.large');
    Route::get('/stock/groups', [App\Http\Controllers\StockController::class, 'groups'])->name('stock.groups');
    Route::get('/stock/groups/{group}', [App\Http\Controllers\StockController::class, 'groupShow'])->name('stock.groups.show');
    Route::delete('/stock/groups/{group}', [App\Http\Controllers\StockController::class, 'groupDestroy'])->name('stock.groups.destroy');
    Route::get('/stock/{meter}', [App\Http\Controllers\StockController::class, 'show'])->name('stock.show');
    Route::patch('/stock/{meter}', [App\Http\Controllers\StockController::class, 'update'])->name('stock.update');
    Route::patch('/stock/{meter}/reassign', [App\Http\Controllers\StockController::class, 'reassign'])->name('stock.reassign');
    Route::patch('/stock/{meter}/status', [App\Http\Controllers\StockController::class, 'changeStatus'])->name('stock.changeStatus');
    Route::patch('/stock/{meter}/lot', [App\Http\Controllers\StockController::class, 'changeLot'])->name('stock.changeLot');
    Route::post('/stock/bulk-delete', [App\Http\Controllers\StockController::class, 'bulkDelete'])->name('stock.bulkDelete');
    Route::post('/stock/bulk-unassign', [App\Http\Controllers\StockController::class, 'bulkUnassign'])->name('stock.bulkUnassign');
    Route::post('/stock/bulk-restore', [App\Http\Controllers\StockController::class, 'bulkRestore'])->name('stock.bulkRestore');
    Route::post('/stock/bulk-force-delete', [App\Http\Controllers\StockController::class, 'bulkForceDelete'])->name('stock.bulkForceDelete');
    Route::post('/stock/bulk-status', [App\Http\Controllers\StockController::class, 'bulkStatusChange'])->name('stock.bulkStatus');
    Route::post('/stock/bulk-assign', [App\Http\Controllers\StockController::class, 'bulkAssign'])->name('stock.bulkAssign');
    Route::post('/stock/bulk-export', [App\Http\Controllers\StockController::class, 'bulkExport'])->name('stock.bulkExport');
    Route::post('/stock/{id}/restore', [App\Http\Controllers\StockController::class, 'restore'])->name('stock.restore');
    Route::delete('/stock/{id}/force', [App\Http\Controllers\StockController::class, 'forceDelete'])->name('stock.forceDelete');

    // Global Search
    Route::get('/search', [App\Http\Controllers\GlobalSearchController::class, 'search'])->name('search');
    Route::delete('/stock/{meter}', [App\Http\Controllers\StockController::class, 'destroy'])->name('stock.destroy');

    // Assignment Routes
    Route::get('/assignments/create', [App\Http\Controllers\AssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/assignments/preview-group', [App\Http\Controllers\AssignmentController::class, 'previewGroup'])->name('assignments.preview-group');
    Route::post('/assignments', [App\Http\Controllers\AssignmentController::class, 'store'])->name('assignments.store');
    Route::get('/assignments/challan/{worker}', [App\Http\Controllers\AssignmentController::class, 'challan'])->name('assignments.challan');
    Route::delete('/assignments/batch/{batchId}', [App\Http\Controllers\AssignmentController::class, 'revokeBatch'])->name('assignments.batch.revoke');

    // Worker Routes
    Route::get('/my-meters', [App\Http\Controllers\WorkerController::class, 'index'])->name('worker.dashboard');
    Route::patch('/my-meters/{meter}', [App\Http\Controllers\WorkerController::class, 'updateStatus'])->name('worker.meter.update');
    Route::post('/my-meters/return', [App\Http\Controllers\ReturnRequestController::class, 'store'])->name('return.store');

    // Return Management (Contractor)
    Route::get('/returns', [App\Http\Controllers\ReturnRequestController::class, 'index'])->name('returns.index');
    Route::patch('/returns/{returnRequest}', [App\Http\Controllers\ReturnRequestController::class, 'update'])->name('returns.update');

    // Worker Profile (Contractor)
    Route::get('/workers/{user}', [App\Http\Controllers\WorkerProfileController::class, 'show'])->name('workers.show');

    // Accounting Reports (Contractor)
    Route::get('/accounting', [App\Http\Controllers\AccountingController::class, 'index'])->name('accounting.index');

    // Custom Report (Contractor)
    Route::get('/reports/custom', [App\Http\Controllers\CustomReportController::class, 'index'])->name('reports.custom');
    Route::post('/reports/custom', [App\Http\Controllers\CustomReportController::class, 'generate'])->name('reports.custom.generate');
    Route::post('/reports/custom/export', [App\Http\Controllers\CustomReportController::class, 'export'])->name('reports.custom.export');
    Route::post('/reports/custom/import', [App\Http\Controllers\CustomReportController::class, 'importMissing'])->name('reports.custom.import');
    Route::post('/reports/custom/bulk', [App\Http\Controllers\CustomReportController::class, 'bulkAction'])->name('reports.custom.bulk');
    Route::post('/reports/custom/save', [App\Http\Controllers\CustomReportController::class, 'saveTemplate'])->name('reports.custom.save');
    Route::patch('/reports/custom/template/{savedReport}', [App\Http\Controllers\CustomReportController::class, 'updateTemplate'])->name('reports.custom.template.update');
    Route::delete('/reports/custom/template/{savedReport}', [App\Http\Controllers\CustomReportController::class, 'deleteTemplate'])->name('reports.custom.template.delete');

    // Reports (Contractor)
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/meters', [App\Http\Controllers\ReportController::class, 'exportMeters'])->name('reports.export.meters');
    Route::get('/reports/export/workers', [App\Http\Controllers\ReportController::class, 'exportWorkers'])->name('reports.export.workers');
    Route::get('/reports/export/mi', [App\Http\Controllers\ReportController::class, 'exportMI'])->name('reports.export.mi');
    Route::get('/reports/export/installations', [App\Http\Controllers\ReportController::class, 'exportInstallations'])->name('reports.export.installations');
    Route::patch('/reports/meter/{meter}/status', [App\Http\Controllers\ReportController::class, 'updateMeterStatus'])->name('reports.meter.status');
    Route::patch('/reports/mi/{activation}/status', [App\Http\Controllers\ReportController::class, 'updateMIStatus'])->name('reports.mi.status');

    // Lots (Contractor)
    Route::get('/lots', [App\Http\Controllers\LotController::class, 'index'])->name('lots.index');
    Route::get('/lots/create', [App\Http\Controllers\LotController::class, 'create'])->name('lots.create');
    Route::post('/lots', [App\Http\Controllers\LotController::class, 'store'])->name('lots.store');
    Route::get('/lots/{lot}', [App\Http\Controllers\LotController::class, 'show'])->name('lots.show');
    Route::patch('/lots/{lot}', [App\Http\Controllers\LotController::class, 'update'])->name('lots.update');
    Route::delete('/lots/{lot}', [App\Http\Controllers\LotController::class, 'destroy'])->name('lots.destroy');
    Route::post('/lots/assign-existing', [App\Http\Controllers\LotController::class, 'assignExisting'])->name('lots.assign-existing');

    // Admin: Installation Field Config (Contractor)
    Route::get('/admin/installation-config', [App\Http\Controllers\InstallationConfigController::class, 'index'])->name('admin.installation-config');
    Route::patch('/admin/installation-config', [App\Http\Controllers\InstallationConfigController::class, 'update'])->name('admin.installation-config.update');

    // Admin: Map View (Contractor)
    Route::get('/admin/map', [App\Http\Controllers\MapController::class, 'index'])->name('admin.map.index');

    // Admin: MI Issue Types (Contractor)
    Route::get('/admin/mi-issue-types', [App\Http\Controllers\MiIssueTypeController::class, 'index'])->name('admin.mi-issue-types');
    Route::post('/admin/mi-issue-types', [App\Http\Controllers\MiIssueTypeController::class, 'store'])->name('admin.mi-issue-types.store');
    Route::patch('/admin/mi-issue-types/{type}/toggle', [App\Http\Controllers\MiIssueTypeController::class, 'toggleActive'])->name('admin.mi-issue-types.toggle');
    Route::delete('/admin/mi-issue-types/{type}', [App\Http\Controllers\MiIssueTypeController::class, 'destroy'])->name('admin.mi-issue-types.destroy');

    // Action Required (Both roles)
    Route::get('/action-required', [App\Http\Controllers\ActionRequiredController::class, 'index'])->name('action-required');

    // MI Routes (Both roles)
    Route::get('/mi', [App\Http\Controllers\MIController::class, 'index'])->name('mi.index');
    Route::get('/mi/{activation}', [App\Http\Controllers\MIController::class, 'show'])->name('mi.show');
    Route::patch('/mi/{activation}', [App\Http\Controllers\MIController::class, 'update'])->name('mi.update');
    Route::post('/mi/{activation}/images', [App\Http\Controllers\MIController::class, 'uploadImages'])->name('mi.upload');
    Route::delete('/mi/images/{image}', [App\Http\Controllers\MIController::class, 'deleteImage'])->name('mi.image.delete');
    Route::patch('/mi/{activation}/details', [App\Http\Controllers\MIController::class, 'updateInstallationDetails'])->name('mi.details.update');
    Route::post('/mi/bulk-status', [App\Http\Controllers\MIController::class, 'bulkUpdateStatus'])->name('mi.bulk-status');

    // Admin: User Management (Contractor only)
    Route::get('/admin/users', [App\Http\Controllers\UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [App\Http\Controllers\UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [App\Http\Controllers\UserManagementController::class, 'store'])->name('admin.users.store');
    Route::patch('/admin/users/{user}/toggle', [App\Http\Controllers\UserManagementController::class, 'toggleActive'])->name('admin.users.toggle');
    Route::post('/admin/users/{user}/impersonate', [App\Http\Controllers\UserManagementController::class, 'impersonate'])->name('admin.users.impersonate');
    Route::delete('/admin/users/{user}', [App\Http\Controllers\UserManagementController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/admin/users/{user}/restore', [App\Http\Controllers\UserManagementController::class, 'restore'])->name('admin.users.restore');
    Route::get('/admin/users/{user}/edit', [App\Http\Controllers\UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [App\Http\Controllers\UserManagementController::class, 'update'])->name('admin.users.update');
    Route::post('/admin/stop-impersonating', [App\Http\Controllers\UserManagementController::class, 'stopImpersonating'])->name('admin.stop-impersonating');
    
    // Admin: Settings / SMTP
    Route::get('/admin/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/admin/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('admin.settings.update');
    Route::post('/admin/settings/test-email', [App\Http\Controllers\SettingController::class, 'testEmail'])->name('admin.settings.test-email');

    // Admin: Email Activity Log
    Route::get('/admin/email-logs', [App\Http\Controllers\EmailLogController::class, 'index'])->name('admin.email-logs.index');
    Route::delete('/admin/email-logs', [App\Http\Controllers\EmailLogController::class, 'clear'])->name('admin.email-logs.clear');

    // Super Admin Portal
    Route::middleware(['super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/', [App\Http\Controllers\SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/companies', [App\Http\Controllers\SuperAdminController::class, 'index'])->name('companies.index');
        Route::get('/companies/create', [App\Http\Controllers\SuperAdminController::class, 'create'])->name('companies.create');
        Route::post('/companies', [App\Http\Controllers\SuperAdminController::class, 'store'])->name('companies.store');
        Route::get('/companies/{company}/edit', [App\Http\Controllers\SuperAdminController::class, 'edit'])->name('companies.edit');
        Route::put('/companies/{company}', [App\Http\Controllers\SuperAdminController::class, 'update'])->name('companies.update');
        Route::patch('/companies/{company}/toggle', [App\Http\Controllers\SuperAdminController::class, 'toggleStatus'])->name('companies.toggle');
        Route::post('/companies/{company}/impersonate', [App\Http\Controllers\SuperAdminController::class, 'impersonate'])->name('companies.impersonate');
        Route::delete('/companies/{company}', [App\Http\Controllers\SuperAdminController::class, 'destroy'])->name('companies.destroy');
    });
});

require __DIR__.'/auth.php';
