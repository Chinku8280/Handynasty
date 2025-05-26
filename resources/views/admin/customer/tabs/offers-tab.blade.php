<div class="tab-pane" id="pc-4" role="tabpanel">
    <ul class="nav nav-pills nav-tabs-rounded nav-justified" role="tablist">
        <li class="nav-item waves-effect waves-light">
            <a class="nav-link active" data-toggle="tab" href="#home-111"
                role="tab">
                <span class="d-block d-sm-none"><i
                        class="fas fa-home"></i></span>
                <span class="d-none d-sm-block"><i
                        class="fa fa-list-alt me-2"></i>Available
                    Offers</span>
            </a>
        </li>
        <li class="nav-item waves-effect waves-light">
            <a class="nav-link" data-toggle="tab" href="#profile-112"
                role="tab">
                <span class="d-block d-sm-none"><i
                        class="far fa-user"></i></span>
                <span class="d-none d-sm-block"><i
                        class="fa fa-hourglass-end me-2"></i>Offers
                    HIstory</span>
            </a>
        </li>


    </ul>
    <div class="tab-content p-3 text-muted">
        <div class="tab-pane active" id="home-111" role="tabpanel">
            <table id="datatable-buttons"
                class="table  table-bordered example table dt-responsive nowrap w-100">
                <thead class="table-light">
                    <tr>
                        {{-- <th>Offer Image</th> --}}
                        <th>Offer Name</th>
                        <th>Max. Person</th>
                        <th>Discount</th>
                        <th>Branch</th>
                        <th>Starting Date & Time</th>
                        <th>Ending Date & Time</th>
                        <th>Discription</th>
                        <th>Status</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($availableOffers as $offer)
                        <tr>
                            <td>{{ $offer->title }} </td>
                            <td>{{ $offer->max_person }} </td>
                            <td>{{ $offer->discount }}% </td>
                            <td>{{ \App\Location::find($offer->branch_id)->name }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($offer->start_date_time)->format('j F Y h:iA') }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($offer->end_date_time)->format('j F Y h:iA') }}
                            </td>
                            <td>{{ $offer->description }} </td>
                            <td><span class="badge bg-success">Active</span>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
        <div class="tab-pane" id="profile-112" role="tabpanel">
            <table id="datatable-buttons"
                class="table  table-bordered example table dt-responsive nowrap w-100">
                <thead class="table-light">
                    <tr>
                        <th>Offer Name</th>
                        <th>Max. Person</th>
                        <th>Discount</th>
                        <th>Branch</th>
                        <th>Starting Date & Time</th>
                        <th>Ending Date & Time</th>
                        <th>Discription</th>
                        <th>Status</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($claimedOffers as $offer)
                        <tr>
                            <td>{{ $offer->title }} </td>
                            <td>{{ $offer->max_person }} </td>
                            <td>{{ $offer->discount }}% </td>
                            <td>{{ \App\Location::find($offer->branch_id)->name }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($offer->start_date_time)->format('j F Y h:iA') }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($offer->end_date_time)->format('j F Y h:iA') }}
                            </td>
                            <td>{{ $offer->description }} </td>
                            <td><span class="badge bg-danger">Used</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>