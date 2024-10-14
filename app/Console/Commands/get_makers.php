<?php
 
namespace App\Console\Commands;
 
use Illuminate\Console\Command;
use App\Models\Maker;
use function Laravel\Prompts\progress;
 
 
class get_makers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get_makers';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
 
    /**
     * Execute the console command.
     */
    private $progress;
 
    public function handle()
    {
        $this->progress = progress(label: 'Reading file (1/3)', steps: 100);
        $this->progress->start();
        $data = $this->getCsvData('car-db.csv');
        $makers = $this->getMakers($data);
        $this->progress->label = 'Uploading to DB (2/3)';
        for ($i=0; $i < count($makers); $i++) {
            $entity = new Maker();
            $entity->name = $makers[$i];
            $entity->save();
            if($i % floor(count($makers)/33) == 0)$this->progress->advance();
        }
        $this->progress->finish();
    }
 
    private function getMakers($data){
        $makers = [];
        $this->progress->label = 'Getting makers (2/3)';
        for ($i=0; $i < count($data); $i++) {
            $maker = $data[$i][1];
            if(array_search($maker, $makers) === false){
                $makers[] = $maker;
            }
            if($i % floor(count($data)/33) == 0)$this->progress->advance();
        }
        return $makers;
    }
 
    private function getCsvData($filename): array{
        $file = fopen($filename, 'r');
        $result = [];
        $i = 0;
        $totalLines = count(file("car-db.csv"));
        while(!feof($file)){
            $line = fgetcsv($file);
            $result[] = $line;
            if($i++ % floor($totalLines/33)  == 0)$this->progress->advance();
        }
        fclose($file);
 
        return $result;
    }
}