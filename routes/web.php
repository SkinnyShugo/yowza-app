<?php

use App\Http\Controllers\Admin\ProgressController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\SMME\SMMEWorkController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DocumentLibraryController;
use App\Http\Controllers\Corporate\CorporateDashboardController;
use App\Http\Controllers\Development\DevelopmentDashboardController;
use App\Http\Controllers\Individual\IndividualDashboardController;
use App\Http\Controllers\SMME\SmmeDashboardController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\Admin\ApplicationsController;
use App\Http\Controllers\Admin\AlternativeContactPersonController;
use \App\Http\Controllers\Admin\OrganisationController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\CTAApplicationController;
//use App\Http\Controllers\Admin\LibraryController;
use App\Http\Controllers\Admin\ProfileController;
//use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\OrganisationWorkspaceController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RSVPController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\MarketplaceCategoryController;
use App\Http\Controllers\Contact\ContactFormController;
use App\Http\Controllers\UserProfileImageController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('download-lib/{id}', 'App\Http\Controllers\Admin\DocumentLibraryController@downloadFile')->name('download-lib');
    // Routes for Administrator
    Route::get('yowza-campus', [\App\Http\Controllers\Yowza\HomeController::class, 'index']);
    Route::get('yowza-campus-courses', [\App\Http\Controllers\Yowza\CourseController::class, 'index']);
    Route::get('yowza-campus-courses/{slug}', [App\Http\Controllers\Yowza\CourseController::class, 'show'])->name('course.show');
    Route::post('yowza-campus-courses/{course_id}/rating', [App\Http\Controllers\Yowza\CourseController::class, 'rating'])->name('course.rating');
    Route::post('courses/payment', [App\Http\Controllers\Yowza\CourseController::class, 'payment'])->name('courses.payment');
    Route::post('course/{course_id}/rating', [App\Http\Controllers\Yowza\CourseController::class, 'rating'])->name('courses.rating');
    Route::get('lessons/{course_id}/{slug}', [App\Http\Controllers\Yowza\LessonController::class, 'show'])->name('lessons.show');
    Route::post('lesson/{slug}/test', [App\Http\Controllers\Yowza\LessonController::class, 'test'])->name('lessons.test');
    Route::post('/courses/{course}/lessons/{lesson}/complete', [ProgressController::class, 'markComplete'])->name('lessons.complete');


    //Users profile
    Route::get('profile', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::post('profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::resource('profile-image', UserProfileImageController::class)->only(['store']);

    //Contact Form for user loggedIn
    Route::get('/contact-form', [ContactFormController::class, 'showContactForm'])->name('contact-form');
    Route::post('/contact-form', [ContactFormController::class, 'submit'])->name('contact-form.submit');

    Route::resource('scorm', \App\Http\Controllers\Admin\ScormController::class);

    Route::group(['middleware' => ['isAdmin'], 'prefix' => 'admin/{prefix}', 'as' => 'admin.'], function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/post/create', [PostController::class, 'create'])->name('post.create');
        Route::get('/post/all', [PostController::class, 'allBlogPost'])->name('post.all_blog_post');
        Route::post('/post', [PostController::class, 'store'])->name('post.store');
        Route::get('/post/{id}', [PostController::class, 'show'])->name('admin.post.show');
        Route::get('/post/{id}/edit', [PostController::class, 'edit'])->name('post.edit');
        Route::put('/post/update/{id}', [PostController::class, 'update'])->name('post.update');
        Route::delete('/post/{id}/delete', [PostController::class, 'destroy'])->name('post.destroy');
        Route::get('admin/post/draft', [PostController::class, 'draftPost'])->name('post.draft');
        Route::get('admin/post/pending_review', [PostController::class, 'pending_review_posts'])->name('post.pending_review_posts');
        //Category Routes
        Route::controller(CategoryController::class)->group(function () {
            Route::get('/category/all', 'AllCategory')->name('all.category');
            Route::get('/category/create', 'CreateCategory')->name('category.create');
            Route::post('/category', 'store')->name('category.store');
            Route::get('/category/{id}/edit', 'edit')->name('category.edit');
            Route::put('/category/update/{id}', 'update')->name('category.update');
            Route::delete('/category/{id}/delete', 'destroy')->name('category.destroy');
            Route::get('/events/all', [EventController::class, 'all_events']); //
        });



        // Events Routes
        Route::controller(EventController::class)->group(function () {
            // Only create, edit, update, and delete routes
            Route::get('/events/create', 'create')->name('events.create'); // Show form to create event
            Route::post('/events', 'store')->name('events.store'); // Store a new event
            Route::get('/events/{event}/edit', 'edit')->name('events.edit'); // Show form to edit event
            Route::patch('/events/{event}', 'update')->name('events.update'); // Update an existing event
            Route::delete('/events/{event}', 'destroy')->name('events.destroy'); // Delete an event
        });

        Route::resource('/organization-workspace', OrganisationWorkspaceController::class);
        Route::post('/organization-workspace/{workspace}/join', [OrganisationWorkspaceController::class, 'join'])->name('workspaces.join');
        Route::resource('/document-library', DocumentLibraryController::class);
        Route::post('/document-library/post', [DocumentLibraryController::class, 'store'])->name('library.store');
        Route::get('library/{document}', [DocumentLibraryController::class, 'view'])->name('documents.view');
        //        Route::get('library/{document}/download', [DocumentLibraryController::class, 'download'])
//            ->name('documents.download');
//      Route::get('document-library/download/{id}', 'DocumentLibraryController@download');
        Route::get('download/{id}', 'App\Http\Controllers\Admin\DocumentLibraryController@downloadFile')->name('download');

        Route::resource('users', UserController::class);
        // Route::get('/user/{$id}/edit', 'App\Http\ControllersAdmin\UserController@edit')->name('user.edit');

        

        // Route::get('user/{user}/test', [UserController::class, 'edit'])->name('admin.user2.edit');

        //        Route::get('/admin/{prefix}/users/{user}/edit', 'UserController@edit')->name('admin.users.edit');



        Route::resource('discover', \App\Http\Controllers\DiscoverController::class);

        Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);
        Route::delete('courses_perma_del/{id}', [\App\Http\Controllers\Admin\CourseController::class, 'perma_del'])->name('courses.perma_del');
        Route::post('courses_restore/{id}', [\App\Http\Controllers\Admin\CourseController::class, 'restore'])->name('courses.restore');

        Route::resource('lessons', \App\Http\Controllers\Admin\LessonController::class);
        Route::post('lessons_restore/{id}', [\App\Http\Controllers\Admin\LessonController::class, 'restore'])->name('lessons.restore');
        Route::delete('lessons_perma_del/{id}', [\App\Http\Controllers\Admin\LessonController::class, 'perma_del'])->name('lessons.perma_del');

        Route::resource('tests', \App\Http\Controllers\Admin\TestController::class);
        Route::post('tests_restore/{id}', [\App\Http\Controllers\Admin\TestController::class, 'restore'])->name('tests.restore');
        Route::delete('tests_perma_del/{id}', [\App\Http\Controllers\Admin\TestController::class, 'perma_del'])->name('tests.perma_del');

        Route::resource('questions', \App\Http\Controllers\Admin\QuestionController::class);
        Route::post('questions_restore/{id}', [\App\Http\Controllers\Admin\QuestionController::class, 'restore'])->name('questions.restore');
        Route::delete('questions_perma_del/{id}', [\App\Http\Controllers\Admin\QuestionController::class, 'perma_del'])->name('questions.perma_del');

        Route::resource('question_options', \App\Http\Controllers\Admin\QuestionOptionController::class);
        Route::post('question_options_restore/{id}', [\App\Http\Controllers\Admin\QuestionOptionController::class, 'restore'])->name('question_options.restore');
        Route::delete('question_options_perma_del/{id}', [\App\Http\Controllers\Admin\QuestionOptionController::class, 'perma_del'])->name('question_options.perma_del');

        Route::resource('yowza-campus', \App\Http\Controllers\Admin\YowzaCampusController::class);

        Route::resource('scorm', \App\Http\Controllers\Admin\ScormController::class);
    });

    // Routes for Individual
    Route::group(['middleware' => ['isIndividual'], 'prefix' => 'individual/{prefix}', 'as' => 'individual.'], function () {
        Route::get('/dashboard', [IndividualDashboardController::class, 'index'])->name('dashboard');
        // Add more routes specific to Individual users as needed
    });

    // Routes for Corporate Sponsors
    Route::group(['middleware' => ['isCorporate'], 'prefix' => 'corporate/{prefix}', 'as' => 'corporate.'], function () {
        Route::get('/dashboard', [CorporateDashboardController::class, 'index'])->name('dashboard');
        // Add more routes specific to Corporate users as needed
    });

    // Routes for Development Partners
    Route::group(['middleware' => ['isDevelopment'], 'prefix' => 'development/{prefix}', 'as' => 'development.'], function () {
        Route::get('/dashboard', [DevelopmentDashboardController::class, 'index'])->name('dashboard');
        // Add more routes specific to Development users as needed
    });

    // Routes for SMME
    Route::group(['middleware' => ['isSmme'], 'prefix' => 'smme/{prefix}', 'as' => 'smme.'], function () {
        Route::get('/dashboard', [SmmeDashboardController::class, 'index'])->name('dashboard');
        Route::resource('/smmeworkspace', \App\Http\Controllers\SMME\SMMEWorkController::class);
        Route::post('/smmeworkspace/{workspace}/join', [SMMEWorkController::class, 'join'])->name('workspaces.join');
        Route::resource('/organization-workspace', OrganisationWorkspaceController::class);
        Route::post('/organization-workspace/{workspace}/join', [OrganisationWorkspaceController::class, 'join'])->name('workspace.join');
        Route::resource('/document-library', DocumentLibraryController::class);
        Route::resource('yowza-campus', \App\Http\Controllers\Admin\YowzaCampusController::class);
        Route::resource('discover', \App\Http\Controllers\DiscoverController::class);

    });
});

