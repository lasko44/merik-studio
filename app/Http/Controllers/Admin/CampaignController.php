<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaigns\StoreCampaignRequest;
use App\Http\Requests\Campaigns\UpdateCampaignRequest;
use App\Models\EmailCampaign;
use App\Models\EmailSubscriber;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Handles email campaign management.
 */
class CampaignController extends Controller
{
    /**
     * Display list of campaigns.
     */
    public function index(): Response
    {
        $this->authorize('campaigns.view');

        return Inertia::render('Admin/Campaigns/Index', [
            'campaigns' => EmailCampaign::with('creator')
                ->orderByDesc('created_at')
                ->paginate(15),
            'statistics' => [
                'total' => EmailCampaign::count(),
                'draft' => EmailCampaign::draft()->count(),
                'sent' => EmailCampaign::sent()->count(),
                'subscribers' => EmailSubscriber::subscribed()->count(),
            ],
        ]);
    }

    /**
     * Show create campaign form.
     */
    public function create(): Response
    {
        $this->authorize('campaigns.create');

        return Inertia::render('Admin/Campaigns/Create', [
            'subscriberCount' => EmailSubscriber::subscribed()->count(),
        ]);
    }

    /**
     * Store a new campaign.
     */
    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        $campaign = EmailCampaign::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
            'status' => EmailCampaign::STATUS_DRAFT,
        ]);

        return redirect()
            ->route('admin.campaigns.edit', $campaign)
            ->with('success', 'Campaign created successfully.');
    }

    /**
     * Show edit campaign form.
     */
    public function edit(EmailCampaign $campaign): Response
    {
        $this->authorize('campaigns.update');

        return Inertia::render('Admin/Campaigns/Edit', [
            'campaign' => $campaign->load('creator'),
            'subscriberCount' => EmailSubscriber::subscribed()->count(),
        ]);
    }

    /**
     * Update a campaign.
     */
    public function update(UpdateCampaignRequest $request, EmailCampaign $campaign): RedirectResponse
    {
        $campaign->update($request->validated());

        return redirect()
            ->route('admin.campaigns.index')
            ->with('success', 'Campaign updated successfully.');
    }

    /**
     * Delete a campaign.
     */
    public function destroy(EmailCampaign $campaign): RedirectResponse
    {
        $this->authorize('campaigns.delete');

        $campaign->delete();

        return redirect()
            ->route('admin.campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }
}
