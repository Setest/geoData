<?php
namespace App\Console\Commands;

use Config;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Chumper\Zipper\Zipper;
use App\Helpers\GeoDataHelpers;
// use Log;

/**
 * GeoData.
 *
 * CLI app for update local geoData databases
 *
 * @author Prishepenko Stepan <itman116@gmail.com>
 *
 * TODO: add lock file for run handle() add send data to mattermost in sendLog();
 */
class GeoData extends Command
{
    private $errors = [];

    /**
     * @var FilesystemAdapter $disk
     */
    private $disk;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geodata:download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download and update last DB for GeoData';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->disk = &self::getDiskInstance();
        $this->disk->exists('tmp') && $this->disk->deleteDirectory('tmp');
        $this->disk->makeDirectory('tmp');
        $this->disk->exists('sypexGeo') ?: $this->disk->makeDirectory('sypexGeo');
        parent::__construct();
    }

    /**
     * Test current database
     *
     * @return bool
     */
    private function testData(): bool
    {
        $result = true;
        try {
            $data = GeoDataHelpers::get('8.8.8.8');
            if (!$data || !is_array($data) || !$data['country']) {
                throw new \Exception("Result is not set");
            }

            if (!isset($data['country']['iso']) || strtoupper($data['country']['iso']) !== 'US') {
                $this->errors[] = "Probably the DB data is not correct: must be 'US', returned '" . $data['country']['iso'] . "'";
            }
        } catch (\Exception $e) {
            $result = false;
        }
        return $result;
    }

    /**
     * Test downloaded temporary database
     *
     * @return bool
     */
    private function testTmpData(): bool
    {
        Config::set('geodata.sypexgeo.config.path', '/storage/app/geoData/tmp/');
        return $this->testData();
    }

    /**
     * Download database
     *
     * @return string download data
     */
    private function download(): string
    {
        $url = config('geodata.sypexgeo.url');
        if (!$url) {
            throw new \Exception("Download URL is not set");
        }

        $client = new Client();
        $response = $client->request('GET', $url);

        $content = $response->getBody()->getContents();
        if ($response->getStatusCode() !== 200 || !$content) {
            throw new \Exception("Bad request from server, with url '$url'");
        }
        return $content;
    }

    /**
     * Log and notification
     *
     * @param string $msg
     * @return void
     */
    private function sendLog(string $msg = ''): void
    {
        if ($this->errors) {
            $msg .= implode("\n", $this->errors);
        }
        if ($msg) {
            // TODO: add notification
            dump($msg);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        try {
            $content = $this->download();

            $temporaryDirectory = (new TemporaryDirectory())->create();
            $tmpFilePath = $temporaryDirectory->path('data.zip');

            file_put_contents($tmpFilePath, $content);

            $zipper = new Zipper;
            $zipper->make($tmpFilePath)->extractTo($temporaryDirectory->path() . '/unpack/');

            $storageTmpPath = $this->disk->path('tmp/');

            \File::move($temporaryDirectory->path() . '/unpack/', $storageTmpPath);

            // проверяем идет ли тестирование по IP
            if ($this->testTmpData()) {
                Config::set('disable_asserts', false);
                // т.к. у нас могут содержаться другие файлы БД, то мы не можем их удалять
                foreach ($this->disk->files('tmp/') as $file) {
                    $this->disk->delete('sypexGeo/' . basename($file));
                    $this->disk->move($file, 'sypexGeo/' . basename($file));
                }
            }
        } catch (\Exception $e) {
            fwrite(STDERR, $e->getMessage());
            $this->errors[] = $e->getMessage();
        } finally {
            $this->sendLog();
            $this->disk->deleteDirectory('tmp');
        }
    }

    /**
     * Get storage disk
     * @see Laravel File Storage
     *
     * @return Filesystem|FilesystemAdapter
     */
    public static fun&ction getDiskInstance()
    {
        return Storage::disk('geoData');
    }
}
