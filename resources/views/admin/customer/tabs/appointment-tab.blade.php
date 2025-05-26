<div class="tab-pane active show" id="pc-1" role="tabpanel">
    <ul class="nav nav-pills nav-tabs-rounded nav-justified" role="tablist">
        <li class="nav-item waves-effect waves-light">
            <a class="nav-link active" data-toggle="tab" href="#therapist"
                role="tab">
                <span class="d-block d-sm-none"><i
                        class="fas fa-home"></i></span>
                <span class="d-none d-sm-block"><i
                        class="fa fa-list-alt me-2"></i> Therapist</span>
            </a>
        </li>
        <li class="nav-item waves-effect waves-light">
            <a class="nav-link" data-toggle="tab" href="#home-1"
                role="tab">
                <span class="d-block d-sm-none"><i
                        class="fas fa-home"></i></span>
                <span class="d-none d-sm-block"><i
                        class="fa fa-list-alt me-2"></i> All
                    Appointment</span>
            </a>
        </li>
        <li class="nav-item waves-effect waves-light">
            <a class="nav-link" data-toggle="tab" href="#profile-1"
                role="tab">
                <span class="d-block d-sm-none"><i
                        class="far fa-user"></i></span>
                <span class="d-none d-sm-block"><i
                        class="fa fa-clock me-2"></i> Pending
                    Appointment</span>
            </a>
        </li>
        <li class="nav-item waves-effect waves-light">
            <a class="nav-link" data-toggle="tab" href="#messages-1"
                role="tab">
                <span class="d-block d-sm-none"><i
                        class="far fa-envelope"></i></span>
                <span class="d-none d-sm-block"><i
                        class="fa fa-check-circle me-2"></i> Completed
                        Appointment</span>
            </a>
        </li>

    </ul>
    <div class="tab-content p-3 text-muted">
        <div class="tab-pane active" id="therapist" role="tabpanel">
            <table id="datatable-buttons"
                class="table  table-bordered example table dt-responsive nowrap w-100">
                <thead class="table-light">
                    <tr>
                        <th>Therapist ID</th>
                        <th>Branch</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>

                    </tr>
                </thead>
                <tbody>
                    {{-- <tr>
                        <td>APT0001</td>
                        <td>Thomson Outlet</td>

                        <td>30 July 2023</td>
                        <td>10:00am - 11:00am</td>
                        <td><span class="badge bg-primary">Pending</span>
                        </td>
                    </tr>
                    <tr>
                        <td>APT0091</td>
                        <td>Thomson Outlet</td>

                        <td>8 July 2023</td>
                        <td>12:00am - 1:00am</td>
                        <td><span class="badge bg-success">Completed</span>
                        </td>
                    </tr> --}}
                </tbody>
            </table>
        </div>
        <div class="tab-pane" id="home-1" role="tabpanel">
            <table id="datatable-buttons"
                class="table  table-bordered example table dt-responsive nowrap w-100">
                <thead class="table-light">
                    <tr>
                        <th>Appointment ID</th>
                        <th>Branch</th>

                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Status</th>

                    </tr>
                </thead>
                <tbody>
                    {{-- <tr>
                        <td>APT0001</td>
                        <td>Thomson Outlet</td>

                        <td>30 July 2023</td>
                        <td>10:00am - 11:00am</td>
                        <td><span class="badge bg-primary">Pending</span>
                        </td>
                    </tr>
                    <tr>
                        <td>APT0091</td>
                        <td>Thomson Outlet</td>

                        <td>8 July 2023</td>
                        <td>12:00am - 1:00am</td>
                        <td><span class="badge bg-success">Completed</span>
                        </td>
                    </tr> --}}
                </tbody>
            </table>
        </div>
        <div class="tab-pane" id="profile-1" role="tabpanel">
            <table id="datatable-buttons"
                class="table   table-bordered example table dt-responsive nowrap w-100"
                style="border-color: #380814;">
                <thead class="table-light">
                    <tr>
                        <th>Appointment ID</th>
                        <th>Branch</th>

                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Status</th>

                    </tr>
                </thead>
                <tbody>
                    {{-- <tr>
                        <td>APT0001</td>
                        <td>Thomson Outlet</td>

                        <td>30 July 2023</td>
                        <td>10:00am - 11:00am</td>
                        <td><span class="badge bg-primary">Pending</span>
                        </td>
                    </tr> --}}

                </tbody>
            </table>
        </div>
        <div class="tab-pane" id="messages-1" role="tabpanel">
            <table id="datatable-buttons"
                class="table  table-bordered example table dt-responsive nowrap w-100">
                <thead class="table-light">
                    <tr>
                        <th>Appointment ID</th>
                        <th>Branch</th>

                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Status</th>

                    </tr>
                </thead>
                <tbody>

                    {{-- <tr>
                        <td>APT0091</td>
                        <td>Thomson Outlet</td>

                        <td>8 July 2023</td>
                        <td>12:00am - 1:00am</td>
                        <td><span class="badge bg-success">Completed</span>
                        </td>
                    </tr> --}}
                </tbody>
            </table>
        </div>
    </div>
</div>