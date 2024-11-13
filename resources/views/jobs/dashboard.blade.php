@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Job Dashboard</h1>

    <!-- Form for Dispatching New Jobs -->
    <form action="{{ route('jobs.dispatch') }}" method="POST" class="mb-4">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <label for="class_name" class="form-label">Class Name</label>
                <input type="text" name="class_name" id="class_name" class="form-control" required placeholder="e.g. App\Jobs\SampleJob">
            </div>
            <div class="col-md-4">
                <label for="method" class="form-label">Method</label>
                <input type="text" name="method" id="method" class="form-control" required placeholder="e.g. execute">
            </div>
            <div class="col-md-2">
                <label for="priority" class="form-label">Priority</label>
                <input type="number" name="priority" id="priority" class="form-control" value="0" min="1" max="3">
            </div>
            <div class="col-md-2">
                <label for="delay" class="form-label">Delay (seconds)</label>
                <input type="number" name="delay" id="delay" class="form-control" value="0" min="0">
            </div>
            <div class="col-md-2">
                <label for="delay" class="form-label">Retries</label>
                <input type="number" name="retries" id="retries" class="form-control" value="0" min="0">
            </div>
            <div class="col-md-12 mt-2">
                <button type="submit" class="btn btn-primary">Dispatch Job</button>
            </div>
        </div>
    </form>

    <!-- Display Active Jobs -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-responsive">
        <thead>
            <tr>
                <th>Class Name</th>
                <th>Method</th>
                <th>Parameters</th>
                <th>Status</th>
                <th>Retry Count</th>
                <th>Priority</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobs as $job)
                <tr>
                    <td>{{ $job->class_name }}</td>
                    <td>{{ $job->method }}</td>
                    <td>{{ json_encode($job->parameters) }}</td>
                    <td>{{ $job->status }}</td>
                    <td>{{ $job->retry_count }}</td>
                    <td>{{ $job->priority }}</td>
                    <td>{{ $job->created_at }}</td>
                    <td>
                        @if($job->status === 'running')
                            <form action="{{ route('jobs.cancel', $job->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger">Cancel</button>
                            </form>
                        @else
                            <button class="btn btn-secondary" disabled>Cancel</button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
