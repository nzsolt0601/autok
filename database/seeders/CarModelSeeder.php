<?php
 
namespace Database\Seeders;
 
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Maker;
use App\Models\CarModel;
use function Laravel\Prompts\progress;
 
 
class CarModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private $progress;
 
    public function run(): void
    {
       
        $this->progress = progress(label: 'Reading file (1/2)', steps: 100);
        $this->getModels($this->getCsvData('car-db.csv'));
    }
 
   
 
    private function getModels($data){
        $models = [];
        $this->progress->label = 'Getting models (2/2)';
        Log::info("Data count: "  . count($data));
        for ($i=0; $i < count($data); $i++) {
            $model = $data[$i][2];
            if(array_search($model, $models) === false){
                $maker = $data[$i][1];
                $makerId = Maker::where('name', $maker)->first()->id;
                $models[] = $model;
 
                $entity = new CarModel();
                $entity->name = $model;
                $entity->idMaker = $makerId; 
                $entity->save();
            }
            if($i % floor(count($data)/50) == 0)$this->progress->advance();
        }
        return $models;
    }
 
    private function getCsvData($filename): array{
        $file = fopen($filename, 'r');
        $result = [];
        $i = 0;
        $totalLines = count(file("car-db.csv"));
        while(!feof($file)){
            $line = fgetcsv($file);
            $result[] = $line;
            if($i++ % floor($totalLines/50)  == 0)$this->progress->advance();
        }
        fclose($file);
 
        return $result;
    }
}
 