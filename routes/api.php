<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'ads','namespace' => 'Api','middleware'=> ['jwt.verify']], function() {
    Route::get('/','AdController@index');
    Route::get('/{ad}','AdController@show');
    Route::post('/{company}','AdController@store');
    Route::put('/{ad}','AdController@update');
    Route::post('/photo/{ad}','AdController@uploadAdPhoto');
});

Route::group(['prefix' => 'static', 'namespace' => 'Api'], function () {
    Route::get('countries/ou', 'BaseDataController@getCountriesWithOUCollege');
    Route::get('countries', 'BaseDataController@getCountries');
    Route::get('states', 'BaseDataController@getStates');
    Route::get('states/filter', 'BaseDataController@filterState');
    Route::get('colleges', 'BaseDataController@getColleges');
    Route::get('college/{cid}', 'BaseDataController@getCollegeInfo');
    Route::get('colleges/filter', 'BaseDataController@filterColleges');
    Route::get('degrees', 'BaseDataController@getDegrees');
    Route::get('athletes', 'BaseDataController@getAthletes');
    Route::get('organizations', 'BaseDataController@getOrganizations');
    Route::get('ibcs', 'BaseDataController@getIBCs');
    Route::get('industries', 'BaseDataController@getIndustries');
    Route::get('ps', 'BaseDataController@getPSData');
    Route::get('hobbies','BaseDataController@getHobbiesData');
});

Route::group(['prefix' => 'auth', 'namespace' => 'Api'], function () {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::get('exist/{social}/{sid}', 'AuthController@isRegisteredUser');
    Route::get('invite/verify/{code}', 'AuthController@verifyInviteCode');
    Route::post('/import','MosController@import');
    Route::get('/get-mos','MosController@getMos');
    Route::get('/get-mos-by-code/{code}/{branch}','MosController@singleMosByCode');
    Route::get('/single-mos','MosController@singleMos');
    Route::group(['middleware' => ['jwt.verify']], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('invite/code', 'AuthController@generateInviteCode');
        Route::post('ticket/account', 'AuthController@createAccountTicket');
    });

});

Route::group(['prefix' => 'company', 'namespace' => 'Api', 'middleware' => ['jwt.verify']], function(){
    Route::get('/','CompanyController@index');
    Route::get('/{company}','CompanyController@show');
    Route::delete('deletecompany','CompanyController@delete');
});

