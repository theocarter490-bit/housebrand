<?php

use App\Http\Controllers\API\SiteMapController;
use App\Http\Controllers\API\V1\ChatMessageController;
use App\Http\Controllers\API\V1\CustomerAssignedDesignerController;
use App\Http\Controllers\API\V1\ExternalAuthenticationController;
use App\Http\Controllers\API\V1\FreeTrailController;
use App\Http\Controllers\API\V1\IdeaBoardController;
use App\Http\Controllers\API\V1\ProjectController;
use App\Http\Controllers\API\V1\ProjectProposalController;
use App\Http\Controllers\API\V1\ProposalInvoiceController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\API\V1\DocumentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\FaqController;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\CartController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\BrandController;
use App\Http\Controllers\API\V1\OrderController;
use App\Http\Controllers\API\V1\ReviewController;
use App\Http\Controllers\API\V1\SliderController;
use App\Http\Controllers\API\V1\GalleryController;
use App\Http\Controllers\API\V1\ProductController;
use App\Http\Controllers\API\V1\ProfileController;
use App\Http\Controllers\API\V1\BlogPostController;


use App\Http\Controllers\API\V1\CategoryController;
use App\Http\Controllers\API\V1\WishlistController;
use App\Http\Controllers\API\V1\ContactUsController;
use App\Http\Controllers\API\V1\ColorThemeController;
use App\Http\Controllers\API\V1\OrderClaimController;
use App\Http\Controllers\API\V1\SubscriberController;
use App\Http\Controllers\API\V1\OrderStatusController;
use App\Http\Controllers\API\V1\ShopSettingController;
use App\Http\Controllers\API\V1\TimeBillingController;
use App\Http\Controllers\API\V1\FooterWidgetController;
use App\Http\Controllers\API\V1\SubscriptionController;
use App\Http\Controllers\API\V1\GeneralSettingController;
use App\Http\Controllers\API\V1\SpecialSectionController;
use App\Http\Controllers\API\V1\DesignerContactController;
use App\Http\Controllers\API\V1\ShippingAddressController;
use App\Http\Controllers\API\V1\BackgroundSettingsController;
use App\Http\Controllers\API\V1\DesignerSharedProductController;
use App\Http\Controllers\ProductClipperController;

Route::get('brands', [BrandController::class, 'list']);
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'list']);
    Route::get('products', [CategoryController::class, 'categoryWithProduct']);
});

Route::prefix('product')->group(function () {
    Route::get('/list', [ProductController::class, 'list']);
    Route::get('/filterItems', [ProductController::class, 'filterItems']);
    Route::get('/{product:id}', [ProductController::class, 'details']);
    Route::get('/{product:id}/related-products', [ProductController::class, 'relatedProducts']);

    Route::prefix('reviews')->middleware('auth:sanctum')->group(function () {
        Route::post('/', [ReviewController::class, 'storeProductReview']);
    });
});

Route::prefix('shared-product')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [DesignerSharedProductController::class, 'index']);
    Route::post('/', [DesignerSharedProductController::class, 'store'])->middleware('checkSubscriptionApi');
});


Route::get('/general-setting', [GeneralSettingController::class, 'index']);
Route::get('/shop-setting', [ShopSettingController::class, 'index']);
Route::get('/footer-widgets', [FooterWidgetController::class, 'widgets']);
Route::get('/page/{slug}', [FooterWidgetController::class, 'pageDetails']);

Route::get('/get-active-payment-methods', [ShopSettingController::class, 'getActivePaymentMethods']);


Route::prefix('designers')->group(function () {
    Route::get('/', [UserController::class, 'designers']);
});


Route::prefix('cart')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/', [CartController::class, 'store'])->middleware('checkSubscriptionApi');
    Route::post('/destroy', [CartController::class, 'destroy']);
    Route::post('/update', [CartController::class, 'update']);
});

Route::prefix('wishlist')->middleware('auth:sanctum')->group(function () {
    Route::post('/toggle', [WishlistController::class, 'toggle']);
    Route::get('/list', [WishlistController::class, 'list']);
    Route::post('/remove', [WishlistController::class, 'remove']);
});

