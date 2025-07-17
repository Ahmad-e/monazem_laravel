<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Employees;
use App\Models\Branches;
use App\Models\Employees_salaries;
use App\Models\Employees_salaries_payments;
use App\Models\Employees_compensation;
use App\Models\Employees_holidays;
use App\Models\Employees_salaries_advance;
use App\Models\Employees_salaries_advances_payments;
use App\Models\Currencies;

use Illuminate\Support\Facades\Auth;
use function Carbon\this;


class EmployeeController extends Controller
{

    public function getEmployeesByBusinessId($id)
    {
        $employees = Employees::where("business_id", $id)->get();

        return response()->json([
            'state' => 200,
            'data' => $employees,
        ]);
    }

    public function getEmployees()
    {
        $user = Auth::user();
        return $this->getEmployeesByBusinessId($user->business_id);
    }

    public function addEmployee(Request $request){

        $request->validate([
            'name' => 'required'
        ]);
        $user = Auth::user();

        Employees::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'position' => $request->position,
            'hire_date' => $request->hire_date,
            'termination_date' => $request->termination_date,
            'note' => $request->note,
            'branch_id' => $request->branch_id,
            'business_id'=>$user->business_id,
            'creator_id'=>$user->id,
        ]);

        return $this->getEmployeesByBusinessId($user->business_id);
    }

    public function updateEmployee(Request $request, $id)
    {
        $employee = Employees::find($id);
        $user = Auth::user();


        if (!$employee) {
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message' => "Employee not found",
            ], 404);
        }

        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        $employee->update($request->only([
            'name',
            'email',
            'phone_number',
            'address',
            'position',
            'hire_date',
            'termination_date',
            'note',
            'branch_id'
        ]));

        return $this->getEmployeesByBusinessId($user->business_id);
    }

    public function toggleBlockedEmployee($id)
    {
        $employee = Employees::find($id);
        $user = Auth::user();

        if (!$employee) {
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message' => "Employee not found",
            ], 404);
        }

        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        // عكس القيمة
        $employee->blocked_employee = !$employee->blocked_employee;
        $employee->save();

        return $this->getEmployeesByBusinessId($user->business_id);
    }

    //************************************************
    // start  salaries controller
    //************************************************

    public function getAllEmployeeSalariesByBusinessId($id){
        $employee = Employees::find($id);
        $data = Employees_salaries:: where('employee_id',$id)
            ->with('currency')->get();

        return response()->json([
            'state' => 200,
            'message'=>"Added successfully",
            'data' => $data,
            'employee_data'=>$employee
        ], 200);
    }

    public function addEmployeeSalary(Request $request){

        $request->validate([
            'value' => 'required',
            'employee_id' => 'required'
        ]);

        $employee = Employees::find($request->employee_id);
        $user = Auth::user();

        if (!$employee) {
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message' => "Employee not found",
            ], 404);
        }

        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        Employees_salaries::where('employee_id', $request->employee_id)-> where('active', '=', 1)->update(['active' => 0]);
        Employees_salaries::create([
            'value' => $request->value,
            'description' => $request->description,
            'pay_time' => $request->pay_time,
            'frequency'=> $request->frequency,
            'employee_id' => $request->employee_id,
            'currency_id' => $request->currency_id,
            'creator_id' => $user->id
        ]);

        return $this->getAllEmployeeSalariesByBusinessId($request->employee_id);
    }

    public function getAllEmployeeSalaries($id){

        $employee = Employees::find($id);
        $user = Auth::user();

        if (!$employee) {
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message' => "Employee not found",
            ], 404);
        }

        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        return $this->getAllEmployeeSalariesByBusinessId($id);
    }

    public function getActiveEmployeeSalaries($id){
        $employee = Employees::find($id);
        $user = Auth::user();

        if (!$employee) {
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message' => "Employee not found",
            ], 404);
        }

        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        return $this->getAllEmployeeSalariesByBusinessId($id);
    }

    public function selectActiveEmployeeSalaries($id){
        $employeeSalary = Employees_salaries::find($id);

        if(!$employeeSalary){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no salary id found",
            ], 200);
        }

        $employee = Employees::find($employeeSalary->employee_id);
        $user = Auth::user();

        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        Employees_salaries::where('active', '=', 1)->update(['active' => 0]);
        Employees_salaries::where('id',$id)->update(['active' => 1]);

        return $this->getAllEmployeeSalariesByBusinessId($employee->id);
    }

    //************************************************
    // start Employee salary payments controller
    //************************************************
    public function showEmployeesSalariesPaymentBySalaryId($id){
        $salaries = Employees_salaries::where('id',$id)
            ->with(['payments','currency'])
            ->get();

        return response()->json([
            'state' => 200,
            'data' => $salaries
        ], 200);
    }
    public function addEmployeesSalariesPayment (Request $request){
        $request->validate([
            'value' => 'required',
            'allowances' => 'required',
            'deductions' => 'required',
            'date' => 'required',
            'salary_id' => 'required',
            'currency_id' => 'required'
        ]);

        $user = Auth::user();

        $employeeSalary = Employees_salaries::find($request->salary_id);

        if(!$employeeSalary){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no salary id found",
            ], 200);
        }

        $employee = Employees::find($employeeSalary->employee_id);
        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        Employees_salaries_payments::create([
            'value' => $request->value,
            'description' => $request->description,
            'allowances' => $request->allowances,
            'deductions' => $request->deductions,
            'date' => $request->date,
            'work_from' => $request->work_from,
            'work_to' => $request->work_to,
            'currency_id' => $request->currency_id,
            'salary_id'=>$request->salary_id,
            'creator_id'=>$user->id,
        ]);

        return $this->showEmployeesSalariesPaymentBySalaryId($request->salary_id);

    }

    public function showEmployeesSalariesPayment ($id){

        $user = Auth::user();
        $employeeSalary = Employees_salaries::find($id);

        if(!$employeeSalary){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no salary id found",
            ], 200);
        }

        $employee = Employees::find($employeeSalary->employee_id);
        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        return $this->showEmployeesSalariesPaymentBySalaryId($id);
    }

    public function showEmployeesSalary_SalaryPayment ($id){

        $user = Auth::user();

        $employee = Employees::find($id);
        if(!$employee)
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message' => "Employee not found",
            ], 404);

        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        $salaries = Employees_salaries::with('payments')
            ->where('employee_id', $id)
            ->get();
        return response()->json([
            'state' => 200,
            'data' => $salaries,
            'employee_data'=>$employee
        ], 200);
    }

    public function deleteEmployeesSalariesPayment ($id){
        $Payment = Employees_salaries_payments::find($id);

        if (!$Payment) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "salary payment not found",
            ], 404);
        }

        $employeeSalary = Employees_salaries::find($Payment->salary_id);
        $employee = Employees::find($employeeSalary->employee_id);
        $user = Auth::user();
        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }
        // حذف العطلة
        $Payment->delete();

        return $this->showEmployeesSalariesPaymentBySalaryId($id);
    }

    //************************************************
    // start Employee salary Advance controller
    //************************************************

    public function getAllEmployeesSalariesAdvanceByEmployeeId($id) {
        $employee = Employees::find($id);
        $data = Employees_salaries_advance::where('employee_id',$id)
            ->with(['AdvancePayments','currency'])
            ->get();

        return response()->json([
            'state' => 200,
            'message' => "Retrieved successfully",
            'data' => $data,
            'employee_data'=>$employee
        ], 200);
    }

    public function addEmployeesSalariesAdvance(Request $request){

        $request->validate([
            'value' => 'required',
            'currency_id' => 'required',
            'employee_id' => 'required'
        ]);
        $employee = Employees::find($request->employee_id);
        $user = Auth::user();

        if (!$employee) {
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message' => "Employee not found",
            ], 404);
        }

        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        Employees_salaries_advance::create([
            'value' => $request->value,
            'description' => $request->description,
            'pay_time' => $request->pay_time,
            'paid'=> $request->paid,
            'is_debts'=> $request->is_debts,
            'employee_id' => $request->employee_id,
            'currency_id' => $request->currency_id,
            'creator_id' => $user->id
        ]);

        return $this->getAllEmployeesSalariesAdvanceByEmployeeId($request->employee_id);
    }

    public function getAllEmployeesSalariesAdvance($id) {
        $employee = Employees::find($id);
        $user = Auth::user();

        if (!$employee) {
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message' => "Employee not found",
            ], 404);
        }

        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        return $this->getAllEmployeesSalariesAdvanceByEmployeeId($id);
    }

    public function deleteEmployeesSalariesAdvance($id) {
        $salaryAdvance = Employees_salaries_advance::find($id);

        if (!$salaryAdvance) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "no record found with this ID",
            ], 404);
        }
        $employee = Employees::find($salaryAdvance->employee_id);
        $user = Auth::user();
        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        $salaryAdvance->delete();
        return $this->getAllEmployeesSalariesAdvanceByEmployeeId($employee->id);
    }

    public function updateEmployeesSalariesAdvance(Request $request, $id) {
        // تحقق مما إذا كان السجل موجودًا
        $salaryAdvance = Employees_salaries_advance::find($id);

        if (!$salaryAdvance) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No record found with this ID",
            ], 404);
        }
        $employee = Employees::find($salaryAdvance->employee_id);
        $user = Auth::user();
        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        // تحديث الحقول بناءً على القيم المدخلة، إذا كانت موجودة
        if ($request->has('employee_id')) {
            $salaryAdvance->employee_id = $request->employee_id;
        }

        if ($request->has('currency_id')) {
            $salaryAdvance->currency_id = $request->currency_id;
        }

        if ($request->has('value')) {
            $salaryAdvance->value = $request->value;
        }

        if ($request->has('description')) {
            $salaryAdvance->description = $request->description;
        }

        if ($request->has('pay_time')) {
            $salaryAdvance->pay_time = $request->pay_time;
        }

        if ($request->has('paid')) {
            $salaryAdvance->paid = $request->paid;
        }

        if ($request->has('is_debts')) {
            $salaryAdvance->is_debts = $request->is_debts;
        }

        // احفظ التغييرات
        $salaryAdvance->save();

        // عرض جميع البيانات بعد التحديث
        return $this->getAllEmployeesSalariesAdvanceByEmployeeId($employee->id);
    }

    //************************************************
    // start Employee salary Advance payments controller
    //************************************************

    public function showEmployeesSalariesAdvancePaymentBySalaryAdvanceId($id){
        $salaries = Employees_salaries_advance::where('id',$id)
            ->with(['AdvancePayments','currency'])
            ->get();

        return response()->json([
            'state' => 200,
            'data' => $salaries
        ], 200);
    }

    public function getEmployeesSalariesAdvancePayments($id){
        $salary_advance = Employees_salaries_advance::find($id);
        if(!$salary_advance){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no Employee_salary_advance id found",
            ], 200);
        }

        $employee = Employees::find($salary_advance->employee_id);
        $user = Auth::user();
        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }
        return $this->showEmployeesSalariesAdvancePaymentBySalaryAdvanceId($id);
    }

    public function addEmployeesSalariesAdvancePayments(Request $request){

        $request->validate([
            'value' => 'required',
            'salaries_advance_id' => 'required'
        ]);

        $salaries_advance = Employees_salaries_advance::find($request->salaries_advance_id);
        if(!$salaries_advance){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no Employee_salary_advance id found",
            ], 200);
        }
        $employee = Employees::find($salaries_advance->employee_id);
        $user = Auth::user();
        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        Employees_salaries_advances_payments::create([
            'value' => $request->value,
            'salaries_advance_id' => $request->salaries_advance_id,
            'date' => $request->date,
        ]);

        return $this->showEmployeesSalariesAdvancePaymentBySalaryAdvanceId($request->salaries_advance_id);
    }

    public function deleteEmployeesSalariesAdvancePayments($id) {
        $salaryAdvancePay = Employees_salaries_advances_payments::find($id);

        if (!$salaryAdvancePay) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "no record found with this ID",
            ], 404);
        }

        $salaries_advance = Employees_salaries_advance::find($salaryAdvancePay->salaries_advance_id);
        if(!$salaries_advance){
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message'=>"no Employee_salary_advance id found",
            ], 200);
        }
        $employee = Employees::find($salaries_advance->employee_id);
        $user = Auth::user();
        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        $salaryAdvancePay->delete();

        return $this->showEmployeesSalariesAdvancePaymentBySalaryAdvanceId($salaryAdvancePay->salaries_advance_id);
    }


    //************************************************
    // start Employee compensation controller
    //************************************************
    public function showEmployeeCompensation($id){

        $employee = Employees::find($id);
        if (!$employee) {
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message' => "Employee not found",
            ], 404);
        }

        $user = Auth::user();
        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        $data = Employees_compensation::where('employee_id',$id)
            ->with('currency')->get();
        return response()->json([
            'state' => 200,
            'data' => $data
        ], 200);
    }

    public function addEmployeeCompensation(Request $request){
        $request->validate([
            'value' => 'required',
            'employee_id' => 'required',
            'currency_id' => 'required'
        ]);

        $employee = Employees::find($request->employee_id);
        $user = Auth::user();

        if (!$employee) {
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message' => "Employee not found",
            ], 404);
        }

        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        if(!(Currencies::where('id',$request->currency_id)->exists())){
            return response()->json([
                'state' => 404,
                'error'=> 2 ,
                'message'=>"no currency id found",
            ], 404);
        }

        Employees_compensation::create([
            'value' => $request->value,
            'description' => $request->description,
            'pay_time' => $request->pay_time,
            'currency_id' => $request->currency_id,
            'employee_id'=>$request->employee_id,
            'creator_id'=>$user->id,
        ]);
        return $this->showEmployeeCompensation($request->employee_id);
    }

    public function changeEmployeeCompensation(Request $request,$id){
        $compensation = Employees_compensation::find($id);

        if (!$compensation) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No compensation record found with this ID",
            ], 404);
        }
        $employee = Employees::find($compensation->employee_id);
        $user = Auth::user();


        if (!$employee) {
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message' => "Employee not found",
            ], 404);
        }

        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }
        // تحديث الحقول بناءً على القيم المدخلة، إذا كانت موجودة
        if ($request->has('value')) {
            $compensation->value = $request->value;
        }

        if ($request->has('description')) {
            $compensation->description = $request->description;
        }

        if ($request->has('pay_time')) {
            $compensation->pay_time = $request->pay_time;
        }

        if ($request->has('currency_id')) {
            // تحقق مما إذا كانت العملة موجودة
            if (!(Currencies::where('id', $request->currency_id)->exists())) {
                return response()->json([
                    'state' => 404,
                    'error' => 2,
                    'message' => "No currency ID found",
                ], 404);
            }
            $compensation->currency_id = $request->currency_id;
        }

        $compensation->save();

        return $this->showEmployeeCompensation($compensation->employee_id);
    }

    public function deleteEmployeeCompensation($id){
        $compensation = Employees_compensation::find($id);

        if (!$compensation) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "No compensation record found with this ID",
            ], 404);
        }
        $employee = Employees::find($compensation->employee_id);
        $user = Auth::user();


        if (!$employee) {
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message' => "Employee not found",
            ], 404);
        }

        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }
        // حذف السجل
        $compensation->delete();

        return $this->showEmployeeCompensation($compensation->employee_id);
    }

    //************************************************
    // start Employee compensation controller
    //************************************************

    public function showEmployeeHolidays($id){

        $employee = Employees::find($id);
        if (!$employee) {
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message' => "Employee not found",
            ], 404);
        }

        $user = Auth::user();
        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        $data = Employees_holidays::where('employee_id',$id)
            ->with('currency')->get();
        return response()->json([
            'state' => 200,
            'data' => $data
        ], 200);
    }

    public function addEmployeeHolidays(Request $request){
        $request->validate([
            'value' => 'required',
            'employee_id' => 'required',
            'currency_id' => 'required'
        ]);

        $employee = Employees::find($request->employee_id);
        $user = Auth::user();

        if (!$employee) {
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message' => "Employee not found",
            ], 404);
        }

        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        Employees_holidays::create([
            'value' => $request->value,
            'description' => $request->description,
            'pay_time' => $request->pay_time,
            'type' => $request->type,
            'currency_id' => $request->currency_id,
            'employee_id'=>$request->employee_id,
            'creator_id'=>$user->id,
        ]);
        return $this->showEmployeeHolidays($request->employee_id);
    }



    public function changeEmployeeHolidays(Request $request,$id){
        $holiday = Employees_holidays::find($id);

        if (!$holiday) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "Holiday not found",
            ], 404);
        }
        $employee = Employees::find($holiday->employee_id);
        $user = Auth::user();

        if (!$employee) {
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message' => "Employee not found",
            ], 404);
        }

        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }

        // تحقق من القيم الموجودة في الطلب وقم بتحديثها فقط إذا كانت موجودة
        if ($request->has('value')) {
            $holiday->value = $request->value;
        }
        if ($request->has('description')) {
            $holiday->description = $request->description;
        }
        if ($request->has('pay_time')) {
            $holiday->pay_time = $request->pay_time;
        }
        if ($request->has('type')) {
            $holiday->type = $request->type;
        }
        if ($request->has('currency_id')) {
            // تحقق من وجود العملة
            if (!Currencies::where('id', $request->currency_id)->exists()) {
                return response()->json([
                    'state' => 404,
                    'error' => 2,
                    'message' => "Currency ID not found",
                ], 404);
            }
            $holiday->currency_id = $request->currency_id;
        }

        // حفظ التغييرات
        $holiday->save();

        return $this->showEmployeeHolidays($employee->employee_id);
    }

    public function deleteEmployeeHolidays($id){
        $holiday = Employees_holidays::find($id);

        if (!$holiday) {
            return response()->json([
                'state' => 404,
                'error' => 1,
                'message' => "Holiday not found",
            ], 404);
        }


        $employee = Employees::find($holiday->employee_id);
        $user = Auth::user();

        if (!$employee) {
            return response()->json([
                'state' => 404,
                'error'=> 1 ,
                'message' => "Employee not found",
            ], 404);
        }

        if($user->business_id != $employee->business_id){
            return response()->json([
                'state' => 402,
                'error'=> 2 ,
                'message'=>"This employee not related to your business",
            ], 402);
        }
        // حذف العطلة
        $holiday->delete();

        return $this->showEmployeeHolidays($employee->employee_id);
    }
}
