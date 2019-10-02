<?php


namespace Account\Controllers;


use Account\Models\CommentTemplate;
use Illuminate\Http\Request;

class CommentTemplateController
{

    public function index()
    {
        $templates = CommentTemplate::get();

        return view('account::commentTemplates.index', [
            'templates' => $templates,
        ]);
    }

    public function create()
    {
        return view('account::commentTemplates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'comment' => ['required'],
        ]);

        CommentTemplate::create($data);

        return response()->redirectToRoute('account.comment-templates.index');
    }

    public function edit(int $id)
    {
        $template = CommentTemplate::findOrFail($id);

        return view('account::commentTemplates.edit', [
            'template' => $template,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'comment' => ['required'],
        ]);
        CommentTemplate::where('id', $id)->update($data);

        return response()->redirectToRoute('account.comment-templates.index');
    }

}