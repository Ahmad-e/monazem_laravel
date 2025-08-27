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
use App\Http\Controllers\BuildingPlaces;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\AssetsController;
use App\Http\Controllers\TaxesController;
use App\Http\Controllers\RentPrepaidController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\BalancesController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\ExternalDeptsController;
use App\Http\Controllers\InternalDeptsController;
use App\Http\Controllers\StockController;

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
use App\Http\Middleware\product_power;
use App\Http\Middleware\product_data_power;
use App\Http\Middleware\buildings_power;
use App\Http\Middleware\places_power;
use App\Http\Middleware\cashes_power;
use App\Http\Middleware\partner_Accounts_power;
use App\Http\Middleware\branches_power;
use App\Http\Middleware\assets_power;
use App\Http\Middleware\taxes_power;
use App\Http\Middleware\rent_power;
use App\Http\Middleware\balance_power;
use App\Http\Middleware\setting_power;
use App\Http\Middleware\invoice_power;
use App\Http\Middleware\transaction_power;
use App\Http\Middleware\depts_power;
use App\Http\Middleware\stock_power;

use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
//Route::get('/test', [AuthController::class, 'test']);
//Route::post('/addBusiness', [BusinessController::class, 'addBusiness']);


Route::get('/showCurrencies', [CurrencyController::class, 'showCurrencies']);