Route::group(['prefix' => 'user', 'namespace' => 'Api', 'middleware' => ['jwt.verify']], function () {
    Route::get('/', 'UserController@index');
    Route::get('college','UserController@getCollege');
    Route::post('college','UserController@saveCollege');
    Route::get('weights', 'UserController@getMatchWeights');
    Route::post('weights', 'UserController@saveMatchWeights');
    Route::get('degrees', 'UserController@getDegrees');
    Route::post('degrees', 'UserController@saveDegrees');
    Route::get('orgs', 'UserController@getOrgs');
    Route::post('orgs', 'UserController@saveOrgs');
    Route::get('athlete', 'UserController@getAthlete');
    Route::post('athlete', 'UserController@saveAthlete');
    Route::post('access-request', 'UserController@accessRequest');
    Route::get('get-access-requests', 'UserController@allAccessRequests');

    Route::get('gae', 'UserController@getGenderAgeEthnicity');
    Route::post('gae', 'UserController@saveGenderAgeEthnicity');
    Route::post('updateUserActivity', 'UserController@updateUserActivity');
    Route::get('getUserActivity', 'UserController@getUserActivity');
    Route::get('languages/speak', 'UserController@getSpeakLanguages');
    Route::post('languages/speak', 'UserController@saveSpeakLanguages');
    Route::get('languages/learn', 'UserController@getLearnLanguages');
    Route::post('languages/learn', 'UserController@saveLearnLanguages');
    Route::get('religion', 'UserController@getReligion');
    Route::post('religion', 'UserController@saveReligion');

    Route::group(['prefix' => 'relationship'], function() {
        Route::get('/', 'UserController@getRelationship');
        Route::post('married', 'UserController@saveRelationshipMarried');
        Route::post('divorced', 'UserController@saveRelationshipDivorced');
        Route::post('widowed', 'UserController@saveRelationshipWidowed');
        Route::post('engaged', 'UserController@saveRelationshipEngaged');
        Route::post('single', 'UserController@saveRelationshipSingle');
        Route::post('other', 'UserController@saveRelationshipOther');
        Route::post('invite-partner', 'UserController@invitePartner');
    });

    Route::group(['prefix' => 'company'], function() {
        Route::get('/','UserController@getCompanies');
        Route::post('/','CompanyController@store');
        Route::put('/{company}','CompanyController@update');
        Route::post('/photo/{company}','CompanyController@uploadCompanyPhoto');
    });

    Route::get('work-career', 'UserController@getWorkCareer');
    Route::post('work-career', 'UserController@saveWorkCareer');
    Route::get('home', 'UserController@getHome');
    Route::post('home', 'UserController@saveHome');
    Route::get('hometown', 'UserController@getHometown');
    Route::post('hometown', 'UserController@saveHometown');
    Route::get('health', 'UserController@getHealth');
    Route::post('health', 'UserController@saveHealth');
    Route::get('hobbies', 'UserController@getHobbies');
    Route::post('hobbies', 'UserController@saveHobbies');
    Route::get('causes', 'UserController@getCauses');
    Route::post('causes', 'UserController@saveCauses');
    Route::get('school', 'UserController@getSchool');
    Route::post('school', 'UserController@saveSchool');

    Route::get('completed/ps', 'UserController@getPSProfileCompleted');
    Route::get('completed/cl', 'UserController@getCLProfileCompleted');
    Route::get('completed', 'UserController@getProfileCompleted');
    Route::get('activate', 'UserController@activateUser');

    Route::get('location/show/{show}', 'UserController@changeLocationShow');
    Route::post('location', 'UserController@updateLocation');
    Route::get('location', 'UserController@getLocation');
    Route::get('push/token/{deviceToken}', 'UserController@saveDeviceToken');
    Route::post('avatar', 'UserController@uploadAvatar');
    Route::get('invite-code', 'UserController@getInviteCode');
    Route::get('generate-code', 'UserController@generateInviteCode');
    Route::delete('delete', 'UserController@deleteUser');
});

Route::group(['prefix' => 'alumni', 'namespace' => 'Api', 'middleware' => ['jwt.verify']], function () {
    Route::get('all', 'AlumniController@index');
    Route::get('match/{uid}', 'AlumniController@getUserDetail');
    Route::get('dashboard', 'AlumniController@getDashboardData');
    Route::get('nears', 'AlumniController@getNears');
    Route::get('leaderboard', 'AlumniController@getLeaderboardData');
    Route::get('users', 'AlumniController@getUsers');
    Route::get('requests', 'AlumniController@getFriendRequests');
    Route::get('suggests', 'AlumniController@getSuggests');
    Route::get('visits', 'AlumniController@getVisits');
    Route::get('friends', 'AlumniController@getFriends');
    Route::get('pendings', 'AlumniController@getPendings');
    Route::get('similar/{category}/{cid}', 'AlumniController@getSimilarUsers');
    Route::post('search', 'AlumniController@searchUsers');
    Route::get('extra/{uid}', 'AlumniController@getMiniAlumniData');
    Route::get('detail/{uid}', 'AlumniController@getFullAlumniData');
    Route::get('blocks','AlumniController@getBlockedUsers');
    Route::post('block/{uid}','AlumniController@blockUser');
});

Route::group(['prefix' => 'friend', 'namespace' => 'Api', 'middleware' => ['jwt.verify']], function () {
    Route::post('approve', 'FriendController@approveFriendRequest');
    Route::post('ignore', 'FriendController@ignoreFriendRequest');
    Route::post('invite', 'FriendController@inviteAsFriend');
    Route::get('all', 'FriendController@getAllFriends');
});

