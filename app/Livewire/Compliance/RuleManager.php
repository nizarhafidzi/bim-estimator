<?php

namespace App\Livewire\Compliance;

use App\Actions\RunComplianceCheck;
use App\Models\Project;
use App\Models\RuleSet;
use App\Models\ComplianceRule;
use App\Models\ModelElement; // Tambahkan ini
use App\Imports\RulesImport;
use App\Exports\RuleTemplateExport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class RuleManager extends Component
{
    use WithFileUploads;

    public $project;
    public $ruleSets;
    public $activeRuleSetId;

    // Data Suggestion (Saran Otomatis)
    public $suggestedCategories = [];
    public $suggestedParameters = [];

    // Form Create Rule Set
    public $name, $description;
    public $showCreateModal = false;

    // Form Import
    public $excelFile;

    // Form Manual Rule
    public $isEditing = false;
    public $showModal = false;
    public $ruleId;
    public $category, $param, $operator, $val, $desc;

    // Validation Target
    public $targetFileId;
    public $lastCheckDate = null;
    public $hasResults = false;

    public function mount($projectId)
    {
        $this->project = Project::findOrFail($projectId);
        $this->loadRuleSets();
        
        // LOAD SARAN OTOMATIS
        $this->loadSuggestions();
    }

    // --- LOGIC BARU: AUTO SUGGEST ---
    public function loadSuggestions()
    {
        // 1. Ambil Kategori Unik dari Project ini
        $this->suggestedCategories = ModelElement::where('project_id', $this->project->id)
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->toArray();

        // 2. Ambil Parameter (Sampling 50 elemen acak agar cepat)
        // Kita tidak perlu cek jutaan elemen, cukup sampel saja karena parameternya biasanya seragam
        $samples = ModelElement::where('project_id', $this->project->id)
            ->whereNotNull('raw_properties')
            ->inRandomOrder()
            ->limit(50)
            ->get();

        $params = [];
        foreach ($samples as $el) {
            if (is_array($el->raw_properties)) {
                foreach ($el->raw_properties as $group => $props) {
                    if (is_array($props)) {
                        // Ambil key (nama parameter)
                        foreach (array_keys($props) as $key) {
                            $params[$key] = true; // Pakai key array agar unik otomatis
                        }
                    }
                }
            }
        }
        
        // Sortir A-Z
        $this->suggestedParameters = collect(array_keys($params))->sort()->values()->toArray();
    }

    public function loadRuleSets()
    {
        $this->ruleSets = RuleSet::where('project_id', $this->project->id)
            ->orderBy('created_at', 'desc')
            ->get();

        if (!$this->activeRuleSetId && $this->ruleSets->isNotEmpty()) {
            $this->activeRuleSetId = $this->ruleSets->first()->id;
        }
    }

    // --- RULE SET MANAGEMENT ---
    public function createRuleSet()
    {
        $this->validate(['name' => 'required|string|max:255']);

        $set = RuleSet::create([
            'project_id' => $this->project->id,
            'name' => $this->name,
            'description' => $this->description
        ]);

        $this->activeRuleSetId = $set->id;
        $this->reset(['name', 'description', 'showCreateModal']);
        $this->loadRuleSets();
        session()->flash('message', 'Rule Set created successfully.');
    }

    public function deleteRuleSet($id)
    {
        RuleSet::find($id)->delete();
        $this->activeRuleSetId = null;
        $this->loadRuleSets();
    }

    // --- EXCEL OPERATIONS ---
    public function downloadTemplate()
    {
        return Excel::download(new RuleTemplateExport, 'compliance_rules_template.xlsx');
    }

    public function importRules()
    {
        $this->validate([
            'excelFile' => 'required|mimes:xlsx,xls',
            'activeRuleSetId' => 'required'
        ]);

        try {
            Excel::import(new RulesImport($this->activeRuleSetId), $this->excelFile);
            $this->reset('excelFile');
            session()->flash('message', 'Rules imported successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Import Failed: ' . $e->getMessage());
        }
    }

    // --- MANUAL RULE CRUD ---
    public function openModal()
    {
        $this->resetInput();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function editRule($id)
    {
        $rule = ComplianceRule::findOrFail($id);
        $this->ruleId = $rule->id;
        $this->category = $rule->category_target;
        $this->param = $rule->parameter;
        $this->operator = $rule->operator;
        $this->val = $rule->value;
        $this->desc = $rule->description;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function saveRule()
    {
        $this->validate([
            'category' => 'required',
            'param' => 'required',
            'operator' => 'required',
            'val' => 'required'
        ]);

        $data = [
            'rule_set_id' => $this->activeRuleSetId,
            'category_target' => $this->category,
            'parameter' => $this->param,
            'operator' => $this->operator,
            'value' => $this->val,
            'description' => $this->desc
        ];

        if ($this->isEditing) {
            ComplianceRule::find($this->ruleId)->update($data);
            session()->flash('message', 'Rule updated.');
        } else {
            ComplianceRule::create($data);
            session()->flash('message', 'Rule created.');
        }

        $this->showModal = false;
        $this->resetInput();
    }

    public function deleteRule($id)
    {
        ComplianceRule::find($id)->delete();
    }

    private function resetInput()
    {
        $this->reset(['category', 'param', 'operator', 'val', 'desc', 'ruleId', 'isEditing']);
    }

    // --- VALIDATION EXECUTION ---
    
    public function updatedTargetFileId()
    {
        $this->reset(['lastCheckDate', 'hasResults']);

        if ($this->targetFileId) {
            $lastResult = \App\Models\ValidationResult::where('project_file_id', $this->targetFileId)
                            ->latest()
                            ->first();

            if ($lastResult) {
                $this->hasResults = true;
                $this->lastCheckDate = $lastResult->created_at->format('d M Y, H:i');
            }
        }
    }

    public function runValidation(RunComplianceCheck $action)
    {
        $this->validate([
            'activeRuleSetId' => 'required',
            'targetFileId' => 'required'
        ]);

        $count = $action->execute($this->targetFileId, $this->activeRuleSetId);
        
        $this->updatedTargetFileId(); // Refresh status
        session()->flash('message', "Validation Complete! Processed $count elements.");
    }

    public function render()
    {
        $rules = [];
        if ($this->activeRuleSetId) {
            $rules = ComplianceRule::where('rule_set_id', $this->activeRuleSetId)->get();
        }

        return view('livewire.compliance.rule-manager', [
            'rules' => $rules
        ])->layout('layouts.app');
    }
}