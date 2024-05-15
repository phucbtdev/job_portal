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

                    {{-- Form create job --}}
                    <div class="card border-0 shadow mb-4 p-3">
                        <div class="card-body card-form">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="fs-4 mb-1">Jobs</h3>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table ">
                                    <thead class="bg-light">
                                    <tr>
                                        <th scope="col">Title</th>
                                        <th scope="col">Created by</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Applicants</th>
                                        <th scope="col">Status</th>
                                        <th scope="col" class="text-center">Action</th> 
                                    </tr>
                                    </thead>
                                    <tbody class="border-0">
                                    @if ($jobsApplications->isNotEmpty())
                                        @foreach ($jobsApplications as $jobsApplication)
                                            <tr class="active">
                                                <td>
                                                    <div class="job-name fw-500">{{ $jobsApplication->job->title }}</div>
                                                    <div class="info1">{{ $jobsApplication->job->jobType->name }} . {{ $jobsApplication->job->location }}
                                                    </div>
                                                </td>
                                                <td>{{ $jobsApplication->job->user->name}}</td>
                                                <td> <span class="badge rounded-pill bg-secondary"> {{ \Carbon\Carbon::parse($jobsApplication->job->create_at)->format('d M, Y') }}</span></td>
                                                <td>{{$jobsApplication->job->jobApplication->count()}} Applications
                                                <td>
                                                    @if( $jobsApplication->job->status === 1 )
                                                        <span class="badge rounded-pill bg-success">Active</span>
                                                    @else
                                                        <span class="badge rounded-pill bg-danger">Block</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="action-dots text-center">
                                                        <button class="btn" data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a class="dropdown-item"
                                                                   href="{{ route('admin.editJob', $jobsApplication->id) }}"><i
                                                                        class="fa fa-edit" aria-hidden="true"></i>
                                                                    Edit</a></li>
                                                            <li><a class="dropdown-item"  onclick="removeJob({{$jobsApplication->id}})" href="#"><i
                                                                        class="fa fa-trash" aria-hidden="true"></i>
                                                                    Remove</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                {{ $jobsApplications->links() }}
                            </div>
                        </div>
                    </div>
                    {{-- End form create job --}}
                </div>
            </div>
        </div>
    </section>
@endsection

@section('customJs')
    <script type="text/javascript">
        function removeJob(id){
            if (confirm("Are you sure you want to delete?")){
                $.ajax({
                    url: '{{route("admin.removeJob")}}',
                    type: 'delete',
                    data: {id: id },
                    dataType: 'json',
                    success: function (response){
                        window.location.href = '{{route('admin.listJob')}}';
                    }
                })
            }
        }
    </script>
@endsection
