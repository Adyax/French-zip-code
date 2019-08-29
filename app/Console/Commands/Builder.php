<?php

namespace App\Console\Commands;

use App\Cities;
use App\Regions;
use App\Departments;
use App\Traits\GeoCoding;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Builder extends Command
{
    use GeoCoding;

    const CITY_CACHEFILE = 'storage/builder/city_cache.txt';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'builder:build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Launch the build';

    protected $cacheCity = [];
    protected $cacheCityFp = NULL;

    /**
     * The patterns used for explode the files.
     *
     * @var array
     */
    protected $patterns = [
        'regions'     => '/([\d]{2})(?:\t([0-9]+))?(?:\t[\d\w]+){2}(?:.*)\t(.*)/',
        'departments' => '/([\d]{2})\t([\d\w]{2,3})(?:\t[\d\w]+){2}(?:.*)\t(.*)/',
        'cities'      => '/(?:\t|[\d]+\t){1,3}([\d\w]{2,3})\t([\d]{2,3})(?:.*)/',
        'cities_1943'      => '/(?:\d)?\t(?:\d)?\t(?:\d)?\t\d?(\t|[\d]+\t){1,3}([\d\w]{2,3})(?:.*)/',
    ];

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        if (file_exists(static::CITY_CACHEFILE)) {
          $this->cacheCity = file(static::CITY_CACHEFILE);
        }

        $this->cacheCityFp = fopen(static::CITY_CACHEFILE, 'a');
    }

    public function __destruct() {
      fclose($this->cacheCityFp);
    }

  /**
     * Save a new entry of a region.
     *
     * @param array $data
     */
    protected function newEntryRegion(array $data)
    {
        $data = $this->cleanArray($data);
        if (0 != Regions::where('code', '=', $data[1])->count()) {
            return false;
        }

        Regions::create([
            'code' => $data[1],
            'cheflieu' => $data[2],
            'name' => $data[3],
            'slug' => str_slug($data[3], ' '),
        ]);
    }

    /**
     * Save a new entry of a department.
     *
     * @param array $data
     */
    protected function newEntryDepartment(array $data)
    {
        $data = $this->cleanArray($data);
        if (0 != Departments::where('code', '=', $data[2])->count()) {
            return false;
        }

        Departments::create([
            'region_code' => $data[1],
            'code'        => $data[2],
            'name'        => $data[3],
            'slug'        => str_slug($data[3], ' '),
        ]);
    }

    public function cityCacheCheckIsBad($code_insee) {
      if (false !== ($key = array_search($code_insee, $this->cacheCity))) {
        return true;
      }

      return false;
    }

    public function cityCacheWriteBad($code_insee) {
      $this->cacheCity[] = $code_insee;
      fwrite($this->cacheCityFp, $code_insee . "\n");
    }

    public function fgets_csv_utf8($fp, $length = 10000) {
      if ($line = fgets($fp, $length)) {
        $line = iconv('ISO-8859-1', 'UTF8//IGNORE', $line);
        $line = str_getcsv($line, "\t");
      }

      return $line;
    }

    /**
     * Save a new entry of a city.
     *
     * @param array $data
     */
    protected function newEntryCity(array $data)
    {
        $data = $this->cleanArray($data);
        $insee_code = $data[3].$data[4];
        $name = $data[11];

        // Check that this city can't be geocoded.
        if ($this->cityCacheCheckIsBad($insee_code)) {
          return false;
        }

        if (0 != Cities::where('insee_code', '=', $insee_code)->count()) {
            return false;
        }

        if (
          false === ($response = $this->geoCodingCity($insee_code))
          &&
          false === ($response = $this->geocodeByNameViaGoogle($name))
        ) {
            $this->cityCacheWriteBad($insee_code);
            return false;
        }

        $multi = (1 != count($response['codes'])) ?? false;
        foreach ($response['codes'] as $code) {
            Cities::create([
                'department_code' => $data[3],
                'insee_code'      => $insee_code,
                'zip_code'        => $code,
                'name'            => $response['name'],
                'slug'            => str_slug(str_replace(["'", '"', '’'], ' ', $response['name']), ' '),
                'old'             => $data['old'] ?? false,
                'gps_lat'         => $response['lat'],
                'gps_lng'         => $response['lng'],
                'multi'           => $multi,
            ]);
        }

        usleep(500);
    }

    /**
     * Save a new entry of a city.
     *
     * @param array $data
     */
    protected function newEntryCOMCity(string $department_code, array $data)
    {
        if (0 != Cities::where('name', '=', $data['name'])
            ->where('department_code', '=', $department_code)
            ->count()) {
            return false;
        }

        Cities::create([
            'department_code' => $department_code,
            'zip_code'        => $data['zip_code'],
            'name'            => $data['name'],
            'slug'            => str_slug(str_replace(["'", '"', '’'], ' ', $data['name']), ' '),
            'gps_lat'         => $data['lat'],
            'gps_lng'         => $data['lng'],
        ]);
    }

    public function readFile($filepath) {
      $file = file_get_contents($filepath);
      $file = iconv('ISO-8859-1', 'UTF8//IGNORE', $file);
      return $file;
    }

    public static function cleanArrayTrimCb($element) {
      return trim($element, "\r\n\t ");
    }

    public static function cleanArray(array $data) {
      return array_map([static::class, 'cleanArrayTrimCb'], $data);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $file = $this->readFile('storage/builder/regions.txt');
        preg_match_all($this->patterns['regions'], $file, $regions, PREG_SET_ORDER);

        $bar = $this->output->createProgressBar(count($regions));
        foreach ($regions as $data) {
            $this->newEntryRegion($data);
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n".'The regions has been generated');

        $file = $this->readFile('storage/builder/departments.txt');
        preg_match_all($this->patterns['departments'], $file, $departments, PREG_SET_ORDER);

        $bar = $this->output->createProgressBar(count($departments));

        foreach ($departments as $data) {
            $this->newEntryDepartment($data);
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n".'The departments has been generated');


        // $stats = DB::unprepared(file_get_contents('Exports/sql/cities.sql'));
        // DB::statement('ALTER TABLE  `cities` ADD  `multi` BOOLEAN NOT NULL DEFAULT FALSE');
        // return;
        // Cities.
        $filename = 'storage/builder/cities.txt';
        $fp = fopen($filename, 'r');

        //preg_match_all($this->patterns['cities'], $file, $cities, PREG_SET_ORDER);

        $bar = $this->output->createProgressBar(filesize($filename));

        // Skip header.
        $this->fgets_csv_utf8($fp);
        while ($data = $this->fgets_csv_utf8($fp)) {
            $this->newEntryCity($data);
            $bar->setProgress(ftell($fp));
        }

        fclose($fp);

        $bar->finish();
        $this->info("\n".'The cities has been generated');

        // Try importing older cities
        $filename = 'storage/builder/cities_1943.txt';
        $fp = fopen($filename, 'r');
        // preg_match_all($this->patterns['cities_1943'], $file, $cities, PREG_SET_ORDER);

        $bar = $this->output->createProgressBar(filesize($filename));
        $bar->setMessage('Importing cities_1943.txt');

        // Skip header.
        $this->fgets_csv_utf8($fp);
        while ($data = $this->fgets_csv_utf8($fp)) {
          $old_city = [
            3 => $data[5], // Departement
            4 => $data[6], // Commune
            11 => $data[15], // Name of the commune
            'old' => true,
          ];

          try {
            $this->newEntryCity($old_city);
          }
          catch (\Exception $e) {
            // Error.
            $bar->setMessage('Error occured for ' . var_export($old_city, TRUE));
          }

          $bar->setProgress(ftell($fp));
        }

        $bar->finish();
        $this->info("\n".'The cities since 1943 has been generated');

        $multiCities = Cities::where('multi', '=', 1)
            ->with('department')
            ->get();

        $bar = $this->output->createProgressBar(count($multiCities));

        foreach ($multiCities as $city) {
            $response = $this->correctCityGPS($city);
            if (false !== $response) {
                $city->gps_lat = $response['lat'];
                $city->gps_lng = $response['lng'];
            }
            $city->multi = 0;
            $city->save();
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n".'The cities whith multi zip-code have their GPS coordonate corrected');

        $this->newEntryRegion([null,'COM', 0, "Collectivités d'Outre-Mer"]);

        $data = $this->getCOMListe();
        $com_list = $data['data'];

        $bar = $this->output->createProgressBar($data['nbr_entries']);

        foreach ($com_list as $com) {
            $this->newEntryDepartment([null, 'COM', $com['code'], $com['title']]);
            $bar->advance();

            $tries = 0;
            foreach ($com['cities'] as $city) {
                while( ($city_data = $this->getDataCityCOM($com['title'], $city)) === false && $tries < 3) {
                    $tries++;
                    usleep(500);
                }

                if (false === $city_data || null === $city_data) {
                    $tries = 0;
                    while( ($city_data = $this->getDataCityCOM($city)) === false && $tries < 3) {
                        $tries++;
                        usleep(500);
                    }

                    if (false === $city_data || null === $city_data) {
                        $tries = 0;
                        while (($city_data = $this->getDataCityCOM($com['title'])) === false && $tries < 3) {
                            $tries++;
                            usleep(500);
                        }

                        if (false === $city_data || null === $city_data) {
                            dd($city); // Can't Find it so debug : search and patch ;)
                        }
                    }
                }

                $this->newEntryCOMCity($com['code'], $city_data);
                $bar->advance();
            }
        }

        $bar->finish();
        $this->info("\n".'The COM cities has been generated');

        // DB::statement('ALTER TABLE cities DROP COLUMN multi');
    }
}