//Contact Form url:
Route::get('/contact-form', [ContactFormController::class, 'showContactForm'])->name('contact-form');
Route::post('/contact-form', [ContactFormController::class, 'submit'])->name('contact-form.submit');


//Blog post Layouts
Route::prefix('blog')->group(function () {
    Route::get('/post', [PostController::class, 'published_posts'])->name('blog.post.all');
})->middleware('auth');

// Additional custom route for all events
Route::get('/events/', [EventController::class, 'index'])->name('events.index')->middleware('auth');
Route::get('/events/show/{event}', [EventController::class, 'show'])->name('events.show')->middleware('auth');


//Route for RSVP
Route::post('/rsvp', [RSVPController::class, 'store'])->middleware('auth');
Route::post('/rsvp/cancel', [RSVPController::class, 'cancel'])->name('rsvp.cancel');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Route for Marketplace
Route::middleware(['auth'])->prefix('marketplace')->group(function () {
    Route::controller(StoreController::class)->group(function () {
        Route::get('/', 'index')->name('marketplace.store.index');
    });
});

Route::get('marketplace_categories', [MarketplaceCategoryController::class, 'index'])->name('marketplace.index');
//Route::get('faq', function () {
//    return view('helpdesk.faq');
//})->name('faq');
// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
// });

Illuminate\Support\Facades\Auth::routes(['verify' => true]);
// Route::get('/create-workspace', 'OrganisationWorkspaceController@create')->name('createWorkspace');
// Route::post('/create-workspace', 'OrganisationWorkspaceController@store')->name('storeWorkspace');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/workspace-test', function () {
    return view('workspace.register_workspace');
});

Route::get('lesson-course', function () {
    return view('yowzacampus.lessons.home');
});

Route::get('/test-sentry', function () {
    throw new Exception('Testing Sentry...');
});