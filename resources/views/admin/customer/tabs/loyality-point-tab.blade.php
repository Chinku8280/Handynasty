<div class="tab-pane" id="pc-5" role="tabpanel">
    <div class="d-flex justify-content-between">
        <div class="l_points d-flex align-items-center loyaltyPointsDisplay_class"
            id="loyaltyPointsDisplay">
            Available Coin:- <span
                id="loyaltyPointsValue">{{ $customer->loyalty_points ?? 0 }}</span><i
                class="fa fa-coins ms-2"></i>

        </div>
        <button class="btn btn-sm mb-3" id="openSecondModalButton"
            onclick="openSecondModal()"
            style="background-color: #541726;color: #fff;">Add
            Coins</button>
    </div>


    <table id="loyaltyPointsTable"
        class="table table-bordered example table dt-responsive nowrap w-100">
        <thead class="table-light">
            <tr>
                <th>S.No</th>
                <th>Loyalty Coins</th>
                <th>Date</th>
                <th>Time</th>
                <th>Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $counter = 1;
            @endphp
            @foreach ($loyaltyPoints as $index => $loyaltyPoint)
                <tr>
                    <td>{{ $counter++ }}</td>
                    <td>{{ $loyaltyPoint->loyalty_points }}</td>
                    <td>{{ $loyaltyPoint->created_at->format('j F Y') }}</td>
                    <td>{{ $loyaltyPoint->created_at->format('g:iA') }}</td>
                    <td>{{ $loyaltyPoint->points_type }}</td>
                    <td><span class="badge bg-success">Active</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- add loyality point modal --}}
<div id="loyality_points" class="modal fade" tabindex="-1"
    aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Add Loyalty Coins
                </h5>

                <button type="button" class="close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="loyalty_points">Loyalty Coins:</label>
                    <input type="text" class="form-control"
                        name="loyalty_points" id="loyalty_points"
                        placeholder="Enter Loyalty Coins">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect"
                    data-dismiss="modal">Close</button>
                <button type="button"
                    class="btn btn-primary waves-effect waves-light"
                    onclick="saveLoyaltyPoints({{ $customer->id }})">Save</button>
            </div>
        </div>
    </div><!-- /.modal-dialog -->
</div>