Route::prefix('special-section')->group(function () {
    Route::get('/', [SpecialSectionController::class, 'list']);
    Route::get('/{section_id}', [SpecialSectionController::class, 'details']);
});


Route::get('/dashboard-overview-counts', [UserController::class, 'dashboardOverviewCounts'])->middleware('auth:sanctum');

Route::prefix('order')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::get('/details/{id}', [OrderController::class, 'details']);
    Route::get('/count', [OrderController::class, 'count']);
    Route::get('/invoice-download/{order_id}', [OrderController::class, 'invoiceDownload'])->name('invoiceDownload');

    Route::post('/make-payment/stripe', [OrderController::class, 'makePaymentStripe']);
    Route::post('/success-payment/stripe', [OrderController::class, 'checkoutSuccessStripe']);
    Route::get('/order-status', [OrderController::class, 'orderStatus']);
    Route::post('/cancel-order', [OrderController::class, 'cancelOrder']);

    Route::post('/make-payment/paypal', [OrderController::class, 'makePaymentPaypal']);
    Route::post('/success-payment/paypal', [OrderController::class, 'checkoutSuccessPaypal']);


    Route::prefix('claim')->group(function () {
        Route::post('/', [OrderClaimController::class, 'claimOrder']);
        Route::get('/', [OrderClaimController::class, 'claimsList']);
        Route::get('/{order_id}', [OrderClaimController::class, 'claims']);
        Route::post('/{claim_id}/reply', [OrderClaimController::class, 'claimReply']);
        Route::get('/detail/{claim_id}', [OrderClaimController::class, 'claimDetails']);
        Route::get('/type/list', [OrderClaimController::class, 'types']);
    });
});

Route::prefix('time-billing')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [TimeBillingController::class, 'index']);
    Route::get('time-breakdown/{id}', [TimeBillingController::class, 'timeBreakdown']);
    Route::get('/details/{id}', [TimeBillingController::class, 'details']);
    Route::get('/invoice/{id}', [TimeBillingController::class, 'invoice']);
    Route::post('/make-payment/stripe', [TimeBillingController::class, 'makePaymentStripe']);
    Route::post('/success-payment/stripe', [TimeBillingController::class, 'checkoutSuccessStripe']);
});

Route::prefix('idea-board')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [IdeaBoardController::class, 'index']);
    Route::get('details/{id}', [IdeaBoardController::class, 'details']);
});

Route::prefix('documents')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [DocumentController::class, 'index']);
    Route::post('/', [DocumentController::class, 'store']);
    Route::get('/delete/{id}', [DocumentController::class, 'destroy']);
});


Route::get('/gallery', [GalleryController::class, 'index']);


Route::prefix('profile')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ProfileController::class, 'index']);
    Route::post('/update', [ProfileController::class, 'update']);
    Route::post('/password-change', [ProfileController::class, 'passwordChange']);
});

Route::get('/faq-list', [FaqController::class, 'list']);

Route::post('/subscribe', [SubscriberController::class, 'store']);

Route::get('/promotion', [ShopSettingController::class, 'promotion']);


Route::prefix('shipping-address')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [ShippingAddressController::class, 'list']);
    Route::post('/store', [ShippingAddressController::class, 'store']);
    Route::post('/update', [ShippingAddressController::class, 'update']);
    Route::post('/destroy', [ShippingAddressController::class, 'destroy']);
    Route::get('/details/{id}', [ShippingAddressController::class, 'details']);
    Route::post('/change-status/{id}', [ShippingAddressController::class, 'changeStatus']);
});

Route::prefix('blogs')->group(function () {
    Route::get('/', [BlogPostController::class, 'index'])->name('post');
    Route::get('/details/{slug}', [BlogPostController::class, 'details']);
    Route::get("/categories", [BlogPostController::class, 'categories']);
});

