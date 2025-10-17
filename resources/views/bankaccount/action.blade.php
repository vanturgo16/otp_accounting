<div class="text-center">
    <button type="button" class="btn btn-sm btn-primary waves-effect btn-label waves-light" data-bs-toggle="modal" data-bs-target="#update{{ $data->id }}">
        <i class="mdi mdi-update label-icon"></i> Update
    </button>
</div>

{{-- Modal Add --}}
<div class="modal fade" id="update{{ $data->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Update Bank Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bankaccount.update', encrypt($data->id)) }}" id="formupdate{{ $data->id }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="max-height: 65vh; overflow:auto">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Bank Name</label><label style="color: darkred">*</label>
                                <input type="text" class="form-control" name="bank_name" value="{{ $data->bank_name ?? '' }}"  placeholder="Input Bank Name.." required>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Account Name</label><label style="color: darkred">*</label>
                                <input type="text" class="form-control" name="account_name" value="{{ $data->account_name ?? '' }}"  placeholder="Input Account Name.." required>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Account Number</label><label style="color: darkred">*</label>
                                <input type="text" class="form-control" name="account_number" value="{{ $data->account_number ?? '' }}"  placeholder="Input Account Number.." required>
                            </div>
                        </div>
                        @php
                            $selectedCurrencyName = $data->currency ?? '';
                        @endphp
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Currency</label><label style="color: darkred">*</label>
                                <select class="form-control" name="currency" required>
                                    <option value="">- Choose -</option>
                                    @foreach($currencies as $item)
                                        <option value="{{ $item->currency_code }}" {{ $selectedCurrencyName == $item->currency_code ? 'selected' : '' }}>{{ $item->currency_code }} - {{ $item->currency }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Swift Code</label><label style="color: darkred">*</label>
                                <input type="text" class="form-control" name="swift_code" value="{{ $data->swift_code ?? '' }}"  placeholder="Input Swift Code.." required>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Branch</label><label style="color: darkred">*</label>
                                <input type="text" class="form-control" name="branch" value="{{ $data->branch ?? '' }}"  placeholder="Input Branch.." required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success waves-effect btn-label waves-light" id="sb{{ $data->id }}"><i class="mdi mdi-update label-icon"></i>Update</button>
                </div>
            </form>
            <script>
                $(document).ready(function() {
                    let idList = "{{ $data->id }}";
                    $('#formupdate' + idList).submit(function(e) {
                        if (!$('#formupdate' + idList).valid()){
                            e.preventDefault();
                        } else {
                            $('#sb' + idList).attr("disabled", "disabled");
                            $('#sb' + idList).html('<i class="mdi mdi-reload label-icon"></i>Please Wait...');
                        }
                    });
                });
            </script>
        </div>
    </div>
</div>