Route::group(['prefix' => 'message', 'namespace' => 'Api', 'middleware' => ['jwt.verify']], function() {
    Route::get('users', 'MessageController@index');
    Route::get('user/{uid}', 'MessageController@getUserMessages');
    Route::post('send', 'MessageController@sendMessage');
    Route::get('read/{mid}', 'MessageController@markAsRead');
    Route::delete('{mid}', 'MessageController@deleteMessage');
    Route::delete('removeall', 'MessageController@deleteAllMessagesByUID')->name('deleteMessages');
    Route::post('send/all', 'MessageController@sendMessageToAll');
    Route::post('send/radius', 'MessageController@sendMessageInRadius');
    Route::post('send/users', 'MessageController@sendMessageToUsers');
    Route::post('sendPush', 'MessageController@sendPush');
});

Route::group(['prefix' => 'event', 'namespace' => 'Api', 'middleware' => ['jwt.verify']], function() {
    Route::post('/','EventController@store');
    Route::put('/{event}','EventController@update');
    Route::get('/','EventController@index');
    Route::get('/{event}','EventController@show');
    Route::get('/category','EventCategoryController@index');
    Route::get('/category/{category}','EventCategoryController@show');
    Route::post('/category','EventCategoryController@store');
    Route::put('/category/{category}','EventCategoryController@update');
});

Route::group(['prefix' => 'checkout','namespace' => 'Api'], function() {
    Route::get('/success','StripeController@success');
    Route::get('/failed','StripeController@failed');
    Route::group(['middleware' => ['jwt.verify']], function() {
        Route::post('/company','StripeController@createCompanyCheckoutSession');
        Route::post('/leads','StripeController@createLeadCheckoutSession');
    });
});

Route::group(['prefix' => 'post', 'namespace' => 'Api', 'middleware' => ['jwt.verify']], function() {

    Route::get('types/{typeId?}', 'PostController@getPostType');
    Route::post('type-create-or-update/{typeId?}', 'PostController@createOrUpdatePostType');
    Route::get('type-categories/{typeId?}', 'PostController@getPostTypeCategories');
    Route::post('type-category-create-or-update/{categoryId?}', 'PostController@createOrUpdatePostCategory');
    Route::post('getPostsByCollege/{collegeID}','PostController@getPostsByCollege');
    Route::post('create-or-update/{postId?}', 'PostController@createOrUpdatePost');
    Route::delete('remove/{postId}', 'PostController@deletePost');
    Route::delete('removeall', 'PostController@deleteAllPosts')->name('deletePosts');
    Route::post('reaction', 'PostController@postReaction');
    Route::get('likes/{postId?}', 'PostController@getPostLike');
    Route::get('comments/{postId}', 'PostController@getPostComments');
    Route::post('categories/', 'PostController@getPostsByCategory');
    Route::post('/{postId?}', 'PostController@getAllPosts');
    Route::get('/reported','PostController@getAllReportedPosts');
    Route::get('getRelevantPosts','PostController@getRelevantPosts');
    Route::post('/getNewJoin/{postId}','PostController@getNewUserPost');
});

Route::group(['prefix' => 'test', 'namespace' => 'Api', 'middleware' => ['jwt.verify']], function () {
    Route::get('send', 'UserController@sendTestPush');
});

Route::group(['prefix' => 'latestVersion', 'namespace' => 'Api', 'middleware' => ['jwt.verify']], function () {
    Route::post('/', 'VersionController@getLatestVersion');
});
Route::group(['prefix' => 'email', 'namespace' => 'Api', 'middleware' => ['jwt.verify']], function () {
    Route::post('send', 'EmailController@sendEmail');
});

/**
 * Admin Routes
 */
Route::group(['prefix' => 'user', 'namespace' => 'Api'], function () {
    Route::post('login', 'AdminController@login');
    Route::middleware(['jwt.verify','cors'])->group(function () {
        Route::post('{user_id?}/{tab?}', 'AdminController@get_users');
    });
});
