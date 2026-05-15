<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\FlowchartNode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FlowchartController extends Controller
{
    public function index()
    {
        $tree = FlowchartNode::buildTree();
        $admins = Admin::where('is_active', true)->orderBy('first_name')->get();
        $profitSources = FlowchartNode::PROFIT_SOURCES;

        return view('backend.accounting.flowchart.index', compact('tree', 'admins', 'profitSources'));
    }

    public function treeJson(): JsonResponse
    {
        return response()->json([
            'tree' => FlowchartNode::buildTree(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:flowchart_nodes,id',
            'profit_percentage' => 'nullable|numeric|min:0|max:100',
            'profit_source' => ['nullable', Rule::in(FlowchartNode::PROFIT_SOURCES)],
            'admin_id' => 'nullable|exists:admins,id',
            'color' => 'nullable|string|max:7',
        ]);

        $maxSort = FlowchartNode::where('parent_id', $data['parent_id'] ?? null)->max('sort_order') ?? 0;
        $data['sort_order'] = $maxSort + 1;
        $data['profit_percentage'] = $data['profit_percentage'] ?? 0;
        $data['profit_source'] = $data['profit_source'] ?? FlowchartNode::SOURCE_NONE;

        $node = FlowchartNode::create($data);
        $node->load('admin');

        return response()->json([
            'success' => true,
            'node' => [
                'id' => $node->id,
                'title' => $node->title,
                'sort_order' => $node->sort_order,
                'profit_percentage' => (float) $node->profit_percentage,
                'profit_source' => $node->profit_source,
                'profit_source_label' => FlowchartNode::profitSourceLabel($node->profit_source),
                'admin_id' => $node->admin_id,
                'admin_name' => $node->admin?->full_name,
                'color' => $node->color,
                'parent_id' => $node->parent_id,
                'children' => [],
            ],
            'tree' => FlowchartNode::buildTree(),
        ]);
    }

    public function update(Request $request, FlowchartNode $flowchartNode): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'profit_percentage' => 'nullable|numeric|min:0|max:100',
            'profit_source' => ['nullable', Rule::in(FlowchartNode::PROFIT_SOURCES)],
            'admin_id' => 'nullable|exists:admins,id',
            'color' => 'nullable|string|max:7',
        ]);

        $data['profit_percentage'] = $data['profit_percentage'] ?? 0;
        $data['profit_source'] = $data['profit_source'] ?? FlowchartNode::SOURCE_NONE;

        $flowchartNode->update($data);

        return response()->json([
            'success' => true,
            'tree' => FlowchartNode::buildTree(),
        ]);
    }

    public function destroy(FlowchartNode $flowchartNode): JsonResponse
    {
        $this->deleteNodeRecursive($flowchartNode);

        return response()->json([
            'success' => true,
            'tree' => FlowchartNode::buildTree(),
        ]);
    }

    public function move(Request $request, FlowchartNode $flowchartNode): JsonResponse
    {
        $data = $request->validate([
            'new_parent_id' => 'nullable|integer',
        ]);

        $newParentId = $data['new_parent_id'] ?: null;

        if ($newParentId && $this->isDescendant($flowchartNode, $newParentId)) {
            return response()->json([
                'success' => false,
                'message' => 'نمی‌توان نود را به فرزند خودش منتقل کرد.',
            ], 422);
        }

        $maxSort = FlowchartNode::where('parent_id', $newParentId)->max('sort_order') ?? 0;
        $flowchartNode->update([
            'parent_id' => $newParentId,
            'sort_order' => $maxSort + 1,
        ]);

        return response()->json([
            'success' => true,
            'tree' => FlowchartNode::buildTree(),
        ]);
    }

    private function deleteNodeRecursive(FlowchartNode $node): void
    {
        foreach ($node->children as $child) {
            $this->deleteNodeRecursive($child);
        }
        $node->delete();
    }

    private function isDescendant(FlowchartNode $node, int $targetId): bool
    {
        if ($node->id === $targetId) {
            return true;
        }

        foreach ($node->children as $child) {
            if ($this->isDescendant($child, $targetId)) {
                return true;
            }
        }

        return false;
    }
}
