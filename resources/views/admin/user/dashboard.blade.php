@extends('clients.layouts.app')

@section('main')
    <section class="section-5 bg-2">
        <div class="container py-5">
            <div class="row">
                <div class="col">
                    <nav aria-label="breadcrumb" class=" rounded-3 p-3 mb-4">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Account Settings</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    @include('admin.sidebar')
                </div>
                <div class="col-lg-9">
                    @include('clients.messages')
                    <div class="card border-0 shadow mb-4 p-3">
                        <div class="card-body card-form">
                            <h2>Dashboard page!</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('customJs')
    <script type="text/javascript">
        function deleteJob(id){
            if (confirm("Are you sure you want to delete?")){
                $.ajax({
                    url: '{{route("account.deleteJob")}}',
                    type: 'post',
                    data: {jobId: id },
                    dataType: 'json',
                    success: function (response){
                        window.location.href = '{{route('account.myJob')}}';
                    }
                })
            }
        }
    </script>
@endsection
