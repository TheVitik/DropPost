<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTemplateRequest;
use App\Http\Resources\TemplateResource;
use App\Models\PostTemplate;
use App\Models\Project;
use Illuminate\Http\Request;

class PostTemplateController extends Controller
{
    public function index(Project $project)
    {
        $templates = $project->postTemplates()->latest()->get();
        return response()->json(new TemplateResource($templates));
    }

    public function store(StoreTemplateRequest $request, Project $project)
    {
        $template = $project->postTemplates()->create($request->validated());
        return response()->json(new TemplateResource($template), 201);
    }

    public function show(Project $project, PostTemplate $postTemplate)
    {
        return response()->json(new TemplateResource($postTemplate));
    }

    public function update(StoreTemplateRequest $request, Project $project, PostTemplate $postTemplate)
    {
        $postTemplate->update($request->validated());
        return response()->json(new TemplateResource($postTemplate));
    }

    public function destroy(Project $project, PostTemplate $postTemplate)
    {
        $postTemplate->delete();
        return response()->json(null, 204);
    }
}
