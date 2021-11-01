<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () { return redirect("auth"); });

Route::get("tes", function () {
	if(Helper::validateDate2("2021-12")){
		return "Yes";
	}else{
		return "No";
	}
});

Route::group([ "prefix" => "auth" ], function(){
	Route::get("/", function () { return redirect("auth/login"); });
	Route::get("login",		"AuthController@login");
	Route::post("login",	"AuthController@doLogin");
	Route::get("logout",	"AuthController@doLogout");
});

Route::group([ "prefix" => "main" , "middleware" => "authlogin" ], function(){

	//
	Route::get("/", function () { return redirect("main/home?periode=".date("Y-m") ); });
	Route::get("home", 		"MainController@home")->middleware('validatePeriode');

	//
	Route::group([ "prefix" => "deteni" ], function(){
		Route::get("/", 		"MainController@deteni")->middleware('validatePeriode');
		Route::post("save", 	"MainController@deteniSave")->middleware('verifyAdministrator');
		Route::post("update", 	"MainController@deteniUpdate")->middleware('verifyAdministrator');
		Route::post("delete", 	"MainController@deteniDelete")->middleware('verifyAdministrator');
		Route::group([ "prefix" => "{id}" ], function(){
			Route::get("/", 		"MainController@log");
			Route::post("save",		"MainController@logSave")->middleware('verifyAdministrator');
			Route::get("detail", 	"MainController@logDetail");
			Route::post("update",	"MainController@logUpdate")->middleware('verifyAdministrator');
			Route::post("delete",	"MainController@logDelete")->middleware('verifyAdministrator');
		});
	});

	// 
	Route::group(["prefix" => "user" , "middleware" => "verifyAdministrator" ], function(){
		Route::get("/", 		"AdministratorController@user");
		Route::post("save", 	"AdministratorController@userSave");
		Route::get("detail", 	"AdministratorController@userDetail");
		Route::post("update", 	"AdministratorController@userUpdate");
		Route::post("delete", 	"AdministratorController@userDelete");
	});

	//
	Route::get('strg/{filename}', function ($filename){
	    $path = public_path('storage/' . $filename);

	    if (!File::exists($path)) {
	        abort(404);
	    }

	    $file = File::get($path);
	    $type = File::mimeType($path);

	    $response = Response::make($file, 200);
	    $response->header("Content-Type", $type);

	    return $response;
	});

	//
	Route::get("download/pdf/{filename}", function ($filename){
		$path = public_path('storage/' . $filename);
        $headers = array(
            "Content-disposition: attachment; filename=" . $filename,
            "Content-type: application/pdf"
            );
        return Response::download($path, $filename, $headers);
	});

});

