@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-left">
                        <a href="{{ route('generalledger.index') }}" class="btn btn-light waves-effect btn-label waves-light">
                            <i class="mdi mdi-arrow-left label-icon"></i> Back To List General Ledger
                        </a>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Accounting</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('generalledger.index') }}">General Ledger</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        @include('layouts.alert')

        <form action="{{ route('generalledger.store') }}" id="formstore" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header text-center py-3">
                            <h5 class="mb-0">Create New</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Transaction Number</label><label style="color: darkred">*</label>
                                    <input type="text" class="form-control" placeholder="Input Transaction Number.." name="transaction_number" value="" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Transaction Date</label><label style="color: darkred">*</label>
                                    <input type="date" class="form-control" name="transaction_date" value="" required>
                                </div>
                                <hr>
                                <div class="col-12">
                                    <label class="form-label">Source</label><label style="color: darkred">*</label>
                                </div>
                                <div class="col-6 mb-3">
                                    <select class="form-select js-example-basic-single" style="width: 100%" name="source" required>
                                        <option value="" selected>-- Select --</option>
                                        @foreach($source as $item)
                                            <option value="{{ $item->name_value }}">{{ $item->name_value }}</option>
                                        @endforeach
                                        <option disabled>──────────</option>
                                        <option value="AddNew">Add New Source</option>
                                    </select>
                                </div>
                                <div class="col-6 mb-3">
                                    <input type="text" name="addsource" class="form-control" placeholder="Input New Source" required>
                                </div>
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        $("input[name='addsource']").hide();
        
                                        $(document.body).on("change.select", "select[name^='source']", function () {
                                            var source = $(this).val();
        
                                            if(source=="AddNew"){
                                                $("input[name='addsource']").show();
                                                $('input[name="addsource"]').attr("required", true);
                                            }
                                            else{
                                                $("input[name='addsource']").hide();
                                                $('input[name="addsource"]').attr("required", false);
                                            }
                                        });
                                    });
                                </script>

                                <div class="col-lg-12 mt-3">
                                    <div class="card">
                                        <div class="card-header text-center">
                                            <h6 class="mb-0">Transaction</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-repsonsive">
                                                <table class="table table-bordered" id="dynamicTable">
                                                    <thead>
                                                        <tr>
                                                            <th>Account Code</th>
                                                            <th>Nominal</th>
                                                            <th>Debit / Kredit</th>
                                                            <th style="text-align:center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <select class="form-select js-example-basic-single addpayment" style="width: 100%" name="addmore[0][account_code]" required>
                                                                    <option value="">- Select Account Code -</option>
                                                                    @foreach( $accountcodes as $item )
                                                                        <option value="{{ $item->id }}">{{ $item->account_code }} - {{ $item->account_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control rupiah-input addpayment" style="width: 100%" placeholder="Input Amount.." name="addmore[0][nominal]" value="" required>
                                                            </td>
                                                            <td>
                                                                <select class="form-select js-example-basic-single addpayment" style="width: 100%" name="addmore[0][type]" required>
                                                                    <option value="">- Select Type -</option>
                                                                    <option value="Debit">Debit</option>
                                                                    <option value="Kredit">Kredit</option>
                                                                </select>
                                                            </td>
                                                            <td style="text-align:center"><button type="button" name="add" id="adds" class="btn btn-success"><i class="fas fa-plus"></i></button></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 align-right">
                                    <a href="{{ route('generalledger.index') }}" type="button" class="btn btn-light waves-effect btn-label waves-light">
                                        <i class="mdi mdi-arrow-left-circle label-icon"></i>Back
                                    </a>
                                    <button type="submit" class="btn btn-success waves-effect btn-label waves-light" name="sb">
                                        <i class="mdi mdi-plus-box label-icon"></i>Create
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Validation Form --}}
<script>
    document.getElementById('formstore').addEventListener('submit', function(event) {
        if (!this.checkValidity()) {
            event.preventDefault();
            return false;
        }
        var submitButton = this.querySelector('button[name="sb"]');
        submitButton.disabled = true;
        submitButton.innerHTML  = '<i class="mdi mdi-reload label-icon"></i>Please Wait...';
        return true;
    });
</script>

@endsection