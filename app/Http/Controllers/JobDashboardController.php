<?php

namespace App\Http\Controllers;

use App\BackgroundJobRunner;
use App\Models\Job;
use Illuminate\Http\Request;

class JobDashboardController extends Controller
{
    /**
     * Display a listing of all jobs.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch all jobs ordered by id in descending order and then by created_at in ascending order
        $jobs = Job::orderBy('id', 'desc')->orderBy('created_at', 'asc')->get();

        // Return the view with the fetched jobs
        return view('jobs.dashboard', compact('jobs'));
    }

    /**
     * Cancels a job by updating its status to 'cancelled'.
     *
     * @param int $id The unique identifier of the job to be cancelled.
     *
     * @return \Illuminate\Http\RedirectResponse Redirects to the job dashboard with a success message.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the job with the given ID does not exist.
     */
    public function cancelJob($id)
    {
        $job = Job::findOrFail($id);
        $job->status = 'cancelled';
        $job->save();

        return redirect()->route('dashboard')->with('success', 'Job cancelled successfully.');
    }

    /**
     * Dispatches a new job to the background job runner.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing the job details.
     *
     * @return \Illuminate\Http\RedirectResponse Redirects to the job dashboard with a success message.
     *
     * @throws \Illuminate\Validation\ValidationException If the request data fails validation.
     */
    public function dispatchJob(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string',
            'method' => 'required|string',
            'priority' => 'integer|min:0',
            'delay' => 'integer|min:0',
            'retries' => 'integer|min:0',
        ]);

        // Use dispatch instead of run to queue the job
        BackgroundJobRunner::dispatch(
            $request->class_name,
            $request->method,
            ['param1' => '1'],
            intVal($request->retries),
            intval($request->delay),
            intVal($request->priority),
        );

        return redirect()->route('dashboard')->with('success', 'Job dispatched successfully.');
    }
}
