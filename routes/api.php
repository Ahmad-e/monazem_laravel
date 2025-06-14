<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\PowerController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ExpencesController;
use App\Http\Controllers\RevenuesController;
use App\Http\Controllers\PartnersController;
use App\Http\Controllers\ProductController;


use App\Http\Middleware\Users;
use App\Http\Middleware\Admins;


use App\Http\Middleware\Employees;

use App\Http\Middleware\accounts_power;
use App\Http\Middleware\employee_power;
use App\Http\Middleware\employee_data_power;
use App\Http\Middleware\access_power;
use App\Http\Middleware\clients_power;
use App\Http\Middleware\partners_power;
use App\Http\Middleware\expencess_power;
use App\Http\Middleware\revenues_power;


use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
//Route::get('/test', [AuthController::class, 'test']);


//Route::post('/addBusiness', [BusinessController::class, 'addBusiness']);
//Route::post('/addBranch', [BranchController::class, 'addBranch']);


Route::get('/showCurrencies', [CurrencyController::class, 'showCurrencies']);


Route::middleware('api')->group(function () {

    Route::get('/profile', [AuthController::class, 'getUser'])->middleware(Users::class);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware(Users::class);
    Route::post('/updateUser', [AuthController::class, 'updateUser'])->middleware(Users::class);

    Route::post('/create_account', [AuthController::class, 'create_account'])->middleware(Users::class)->middleware(accounts_power::class);
    Route::get('/showAccounts', [AuthController::class, 'showAccounts'])->middleware(Users::class)->middleware(accounts_power::class);
    Route::post('/updateAccount/{id}', [AuthController::class, 'updateAccount'])->middleware(Users::class)->middleware(accounts_power::class);
    Route::get('/toggleOverPowerUser/{id}', [AuthController::class, 'toggleOverPowerUser'])->middleware(Users::class)->middleware(accounts_power::class);
    Route::get('/toggleBlockUser/{id}', [AuthController::class, 'toggleBlockUser'])->middleware(Users::class)->middleware(accounts_power::class);
    Route::get('/showBusiness', [BusinessController::class, 'showBusiness'])->middleware(Users::class);
    Route::post('/updateBusiness', [BusinessController::class, 'updateBusiness'])->middleware(Users::class);

//    employee api
    Route::post('/addEmployee', [EmployeeController::class, 'addEmployee'])->middleware(Users::class)->middleware(employee_power::class) ;
    Route::get('/getEmployeesByBusiness', [EmployeeController::class, 'getEmployeesByBusinessId'])->middleware(Users::class)->middleware(employee_power::class);
    Route::get('/getEmployeesByBranch/{branchId}', [EmployeeController::class, 'getEmployeesByBranchId'])->middleware(Users::class)->middleware(employee_power::class);
    Route::post('/changeEmployees/{id}', [EmployeeController::class, 'updateEmployee'])->middleware(Users::class)->middleware(employee_power::class);
    Route::get('/toggleBlockedEmployees/{id}', [EmployeeController::class, 'toggleBlockedEmployee'])->middleware(Users::class)->middleware(employee_power::class);

    Route::post('/addEmployeeSalary', [EmployeeController::class, 'addEmployeeSalary'])->middleware(Users::class)->middleware(employee_data_power::class);
    Route::get('/getAllEmployeeSalaries/{id}', [EmployeeController::class, 'getAllEmployeeSalaries'])->middleware(Users::class)->middleware(employee_data_power::class);
    Route::get('/getActiveEmployeeSalaries/{id}', [EmployeeController::class, 'getActiveEmployeeSalaries'])->middleware(Users::class)->middleware(employee_data_power::class);
    Route::get('/selectActiveEmployeeSalaries/{id}', [EmployeeController::class, 'selectActiveEmployeeSalaries'])->middleware(Users::class)->middleware(employee_data_power::class);

    Route::post('/addEmployeesSalariesPayment', [EmployeeController::class, 'addEmployeesSalariesPayment'])->middleware(Users::class)->middleware(employee_data_power::class);
    Route::get('/showEmployeesSalariesPayment/{id}', [EmployeeController::class, 'showEmployeesSalariesPayment'])->middleware(Users::class)->middleware(employee_data_power::class);
    Route::get('/showEmployeesSalary_SalaryPayment/{id}', [EmployeeController::class, 'showEmployeesSalary_SalaryPayment'])->middleware(Users::class)->middleware(employee_data_power::class);
    Route::get('/deleteEmployeesSalariesPayment/{id}', [EmployeeController::class, 'deleteEmployeesSalariesPayment'])->middleware(Users::class)->middleware(employee_data_power::class);

    Route::post('/addEmployeesSalariesAdvance', [EmployeeController::class, 'addEmployeesSalariesAdvance'])->middleware(Users::class)->middleware(employee_data_power::class);
    Route::get('/getAllEmployeesSalariesAdvance/{id}', [EmployeeController::class, 'getAllEmployeesSalariesAdvance'])->middleware(Users::class)->middleware(employee_data_power::class);
    Route::post('/updateEmployeesSalariesAdvance/{id}', [EmployeeController::class, 'updateEmployeesSalariesAdvance'])->middleware(Users::class)->middleware(employee_data_power::class);
    Route::get('/deleteEmployeesSalariesAdvance/{id}', [EmployeeController::class, 'deleteEmployeesSalariesAdvance'])->middleware(Users::class)->middleware(employee_data_power::class);

    Route::get('/getAllEmployeesSalariesAdvanceWithPayments/{id}', [EmployeeController::class, 'getAllEmployeesSalariesAdvanceWithPayments'])->middleware(Users::class)->middleware(employee_data_power::class);
    Route::post('/addEmployeesSalariesAdvancePayments', [EmployeeController::class, 'addEmployeesSalariesAdvancePayments'])->middleware(Users::class)->middleware(employee_data_power::class);
    Route::get('/deleteEmployeesSalariesAdvancePayments/{id}', [EmployeeController::class, 'deleteEmployeesSalariesAdvancePayments'])->middleware(Users::class)->middleware(employee_data_power::class);

    Route::post('/addEmployeeCompensation', [EmployeeController::class, 'addEmployeeCompensation'])->middleware(Users::class)->middleware(employee_data_power::class);
    Route::get('/showEmployeeCompensation/{id}', [EmployeeController::class, 'showEmployeeCompensation'])->middleware(Users::class)->middleware(employee_data_power::class);
    Route::post('/changeEmployeeCompensation/{id}', [EmployeeController::class, 'changeEmployeeCompensation'])->middleware(Users::class)->middleware(employee_data_power::class);
    Route::get('/deleteEmployeeCompensation/{id}', [EmployeeController::class, 'deleteEmployeeCompensation'])->middleware(Users::class)->middleware(employee_data_power::class);

    Route::post('/addEmployeeHolidays', [EmployeeController::class, 'addEmployeeHolidays'])->middleware(Users::class)->middleware(employee_data_power::class);
    Route::get('/showEmployeeHolidays/{id}', [EmployeeController::class, 'showEmployeeHolidays'])->middleware(Users::class)->middleware(employee_data_power::class);
    Route::post('/changeEmployeeHolidays/{id}', [EmployeeController::class, 'changeEmployeeHolidays'])->middleware(Users::class)->middleware(employee_data_power::class);
    Route::get('/deleteEmployeeHolidays/{id}', [EmployeeController::class, 'deleteEmployeeHolidays'])->middleware(Users::class)->middleware(employee_data_power::class);

//   power api
    Route::post('/addUserPower', [PowerController::class, 'addUserPower'])->middleware(Users::class)->middleware(access_power::class);
    Route::get('/showUser_Power/{id}', [PowerController::class, 'showUser_Power'])->middleware(Users::class)->middleware(access_power::class);
    Route::post('/getPowers', [PowerController::class, 'getPowers']);
    Route::post('/deleteUser_Power', [PowerController::class, 'deleteUserPower'])->middleware(Users::class)->middleware(access_power::class);

    Route::post('/addCurrencies', [CurrencyController::class, 'addCurrencies'])->middleware(Admins::class);
    Route::post('/updateCurrency/{id}', [CurrencyController::class, 'updateCurrency'])->middleware(Admins::class);
    Route::get('/toggleBlockedCurrencies/{id}', [CurrencyController::class, 'toggleBlockedCurrency'])->middleware(Admins::class);
    Route::post('/currencies/migrate', [CurrencyController::class, 'addMultipleCurrencies'])->middleware(Admins::class);
    Route::get('/showAllBranches', [BranchController::class, 'showAllBranches'])->middleware(Admins::class);

    Route::post('/addPowers', [PowerController::class, 'addPower'])->middleware(Admins::class);
    Route::get('/getPowers', [PowerController::class, 'getPowers']);
    Route::post('/updatePower/{id}', [PowerController::class, 'updatePower'])->middleware(Admins::class);
    Route::get('/toggleBlockedPower/{id}', [PowerController::class, 'toggleBlocked'])->middleware(Admins::class);

//    clients api
    Route::post('/addClient', [ClientController::class, 'addClient'])->middleware(Users::class)->middleware(clients_power::class);
    Route::get('/showClientsByBusiness', [ClientController::class, 'showClientsByBusiness'])->middleware(Users::class)->middleware(clients_power::class);
    Route::get('/showClientsByBranches', [ClientController::class, 'showClientsByBranches'])->middleware(Users::class)->middleware(clients_power::class);
    Route::post('/updateClient/{id}', [ClientController::class, 'updateClient'])->middleware(Users::class)->middleware(clients_power::class);
    Route::get('/toggleBlockedClient/{id}', [ClientController::class, 'toggleBlockedClient'])->middleware(Users::class)->middleware(clients_power::class);

//    partners api
    Route::post('/addPartner', [PartnersController::class, 'addPartner'])->middleware(Users::class)->middleware(partners_power::class);
    Route::post('/updatePartner/{id}', [PartnersController::class, 'updatePartner'])->middleware(Users::class)->middleware(partners_power::class);
    Route::get('/showPartners', [PartnersController::class, 'showPartners'])->middleware(Users::class)->middleware(partners_power::class);
    Route::get('/toggleBlockPartner/{id}', [PartnersController::class, 'toggleBlockPartner'])->middleware(Users::class)->middleware(partners_power::class);

    Route::post('/addPartnerPayment', [PartnersController::class, 'addPartnerPayment'])->middleware(Users::class)->middleware(partners_power::class);
    Route::get('/deletePartnerPayment/{id}', [PartnersController::class, 'deletePartnerPayment'])->middleware(Users::class)->middleware(partners_power::class);
    Route::get('/showPartnerPayment/{id}', [PartnersController::class, 'showPartnerPayment'])->middleware(Users::class)->middleware(partners_power::class);

    Route::post('/addWithdrawalsPayment', [PartnersController::class, 'addWithdrawalsPayment'])->middleware(Users::class)->middleware(partners_power::class);
    Route::get('/showWithdrawalsPayment/{id}', [PartnersController::class, 'showWithdrawalsPayment'])->middleware(Users::class)->middleware(partners_power::class);
    Route::get('/deleteWithdrawalsPayment/{id}', [PartnersController::class, 'deleteWithdrawalsPayment'])->middleware(Users::class)->middleware(partners_power::class);

//    Expenses api
    Route::post('/addExpense', [ExpencesController::class, 'addExpense'])->middleware(Users::class)->middleware(expencess_power::class);
    Route::get('/deleteExpense/{id}', [ExpencesController::class, 'deleteExpense'])->middleware(Users::class)->middleware(expencess_power::class);
    Route::get('/showExpensesByBusiness', [ExpencesController::class, 'showExpensesByBusiness'])->middleware(Users::class)->middleware(expencess_power::class);
    Route::get('/showExpensesByBranches/{id}', [ExpencesController::class, 'showExpensesByBranches'])->middleware(Users::class)->middleware(expencess_power::class);

    Route::get('/showExpensePayment/{id}', [ExpencesController::class, 'showExpensePayment'])->middleware(Users::class)->middleware(expencess_power::class);
    Route::post('/addExpensePayment', [ExpencesController::class, 'addExpensePayment'])->middleware(Users::class)->middleware(expencess_power::class);
    Route::get('/deleteExpensePayment/{id}', [ExpencesController::class, 'deleteExpensePayment'])->middleware(Users::class)->middleware(expencess_power::class);

//    Revenues api
    Route::post('/addRevenue', [RevenuesController::class, 'addRevenue'])->middleware(Users::class)->middleware(revenues_power::class);
    Route::get('/deleteRevenue/{id}', [RevenuesController::class, 'deleteRevenue'])->middleware(Users::class)->middleware(revenues_power::class);
    Route::get('/showRevenuesByBusiness', [RevenuesController::class, 'showRevenuesByBusiness'])->middleware(Users::class)->middleware(revenues_power::class);
    Route::get('/showRevenuesByBranches/{id}', [RevenuesController::class, 'showRevenuesByBranches'])->middleware(Users::class)->middleware(revenues_power::class);

    Route::post('/addRevenuePayment', [RevenuesController::class, 'addRevenuePayment'])->middleware(Users::class)->middleware(revenues_power::class);
    Route::get('/deleteRevenuePayment/{id}', [RevenuesController::class, 'deleteRevenuePayment'])->middleware(Users::class)->middleware(revenues_power::class);
    Route::get('/showRevenuePayment/{id}', [RevenuesController::class, 'showRevenuePayment'])->middleware(Users::class)->middleware(revenues_power::class);

//    Products api

    Route::post('/addProductType', [ProductController::class, 'addProductType'])->middleware(Users::class);
    Route::get('/showProductType', [ProductController::class, 'showProduct_Types'])->middleware(Users::class);
    Route::post('/changeProductType/{id}', [ProductController::class, 'changeProductType'])->middleware(Users::class);
    Route::get('/toggleBlockProductType/{id}', [ProductController::class, 'toggleBlockProductType'])->middleware(Users::class);

    Route::get('/showProductUnit', [ProductController::class, 'showProduct_Unit'])->middleware(Users::class);
    Route::post('/addProductUnit', [ProductController::class, 'addProductUnit'])->middleware(Users::class);
    Route::post('/changeProductUnit/{id}', [ProductController::class, 'changeProductUnit'])->middleware(Users::class);
    Route::get('/toggleBlockProductUnit/{id}', [ProductController::class, 'toggleBlockProductUnit'])->middleware(Users::class);

    Route::get('/test', [AuthController::class, 'test'])->middleware(Users::class);
});
