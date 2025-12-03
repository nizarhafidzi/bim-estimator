<?php

namespace App\Actions;

use App\Models\ProjectFile;
use App\Models\RuleSet;
use App\Models\ValidationResult;
use App\Models\ModelElement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RunComplianceCheck
{
    public function execute($projectFileId, $ruleSetId)
    {
        $file = ProjectFile::findOrFail($projectFileId);
        $ruleSet = RuleSet::with('rules')->findOrFail($ruleSetId);

        // 1. Bersihkan hasil validasi lama untuk file & ruleset ini
        // Agar tidak duplikat setiap kali run check
        ValidationResult::where('project_file_id', $file->id)
            ->whereIn('rule_id', $ruleSet->rules->pluck('id'))
            ->delete();

        $resultsToInsert = [];

        // 2. Loop Setiap Aturan (Rule)
        foreach ($ruleSet->rules as $rule) {
            
            // 3. Ambil Elemen yang Relevan dengan Rule (Filter by Category)
            // Menggunakan LIKE agar fleksibel. Contoh: Rule "Walls" akan mengambil "Basic Wall", "Curtain Wall"
            $elements = ModelElement::where('project_file_id', $file->id)
                        ->where('category', 'LIKE', '%' . $rule->category_target . '%')
                        ->get();

            foreach ($elements as $element) {
                // 4. Ambil Nilai Aktual dari Properti JSON
                $actualValue = $this->findPropertyValue($element->raw_properties, $rule->parameter);
                
                // Jika parameter tidak ditemukan di elemen ini, skip atau tandai fail (tergantung kebijakan)
                // Di sini kita anggap fail dengan pesan "Parameter not found"
                if ($actualValue === null) {
                    $status = 'fail';
                    $msg = "Parameter '{$rule->parameter}' not found on this element.";
                    $actualValueStr = 'N/A';
                } else {
                    // 5. Lakukan Perbandingan (Logic Inti)
                    $isPass = $this->compare($actualValue, $rule->operator, $rule->value);
                    $status = $isPass ? 'pass' : 'fail';
                    $msg = $isPass ? 'Compliance OK' : "Value '{$actualValue}' failed condition {$rule->operator} {$rule->value}";
                    $actualValueStr = (string) $actualValue;
                }

                // Siapkan Data Insert
                $resultsToInsert[] = [
                    'project_id' => $file->project_id,
                    'project_file_id' => $file->id,
                    'rule_id' => $rule->id,
                    'model_element_id' => $element->id,
                    'status' => $status,
                    'actual_value' => substr($actualValueStr, 0, 255), // Potong jika kepanjangan
                    'message' => $msg,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // 6. Batch Insert ke Database (Chunking agar hemat memori)
        foreach (array_chunk($resultsToInsert, 500) as $chunk) {
            ValidationResult::insert($chunk);
        }

        return count($resultsToInsert);
    }

    /**
     * Helper: Mencari nilai parameter dalam struktur JSON Autodesk yang dalam
     * JSON Autodesk biasanya: Group -> Property Name -> Value
     */
    private function findPropertyValue($properties, $paramName)
    {
        if (!is_array($properties)) return null;

        foreach ($properties as $groupName => $props) {
            if (!is_array($props)) continue;

            // Cek apakah nama parameter ada di group ini?
            // Case insensitive search
            foreach ($props as $key => $val) {
                if (strcasecmp($key, $paramName) == 0) {
                    return $this->cleanValue($val);
                }
            }
        }
        return null;
    }

    /**
     * Helper: Membersihkan nilai (Misal: "1200 mm" jadi 1200)
     */
    private function cleanValue($val)
    {
        // Jika string mengandung angka dan satuan, coba ambil angkanya saja
        // Contoh: "2.5 m^2" -> 2.5
        if (is_string($val) && preg_match('/^[\d\.]+/', $val)) {
            // Cek apakah ini murni angka atau teks biasa
            // Jika user membandingkan teks (misal "Level 1"), kita biarkan string
            // Tapi jika operator matematika, kita butuh angka.
            // Untuk amannya, kita return string aslinya dulu, nanti dikonversi saat compare.
        }
        return $val;
    }

    /**
     * Helper: Logika Perbandingan
     */
    private function compare($actual, $operator, $target)
    {
        // Bersihkan satuan unit (misal "1200 mm" -> 1200) untuk operasi matematika
        $numericActual = floatval(preg_replace('/[^\d\.]/', '', $actual));
        $numericTarget = floatval(preg_replace('/[^\d\.]/', '', $target));
        
        // Cek apakah perbandingan angka atau teks?
        $isNumericCompare = is_numeric($target) && in_array($operator, ['>', '<', '>=', '<=']);

        if ($isNumericCompare) {
            $act = $numericActual;
            $tgt = $numericTarget;
        } else {
            $act = strtolower((string)$actual);
            $tgt = strtolower((string)$target);
        }

        switch ($operator) {
            case '=': return $act == $tgt;
            case '!=': return $act != $tgt;
            case '>': return $act > $tgt;
            case '<': return $act < $tgt;
            case '>=': return $act >= $tgt;
            case '<=': return $act <= $tgt;
            case 'contains': return str_contains($act, $tgt);
            case 'not_contains': return !str_contains($act, $tgt);
            default: return false;
        }
    }
}