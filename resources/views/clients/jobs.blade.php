@extends('clients.layouts.app')

@section('main')
    <section class="section-3 py-5 bg-2 ">
        <div class="container">
            <div class="row">
                <div class="col-6 col-md-10 ">
                    <h2>Find Jobs</h2>
                </div>
                <div class="col-6 col-md-2">
                    <div class="align-end">
                        <select name="sort" id="sort" class="form-control">
                            <option {{Request::get('sort') == '0' ? 'selected': ''}}  value="0">Latest</option>
                            <option {{Request::get('sort') == '1' ? 'selected': ''}}  value="1">Oldest</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row pt-5">
                <div class="col-md-4 col-lg-3 sidebar mb-4">
                    <form action="" name="searchForm" id="searchForm">
                        <div class="card border-0 shadow p-4">
                            <div class="mb-4">
                                <h2>Keywords</h2>
                                <input value="{{ Request::get('keyword')  }}" type="text" name="keyword" id="keyword" placeholder="Keywords" class="form-control">
                            </div>

                            <div class="mb-4">
                                <h2>Location</h2>
                                <input value="{{Request::get('location')}}" type="text" name="location" id="location" placeholder="Location" class="form-control">
                            </div>

                            <div class="mb-4">
                                <h2>Category</h2>
                                <select name="category" id="category" class="form-control">
                                    <option value="">Select a Category</option>
                                    @if($categories->isNotEmpty())
                                        @foreach($categories as $category)
                                            <option {{Request::get('category') == $category->id ? "selected" : ''}} value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="mb-4">
                                <h2>Job Type</h2>
                                @if($jobTypes->isNotEmpty())
                                    @foreach($jobTypes as $jobType)
                                        <div class="form-check mb-2">
                                            <input {{ (in_array($jobType->id,$jobTypeArray)) ? 'checked' : '' }} class="form-check-input" name="job_type" type="checkbox" value="{{$jobType->id}}" id="job-type-{{$jobType->id}}">
                                            <label class="form-check-label" for="job-type-{{$jobType->id}}">{{$jobType->name}}</label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="mb-4">
                                <h2>Experience</h2>
                                <select name="experience" id="experience" class="form-control">
                                    <option  value="">Select Experience</option>
                                    <option {{Request::get('experience') == '1' ? 'selected' : ''}}  value="1">1 Year</option>
                                    <option {{Request::get('experience') == '2' ? 'selected' : ''}}  value="2">2 Years</option>
                                    <option {{Request::get('experience') == '3' ? 'selected' : ''}}  value="3">3 Years</option>
                                    <option {{Request::get('experience') == '4' ? 'selected' : ''}}  value="4">4 Years</option>
                                    <option {{Request::get('experience') == '5' ? 'selected' : ''}}  value="5">5 Years</option>
                                    <option {{Request::get('experience') == '6' ? 'selected' : ''}}  value="6">6 Years</option>
                                    <option {{Request::get('experience') == '7' ? 'selected' : ''}}  value="7">7 Years</option>
                                    <option {{Request::get('experience') == '8' ? 'selected' : ''}}  value="8">8 Years</option>
                                    <option {{Request::get('experience') == '9' ? 'selected' : ''}}  value="9">9 Years</option>
                                    <option {{Request::get('experience') == '10' ? 'selected' : ''}} value="10">10 Years</option>
                                    <option {{Request::get('experience') == '10_plus' ? 'selected' : ''}}  value="10_plus">10+ Years</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary mb-3">Search</button>
                            <a href="{{route('jobs')}}" class="btn btn-dark">Reset</a>
                        </div>
                    </form>
                </div>
                <div class="col-md-8 col-lg-9 ">
                    <div class="job_listing_area">
                        <div class="job_lists">
                            <div class="row">
                                @if($jobs->isNotEmpty())
                                    @foreach($jobs as $job)
                                        <div class="col-md-4">
                                            <div class="card border-0 p-3 shadow mb-4">
                                                <div class="card-body">
                                                    <h3 class="border-0 fs-5 pb-2 mb-0">{{ $job->title}}</h3>
                                                    <p>{{Str::words( $job->description, 4)}}</p>
                                                    <div class="bg-light p-3 border">
                                                        <p class="mb-0">
                                                            <span class="fw-bolder"><i class="fa fa-map-marker" style="width: 10px"></i></span>
                                                            <span class="ps-1">{{ $job->location}}</span>
                                                        </p>
                                                        <p class="mb-0">
                                                            <span class="fw-bolder"><i class="fa fa-clock-o" style="width: 10px"></i></span>
                                                            <span class="ps-1">{{ $job->jobType->name}}</span>
                                                        </p>
                                                        <p class="mb-0">
                                                            <span class="fw-bolder"><i class="fa fa-map-marker" style="width: 10px"></i></span>
                                                            <span class="ps-1">{{ $job->experience}} years</span>
                                                        </p>
                                                        <p class="mb-0">
                                                            <span class="fw-bolder"><i class="fa fa-map-marker" style="width: 10px"></i></span>
                                                            <span class="ps-1">{{ $job->jobType->name}}</span>
                                                        </p>
                                                        <p class="mb-0">
                                                            <span class="fw-bolder"><i class="fa fa-map-marker" style="width: 10px"></i></span>
                                                            <span class="ps-1">{{ $job->category->name}}</span>
                                                        </p>

                                                        @if(!is_null($job->salary))
                                                            <p class="mb-0">
                                                                <span class="fw-bolder"><i class="fa fa-usd" style="width: 10px"></i></span>
                                                                <span class="ps-1">{{$job->salary }}</span>
                                                            </p>
                                                        @else
                                                            <p class="mb-0">
                                                                <span class="fw-bolder"><i class="fa fa-usd"></i></span>
                                                                <span class="ps-1">None</span>
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <div class="d-grid mt-3">
                                                        <a href="job-detail.html" class="btn btn-primary btn-lg">Details</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div>
                            {{$jobs->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('customJs')
    <script type="text/javascript">
        $("#searchForm").submit(function(e) {
            e.preventDefault();
            var url =  '{{ route('jobs') }}?';

            var keyword = $('#keyword').val();
            var location = $('#location').val();
            var category = $('#category').val();
            var experience = $('#experience').val();
            var sort = $('#sort').val();
            var checkedJobTypes = $("input:checkbox[name='job_type']:checked").map(function (){
                return $(this).val();
            }).get();

            if (keyword != "" ){
                url += '&keyword='+keyword;
            }

            if (location != "" ){
                url += '&location='+location;
            }

            if (category != "" ){
                url += '&category='+category;
            }

            if (experience != "" ){
                url += '&experience='+experience;
            }

            if (checkedJobTypes.length >0){
                url += '&jobType='+checkedJobTypes;
            }

            url += '&sort='+sort;

            window.location.href = url;
        });

        $("#sort").change(function (){
            $("#searchForm").submit();
        });
    </script>
@endsection