Route::middleware('api')->group(function () {

    Route::get('/profile', [AuthController::class, 'getUser'])->middleware(Users::class);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware(Users::class);
    Route::post('/updateUser', [AuthController::class, 'updateUser'])->middleware(Users::class);

    Route::post('/create_account', [AuthController::class, 'create_account'])->middleware(Users::class)->middleware(accounts_power::class);
    Route::get('/showAccountById/{id}', [AuthController::class, 'showAccountById'])->middleware(Users::class)->middleware(accounts_power::class);
    Route::get('/show_user_Accounts', [AuthController::class, 'showAccounts'])->middleware(Users::class)->middleware(accounts_power::class);
    Route::post('/update_user_Account/{id}', [AuthController::class, 'updateAccount'])->middleware(Users::class)->middleware(accounts_power::class);
    Route::post('/create_admin_account' , [AuthController::class, 'create_admin_account'])->middleware(Admins::class);
    Route::post('/show_Admins_Accounts' , [AuthController::class, 'show_Admins_Accounts'])->middleware(Admins::class);
    Route::post('/create_admin_account' , [AuthController::class, 'create_admin_account'])->middleware(Admins::class);

    Route::get('/toggleOverPowerUser/{id}', [AuthController::class, 'toggleOverPowerUser'])->middleware(Users::class)->middleware(accounts_power::class);
    Route::get('/toggleBlockUser/{id}', [AuthController::class, 'toggleBlockUser'])->middleware(Users::class)->middleware(accounts_power::class);
    Route::get('/showBusiness', [BusinessController::class, 'showBusiness'])->middleware(Users::class);
    Route::post('/updateBusiness', [BusinessController::class, 'updateBusiness'])->middleware(Users::class);

//    branches api

    Route::get('/showBranches', [BranchController::class, 'showAllBranches'])->middleware(Users::class)->middleware(branches_power::class);
    Route::post('/addBranch', [BranchController::class, 'addBranch'])->middleware(Users::class)->middleware(branches_power::class);
    Route::post('/changeBranch/{id}', [BranchController::class, 'changeBranch'])->middleware(Users::class)->middleware(branches_power::class);
    Route::get('/setMainBranch/{id}', [BranchController::class, 'setMainBranch'])->middleware(Users::class)->middleware(branches_power::class);

//    employee api
    Route::post('/addEmployee', [EmployeeController::class, 'addEmployee'])->middleware(Users::class)->middleware(employee_power::class) ;
    Route::get('/getEmployees', [EmployeeController::class, 'getEmployees'])->middleware(Users::class)->middleware(employee_power::class);
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

    Route::get('/getEmployeesSalariesAdvancePayments/{id}', [EmployeeController::class, 'getEmployeesSalariesAdvancePayments'])->middleware(Users::class)->middleware(employee_data_power::class);
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
    Route::post('/addMultiplePowers', [PowerController::class, 'addMultiplePowers'])->middleware(Admins::class);

//   currencies api

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
    Route::post('/changeExpense/{id}', [ExpencesController::class, 'changeExpense'])->middleware(Users::class)->middleware(expencess_power::class);
    Route::get('/deleteExpense/{id}', [ExpencesController::class, 'deleteExpense'])->middleware(Users::class)->middleware(expencess_power::class);
    Route::get('/showExpenses', [ExpencesController::class, 'showExpenses'])->middleware(Users::class)->middleware(expencess_power::class);

    Route::get('/showExpensePayment/{id}', [ExpencesController::class, 'showExpensePayment'])->middleware(Users::class)->middleware(expencess_power::class);
    Route::post('/addExpensePayment', [ExpencesController::class, 'addExpensePayment'])->middleware(Users::class)->middleware(expencess_power::class);
    Route::get('/deleteExpensePayment/{id}', [ExpencesController::class, 'deleteExpensePayment'])->middleware(Users::class)->middleware(expencess_power::class);

//    ExternalDept api
    Route::post('/addExternalDept', [ExternalDeptsController::class, 'addExternalDept'])->middleware(Users::class)->middleware(depts_power::class);
    Route::get('/showExternalDept', [ExternalDeptsController::class, 'showExternalDept'])->middleware(Users::class)->middleware(depts_power::class);
    Route::post('/changeExternalDept/{id}', [ExternalDeptsController::class, 'changeExternalDept'])->middleware(Users::class)->middleware(depts_power::class);
    Route::get('/deleteExternalDept/{id}', [ExternalDeptsController::class, 'deleteExternalDept'])->middleware(Users::class)->middleware(depts_power::class);

    Route::post('/addExternalDeptPayment', [ExternalDeptsController::class, 'addExternalDeptPayment'])->middleware(Users::class)->middleware(depts_power::class);
    Route::get('/deleteExternalDeptPayment/{id}', [ExternalDeptsController::class, 'deleteExternalDeptPayment'])->middleware(Users::class)->middleware(depts_power::class);

//    InternalDept  api
    Route::post('/addInternalDept', [InternalDeptsController::class, 'addInternalDept'])->middleware(Users::class)->middleware(depts_power::class);
    Route::get('/showInternalDept', [InternalDeptsController::class, 'showInternalDept'])->middleware(Users::class)->middleware(depts_power::class);
    Route::post('/changeInternalDept/{id}', [InternalDeptsController::class, 'changeInternalDept'])->middleware(Users::class)->middleware(depts_power::class);
    Route::get('/deleteInternalDept/{id}', [InternalDeptsController::class, 'deleteInternalDept'])->middleware(Users::class)->middleware(depts_power::class);

    Route::post('/addInternalDeptPayment', [InternalDeptsController::class, 'addInternalDeptPayment'])->middleware(Users::class)->middleware(depts_power::class);
    Route::get('/deleteInternalDeptPayment/{id}', [InternalDeptsController::class, 'deleteInternalDeptPayment'])->middleware(Users::class)->middleware(depts_power::class);

//    Revenues api
    Route::post('/addRevenue', [RevenuesController::class, 'addRevenue'])->middleware(Users::class)->middleware(revenues_power::class);
    Route::post('/changeRevenue/{id}', [RevenuesController::class, 'changeRevenue'])->middleware(Users::class)->middleware(revenues_power::class);
    Route::get('/deleteRevenue/{id}', [RevenuesController::class, 'deleteRevenue'])->middleware(Users::class)->middleware(revenues_power::class);
    Route::get('/showRevenues', [RevenuesController::class, 'showRevenues'])->middleware(Users::class)->middleware(revenues_power::class);

    Route::post('/addRevenuePayment', [RevenuesController::class, 'addRevenuePayment'])->middleware(Users::class)->middleware(revenues_power::class);
    Route::get('/deleteRevenuePayment/{id}', [RevenuesController::class, 'deleteRevenuePayment'])->middleware(Users::class)->middleware(revenues_power::class);
    Route::get('/showRevenuePayment/{id}', [RevenuesController::class, 'showRevenuePayment'])->middleware(Users::class)->middleware(revenues_power::class);

//    Products api

    Route::post('/addProductType', [ProductController::class, 'addProductType'])->middleware(Users::class)->middleware(product_data_power::class);
    Route::get('/showProductType', [ProductController::class, 'showProduct_Types'])->middleware(Users::class)->middleware(product_data_power::class);
    Route::post('/changeProductType/{id}', [ProductController::class, 'changeProductType'])->middleware(Users::class)->middleware(product_data_power::class);
    Route::get('/toggleBlockProductType/{id}', [ProductController::class, 'toggleBlockProductType'])->middleware(Users::class)->middleware(product_data_power::class);

    Route::get('/showProductUnit', [ProductController::class, 'showProduct_Unit'])->middleware(Users::class)->middleware(product_data_power::class);
    Route::post('/addProductUnit', [ProductController::class, 'addProductUnit'])->middleware(Users::class)->middleware(product_data_power::class);
    Route::post('/changeProductUnit/{id}', [ProductController::class, 'changeProductUnit'])->middleware(Users::class)->middleware(product_data_power::class);
//    Route::get('/toggleBlockProductUnit/{id}', [ProductController::class, 'toggleBlockProductUnit'])->middleware(Users::class);

    Route::post('/addProduct', [ProductController::class, 'addProduct'])->middleware(Users::class)->middleware(product_power::class);
    Route::get('/showProduct', [ProductController::class, 'showProduct'])->middleware(Users::class)->middleware(product_power::class);
    Route::post('/changeProduct/{id}', [ProductController::class, 'changeProduct'])->middleware(Users::class)->middleware(product_power::class);
    Route::get('/toggleBlockProduct/{id}', [ProductController::class, 'toggleBlockProduct'])->middleware(Users::class)->middleware(product_power::class);

    Route::get('/showProductFavorite', [ProductController::class, 'showProductFavorite'])->middleware(Users::class);
    Route::get('/addProductFavorite/{id}', [ProductController::class, 'addProductFavorite'])->middleware(Users::class);
    Route::get('/deleteProductFavorite/{id}', [ProductController::class, 'deleteProductFavorite'])->middleware(Users::class);

    Route::post('/addProductsPrices', [ProductController::class, 'addProductsPrices'])->middleware(Users::class)->middleware(product_power::class);
    Route::post('/changeProductsPrices/{id}', [ProductController::class, 'changeProductsPrices'])->middleware(Users::class)->middleware(product_power::class);
    Route::get('/showProductsPrices/{id}', [ProductController::class, 'showProductsPrices'])->middleware(Users::class)->middleware(product_power::class);

    Route::post('/addProductCode', [ProductController::class, 'addProductCode'])->middleware(Users::class)->middleware(product_data_power::class);
    Route::post('/changeProductCode/{id}', [ProductController::class, 'changeProductCode'])->middleware(Users::class)->middleware(product_data_power::class);
    Route::get('/showProductCode/{id}', [ProductController::class, 'showProductCode'])->middleware(Users::class)->middleware(product_data_power::class);

    Route::get('/showProductMovesPlaces/{id}', [ProductController::class, 'showProductMovesPlaces'])->middleware(Users::class)->middleware(product_data_power::class);
    Route::post('/addMoveProduct', [ProductController::class, 'AddMoveProduct'])->middleware(Users::class)->middleware(product_data_power::class);
    Route::post('/addPlaceProduct', [ProductController::class, 'addPlaceProduct'])->middleware(Users::class)->middleware(product_data_power::class);

//    invoice api

    Route::get('/showInvoices', [InvoicesController::class, 'showInvoices'])->middleware(Users::class)->middleware(invoice_power::class);
    Route::post('/addInvoices', [InvoicesController::class, 'addInvoices'])->middleware(Users::class)->middleware(invoice_power::class);
    Route::post('/changInvoices/{id}', [InvoicesController::class, 'changInvoices'])->middleware(Users::class)->middleware(invoice_power::class);

//    Transactions api

    Route::get('/showTransaction', [TransactionsController::class, 'showTransaction'])->middleware(Users::class)->middleware(transaction_power::class);
    Route::post('/addTransaction', [TransactionsController::class, 'addTransaction'])->middleware(Users::class)->middleware(transaction_power::class);
    Route::post('/changeTransaction/{id}', [TransactionsController::class, 'changeTransaction'])->middleware(Users::class)->middleware(transaction_power::class);

//    building api

    Route::get('/showBuildings/{id}', [BuildingPlaces::class, 'showBuildings'])->middleware(Users::class)->middleware(buildings_power::class);
    Route::post('/addBuildings', [BuildingPlaces::class, 'addBuildings'])->middleware(Users::class)->middleware(buildings_power::class);
    Route::get('/toggleBlockBuildings/{id}', [BuildingPlaces::class, 'toggleBlockBuildings'])->middleware(Users::class)->middleware(buildings_power::class);
    Route::post('/changeBuildings/{id}', [BuildingPlaces::class, 'changeBuildings'])->middleware(Users::class)->middleware(buildings_power::class);

//    places api

    Route::get('/showPlaces/{id}', [BuildingPlaces::class, 'showPlaces'])->middleware(Users::class)->middleware(places_power::class);
    Route::post('/addPlace', [BuildingPlaces::class, 'addPlace'])->middleware(Users::class)->middleware(places_power::class);
    Route::get('/toggleBlockPlaces/{id}', [BuildingPlaces::class, 'toggleBlockPlaces'])->middleware(Users::class)->middleware(places_power::class);
    Route::post('/changePlace/{id}', [BuildingPlaces::class, 'changePlace'])->middleware(Users::class)->middleware(places_power::class);

//    places api

    Route::get('/showStocks/{id}', [StockController::class, 'showStocks'])->middleware(Users::class)->middleware(stock_power::class);
    Route::post('/addStocks', [StockController::class, 'addStocks'])->middleware(Users::class)->middleware(stock_power::class);
    Route::get('/deleteStocks/{id}', [StockController::class, 'deleteStocks'])->middleware(Users::class)->middleware(stock_power::class);
    Route::post('/changeStocks/{id}', [StockController::class, 'changeStocks'])->middleware(Users::class)->middleware(stock_power::class);

//    Cashes api

    Route::get('/showCashes/{id}', [BranchController::class, 'showCashes'])->middleware(Users::class)->middleware(cashes_power::class);
    Route::post('/addCash', [BranchController::class, 'addCash'])->middleware(Users::class)->middleware(cashes_power::class);
    Route::post('/ChangeCash/{id}', [BranchController::class, 'ChangeCash'])->middleware(Users::class)->middleware(cashes_power::class);
    Route::get('/DeleteCash/{id}', [BranchController::class, 'DeleteCash'])->middleware(Users::class)->middleware(cashes_power::class);

//    Accounts api

    Route::get('/showAccounts', [AccountsController::class, 'showAccounts'])->middleware(Users::class)->middleware(partner_Accounts_power::class);
    Route::post('/addAccount', [AccountsController::class, 'addAccount'])->middleware(Users::class)->middleware(partner_Accounts_power::class);
    Route::post('/changeAccounts/{id}', [AccountsController::class, 'changeAccounts'])->middleware(Users::class)->middleware(partner_Accounts_power::class);
    Route::get('/deleteAccounts/{id}', [AccountsController::class, 'deleteAccounts'])->middleware(Users::class)->middleware(partner_Accounts_power::class);
    Route::get('/importAccountTree/{id}', [AccountsController::class, 'importAccountTree'])->middleware(Users::class)->middleware(partner_Accounts_power::class);

//    Taxes api

    Route::post('/addTax', [TaxesController::class, 'addTax'])->middleware(Users::class)->middleware(taxes_power::class);
    Route::get('/showTaxes', [TaxesController::class, 'showTaxes'])->middleware(Users::class)->middleware(taxes_power::class);
    Route::post('/changeTax/{id}', [TaxesController::class, 'changeTax'])->middleware(Users::class)->middleware(taxes_power::class);
    Route::get('/taggleblockTax/{id}', [TaxesController::class, 'taggleblockTax'])->middleware(Users::class)->middleware(taxes_power::class);

    Route::get('/showProductTaxes/{id}', [TaxesController::class, 'showProductTaxes'])->middleware(Users::class)->middleware(taxes_power::class);
    Route::post('/addProductTaxes', [TaxesController::class, 'addProductTaxes'])->middleware(Users::class)->middleware(taxes_power::class);
    Route::get('/deleteProductTaxes/{id}', [TaxesController::class, 'deleteProductTaxes'])->middleware(Users::class)->middleware(taxes_power::class);

//    Assets api

    Route::post('/addAssets', [AssetsController::class, 'addAssets'])->middleware(Users::class)->middleware(assets_power::class);
    Route::get('/showAssets', [AssetsController::class, 'showAssets'])->middleware(Users::class)->middleware(assets_power::class);
    Route::post('/changeAssets/{id}', [AssetsController::class, 'changeAssets'])->middleware(Users::class)->middleware(assets_power::class);
//    Route::get('/deleteAssets/{id}', [AssetsController::class, 'deleteAssets'])->middleware(Users::class)->middleware(assets_power::class);

    //    RentPrepaid api

    Route::get('/showRentPreExpenses', [RentPrepaidController::class, 'showRentPreExpenses'])->middleware(Users::class)->middleware(rent_power::class);
    Route::post('/addRentPreExpenses', [RentPrepaidController::class, 'addRentPreExpenses'])->middleware(Users::class)->middleware(rent_power::class);
    Route::get('/deleteRentPreExpenses/{id}', [RentPrepaidController::class, 'deleteRentPreExpenses'])->middleware(Users::class)->middleware(rent_power::class);

    Route::get('/showRentPreRevenues', [RentPrepaidController::class, 'showRentPreRevenues'])->middleware(Users::class)->middleware(rent_power::class);
    Route::post('/addRentPreRevenues', [RentPrepaidController::class, 'addRentPreRevenues'])->middleware(Users::class)->middleware(rent_power::class);
    Route::get('/deleteRentPreRevenues/{id}', [RentPrepaidController::class, 'deleteRentPreRevenues'])->middleware(Users::class)->middleware(rent_power::class);

//    Balance api

    Route::get('/showTrialBalance/{id}', [BalancesController::class, 'showTrialBalance'])->middleware(Users::class)->middleware(balance_power::class);
    Route::post('/addTrialBalance', [BalancesController::class, 'addTrialBalance'])->middleware(Users::class)->middleware(balance_power::class);
    Route::post('/changeTrialBalance/{id}', [BalancesController::class, 'changeTrialBalance'])->middleware(Users::class)->middleware(balance_power::class);
    Route::get('/deleteTrialBalance/{id}', [BalancesController::class, 'deleteTrialBalance'])->middleware(Users::class)->middleware(balance_power::class);

    Route::get('/showClientsBalance/{id}', [BalancesController::class, 'showClientsBalance'])->middleware(Users::class)->middleware(clients_power::class);
    Route::post('/addClientsBalance', [BalancesController::class, 'addClientsBalance'])->middleware(Users::class)->middleware(clients_power::class);
    Route::post('/changeClientsBalance/{id}', [BalancesController::class, 'changeClientsBalance'])->middleware(Users::class)->middleware(clients_power::class);
    Route::get('/deleteClientsBalance/{id}', [BalancesController::class, 'deleteClientsBalance'])->middleware(Users::class)->middleware(clients_power::class);

//    feedback api

    Route::get('/showUserFeedback', [SupportController::class, 'showUserFeedback'])->middleware(Users::class);
    Route::get('/showAdminFeedback', [SupportController::class, 'showAdminFeedback'])->middleware(Admins::class);
    Route::post('/addFeedback', [SupportController::class, 'addFeedback'])->middleware(Users::class);
    Route::get('/deleteFeedback/{id}', [SupportController::class, 'deleteFeedback'])->middleware(Admins::class);

//    support api

    Route::get('/showUserSupport', [SupportController::class, 'showUserSupport'])->middleware(Users::class);
    Route::get('/showAdminSupport', [SupportController::class, 'showAdminSupport'])->middleware(Admins::class);
    Route::post('/addSupport', [SupportController::class, 'addSupport'])->middleware(Users::class);
    Route::post('/addReSupport/{id}', [SupportController::class, 'addReSupport'])->middleware(Admins::class);
    Route::get('/deleteSupport/{id}', [SupportController::class, 'deleteSupport'])->middleware(Admins::class);

//    setting api

    Route::get('/showSettings', [SettingController::class, 'showSettings'])->middleware(Users::class)->middleware(setting_power::class);
    Route::post('/addSettings', [SettingController::class, 'addSettings'])->middleware(Users::class)->middleware(setting_power::class);
    Route::post('/changeSettings/{id}', [SettingController::class, 'changeSettings'])->middleware(Users::class)->middleware(setting_power::class);
    Route::get('/deleteSettings/{id}', [SettingController::class, 'deleteSettings'])->middleware(Users::class)->middleware(setting_power::class);

//    Route::get('/test', [AuthController::class, 'test'])->middleware(Users::class);
    Route::get('/test', [RentPrepaidController::class, 'test'])->middleware(Users::class);
});
