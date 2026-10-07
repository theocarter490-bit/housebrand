<?php


use App\Http\Controllers\CacheSetupController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\FreeSignupController;
use App\Http\Controllers\ProductClipperController;
use App\Http\Controllers\QuickShopSettingController;
use App\Models\ShopSetting;
use App\Models\TaskChecklist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\MyOrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LiveChatController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventTypeController;
use App\Http\Controllers\IdeaBoardController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\TaskLabelController;
use App\Http\Controllers\APISettingController;
use App\Http\Controllers\BulkExportController;
use App\Http\Controllers\BulkImportController;
use App\Http\Controllers\ColorThemeController;
use App\Http\Controllers\FileSystemController;
use App\Http\Controllers\NoticeTypeController;
use App\Http\Controllers\OrderClaimController;
use App\Http\Controllers\ProductSeoController;
use App\Http\Controllers\ReviewTypeController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\TaskStatusController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\NoticeBoardController;
use App\Http\Controllers\ShopSettingController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\TimeBillingController;
use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\EmailSettingController;
use App\Http\Controllers\FooterWidgetController;
use App\Http\Controllers\SubscripitonController;
use App\Http\Controllers\EmailCampaignController;
use App\Http\Controllers\GlobalSettingController;
use App\Http\Controllers\IdeaBoardItemController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProjectStatusController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\TaskChecklistController;
use App\Http\Controllers\AttributeValueController;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\GalleryDetailsController;
use App\Http\Controllers\GeneralSettingController;
use App\Http\Controllers\ProductServiceController;
use App\Http\Controllers\ProjectServiceController;
use App\Http\Controllers\SpecialSectionController;
use App\Http\Controllers\DesignerContactController;
use App\Http\Controllers\OrderClaimReplyController;
use App\Http\Controllers\ProjectCategoryController;
use App\Http\Controllers\ProjectProposalController;
use App\Http\Controllers\ProposalInvoiceController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ShippingAddressController;
use App\Http\Controllers\BackgroundSettingsController;
use App\Http\Controllers\ProjectProposalItemController;
use App\Http\Controllers\AppointmentSchedulerController;
use App\Http\Controllers\SpecialSectionDetailController;
use App\Http\Controllers\DesignerSharedProductController;
use App\Http\Controllers\SiteMapController;
use App\Http\Controllers\SpecialSectionCategoryController;
use App\Http\Controllers\SubscriptionCancelRequestController;
use App\Http\Controllers\CustomerAssignedDesignerController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/css/custom-style.css', function () {
    $css = view('css.custom-style')->render();
    return response($css)->header('Content-Type', 'text/css');
});