Route::prefix('subscription')->name('subscription.')->group(function () {
    Route::prefix('plan')->name('plan.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index']);
        Route::get("/details/{id}", [SubscriptionController::class, 'details']);
        Route::post("/make-payment", [SubscriptionController::class, 'makePayment'])->middleware('auth:sanctum');
        Route::get('/free-trial', [SubscriptionController::class, 'freeTrail']);
        Route::post('/free-trial-start', [SubscriptionController::class, 'freeTrailStart'])->middleware('auth:sanctum');
    });
});

Route::prefix('free-trail-code-verify')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [FreeTrailController::class, 'store']);
});

Route::get('/sliders/{type}', [SliderController::class, 'sliderList']);

Route::post('designer-contacts', [DesignerContactController::class, 'store']);
Route::get('/color-themes', [ColorThemeController::class, 'colorThemes']);
Route::post('/contact-us', [ContactUsController::class, 'contactUs']);
Route::get('/background-settings/{purpose}', [BackgroundSettingsController::class, 'backgroundSetting']);
Route::get('payment/methods', [SubscriptionController::class, 'paymentMethods']);
Route::get('/countries', [ShippingAddressController::class, 'countries']);


Route::prefix('reviews')->group(function () {
    Route::get('/types', [ReviewController::class, 'getAllTypes']);
    Route::post('/', [ReviewController::class, 'storeReview'])->middleware('auth:sanctum');
    Route::get('/', [ReviewController::class, 'designerReviews']);
    Route::get('/product/{id}', [ReviewController::class, 'productReview']);
});

Route::prefix('proposal')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ProjectProposalController::class, 'index']);
    Route::get('/{id}', [ProjectProposalController::class, 'details']);
    Route::get('/{id}/approveStatus', [ProjectProposalController::class, 'approveStatus']);
    Route::get('/{id}/rejectStatus', [ProjectProposalController::class, 'rejectStatus']);

});
Route::prefix('proposal-invoice')->group(function () {
    Route::get('list', [ProposalInvoiceController::class, 'list']);
    Route::get('details/{id}', [ProposalInvoiceController::class, 'details']);
    Route::get('download-invoice/{invoice_id}', [ProposalInvoiceController::class, 'downloadInvoice']);
    Route::post('make-payment/stripe', [ProposalInvoiceController::class, 'makePaymentStripe']);
    Route::post('success-payment/stripe', [ProposalInvoiceController::class, 'checkoutSuccessStripe']);
});

Route::prefix('chat')->middleware('auth:sanctum')->group(function () {
    Route::get('/recent-users', [ChatMessageController::class, 'recentUsers'])->name('recent.users');
    Route::get('/all-users', [ChatMessageController::class, 'allUsers'])->name('all.users');
    Route::get('/messages', [ChatMessageController::class, 'messages']);
    Route::post('/send-message', [ChatMessageController::class, 'sendMessage']);
});


Route::prefix('project')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ProjectController::class, 'index']);
    Route::get('/{id}', [ProjectController::class, 'details']);
});

Route::prefix('project-clipper')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ProductClipperController::class, 'index']);
    Route::post('/', [ProductClipperController::class, 'store']);
    Route::get('/{id}', [ProductClipperController::class, 'show']);
    Route::put('/{id}', [ProductClipperController::class, 'update']);
    Route::delete('/{id}', [ProductClipperController::class, 'destroy']);
});


Route::prefix('assigned-designer')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CustomerAssignedDesignerController::class, 'list']);
    Route::get('/get-available-designer-list', [CustomerAssignedDesignerController::class, 'getAvailableDesignerList']);
    Route::post('/send-join-request', [CustomerAssignedDesignerController::class, 'sendJoinRequest']);
    Route::post('/switch-designer', [CustomerAssignedDesignerController::class, 'setDefaultDesigner']);
    Route::post('/leave-designer', [CustomerAssignedDesignerController::class, 'leaveDesigner']);
    Route::post('/delete-request', [CustomerAssignedDesignerController::class, 'deleteRequest']);
});


Route::post('/verify-access-token', [ExternalAuthenticationController::class, 'verifyAccessToken']);
Route::post('/get-hb-access-token', [ExternalAuthenticationController::class, 'getHBAccessTokens']);
