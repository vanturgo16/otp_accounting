<div class="btn-group" role="group">
    <button id="btnGroupDrop{{ $data->id }}" type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown"
        aria-expanded="false">
        Action <i class="mdi mdi-chevron-down"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu2" aria-labelledby="btnGroupDrop{{ $data->id }}">
        <li><a class="dropdown-item drpdwn" href="#" data-bs-toggle="modal" data-bs-target="#info{{ $data->id }}"><span class="mdi mdi-information"></span> | Info</a></li>
        @if($data->is_used == null)
            <li><a class="dropdown-item drpdwn" href="{{ route('accountcode.edit', encrypt($data->id)) }}"><span class="mdi mdi-file-edit"></span> | Edit</a></li>
            {{-- @if($data->is_active == 0)
                <li><a class="dropdown-item drpdwn-scs" href="#" data-bs-toggle="modal" data-bs-target="#activate{{ $data->id }}"><span class="mdi mdi-check-circle"></span> | Activate</a></li>
            @else
                <li><a class="dropdown-item drpdwn-dgr" href="#" data-bs-toggle="modal" data-bs-target="#deactivate{{ $data->id }}"><span class="mdi mdi-close-circle"></span> | Deactivate</a></li>
            @endif --}}
            
            @if(Auth::user()->role == 'Super Admin')
                <li><a class="dropdown-item drpdwn-dgr" href="#" data-bs-toggle="modal" data-bs-target="#delete{{ $data->id }}"><span class="mdi mdi-delete-alert"></span> | Delete</a></li>
            @endif
        @endif
    </ul>
</div>

{{-- Modal --}}
<div class="left-align truncate-text">
    {{-- Modal Info --}}
    <div class="modal fade" id="info{{ $data->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Info Account Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 mb-2">
                            <div class="form-group">
                                <div><span class="fw-bold">Status :</span></div>
                                <span>
                                    @if($data->is_used == 1)
                                        <span class="badge bg-warning text-white">Running</span>
                                    @else
                                        <span class="badge bg-secondary text-white">Initiate</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-12 mb-2">
                            <div class="form-group">
                                <div><span class="fw-bold">Type :</span></div>
                                <span>
                                    <span>{{ $data->account_type_name }}</span>
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <div><span class="fw-bold">Account Code :</span></div>
                                <span>
                                    <span>{{ $data->account_code }}</span>
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <div><span class="fw-bold">Account Name :</span></div>
                                <span>
                                    <span>{{ $data->account_name }}</span>
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <div><span class="fw-bold">Opening Balance :</span></div>
                                <span>
                                    <span>
                                        {{ number_format($data->opening_balance, 3, ',', '.') }}
                                        @if($data->opening_balance_type == "D")
                                            <span class="badge bg-success text-white"><span class="mdi mdi-plus-circle"></span> | Debit</span>
                                        @else
                                            <span class="badge bg-danger text-white"><span class="mdi mdi-minus-circle"></span> | Kredit</span>
                                        @endif
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="form-group">
                                <div><span class="fw-bold">Balance :</span></div>
                                <span>
                                    <span>
                                        {{ number_format($data->balance, 3, ',', '.') }}
                                        @if($data->balance_type == "D")
                                            <span class="badge bg-success text-white"><span class="mdi mdi-plus-circle"></span> | Debit</span>
                                        @else
                                            <span class="badge bg-danger text-white"><span class="mdi mdi-minus-circle"></span> | Kredit</span>
                                        @endif
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div><span class="fw-bold">Created At :</span></div>
                                <span>
                                    <span>{{ $data->created_at }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @if($data->is_used == null)
        {{-- Modal Activate --}}
        <div class="modal fade" id="activate{{ $data->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Activate Account Code</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('accountcode.activate', encrypt($data->id)) }}" id="formactivate{{ $data->id }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="text-center">
                                Are You Sure to <b>Activate</b> This Account Code?
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success waves-effect btn-label waves-light" id="sb-activate{{ $data->id }}"><i class="mdi mdi-check-circle label-icon"></i>Activate</button>
                        </div>
                    </form>
                    <script>
                        $(document).ready(function() {
                            let idList = "{{ $data->id }}";
                            $('#formactivate' + idList).submit(function(e) {
                                if (!$('#formactivate' + idList).valid()){
                                    e.preventDefault();
                                } else {
                                    $('#sb-activate' + idList).attr("disabled", "disabled");
                                    $('#sb-activate' + idList).html('<i class="mdi mdi-reload label-icon"></i>Please Wait...');
                                }
                            });
                        });
                    </script>
                </div>
            </div>
        </div>

        {{-- Modal Deactivate --}}
        <div class="modal fade" id="deactivate{{ $data->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Deactivate Account Code</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('accountcode.deactivate', encrypt($data->id)) }}" id="formdeactivate{{ $data->id }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="text-center">
                                Are You Sure to <b>Deactivate</b> This Account Code?
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger waves-effect btn-label waves-light" id="sb-deactivate{{ $data->id }}"><i class="mdi mdi-close-circle label-icon"></i>Deactivate</button>
                        </div>
                    </form>
                    <script>
                        $(document).ready(function() {
                            let idList = "{{ $data->id }}";
                            $('#formdeactivate' + idList).submit(function(e) {
                                if (!$('#formdeactivate' + idList).valid()){
                                    e.preventDefault();
                                } else {
                                    $('#sb-deactivate' + idList).attr("disabled", "disabled");
                                    $('#sb-deactivate' + idList).html('<i class="mdi mdi-reload label-icon"></i>Please Wait...');
                                }
                            });
                        });
                    </script>
                </div>
            </div>
        </div>

        {{-- Modal Delete --}}
        <div class="modal fade" id="delete{{ $data->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('accountcode.delete', encrypt($data->id)) }}" id="formdelete{{ $data->id }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <p class="text-center">Are You Sure To Delete This Data?</p>
                                <p class="text-center"><b>{{ $data->account_name }}</b></p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger waves-effect btn-label waves-light" id="sb-delete{{ $data->id }}"><i class="mdi mdi-delete label-icon"></i>Delete</button>
                        </div>
                    </form>
                    <script>
                        $(document).ready(function() {
                            let idList = "{{ $data->id }}";
                            $('#formdelete' + idList).submit(function(e) {
                                if (!$('#formdelete' + idList).valid()){
                                    e.preventDefault();
                                } else {
                                    $('#sb-delete' + idList).attr("disabled", "disabled");
                                    $('#sb-delete' + idList).html('<i class="mdi mdi-reload label-icon"></i>Please Wait...');
                                }
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    @endif
</div>