Route::middleware(['auth', 'checkSubscription'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/orderCount', [DashboardController::class, 'getOrderCount'])->name('dashboard.getOrderCount');
    Route::get('/dashboard/orderPaymentCount', [DashboardController::class, 'getOrderPaymentCount'])->name('dashboard.getOrderPaymentCount');
    Route::get('/dashboard/orderClaimCount', [DashboardController::class, 'orderClaimCount'])->name('dashboard.orderClaimCount');
    Route::get('/dashboard/productStatusCount', [DashboardController::class, 'productStatusCount'])->name('dashboard.productStatusCount');
    Route::get('/dashboard/topProductSaleCount', [DashboardController::class, 'topProductSaleCount'])->name('dashboard.topProductCount');
    Route::get('/dashboard/userRoleCount', [DashboardController::class, 'userRoleCount'])->name('dashboard.userRoleCount');
    Route::get('/dashboard/expense-graph', [DashboardController::class, 'expenseGraph'])->name('dashboard.expenseGraph');
    Route::get('/dashboard/google-analytics', [DashboardController::class, 'googleAnalytics'])->name('dashboard.googleAnalytics');
    Route::get('/dashboard/google-analytics/pageView', [DashboardController::class, 'googlePageView'])->name('dashboard.googlePaveView');
    Route::get('/dashboard/google-analytics/topCountries', [DashboardController::class, 'googleTopCountries'])->name('dashboard.googleTopCountries');


    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::prefix('product')->name('product.')->group(function () {
        Route::get('/index', [ProductController::class, 'index'])->name('index')->middleware('checkPermission:product_read');
        Route::get('/create', [ProductController::class, 'create'])->name('create')->middleware('checkPermission:product_create');
        Route::post('/store', [ProductController::class, 'store'])->name('store')->middleware('checkPermission:product_create');
        Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('edit')->middleware('checkPermission:product_update');
        Route::post('/update', [ProductController::class, 'update'])->name('update')->middleware('checkPermission:product_update');
        Route::post('/destroy', [ProductController::class, 'destroy'])->name('destroy')->middleware('checkPermission:product_delete');
        Route::post('image/destroy', [ProductController::class, 'imageDestroy'])->name('image.destroy');
        Route::post('/attribute_value/list', [ProductController::class, 'attributeValueList'])->name('attribute_value.list');
        Route::post('/change-status', [ProductController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:product_status_change');
        Route::get('/user/{id}', [ProductController::class, 'user'])->name('user');

        Route::get('/assign-visitor/{id}', [ProductController::class, 'assignVisitor'])->name('assignVisitor')->middleware('checkPermission:assign_visitors');
        Route::get('/get-users-by-type', [ProductController::class, 'getUsersByType'])->name('get-users-by-type');
        Route::post('/storeVisitor/{id}', [ProductController::class, 'storeVisitor'])->name('storeVisitor')->middleware('checkPermission:assign_visitors');

        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [ReviewController::class, 'productReviews'])->name("index")->middleware('checkPermission:product_reviews_read');
            Route::get('/details/{id}', [ReviewController::class, 'productReviewsDetails'])->name("details")->middleware('checkPermission:product_reviews_read');
            Route::post('/change-status', [ReviewController::class, 'productReviewStatusChange'])->name('changeStatus')->middleware('checkPermission:product_reviews_change_status');

        });


        // test routes
        Route::get('/duplicate/{productId}/{sharedID}', [ProductController::class, 'duplicate'])->name('duplicate')->middleware('checkPermission:product_create');
    });

    // Product Clippers route
    Route::prefix('product-clipper')->name('productClipper.')->group(function () {
        Route::get('/index', [ProductClipperController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [ProductClipperController::class, 'edit'])->name('edit');
        Route::post('/migrate', [ProductClipperController::class, 'migrate'])->name('migrate');

    });

    Route::prefix('shared-product')->name('sharedProduct.')->group(function () {
        Route::get('/', [DesignerSharedProductController::class, 'index'])->name('index')->middleware('checkPermission:white_list_product_read');
        Route::post('/approve', [DesignerSharedProductController::class, 'approve'])->name('approve')->middleware('checkPermission:white_list_product_approve');
        Route::post('/cancel', [DesignerSharedProductController::class, 'cancel'])->name('cancel')->middleware('checkPermission:white_list_product_cancel');
        Route::post('/change-status', [DesignerSharedProductController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:white_list_product_status_change');
        Route::post('/destroy', [DesignerSharedProductController::class, 'destroy'])->name('destroy')->middleware('checkPermission:white_list_product_delete');

    });

    Route::prefix('bulk-import')->name('bulkImport.')->group(function () {
        Route::get('/', [BulkImportController::class, 'index'])->name('index')->middleware('checkPermission:bulk_import_read');
        Route::post('/', [BulkImportController::class, 'import'])->name('import')->middleware('checkPermission:bulk_import_read');

        Route::get('/product-export', [BulkImportController::class, 'productExport'])->name('productExport')->middleware('checkPermission:bulk_export_read');
        Route::get('/category-export', [BulkImportController::class, 'categoryExport'])->name('categoryExport')->middleware('checkPermission:bulk_export_read');
        Route::get('/brand-export', [BulkImportController::class, 'brandExport'])->name('brandExport')->middleware('checkPermission:bulk_export_read');
    });


    Route::prefix('bulk-export')->name('bulkExport.')->group(function () {
        Route::get('/', [BulkExportController::class, 'index'])->name('index');
        Route::post('/export', [BulkExportController::class, 'export'])->name('export');
        Route::post('/product-list', [BulkExportController::class, 'getProductList'])->name('productList');
    });


    Route::prefix('category')->name('category.')->group(function () {

        Route::get('/', [CategoryController::class, 'index'])->name('index')->middleware('checkPermission:category_read');
        Route::post('/store', [CategoryController::class, 'store'])->name('store')->middleware('checkPermission:category_create');
        Route::get('/{id}', [CategoryController::class, 'edit'])->name('edit')->middleware('checkPermission:category_update');
        Route::post('/update', [CategoryController::class, 'update'])->name('update')->middleware('checkPermission:category_update');
        Route::post('/destroy', [CategoryController::class, 'destroy'])->name('destroy')->middleware('checkPermission:category_delete');
        Route::post('/change-status', [CategoryController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:product_category_status_change');
    });

    Route::prefix('brand')->name('brand.')->group(function () {

        Route::get('/', [BrandController::class, 'index'])->name('index')->middleware('checkPermission:brand_read');
        Route::post('/store', [BrandController::class, 'store'])->name('store')->middleware('checkPermission:brand_create');
        Route::get('/{id}', [BrandController::class, 'edit'])->name('edit')->middleware('checkPermission:brand_update');
        Route::post('/update', [BrandController::class, 'update'])->name('update')->middleware('checkPermission:brand_update');
        Route::post('/destroy', [BrandController::class, 'destroy'])->name('destroy')->middleware('checkPermission:brand_delete');
        Route::post('/change-status', [BrandController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:brand_status_change');

    });

    Route::prefix('attribute')->name('attribute.')->group(function () {
        Route::get('/', [AttributeController::class, 'index'])->name('index')->middleware('checkPermission:attribute_read');
        Route::post('/store', [AttributeController::class, 'store'])->name('store')->middleware('checkPermission:attribute_create');
        Route::get('/{id}', [AttributeController::class, 'edit'])->name('edit')->middleware('checkPermission:attribute_update');
        Route::post('/update', [AttributeController::class, 'update'])->name('update')->middleware('checkPermission:attribute_update');
        Route::post('/destroy', [AttributeController::class, 'destroy'])->name('destroy')->middleware('checkPermission:attribute_delete');
        Route::post('/change-status', [AttributeController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:attribute_status_change');

        Route::prefix('value')->name('value.')->group(function () {
            Route::get('/{attribute_id}/values', [AttributeValueController::class, 'index'])->name('index')->middleware('checkPermission:attribute_value_create');
            Route::post('/store', [AttributeValueController::class, 'store'])->name('store')->middleware('checkPermission:attribute_value_create');
            Route::get('/{id}', [AttributeValueController::class, 'edit'])->name('edit')->middleware('checkPermission:attribute_value_update');
            Route::post('/update', [AttributeValueController::class, 'update'])->name('update')->middleware('checkPermission:attribute_value_update');
            Route::post('/destroy', [AttributeValueController::class, 'destroy'])->name('destroy')->middleware('checkPermission:attribute_value_delete');
        });
    });


    Route::prefix('manufacturers')->name('vendor.')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->name('index')->middleware('checkPermission:manufacturer_read');
        Route::post('/store', [VendorController::class, 'store'])->name('store')->middleware('checkPermission:manufacturer_create');
        Route::get('/{id}', [VendorController::class, 'edit'])->name('edit')->middleware('checkPermission:manufacturer_update');
        Route::post('/update', [VendorController::class, 'update'])->name('update')->middleware('checkPermission:manufacturer_update');
        Route::post('/destroy', [VendorController::class, 'destroy'])->name('destroy')->middleware('checkPermission:manufacturer_delete');
        Route::post('/change-status', [VendorController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:manufacturer_status_change');

    });

    Route::prefix('unit')->name('unit.')->group(function () {
        Route::get('/', [UnitController::class, 'index'])->name('index')->middleware('checkPermission:unit_read');
        Route::post('/store', [UnitController::class, 'store'])->name('store')->middleware('checkPermission:unit_create');
        Route::get('/{id}', [UnitController::class, 'edit'])->name('edit')->middleware('checkPermission:unit_update');
        Route::post('/update', [UnitController::class, 'update'])->name('update')->middleware('checkPermission:unit_update');
        Route::post('/destroy', [UnitController::class, 'destroy'])->name('destroy')->middleware('checkPermission:unit_delete');
        Route::post('/change-status', [UnitController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:unit_status_change');
    });

    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index')->middleware('checkPermission:customer_cart_list_read');
        Route::get('/details/{user_id}', [CartController::class, 'details'])->name('details')->middleware('checkPermission:customer_cart_list_details');
        Route::post('/store', [CartController::class, 'store'])->name('store');
    });


    Route::prefix('project-management')->name('project-management.')->group(function () {
        Route::prefix('project')->name('project.')->group(function () {

            Route::get('/index', [ProjectController::class, 'index'])->name('index')->middleware('checkPermission:projects_read');
            Route::get('/create', [ProjectController::class, 'create'])->name('create')->middleware('checkPermission:add_project');
            Route::post('/store', [ProjectController::class, 'store'])->name('store')->middleware('checkPermission:add_project');
            Route::get('/edit/{id}', [ProjectController::class, 'edit'])->name('edit')->middleware('checkPermission:update_projects');
            Route::post('/update/{id}', [ProjectController::class, 'update'])->name('update')->middleware('checkPermission:update_projects');
            Route::get('/{id}/overview', [ProjectController::class, 'overview'])->name('overview')->middleware('checkPermission:project_overview');
            Route::get('/{id}/task', [ProjectController::class, 'task'])->name('task');

            Route::prefix('category')->name('category.')->group(function () {
                Route::get('/', [ProjectCategoryController::class, 'index'])->name('index')->middleware('checkPermission:essentials_category_read');
                Route::post('/store', [ProjectCategoryController::class, 'store'])->name('store')->middleware('checkPermission:create_essentials_category');
                Route::get('/{id}', [ProjectCategoryController::class, 'edit'])->name('edit')->middleware('checkPermission:update_essentials_category');
                Route::post('/update', [ProjectCategoryController::class, 'update'])->name('update')->middleware('checkPermission:update_essentials_category');
                Route::post('/destroy', [ProjectCategoryController::class, 'destroy'])->name('destroy')->middleware('checkPermission:delete_essentials_category');
                Route::post('/change-status', [ProjectCategoryController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:change_status_essentials_category');
            });

            Route::prefix('task')->name('task.')->group(function () {
                Route::get('/', [TaskController::class, 'index'])->name('index')->middleware('checkPermission:tasks_read');
                Route::post('{project_id}/task-status/store', [TaskController::class, 'taskStatusStore'])->name('taskStatusStore');
                Route::get('/{project_id}/kanban', [TaskController::class, 'kanban'])->name('kanban');
                Route::post('/{project_id}/store', [TaskController::class, 'store'])->name('store')->middleware('checkPermission:create_task');
                Route::post('/{project_id}/update', [TaskController::class, 'update'])->name('update')->middleware('checkPermission:update_task');
                Route::post('/{project_id}/destroy', [TaskController::class, 'destroy'])->name('destroy')->middleware('checkPermission:delete_task');
                Route::post('/{project_id}/status/update', [TaskController::class, 'changeStatus'])->name('changeStatus');
                Route::get('/{task_id}', [TaskController::class, 'getTask'])->name('getTask');
                Route::get('/{task_id}/details', [TaskController::class, 'details'])->name('details');
                Route::post('/add-attachment', [TaskController::class, 'addAttachment'])->name('addAttachment');
                Route::get('/user/list', [TaskController::class, 'getUsers'])->name('getUsers');
                Route::post('/assign/user', [TaskController::class, 'assignUser'])->name('assignUser');
                Route::post('/add-comment', [TaskCommentController::class, 'storeComment'])->name('storeComment');
                Route::post('/delete-attachment', [TaskController::class, 'deleteAttachment'])->name('deleteAttachment');

                Route::prefix('checklist')->name('checklist.')->group(function () {
                    Route::post('/', [TaskChecklistController::class, 'store'])->name('store');
                    Route::post('/change-status', [TaskChecklistController::class, 'changeStatus'])->name('changeStatus');
                    Route::post('/delete', [TaskChecklistController::class, 'delete'])->name('delete');
                });

            });

            Route::prefix('time-billing')->name('time-billing.')->group(function () {
                Route::get('/{project_id}', [TimeBillingController::class, 'index'])->name('index')->middleware('checkPermission:time_billing_read');
                Route::get('{bill_id}/time-breakdown', [TimeBillingController::class, 'timeBreakdown'])->name('timeBreakdown')->middleware('checkPermission:read_time_breakdown');
                Route::get('{bill_id}/invoice-preview', [TimeBillingController::class, 'invoicePreview'])->name('invoicePreview')->middleware('checkPermission:time_billing_invoice_read');
                Route::get('{bill_id}/invoice/download', [TimeBillingController::class, 'invoice'])->name('invoice')->middleware('checkPermission:time_billing_invoice_download');
                Route::get('/customer/get', [TimeBillingController::class, 'customers'])->name('customers');
                Route::get('/projects/get', [TimeBillingController::class, 'projects'])->name('projects');
                Route::get('/employees/get', [TimeBillingController::class, 'employees'])->name('employees');
                Route::get('/employee/{employee_id}/services', [TimeBillingController::class, 'services'])->name('services');
                Route::post('/change-status', [TimeBillingController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:change_bill_type');
                Route::post('/delete', [TimeBillingController::class, 'deleteTimeBilling'])->name('deleteTimeBilling')->middleware('checkPermission:time_billing_delete');

                Route::post('/time-tracking/start', [TimeBillingController::class, 'startTracking'])->name('startTracking');
                Route::post('/time-tracking/pauseTracking', [TimeBillingController::class, 'pauseTracking'])->name('pauseTracking');
                Route::post('/time-tracking/restartTracking', [TimeBillingController::class, 'restartTracking'])->name('restartTracking');
                Route::post('/time-tracking/saveTracking', [TimeBillingController::class, 'saveTracking'])->name('saveTracking');
                Route::get('/time-tracking/getRunningTime', [TimeBillingController::class, 'getRunningTime'])->name('getRunningTime');

                Route::post('/payment-store', [TimeBillingController::class, 'paymentStore'])->name('paymentStore');
                Route::get('/payment-history/{id}', [TimeBillingController::class, 'paymentHistry'])->name('paymentHistry');
                Route::post('payment-history/delete', [TimeBillingController::class, 'deleteHistory'])->name('deletePaymentHistory');
                Route::post('/invoice-send', [TimeBillingController::class, 'sendInvoice'])->name('sendInvoice')->middleware('checkPermission:time_billing_send_invoice');
                Route::get('/invoice-print/{id}', [TimeBillingController::class, 'invoicePrint'])->name('invoicePrint')->middleware('checkPermission:time_billing_invoice_print');
                Route::get('/invoice-download/{id}', [TimeBillingController::class, 'invoiceDownload'])->name('invoiceDownload')->middleware('checkPermission:time_billing_invoice_download');
                Route::post('payment-history/delete', [TimeBillingController::class, 'deleteHistory'])->name('deletePaymentHistory');
            });

            Route::prefix('idea-board')->name('idea-board.')->group(function () {
                Route::get('/products', [IdeaBoardController::class, 'getProducts'])->name('products');
                Route::get('/manufacturer/products', [IdeaBoardController::class, 'getManufacturerProducts']);
                Route::get('/{project_id}', [IdeaBoardController::class, 'index'])->name('index');
                Route::post('/{project}/store', [IdeaBoardController::class, 'store'])->name('store');
                Route::get('/{project}/edit/{id}', [IdeaBoardController::class, 'edit'])->name('edit');
                Route::post('/{project}/update', [IdeaBoardController::class, 'update'])->name('update');
                Route::post('/{project}/destroy', [IdeaBoardController::class, 'destroy'])->name('destroy');
                Route::get('/{project}/details/{id}', [IdeaBoardController::class, 'details'])->name('details');


                Route::prefix('{project_id}/item')->name('items.')->group(function () {
                    Route::post('/store/{idea_board_id}', [IdeaBoardItemController::class, 'store'])->name('store');
                    Route::get('/edit/{idea_board_id}', [IdeaBoardItemController::class, 'edit'])->name('edit');
                    Route::post('/update/{idea_board_id}', [IdeaBoardItemController::class, 'update'])->name('update');
                    Route::post('/destroy/{idea_board_id}', [IdeaBoardItemController::class, 'destroy'])->name('destroy');

                });
            });

            Route::prefix('proposal')->name('proposal.')->group(function () {
                Route::get('/{project_id}', [ProjectProposalController::class, 'index'])->name('index');
                Route::get('/edit/{id}', [ProjectProposalController::class, 'edit'])->name('edit');
                Route::post('/update', [ProjectProposalController::class, 'update'])->name('update');
                Route::post('/{project_id}/store', [ProjectProposalController::class, 'store'])->name('store');
                Route::post('/{project_id}/change-status', [ProjectProposalController::class, 'changeStatus'])->name('changeStatus');
                Route::post('/{project_id}/published-change-status', [ProjectProposalController::class, 'changePublishStatus'])->name('changePublishStatus');
                Route::get('/{project_id}/preview/{proposal_id}', [ProjectProposalController::class, 'preview'])->name('preview');
                Route::get('/{project_id}/details/{proposal_id}', [ProjectProposalController::class, 'details'])->name('details');
                Route::get('/{project_id}/generate-proposal/{idea_board_id}', [ProjectProposalController::class, 'generateProposal'])->name('generateProposal');
                Route::get('{project_id}/generate-invoice/{proposal_id}', [ProjectProposalController::class, 'generateInvoice'])->name('generateInvoice');

                Route::post('/delete', [ProjectProposalController::class, 'deleteProposal'])->name('deleteProposal');

                Route::prefix('{project_id}/item')->name('items.')->group(function () {
                    Route::post('/store/{proposal_id}', [ProjectProposalItemController::class, 'store'])->name('store');
                });
            });

            Route::prefix('invoice')->name('invoice.')->group(function () {
                Route::get('/{project_id}', [ProposalInvoiceController::class, 'index'])->name('index');
                Route::get('{project_id}/preview/{invoice_id}', [ProposalInvoiceController::class, 'preview'])->name('preview');
                Route::post('/invoice-send', [ProposalInvoiceController::class, 'sendInvoice'])->name('sendInvoice')->middleware('checkPermission:time_billing_send_invoice');
                Route::get('/invoice-print/{id}', [ProposalInvoiceController::class, 'invoicePrint'])->name('invoicePrint')->middleware('checkPermission:time_billing_invoice_print');
                Route::get('/{project_id}/invoice-download/{invoice_id}', [ProposalInvoiceController::class, 'invoiceDownload'])->name('invoiceDownload')->middleware('checkPermission:time_billing_invoice_download');
                Route::post('/change-status', [ProposalInvoiceController::class, 'changeStatus'])->name('changeStatus');
                Route::post('/destroy', [ProposalInvoiceController::class, 'destroy'])->name('destroy');

                Route::post('/{project_id}/payment-store', [ProposalInvoiceController::class, 'paymentStore'])->name('paymentStore');
                Route::get('/{project_id}/payment-history/{id}', [ProposalInvoiceController::class, 'paymentHistry'])->name('paymentHistry');
                Route::post('/{project_id}/payment-history/delete', [ProposalInvoiceController::class, 'deleteHistory'])->name('deletePaymentHistory');

            });

            Route::prefix('category')->name('category.')->group(function () {
                Route::get('/', [ProjectCategoryController::class, 'index'])->name('index');
                Route::post('/store', [ProjectCategoryController::class, 'store'])->name('store');
                Route::get('/{id}', [ProjectCategoryController::class, 'edit'])->name('edit');
                Route::post('/update', [ProjectCategoryController::class, 'update'])->name('update');
                Route::post('/destroy', [ProjectCategoryController::class, 'destroy'])->name('destroy');
                Route::post('/change-status', [ProjectCategoryController::class, 'changeStatus'])->name('changeStatus');
            });
        });


        Route::prefix('service')->name('service.')->group(function () {
            Route::prefix('category')->name('category.')->group(function () {
                Route::get('/', [ServiceCategoryController::class, 'index'])->name('index');
                Route::post('/store', [ServiceCategoryController::class, 'store'])->name('store');
                Route::get('/edit/{id}', [ServiceCategoryController::class, 'edit'])->name('edit');
                Route::post('/update', [ServiceCategoryController::class, 'update'])->name('update');
                Route::post('/delete', [ServiceCategoryController::class, 'delete'])->name('destroy');
                Route::post('/change-status', [ServiceCategoryController::class, 'changeStatus'])->name('changeStatus');

            });

            Route::get('/', [ProjectServiceController::class, 'index'])->name('index');
            Route::post('/store', [ProjectServiceController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [ProjectServiceController::class, 'edit'])->name('edit');
            Route::post('/update', [ProjectServiceController::class, 'update'])->name('update');
            Route::post('/delete', [ProjectServiceController::class, 'delete'])->name('destroy');
            Route::post('/change-status', [ProjectServiceController::class, 'changeStatus'])->name('changeStatus');

            Route::prefix('assign-user')->name('assign-user.')->group(function () {
                Route::get('/{id}', [ProjectServiceController::class, 'assignUser'])->name('index');
                Route::post('/store', [ProjectServiceController::class, 'storeAssignUser'])->name('store')->middleware('checkPermission:assign_user_service_setup');
                Route::get('/edit/{id}', [ProjectServiceController::class, 'editAssignUser'])->name('editUser');
                Route::post('/delete', [ProjectServiceController::class, 'deleteAssignUser'])->name('delete');
                Route::post('/change-status', [ProjectServiceController::class, 'assignUserChangeStatus'])->name('changeStatus');
            });

        });

        Route::prefix('essentials')->name('essentials.')->group(function () {
            Route::prefix('task')->name('task.')->group(function () {
                Route::prefix('status')->name('status.')->group(function () {
                    Route::get('/', [TaskStatusController::class, 'index'])->name('index');
                    Route::post('/store', [TaskStatusController::class, 'store'])->name('store')->middleware('checkPermission:create_task_status');
                    Route::get('/edit/{id}', [TaskStatusController::class, 'edit'])->name('edit')->middleware('checkPermission:update_task_status');
                    Route::post('/update', [TaskStatusController::class, 'update'])->name('update')->middleware('checkPermission:update_task_status');
                    Route::post('/delete', [TaskStatusController::class, 'delete'])->name('destroy')->middleware('checkPermission:delete_task_status');
                    Route::post('/change-status', [TaskStatusController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:change_status_task_status');
                    Route::post('/update-serial-number', [TaskStatusController::class, 'updateSerial'])->name('updateSerial');
                    Route::get('/count/{projectId}', [TaskController::class, 'taskStatusCounts'])->name('count');

                });

                Route::get('/', [TaskController::class, 'index'])->name('index')->middleware('checkPermission:tasks_read');
                Route::post('{project_id}/task-status/store', [TaskController::class, 'taskStatusStore'])->name('taskStatusStore');
                Route::get('/{project_id}/kanban', [TaskController::class, 'kanban'])->name('kanban');
                Route::post('/{project_id}/store', [TaskController::class, 'store'])->name('store')->middleware('checkPermission:create_task');
                Route::post('/{project_id}/update', [TaskController::class, 'update'])->name('update')->middleware('checkPermission:update_task');
                Route::post('/{project_id}/destroy', [TaskController::class, 'destroy'])->name('destroy')->middleware('checkPermission:delete_task');
                Route::post('/{project_id}/status/update', [TaskController::class, 'changeStatus'])->name('changeStatus');
                Route::get('/{task_id}', [TaskController::class, 'getTask'])->name('getTask');
                Route::get('/{task_id}/details', [TaskController::class, 'details'])->name('details');
                Route::post('/add-attachment', [TaskController::class, 'addAttachment'])->name('addAttachment');
                Route::get('/user/list', [TaskController::class, 'getUsers'])->name('getUsers');
                Route::post('/assign/user', [TaskController::class, 'assignUser'])->name('assignUser');
                Route::post('/add-comment', [TaskCommentController::class, 'storeComment'])->name('storeComment');
                Route::post('/delete-attachment', [TaskController::class, 'deleteAttachment'])->name('deleteAttachment');

                Route::prefix('checklist')->name('checklist.')->group(function () {
                    Route::post('/', [TaskChecklistController::class, 'store'])->name('store');
                    Route::post('/change-status', [TaskChecklistController::class, 'changeStatus'])->name('changeStatus');
                    Route::post('/delete', [TaskChecklistController::class, 'delete'])->name('delete');
                });

            });

            Route::prefix('status')->name('status.')->group(function () {
                Route::get('/', [ProjectStatusController::class, 'index'])->name('index')->middleware('checkPermission:project_status_read');
                Route::post('/store', [ProjectStatusController::class, 'store'])->name('store')->middleware('checkPermission:create_project_status');
                Route::get('/edit/{id}', [ProjectStatusController::class, 'edit'])->name('edit')->middleware('checkPermission:update_project_status');
                Route::post('/update', [ProjectStatusController::class, 'update'])->name('update')->middleware('checkPermission:update_project_status');
                Route::post('/delete', [ProjectStatusController::class, 'delete'])->name('destroy')->middleware('checkPermission:delete_project_status');
                Route::post('/change-status', [ProjectStatusController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:change_status_project_status');
            });


            Route::prefix('task-label')->name('task.label.')->group(function () {
                Route::get('/', [TaskLabelController::class, 'index'])->name('index')->middleware('checkPermission:task_label_read');
                Route::post('/store', [TaskLabelController::class, 'store'])->name('store')->middleware('checkPermission:create_task_label');
                Route::get('/edit/{id}', [TaskLabelController::class, 'edit'])->name('edit')->middleware('checkPermission:update_task_label');
                Route::post('/update', [TaskLabelController::class, 'update'])->name('update')->middleware('checkPermission:update_task_label');
                Route::post('/delete', [TaskLabelController::class, 'delete'])->name('destroy')->middleware('checkPermission:delete_task_label');
                Route::post('/change-status', [TaskLabelController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:change_status_task_label');
            });

//            Route::prefix('service')->name('service.')->group(function () {
//                Route::get('/', [ProjectServiceController::class, 'index'])->name('index')->middleware('checkPermission:service_setup_read');
//                Route::post('/', [ProjectServiceController::class, 'store'])->name('store')->middleware('checkPermission:create_service_setup');
//                Route::get('/edit/{id}', [ProjectServiceController::class, 'edit'])->name('edit')->middleware('checkPermission:update_service_setup');
//                Route::post('/update', [ProjectServiceController::class, 'update'])->name('update')->middleware('checkPermission:update_service_setup');
//                Route::post('/delete', [ProjectServiceController::class, 'delete'])->name('destroy')->middleware('checkPermission:delete_service_setup');
//                Route::post('/change-status', [ProjectServiceController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:change_status_service_setup');
//
//
//
//            });
        });

        Route::prefix('document')->name('document.')->group(function () {
            Route::get('/edit', [DocumentController::class, 'edit'])->name('edit');
            Route::get('/{source}/{source_id?}', [DocumentController::class, 'index'])->name('index');
            Route::post('/store', [DocumentController::class, 'store'])->name('store');
            Route::post('/update', [DocumentController::class, 'update'])->name('update');
            Route::delete('/delete', [DocumentController::class, 'delete'])->name('destroy');
            Route::post('/status-change', [DocumentController::class, 'changeStatus'])->name('changeStatus');
        });
    });


    Route::prefix('order')->name('order.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index')->middleware('checkPermission:customer_order_list_read');
        Route::get('/create', [OrderController::class, 'create'])->name('create')->middleware('checkPermission:customer_order_create');
        Route::post('/store', [OrderController::class, 'store'])->name('store')->middleware('checkPermission:customer_order_create');
        Route::get('/details/{order_id}', [OrderController::class, 'details'])->name('details')->middleware('checkPermission:customer_order_list_details');
        Route::get('/edit/{order_id}', [OrderController::class, 'edit'])->name('edit')->middleware('checkPermission:customer_order_list_edit');
        Route::post('/update', [OrderController::class, 'update'])->name('update')->middleware('checkPermission:customer_order_list_edit');
        Route::post('/destroy', [OrderController::class, 'destroy'])->name('destroy')->middleware('checkPermission:customer_order_list_delete');
        Route::post('/change-status', [OrderController::class, 'changeStatus'])->name('changeStatus');
        Route::get('/invoice-preview/{order_id}', [OrderController::class, 'invoicePreview'])->name('invoicePreview')->middleware('checkPermission:customer_order_list_read_invoice');
        Route::get('/invoice-print/{order_id}', [OrderController::class, 'invoicePrint'])->name('invoicePrint')->middleware('checkPermission:customer_order_list_print_invoice');
        Route::get('/invoice-download/{order_id}', [OrderController::class, 'invoiceDownload'])->name('invoiceDownload')->middleware('checkPermission:customer_order_list_download_invoice');
        Route::post('/invoice-send', [OrderController::class, 'sendInvoice'])->name('sendInvoice')->middleware('checkPermission:customer_order_list_send_invoice');
        Route::get('/product/{id}', [OrderController::class, 'getProduct']);
        Route::get('requested-product/{id}', [OrderController::class, 'getRequestedProduct']);

        Route::post('/payment-store', [OrderController::class, 'paymentStore'])->name('paymentStore');
        Route::get('/payment-histry/{id}', [OrderController::class, 'paymentHistry'])->name('paymentHistry');
        Route::post('payment-history/delete', [OrderController::class, 'deleteHistory'])->name('deletePaymentHistory');

        Route::prefix('items')->name('items.')->group(function () {
            Route::post('/status/store', [OrderItemController::class, 'statusStore'])->name('status.store');
        });
    });

    Route::prefix('order-claim')->name('order-claim.')->group(function () {
        Route::get('/', [OrderClaimController::class, 'index'])->name('index')->middleware('checkPermission:customer_order_claim_read');
        Route::get('/details/{id}', [OrderClaimController::class, 'details'])->name('details')->middleware('checkPermission:customer_order_claim_details');
        Route::post('/status-change', [OrderClaimController::class, 'statusChange'])->name('statusChange')->middleware('checkPermission:customer_order_claim_status_change');
    });

    Route::prefix('order-claim-reply')->name('order-claim-reply.')->group(function () {
        Route::post('/store', [OrderClaimReplyController::class, 'store'])->name('store')->middleware('checkPermission:customer_order_claim_reply');
    });


    Route::prefix('myOrder')->name('myOrder.')->group(function () {

        Route::prefix('order')->name('order.')->group(function () {
            Route::get('/', [MyOrderController::class, 'index'])->name('index')->middleware('checkPermission:my_order_read');
            Route::get('/details/{order_id}', [MyOrderController::class, 'details'])->name('details')->middleware('checkPermission:my_order_list_details');
            Route::get('/invoice-preview/{order_id}', [MyOrderController::class, 'invoicePreview'])->name('invoicePreview');
            Route::get('/make-payment/{order_id}/stripe', [MyOrderController::class, 'makePaymentStripe'])->name('makePayment.stripe')->middleware('checkPermission:make_payment');
            Route::get('/checkout/success/stripe', [MyOrderController::class, 'checkoutSuccessStripe'])->name('checkout-success.stripe');
            Route::view('/checkout/cancel/stripe', 'checkout.cancel')->name('checkout-cancel.stripe');

            Route::get('/make-payment/{order_id}/paypal', [MyOrderController::class, 'makePaymentPaypal'])->name('makePayment.paypal')->middleware('checkPermission:make_payment');
            Route::get('/checkout/success/paypal', [MyOrderController::class, 'checkoutSuccessPaypal'])->name('checkout-success.paypal');
            Route::view('/checkout/cancel/paypal', 'checkout.cancel')->name('checkout-cancel.paypal');

        });

        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [MyOrderController::class, 'cartList'])->name('index')->middleware('checkPermission:my_order_cart_list_read');
            Route::post('/destroy', [MyOrderController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('order-claim')->name('order-claim.')->group(function () {
//            ->middleware('checkPermission:my_order_claim_read')
            Route::get('/', [\App\Http\Controllers\MyOrderClaimController::class, 'index'])->name('index');
            Route::get('/details/{id}', [\App\Http\Controllers\MyOrderClaimController::class, 'details'])->name('details');

        });
    });


    Route::prefix('section')->name('section.')->group(function () {
        Route::prefix('category')->name('category.')->group(function () {
            Route::get('/index', [SpecialSectionCategoryController::class, 'index'])->name('index')->middleware('checkPermission:section_category_read');
            Route::get('/getCategories', [SpecialSectionCategoryController::class, 'getCategories'])->name('getCategories');
            Route::post('/store', [SpecialSectionCategoryController::class, 'store'])->name('store')->middleware('checkPermission:section_category_create');
            Route::get('/{id}', [SpecialSectionCategoryController::class, 'edit'])->name('edit')->middleware('checkPermission:section_category_update');
            Route::post('/update', [SpecialSectionCategoryController::class, 'update'])->name('update')->middleware('checkPermission:section_category_update');
            Route::post('/destroy', [SpecialSectionCategoryController::class, 'destroy'])->name('destroy')->middleware('checkPermission:section_category_delete');
            Route::post('/change-status', [SpecialSectionCategoryController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:section_category_status_change');
        });

        Route::prefix('portfolioAndInspiration')->name('portfolioAndInspiration.')->group(function () {

            Route::get('/{section_type}/index', [SpecialSectionController::class, 'index'])->name('index')->middleware('checkPermission:portfolio_and_inspiration_read');
            Route::post('/{section_type}/store', [SpecialSectionController::class, 'store'])->name('store')->middleware('checkPermission:portfolio_and_inspiration_create');
            Route::get('/{section_type}/{id}', [SpecialSectionController::class, 'edit'])->name('edit')->middleware('checkPermission:portfolio_and_inspiration_update');
            Route::post('/{section_type}/update', [SpecialSectionController::class, 'update'])->name('update')->middleware('checkPermission:portfolio_and_inspiration_update');
            Route::post('/{section_type}/destroy', [SpecialSectionController::class, 'destroy'])->name('destroy')->middleware('checkPermission:portfolio_inspiration_delete');
            Route::post('/change-status', [SpecialSectionController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:portfolio_inspiration_status_change');

            Route::get('/{section_id}/details/create', [SpecialSectionDetailController::class, 'create'])->name('details.create');
            Route::post('/{section_id}/details/store', [SpecialSectionDetailController::class, 'store'])->name('details.store');

            Route::get('/product', [SpecialSectionCategoryController::class, 'userProduct'])->name('products');
        });
    });

    Route::prefix('gallery')->name('gallery.')->group(function () {
        Route::get('/list', [GalleryController::class, 'index'])->name('index')->middleware('checkPermission:gallery_read');
        Route::post('/store', [GalleryController::class, 'store'])->name('store')->middleware('checkPermission:gallery_create');
        Route::get('/{id}', [GalleryController::class, 'edit'])->name('edit')->middleware('checkPermission:gallery_update');
        Route::post('/update', [GalleryController::class, 'update'])->name('update')->middleware('checkPermission:gallery_update');
        Route::post('/destroy', [GalleryController::class, 'destroy'])->name('destroy')->middleware('checkPermission:gallery_delete');
        Route::post('/change-status', [GalleryController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:gallery_status_change');
        Route::get('/getGallery/list', [GalleryController::class, 'getGallery'])->name('getGallery');

        Route::prefix('details')->name('details.')->group(function () {
            Route::get('/{gallery_id}', [GalleryDetailsController::class, 'index'])->name('index')->middleware('checkPermission:read_images');
            Route::post('/store', [GalleryDetailsController::class, 'store'])->name('store')->middleware('checkPermission:create_image');
            Route::post('/storeSingleDetails', [GalleryDetailsController::class, 'storeSingleDetails']);
        });
    });

    Route::prefix('live-chat')->name('live-chat.')->group(function () {
        Route::get("/chat", [LiveChatController::class, 'chats'])->name('index');
    });

    Route::prefix('appointment-scheduler')->name('appointment-scheduler.')->group(function () {
        Route::get("/appointments", [AppointmentSchedulerController::class, 'appointments'])->name('appointments');
        Route::post("/appointments/store", [AppointmentSchedulerController::class, 'storeAppointment'])->name('storeAppointment');
        Route::get("/appointments/edit/{id}", [AppointmentSchedulerController::class, 'editAppointment'])->name('editAppointment');
        Route::post("/appointments/update", [AppointmentSchedulerController::class, 'updateAppointment'])->name('updateAppointment');
        Route::post("/appointments/dateTimeUpdate", [AppointmentSchedulerController::class, 'dateTimeUpdate'])->name('dateTimeUpdate');
        Route::get("/calender-events", [AppointmentSchedulerController::class, 'calenderEvents'])->name('calenderEvents');
        Route::get("/calender-events/overview", [AppointmentSchedulerController::class, 'overview'])->name('overview');
        Route::post("/assign-employee", [AppointmentSchedulerController::class, 'assignEmployee'])->name('assignEmployee');
    });

    Route::prefix('event-management')->name('event-management.')->group(function () {
        Route::prefix('event')->name('event.')->group(function () {
            Route::get("/", [EventController::class, 'index'])->name('index')->middleware('checkPermission:event_read');
            Route::post('/store', [EventController::class, 'store'])->name('store')->middleware('checkPermission:event_create');
            Route::get('/edit/{id}', [EventController::class, 'edit'])->name('edit')->middleware('checkPermission:event_update');
            Route::get('/details/{id}', [EventController::class, 'getEventDetails']);
            Route::post('/update', [EventController::class, 'update'])->name('update')->middleware('checkPermission:event_update');
            Route::post('/changeStatus', [EventController::class, 'changeStatus'])->name('change-status')->middleware('checkPermission:event_change_status');
            Route::post('/delete', [EventController::class, 'delete'])->name('delete')->middleware('checkPermission:event_delete');
            Route::get('/email-logs/{id}', [EventController::class, 'emailLog'])->name('email.log');

            // assign user
            Route::get('/assign-user/{event_id}', [EventController::class, 'assignUser'])->name('assign-user')->middleware('checkPermission:assign_user_read');
            Route::get('/get-users-by-type', [EventController::class, 'getUsersByType'])->name('get-users-by-type')->middleware('checkPermission:assign_user_read');
            Route::post('/event/{id}/update', [EventController::class, 'updateEventWithUser'])->name('update-event')->middleware('checkPermission:assign_user_read');

            Route::get("/calender", [EventController::class, 'calender'])->name('calender')->middleware('checkPermission:event_calender_read');
            Route::get("/calender-events", [EventController::class, 'calenderEvents'])->name('calenderEvents');
        });
        Route::prefix('types')->name('type.')->group(function () {
            Route::get('/', [EventTypeController::class, 'index'])->name('index')->middleware('checkPermission:event_type_read');
            Route::post('/store', [EventTypeController::class, 'store'])->name('store')->middleware('checkPermission:event_type_create');
            Route::get('/edit/{id}', [EventTypeController::class, 'edit'])->name('edit')->middleware('checkPermission:event_type_update');
            Route::post('/update', [EventTypeController::class, 'update'])->name('update')->middleware('checkPermission:event_type_update');
            Route::post('/change-status', [EventTypeController::class, 'changeStatus'])->name('change-status')->middleware('checkPermission:event_type_change_status');
            Route::post('/delete', [EventTypeController::class, 'delete'])->name('delete')->middleware('checkPermission:event_type_delete');
        });
    });

    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/category', [BlogCategoryController::class, 'index'])->name('category.index')->middleware('checkPermission:blog_category_read');
        Route::post('category/change-status', [BlogCategoryController::class, 'changeStatus'])->name('category.changeStatus')->middleware('checkPermission:blog_category_status_change');
        Route::post('/category/store', [BlogCategoryController::class, 'store'])->name('category.store')->middleware('checkPermission:blog_category_create');
        Route::get('/category/{id}', [BlogCategoryController::class, 'edit'])->name('category.edit')->middleware('checkPermission:blog_category_update');
        Route::post('/category/update', [BlogCategoryController::class, 'update'])->name('category.update')->middleware('checkPermission:blog_category_update');
        Route::post('/destroy', [BlogCategoryController::class, 'destroy'])->name('category.destroy')->middleware('checkPermission:blog_category_delete');

        Route::prefix('post')->name('post.')->group(function () {
            Route::get('/', [BlogPostController::class, 'index'])->name('index')->middleware('checkPermission:blog_post_read');
            Route::post('/change-status', [BlogPostController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:blog_post_status_change');
            Route::post('/change-publish-status', [BlogPostController::class, 'changePublishStatus'])->name('changePublishStatus')->middleware('checkPermission:blog_post_publish_status_change');
            Route::get('/create', [BlogPostController::class, 'create'])->name('create')->middleware('checkPermission:blog_post_create');
            Route::post('/store', [BlogPostController::class, 'store'])->name('store')->middleware('checkPermission:blog_post_create');
            Route::delete('/delete', [BlogPostController::class, 'destroy'])->name('delete')->middleware('checkPermission:blog_post_delete');
            Route::get('/post-details/{id}', [BlogPostController::class, 'show'])->name('show')->middleware('checkPermission:blog_post_read');
            Route::get('/post-details/{id}/edit', [BlogPostController::class, 'edit'])->name('edit')->middleware('checkPermission:blog_post_update');
            Route::post('/update/{id}', [BlogPostController::class, 'update'])->name('update')->middleware('checkPermission:blog_post_update');
        });
    });

    Route::prefix('user')->name('employee.')->group(function () {
        Route::get('/employee-list', [UserController::class, 'employeeList'])->name('employeeList')->middleware('checkPermission:employees_read');
        Route::prefix('service')->name('service.')->group(function () {
            Route::get('/', [ProjectServiceController::class, 'index'])->name('index')->middleware('checkPermission:service_setup_read');
            Route::post('/', [ProjectServiceController::class, 'store'])->name('store')->middleware('checkPermission:create_service_setup');
            Route::get('/edit/{id}', [ProjectServiceController::class, 'edit'])->name('edit')->middleware('checkPermission:update_service_setup');
            Route::post('/update', [ProjectServiceController::class, 'update'])->name('update')->middleware('checkPermission:update_service_setup');
            Route::post('/delete', [ProjectServiceController::class, 'delete'])->name('destroy')->middleware('checkPermission:delete_service_setup');
            Route::post('/change-status', [ProjectServiceController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:change_status_service_setup');


            Route::prefix('assign-user')->name('assign-user.')->group(function () {
                Route::get('/{id}', [ProjectServiceController::class, 'assignUser'])->name('index');
                Route::post('/store', [ProjectServiceController::class, 'storeAssignUser'])->name('store')->middleware('checkPermission:assign_user_service_setup   ');
                Route::get('/edit/{id}', [ProjectServiceController::class, 'editAssignUser'])->name('editUser');
                Route::post('/delete', [ProjectServiceController::class, 'deleteAssignUser'])->name('delete');
                Route::post('/change-status', [ProjectServiceController::class, 'assignUserChangeStatus'])->name('changeStatus');
            });
        });

    });

    Route::prefix('assigned-designer')->name('assignedDesigner.')->group(function () {
        Route::get('/', [CustomerAssignedDesignerController::class, 'index'])->name('index');
        Route::post('/assigned-designer/update-status', [CustomerAssignedDesignerController::class, 'updateStatus'])->name('updateStatus');
    });


    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/list/{role_id}', [UserController::class, 'index'])->name('index')->middleware('checkPermission:user_management_read');
        Route::get('/profile/{user:id}', [UserController::class, 'profile'])->name('profile');
        Route::get('/orders/{user_id}', [UserController::class, 'orders'])->name('orders');
        Route::get('/carts/{user_id}', [UserController::class, 'carts'])->name('carts');
        Route::get('/wishlist/{user_id}', [UserController::class, 'wishlist'])->name('wishlist');
        Route::get('/products/{user_id}', [UserController::class, 'products'])->name('products');
        Route::get('/getUser/{user_id}', [UserController::class, 'getUser'])->name('getUser');
        Route::post('/send-credential', [UserController::class, 'sendCredential'])->name('sendCredential');

        Route::get('/assign-permission/{user_id}', [UserController::class, 'assignPermission'])->name('assignPermission');
        Route::post('/permission-update/aa', [UserController::class, 'permissionUpdate'])->name('permissionUpdate');

        Route::post('/password-reset', [UserController::class, 'passwordReset'])->name('passwordReset');
        Route::post('/update', [UserController::class, 'update'])->name('update');
        Route::post('/change-status', [UserController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:user_status_change');
        Route::post('/login-as', [UserController::class, 'loginAs'])->name('loginAs');
        Route::post('/default-password', [UserController::class, 'resetDefaultPassword'])->name('defaultPassword');


        Route::post('/shop-info-update', [UserController::class, 'shopInfoUpdate'])->name('shopInfoUpdate')->middleware('checkPermission:site_info_update');
        Route::post('/shop-logo-update', [UserController::class, 'shoplogoUpdate'])->name('shoplogoUpdate')->middleware('checkPermission:site_logo_update');
        Route::post('/shop-link-update', [UserController::class, 'shoplinkUpdate'])->name('shoplinkUpdate')->middleware('checkPermission:social_links_update');

        Route::post('/destroy', [UserController::class, 'destroy'])->name('destroy');

        Route::post('/store', [UserController::class, 'store'])->name('store');
    });

    Route::post('customer/store', [UserController::class, 'customerStore'])->name('customer.store');


    Route::prefix('subscriber')->name('subscriber.')->group(function () {
        Route::get('/index', [SubscriberController::class, 'index'])->name('index')->middleware('checkPermission:subscribers_read');
        Route::get('/{id}', [SubscriberController::class, 'get'])->name('get')->middleware('checkPermission:subscribers_read');
        Route::post('/change-status', [SubscriberController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:subscribers_status_change');
        Route::post('/send-reply', [SubscriberController::class, 'sendReply'])->name('sendReply')->middleware('checkPermission:subscribers_reply');
        Route::post('/delete', [SubscriberController::class, 'destroy'])->name('destroy')->middleware('checkPermission:subscribers_read');
        Route::get('/export/email', [SubscriberController::class, 'exportEmails'])->name('exportEmails');
    });

    Route::prefix('marketing')->name('marketing.')->group(function () {
        Route::get('/email-campaign', [EmailCampaignController::class, 'index'])->name('campaign.index')->middleware('checkPermission:email_campaign_read');
        Route::post('/email-campaign', [EmailCampaignController::class, 'store'])->name('store')->middleware('checkPermission:add_email_campaign');
        Route::post('/change-status', [EmailCampaignController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:email_campaign_status_change');
        Route::get('/campaign/{id}/edit', [EmailCampaignController::class, 'edit'])->name('marketing.campaign.edit')->middleware('checkPermission:email_campaign_update');
        Route::post('/campaign', [EmailCampaignController::class, 'update'])->name('marketing.campaign.update')->middleware('checkPermission:email_campaign_update');
        Route::delete('/delete', [EmailCampaignController::class, 'destroy'])->name('delete')->middleware('checkPermission:email_campaign_delete');
        Route::get('/assign-email-users/{id}', [EmailCampaignController::class, 'launch'])->name('launch.index')->middleware('checkPermission:email_campaign_assign_user');
        Route::get('/get-users-by-type', [EmailCampaignController::class, 'getUsersByType'])->name('get-users-by-type');
        Route::post('/email-campaign/{id}/update-emails', [EmailCampaignController::class, 'updateEmailcampaignEmails'])->name('updateEmailcampaignEmails')->middleware('checkPermission:email_campaign_update');
        Route::post('/send-email', [EmailCampaignController::class, 'sendEmail'])->name('send.email')->middleware('checkPermission:email_campaign_status_change');

        Route::get('/download-demo-file', [EmailCampaignController::class, 'downloadDemoFile'])->name('downloadDemoFile');
        Route::get('/email-logs/{id}', [EmailCampaignController::class, 'emailLog'])->name('email.log');

        Route::get('/download-demo-file', [EmailCampaignController::class, 'downloadDemoFile'])->name('downloadDemoFile');
    });


    Route::prefix('expense-management')->name('expense-management.')->group(function () {
        Route::get('/expense-type', [ExpenseTypeController::class, 'index'])->name('type.index')->middleware('checkPermission:expense_type_read');
        Route::post('/expense-type/store', [ExpenseTypeController::class, 'store'])->name('type.store')->middleware('checkPermission:expense_type_create');
        Route::post('/change-status', [ExpenseTypeController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:expense_type_change_status');
        Route::get('/expense-type/{id}', [ExpenseTypeController::class, 'edit'])->name('type.edit')->middleware('checkPermission:expense_type_update');
        Route::post('/expense-type/update', [ExpenseTypeController::class, 'update'])->name('type.update')->middleware('checkPermission:expense_type_update');
        Route::post('/destroy', [ExpenseTypeController::class, 'destroy'])->name('type.destroy')->middleware('checkPermission:expense_type_delete');

        Route::prefix('expenses')->name('expenses.')->group(function () {
            Route::get('/', [ExpenseController::class, 'index'])->name('index')->middleware('checkPermission:expense_read');
            Route::post('/store', [ExpenseController::class, 'store'])->name('store')->middleware('checkPermission:expense_create');
            Route::get('/{id}/edit', [ExpenseController::class, 'edit'])->name('edit')->middleware('checkPermission:expense_update');
            Route::post('/', [ExpenseController::class, 'update'])->name('update')->middleware('checkPermission:expense_update');
            Route::delete('/delete', [ExpenseController::class, 'destroy'])->name('destroy')->middleware('checkPermission:expense_delete');
            Route::post('/change-status', [ExpenseController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:expense_change_status');
        });
    });

    Route::prefix('notice-board')->name('notice-board.')->group(function () {
        Route::get('/notice-type', [NoticeTypeController::class, 'index'])->name('type.index')->middleware('checkPermission:notice_type_read');
        Route::post('/notice-type/store', [NoticeTypeController::class, 'store'])->name('type.store')->middleware('checkPermission:notice_type_create');
        Route::post('/change-status', [NoticeTypeController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:notice_type_change_status');
        Route::get('/notice-type/{id}', [NoticeTypeController::class, 'edit'])->name('type.edit')->middleware('checkPermission:notice_type_update');
        Route::post('/notice-type/update', [NoticeTypeController::class, 'update'])->name('type.update')->middleware('checkPermission:notice_type_update');
        Route::post('/destroy', [NoticeTypeController::class, 'destroy'])->name('type.destroy')->middleware('checkPermission:notice_type_delete');

        Route::prefix('notice')->name('notice.')->group(function () {
            Route::get('/', [NoticeBoardController::class, 'index'])->name('index')->middleware('checkPermission:notice_read');
            Route::post('/store', [NoticeBoardController::class, 'store'])->name('store')->middleware('checkPermission:notice_create');
            Route::get('/{id}/edit', [NoticeBoardController::class, 'edit'])->name('edit')->middleware('checkPermission:notice_update');
            Route::post('/update', [NoticeBoardController::class, 'update'])->name('update')->middleware('checkPermission:notice_update');
            Route::delete('/delete', [NoticeBoardController::class, 'destroy'])->name('destroy')->middleware('checkPermission:notice_delete');
            Route::post('/change-status', [NoticeBoardController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:notice_change_status');
            Route::get('/assign-recievers/{id}', [NoticeBoardController::class, 'assign'])->name('assign')->middleware('checkPermission:assign_user_read');
            Route::get('/get-users-by-type', [NoticeBoardController::class, 'getUsersByType'])->name('get-users-by-type')->middleware('checkPermission:assign_user_read');
            Route::post('/{id}/update-receivers', [NoticeBoardController::class, 'updateNoticeBoardReceivers'])->name('updateReceivers')->middleware('checkPermission:assign_user_read');
            Route::get('/board', [NoticeBoardController::class, 'noticeBoard'])->name('noticeBoard')->middleware('checkPermission:notice_board_read');
            Route::post('/send-email', [NoticeBoardController::class, 'sendEmail'])->name('send.email');
            Route::get('/email-logs/{id}', [NoticeBoardController::class, 'emailLog'])->name('email.log');
        });
    });

    Route::prefix('contact-request')->name('contact-request.')->group(function () {
        Route::get('/', [ContactRequestController::class, 'index'])->name('index')->middleware('checkPermission:contact_request_read');
        Route::post('/change-status', [ContactRequestController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:contact_request_status_change');
        Route::delete('/delete', [ContactRequestController::class, 'destroy'])->name('delete')->middleware('checkPermission:contact_request_delete');
        Route::get('/{id}', [ContactRequestController::class, 'getContactRequest'])->name('get')->middleware('checkPermission:contact_request_read');
        Route::post('/send-reply', [ContactRequestController::class, 'sendReply'])->name('sendReply')->middleware('checkPermission:contact_request_reply');
        Route::get('/logs/{id}', [ContactRequestController::class, 'emailLog'])->name('emailLog');
    });


    Route::prefix('designer-contact')->name('designer-contact.')->group(function () {
        Route::get('/', [DesignerContactController::class, 'index'])->name('index')->middleware('checkPermission:designer_contact_read');
        Route::get('/{id}', [DesignerContactController::class, 'getDesignerContact'])->name('get')->middleware('checkPermission:designer_contact_read');
        Route::get('/logs/{id}', [DesignerContactController::class, 'emailLog'])->name('emailLog')->middleware('checkPermission:designer_contact_read');
        Route::delete('/delete', [DesignerContactController::class, 'destroy'])->name('delete')->middleware('checkPermission:designer_contact_delete');
        Route::post('/send-reply', [DesignerContactController::class, 'sendReply'])->name('sendReply')->middleware('checkPermission:designer_contact_reply');
    });

    Route::prefix('role')->name('role.')->group(function () {
        Route::get('/index', [RoleController::class, 'index'])->name('index')->middleware('checkPermission:role_read');
        Route::post('/store', [RoleController::class, 'store'])->name('store')->middleware('checkPermission:role_create');
        Route::post('/update', [RoleController::class, 'update'])->name('update')->middleware('checkPermission:role_update');
        Route::post('/delete', [RoleController::class, 'destroy'])->name('destroy')->middleware('checkPermission:role_delete');
        Route::get('/assign-permission/{id}', [RoleController::class, 'assignPermission'])->name('assignPermission')->middleware('checkPermission:give_permission');
        Route::post('/permission-update', [RoleController::class, 'permissionUpdate'])->name('permissionUpdate')->middleware('checkPermission:give_permission');
    });

    Route::prefix('cms')->name('cms.')->group(function () {
        Route::prefix('footer-widget')->name('footer-widget.')->group(function () {
            Route::get('/', [FooterWidgetController::class, 'index'])->name('index')->middleware('checkPermission:footer_widget_read');
            Route::get('/{id}', [FooterWidgetController::class, 'edit'])->name('edit')->middleware('checkPermission:footer_widget_update');
            Route::post('/update', [FooterWidgetController::class, 'update'])->name('update')->middleware('checkPermission:footer_widget_update');
            Route::post('/destroy', [FooterWidgetController::class, 'destroy'])->name('destroy')->middleware('checkPermission:footer_widget_delete');
            Route::post('/change-status', [FooterWidgetController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:footer_widget_status_change');
        });

        Route::prefix('pages')->name('pages.')->group(function () {
            Route::get('/', [PageController::class, 'index'])->name('index')->middleware('checkPermission:pages_read');
            Route::get('/edit/{id}', [PageController::class, 'edit'])->name('edit')->middleware('checkPermission:pages_update');
            Route::post('/update', [PageController::class, 'update'])->name('update')->middleware('checkPermission:pages_update');
            Route::post('/change-status', [PageController::class, 'changeStatus'])->name('changeStatus');
        });

        Route::prefix('faq')->name('faq.')->group(function () {
            Route::get('/', [FaqController::class, 'index'])->name('index')->middleware('checkPermission:faqs_read');
            Route::post('/store', [FaqController::class, 'store'])->name('store')->middleware('checkPermission:faqs_create');
            Route::get('/edit/{id}', [FaqController::class, 'edit'])->name('edit')->middleware('checkPermission:faqs_update');
            Route::post('/update', [FaqController::class, 'update'])->name('update')->middleware('checkPermission:faqs_update');
            Route::post('/delete', [FaqController::class, 'destroy'])->name('destroy')->middleware('checkPermission:faqs_delete');
            Route::post('/change-status', [FaqController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:faqs_status_change');
        });

        Route::prefix('slider')->name('slider.')->group(function () {
            Route::get('/', [SliderController::class, 'index'])->name('index')->middleware('checkPermission:slider_read');
            Route::post('/store', [SliderController::class, 'store'])->name('store')->middleware('checkPermission:slider_create');
            Route::get('/edit/{id}', [SliderController::class, 'edit'])->name('edit')->middleware('checkPermission:slider_update');
            Route::post('/update', [SliderController::class, 'update'])->name('update')->middleware('checkPermission:slider_update');
            Route::post('/change-status', [SliderController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:slider_status_change');
            Route::post('/delete-slider', [SliderController::class, 'delete'])->name('destroy')->middleware('checkPermission:slider_delete');
        });

    });

    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::prefix('plan')->name('plan.')->group(function () {
            Route::get('/', [PlanController::class, 'index'])->name('index')->middleware('checkPermission:plan_read');
            Route::post('/store', [PlanController::class, 'store'])->name('store')->middleware('checkPermission:plan_create');
            Route::get('/edit/{id}', [PlanController::class, 'edit'])->name('edit')->middleware('checkPermission:plan_update');
            Route::post('/update', [PlanController::class, 'update'])->name('update')->middleware('checkPermission:plan_update');
            Route::post('/destroy', [PlanController::class, 'destroy'])->name('destroy');
            Route::post('/change-status', [PlanController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:plan_status_change');
            Route::post('/make-popular', [PlanController::class, 'makePopular'])->name('makePopular')->middleware('checkPermission:plan_make_popular');

            Route::get('/buy-plan', [PlanController::class, 'buyPlan'])->name('buyPlan')->withoutMiddleware('checkSubscription');
            Route::post('/plan-make-payment', [PlanController::class, 'makePayment'])->name('make-payment')->withoutMiddleware('checkSubscription');
        });
        Route::prefix('customer')->name('customer.')->group(function () {
            Route::get('/', [SubscripitonController::class, 'index'])->name('index')->middleware('checkPermission:subscription_read');
            Route::get('/details/{user_id}', [SubscripitonController::class, 'details'])->name('details');

            Route::get('/invoice-preview/{invoice_no}', [SubscripitonController::class, 'invoicePreview'])->name('invoicePreview');
            Route::get('/invoice-print/{order_id}', [SubscripitonController::class, 'invoicePrint'])->name('invoicePrint');
            Route::get('/invoice-download/{order_id}', [SubscripitonController::class, 'invoiceDownload'])->name('invoiceDownload');

            Route::get('/stripe-generate-invoice/{invoice_no}', [SubscripitonController::class, 'stripeGenerateInvoice'])->name('stripeGenerateInvoice');

            Route::get('/immediate-cancel-stripe-subscription/{user_id}', [SubscripitonController::class, 'immediateSubscriptionCancel'])->name('immediateSubscriptionCancel');
            Route::get('/revoke-cancel-stripe-subscription/{user_id}', [SubscripitonController::class, 'revokeSubscriptionCancel'])->name('revokeSubscriptionCancel');
        });
        Route::prefix('free-trail')->group(function () {
            Route::get('/', [SubscripitonController::class, 'freeTrail'])->name('free-trail')->middleware('checkPermission:subscription_read');
            Route::post('/terminate', [SubscripitonController::class, 'freeTrailTerminate'])->name('free-trail.terminate')->middleware('checkPermission:subscription_read');
        });

        Route::prefix('cancel-request')->name('cancelRequest.')->group(function () {
            Route::get('/', [SubscriptionCancelRequestController::class, 'index'])->name('index')->middleware('checkPermission:subscription_calcel_list_read');
            Route::post('/store', [SubscriptionCancelRequestController::class, 'store'])->name('store');
            Route::post('/approve-request', [SubscriptionCancelRequestController::class, 'approve'])->name('approve');
            Route::post('/cancel-request', [SubscriptionCancelRequestController::class, 'cancel'])->name('cancel');
            Route::post('/order-count', [SubscriptionCancelRequestController::class, 'orderCount'])->name('orderCount');
        });
    });

    Route::prefix('coupon')->name('coupon.')->group(function () {
        Route::get('/', [CouponController::class, 'index'])->name('index');
        Route::post('/store', [CouponController::class, 'store'])->name('store');
        Route::get('/{id}', [CouponController::class, 'edit'])->name('edit');
        Route::post('/update', [CouponController::class, 'update'])->name('update');
        Route::post('/destroy', [CouponController::class, 'destroy'])->name('destroy');
        Route::post('/change-status', [CouponController::class, 'changeStatus'])->name('changeStatus');
    });


    Route::prefix('designer-reviews')->name('review.')->group(function () {
        Route::prefix('type')->name('type.')->group(function () {
            Route::get('/', [ReviewTypeController::class, 'index'])->name('index');
            Route::post('/store', [ReviewTypeController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [ReviewTypeController::class, 'edit'])->name('edit');
            Route::post('/update', [ReviewTypeController::class, 'update'])->name('update');
            Route::delete('/delete', [ReviewTypeController::class, 'destroy'])->name('destroy');
            Route::post('/change-status', [ReviewTypeController::class, 'changeStatus'])->name('changeStatus');
        });

        Route::get('/', [ReviewController::class, 'index'])->name('index');
        Route::post('/change-status', [ReviewController::class, 'changeStatus'])->name('changeStatus');
    });

    Route::prefix('seo-content')->name('seoContent.')->group(function () {
        Route::prefix('product')->name('product.')->group(function () {
            Route::get('/', [ProductSeoController::class, 'index'])->name('index');
            Route::get('/edit/{id}', [ProductSeoController::class, 'edit'])->name('edit');
            Route::post('/update', [ProductSeoController::class, 'update'])->name('update');
        });
    });

    Route::prefix('sitemap')->group(function () {
        Route::get('/', [SiteMapController::class, 'index'])->name('sitemap.index');
        Route::get('/sync/{file}', [SiteMapController::class, 'sync'])->name('sitemap.sync');
        Route::get('/regenerate', [SiteMapController::class, 'regenerate'])->name('sitemap.generate');
    });

    Route::prefix('setting')->name('setting.')->group(function () {
        Route::prefix('/general-setting')->name('general-setting.')->group(function () {
            Route::get('/', [GeneralSettingController::class, 'index'])->name('index')->middleware('checkPermission:general_settings_read');
            Route::Post('/system-info/store', [GeneralSettingController::class, 'storeSystemInfo'])->name('system_info.store')->middleware('checkPermission:general_settings_update');
        });

        Route::prefix('/payment-method')->name('payment-method.')->group(function () {
            Route::get('/', [PaymentMethodController::class, 'index'])->name('index')->middleware('checkPermission:payment_method_read');
            Route::post('/stripe-credential-store', [PaymentMethodController::class, 'stripeCredentialStore'])->name('stripeCredentialStore')->middleware('checkPermission:payment_method_credentials_update');
            Route::post('/paypal-credential-store', [PaymentMethodController::class, 'paypalCredentialStore'])->name('paypalCredentialStore')->middleware('checkPermission:payment_method_credentials_update');

//            Route::post('/stripe-change-status', [PaymentMethodController::class, 'stripeChangeStatus'])->name('stripeChangeStatus')->middleware('checkPermission:payment_method_status_change');
//            Route::post('/paypal-change-status', [PaymentMethodController::class, 'paypalChangeStatus'])->name('paypalChangeStatus')->middleware('checkPermission:payment_method_status_change');

            Route::post('/change-publish-status', [PaymentMethodController::class, 'changePublishStatus'])->name('changePublishStatus')->middleware('checkPermission:payment_method_publish_status_change');

            Route::post("/store", [PaymentMethodController::class, 'store'])->name('store')->middleware('checkPermission:payment_method_create');
            Route::post('/delete/{id}', [PaymentMethodController::class, 'delete'])->name('delete')->middleware('checkPermission:payment_method_delete');
            Route::get('/edit/{id}', [PaymentMethodController::class, 'edit'])->name('edit')->middleware('checkPermission:payment_method_update');
            Route::post("/update", [PaymentMethodController::class, 'update'])->name('update')->middleware('checkPermission:payment_method_update');
            Route::post("/verify-password", [PaymentMethodController::class, 'verifyPassword'])->name('verifyPassword');
        });

        Route::prefix('file-system')->name('file-system.')->group(function () {
            Route::get('/', [FileSystemController::class, 'index'])->name('index')->middleware('checkPermission:file_system_read');
            Route::post('/credentials/store', [FileSystemController::class, 'storeCredentials'])->name('storeCredentials')->middleware('checkPermission:file_system_update');
        });

        Route::prefix('/global-setting')->name('global-setting.')->group(function () {
            Route::get('/', [GlobalSettingController::class, 'index'])->name('index')->middleware('checkPermission:global_settings_read');
            Route::post('/update', [GlobalSettingController::class, 'update'])->name('update')->middleware('checkPermission:global_settings_update');
        });

        Route::prefix('/api-setting')->name('api-setting.')->group(function () {
            Route::get('/', [APISettingController::class, 'index'])->name('index');
        });

        Route::prefix('/email-setting')->name('email-setting.')->group(function () {
            Route::get('/', [EmailSettingController::class, 'index'])->name('index')->middleware('checkPermission:email_settings_read');
            Route::post('/store', [EmailSettingController::class, 'store'])->name('store')->middleware('checkPermission:email_settings_update');
            Route::post('/send-mail', [EmailSettingController::class, 'sendTestEmail'])->name('sendTestEmail')->middleware('checkPermission:send_test_email');
        });

        Route::prefix('/shop-setting')->name('shop-setting.')->group(function () {
            Route::get('/', [ShopSettingController::class, 'index'])->name('index')->middleware('checkPermission:shop_settings_read');
            Route::Post('/system-info/store', [ShopSettingController::class, 'storeSystemInfo'])->name('system_info.store')->middleware('checkPermission:site_info_update');
            Route::Post('/site-logo/store', [ShopSettingController::class, 'storeSiteLogo'])->name('site_logo.store')->middleware('checkPermission:site_logo_update');
            Route::Post('/social-link/store', [ShopSettingController::class, 'storeSocialLink'])->name('social_link.store')->middleware('checkPermission:social_links_update');
            Route::Post('/terms-polices/store', [ShopSettingController::class, 'storeTermsPolices'])->name('terms_polices.store')->middleware('checkPermission:terms_and_policies_update');
            Route::post('/emergency-notice/store', [ShopSettingController::class, 'storeEmergencyNotice'])->name('emergency-notice.store')->middleware('checkPermission:emergency_notice_update');
            Route::post('/emergency-notice/change-status', [ShopSettingController::class, 'changeEmergencyNoticeStatus'])->name('emergency-notice.changeStatus')->middleware('checkPermission:emergency_notice_change_status');
            Route::post('/product-setting/change-status', [ShopSettingController::class, 'changeProductSettingStatus'])->name('product-setting.changeStatus')->middleware('checkPermission:product_setting_change_status');
            Route::post('/shop-status/change', [ShopSettingController::class, 'changeShopStatus'])->name('shop-setting.changeStatus');
        });

        Route::prefix('/color-themes')->name('color-themes.')->group(function () {
            Route::get('/', [ColorThemeController::class, 'index'])->name('index')->middleware('checkPermission:color_themes_read');
            Route::post('/store', [ColorThemeController::class, 'store'])->name('store')->middleware('checkPermission:color_themes_create');
            Route::delete('/delete', [ColorThemeController::class, 'delete'])->name('delete')->middleware('checkPermission:color_themes_delete');
            Route::post('/apply', [ColorThemeController::class, 'applyTheme'])->name('apply')->middleware('checkPermission:color_themes_apply');
        });

        Route::prefix('/language')->name('language.')->group(function () {
            Route::get('/', [LanguageController::class, 'index'])->name('index')->middleware('checkPermission:language_settings_read');
            Route::post('/store', [LanguageController::class, 'store'])->name('store')->middleware('checkPermission:language_settings_create');
            Route::get('/edit/{id}', [LanguageController::class, 'edit'])->name('edit')->middleware('checkPermission:language_settings_update');
            Route::post('/update', [LanguageController::class, 'update'])->name('update')->middleware('checkPermission:language_settings_update');
            Route::delete('/delete', [LanguageController::class, 'delete'])->name('delete')->middleware('checkPermission:language_settings_delete');
            Route::post('/change-status', [LanguageController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:language_settings_status_change');
            Route::get('/setup/{code}', [LanguageController::class, 'setup'])->name('setup')->middleware('checkPermission:language_settings_read');
            Route::get("/read-file", [LanguageController::class, 'readFile'])->name('readFile')->middleware('checkPermission:language_settings_create');
            Route::post("/update/file", [LanguageController::class, 'updateFile'])->name('updateFile')->middleware('checkPermission:language_settings_update');
            Route::post("/change/language", [LanguageController::class, 'changeLanguage'])->name('changeLanguage')->middleware('checkPermission:language_settings_update');
        });

        Route::prefix('/background-settings')->name('background-settings.')->group(function () {
            Route::get('/', [BackgroundSettingsController::class, 'index'])->name('index')->middleware('checkPermission:background_settings_read');
            Route::post('/change-status', [BackgroundSettingsController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:background_settings_status_change');
            Route::delete('/delete', [BackgroundSettingsController::class, 'delete'])->name('delete')->middleware('checkPermission:background_settings_delete');
            Route::post('/store', [BackgroundSettingsController::class, 'store'])->name('store')->middleware('checkPermission:background_settings_create');
            Route::get('/edit/{id}', [BackgroundSettingsController::class, 'edit'])->name('edit')->middleware('checkPermission:background_settings_update');
            Route::post('/update', [BackgroundSettingsController::class, 'update'])->name('update')->middleware('checkPermission:background_settings_update');
        });

        Route::prefix('activity-log')->name('activity-log.')->group(function () {
            Route::get('/', [ActivityLogController::class, 'index'])->name('index')->middleware('checkPermission:activity_log_read');
            Route::get('/report/{type}', [ActivityLogController::class, 'activityLogReportPDF'])->name('report')->middleware('checkPermission:activity_log_export');
        });

        Route::prefix('free-signup')->name('free-signup.')->group(function () {
            Route::get('/', [FreeSignupController::class, 'index'])->name('index')->middleware('checkPermission:free_signup_read');
            Route::post('/', [FreeSignupController::class, 'store'])->name('store')->middleware('checkPermission:free_signup_create');
            Route::post('/changeStatus', [FreeSignupController::class, 'changeStatus'])->name('changeStatus')->middleware('checkPermission:free_signup_status_change');
            Route::get('/generateKey', [FreeSignupController::class, 'generateKey'])->name('generateKey');
            Route::post('/destroy', [FreeSignupController::class, 'destroy'])->name('destroy')->middleware('checkPermission:free_signup_delete');
            Route::get('/user-list/{id}', [FreeSignupController::class, 'userList'])->name('userList')->middleware('checkPermission:free_signup_read');
        });

        Route::prefix('quick-shop-setup')->name('quickShopSetting.')->group(function () {
            Route::get('/', [QuickShopSettingController::class, 'index'])->name('index');
            Route::post('/heroSection', [QuickShopSettingController::class, 'heroSection'])->name('heroSection');
            Route::get('/category', [QuickShopSettingController::class, 'category'])->name('category');
            Route::get('/getShopSetting', [QuickShopSettingController::class, 'getShopSetting'])->name('getShopSetting');
            Route::get('/getColorTheme', [QuickShopSettingController::class, 'getColorTheme'])->name('getColorTheme');
            Route::get('/getFooter', [QuickShopSettingController::class, 'getFooter'])->name('getFooter');
            Route::post('/section-content', [QuickShopSettingController::class, 'sectionContent'])->name('sectionContent');
            Route::get('/shop-setting-percentage', [QuickShopSettingController::class, 'shopSettingPercentage'])->name('shopSettingPercentage');
        });

        Route::prefix('cache-setup')->name('cacheSetup.')->group(function () {
            Route::get('/', [CacheSetupController::class, 'index'])->name('index');
            Route::post('/clear-cache', [CacheSetupController::class, 'cacheClear'])->name('cacheClear');
        });
    });

    Route::prefix('report')->name('report.')->group(function () {
        Route::get('/expense-report', [ReportController::class, 'expenseReport'])->name('expenseReport')->middleware('checkPermission:expense_report_read');
        Route::get('/expense-details/{id}', [ReportController::class, 'expenseDetails'])->name('expenseDetails');
        Route::get('/expense-report-export/{type}', [ReportController::class, 'expenseReportExport'])->name('expenseReportExport')->middleware('checkPermission:expense_report_export');

        Route::get('/order-report', [ReportController::class, 'orderReport'])->name('orderReport')->middleware('checkPermission:order_report_read');
        Route::get('/order-report-export/{type}', [ReportController::class, 'orderReportExport'])->name('orderReportExport')->middleware('checkPermission:order_report_export');

        Route::get('/product-report', [ReportController::class, 'productReport'])->name('productReport')->middleware('checkPermission:product_report_read');
        Route::get('/product-report-export/{type}', [ReportController::class, 'productReportPDF'])->name('productReportPDF')->middleware('checkPermission:product_report_export');

        Route::get('/user-report', [ReportController::class, 'userReport'])->name('userReport')->middleware('checkPermission:user_report_read');
        Route::get('/user-report-export/{type}', [ReportController::class, 'userReportPDF'])->name('userReportPDF')->middleware('checkPermission:user_report_export');

        Route::get('/subscription-report', [ReportController::class, 'subscriptionReport'])->name('subscriptionReport')->middleware('checkPermission:subscription_plan_report_read');
        Route::get('/subscription-report-export/{type}', [ReportController::class, 'subscriptionReportPDF'])->name('subscriptionReportPDF')->middleware('checkPermission:subscription_plan_report_export');

        Route::get('/shared-product-report', [ReportController::class, 'sharedProductReport'])->name('sharedProductReport')->middleware('checkPermission:white_list_product_report_read');
        Route::get('/shared-product-report-export/{type}', [ReportController::class, 'sharedProductReportPDF'])->name('sharedProductPDF')->middleware('checkPermission:white_list_product_report_export');
        Route::get('/shared-product-designer-list/{id}', [ReportController::class, 'sharedProductDesignerList'])->name('sharedProductDesignerList');

        Route::get('/search-keyword-report', [ReportController::class, 'searchKeywordReport'])->name('searchKeywordReport')->middleware('checkPermission:search_keyword_report_read');
    });

    Route::post('/shippingAddress', [ShippingAddressController::class, 'store'])->name('shippingAddress.store');

    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::post('/', [ChatController::class, 'store'])->name('store');
        Route::get('/recent-users/{user_id}', [ChatController::class, 'recentUsers'])->name('recent.users');
        Route::get('/all-users/{user_id}', [ChatController::class, 'allUsers'])->name('all.users');
        Route::get('/messages/{user_id}', [ChatController::class, 'messages'])->name('messages');
        Route::post('/send-message', [ChatController::class, 'store'])->name('sendMessage');
    });

    Route::get('/access-houseBrands-ai', [UserController::class, 'accessHouseBrandsAI'])->name('accessHouseBrandsAI');



});
Route::post('stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook')->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

Route::get('/access-admin-portal', [UserController::class, 'accessAdminPortal']);

//Route::get('/section-setting', function () {
//    $shopSetting = ShopSetting::first();
//    $shopSetting->section_content = [
//
//        'hero' => [
//            'display_control' => 1,
//        ],
//        'category' => [
//            'display_control' => 1,
//            'label' => "Browse The Category",
//        ],
//        'product' => [
//            'display_control' => 1,
//            'label' => "Browse The Product",
//        ],
//        'inspiration' => [
//            'display_control' => 1,
//            'label' => "Browse The Inspiration",
//        ],
//        'portfolio' => [
//            'display_control' => 1,
//            'label' => "Portfolio",
//        ],
//        'gallery' => [
//            'display_control' => 1,
//            'label' => "Gallery",
//        ],
//
//    ];
//    $shopSetting->save();
//});


require __DIR__ . '/auth.php';
