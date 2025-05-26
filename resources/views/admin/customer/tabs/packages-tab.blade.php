<div class="tab-pane" id="pc-7" role="tabpanel">
    <div class="d-flex justify-content-between">
        <div class="d-flex align-items-center">
            <h4>Assigned Packages</h4>
        </div>
        <button class="btn btn-sm mb-3" id="openAssignPackageButton" onclick="openAssignPackageModal()" style="background-color: #541726;color: #fff;">Assign Package</button>
    </div>
    <div id="assign_package" class="modal fade" tabindex="-1"
    aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Package</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="{{ route('admin.assign.package') }}" method="POST">
                        <div class="form-group">
                            <label for="package_id">Select Package:</label>
                            <select class="form-control" id="package_select" name="package_id">
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}">{{ $package->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button class="btn btn-success" onclick="assignPackage()">Assign</button>
                </div>
                
            </div>
        </div>
    </div> 

    <div class="tab-content p-3 text-muted">
        <div class="tab-pane active" id="home-1201" role="tabpanel">
            <table id="PackageTableBody" class="table table-bordered example table dt-responsive nowrap w-100">
                <thead class="table-light">
                    <tr>
                        <th>S.No</th>
                        <th>Package Name</th>
                        <th>Amount</th>
                        <th>Coin</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assignedPackages as $index => $packageUser)
                        <tr>
                            <td>{{ $index+1 }}</td>
                            <td>{{ $packageUser->package->title }}</td>
                            <td>{{ $packageUser->package->amount }}</td>
                            <td>{{ $packageUser->package->coin }}</td>
                            <td>
                                @if ($packageUser->status == '1')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">
                                        Disable</span>             
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>