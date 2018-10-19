<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Traits\MetaTrait;
use App\OrganizationHierarchy;
use App\Employee;

class OrganizationHierarchyController extends Controller
{
    

    use MetaTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->meta['title'] = 'Organization Hierarchy';

        $organization_hierarchies = OrganizationHierarchy::with('employee')
        ->with('lineManager')
        ->with('parentEmployee')
        ->get();
        return view('admin.organization_hierarchy.index',$this->metaResponse())->with([
            'organization_hierarchies' => $organization_hierarchies,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->meta['title'] = 'Create Role';
        $all_controllers = [];
        
        $employees = Employee::all();
        $OrganizationHierarchyCnt = OrganizationHierarchy::all()->count();
        return view('admin.organization_hierarchy.create',$this->metaResponse())->with([
            'employees' => $employees,
			'OrganizationHierarchyCnt' => $OrganizationHierarchyCnt,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */

    public function store(Request $request)
    {
        // $this->validate($request,[
        //     'name' => 'required|unique:roles',
        // ]);

        $OrganizationHierarchy = OrganizationHierarchy::create([
        	'employee_id' => $request->employee_id,
        	'line_manager_id' => $request->line_manager_id,
        	'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('organization_hierarchy.index')->with('success','Employee added to OrganizationHierarchy succesfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->meta['title'] = 'Edit Organize Employee';

		$employees = Employee::all();
		$organization_hierarchy = OrganizationHierarchy::find($id);

        return view('admin.organization_hierarchy.edit',$this->metaResponse())->with([
            'organization_hierarchy' => $organization_hierarchy,
            'employees' => $employees,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $organization_hierarchy = OrganizationHierarchy::find($id);
        
        $organization_hierarchy->employee_id = $request->employee_id;
        $organization_hierarchy->line_manager_id = $request->line_manager_id;
        $organization_hierarchy->parent_id = $request->parent_id;

        $organization_hierarchy->save();

        return redirect()->route('organization_hierarchy.index')->with('success','Employee updated in OrganizationHierarchy succesfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        foreach ($role->permissions as $key => $permission) {
            $role->revokePermissionTo($permission);
        }
        $role->delete();

        return redirect()->back()->with('success','Role and assigned permissions is deleted successfully.');
    }
}