<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CinemaSeeder extends Seeder
{

    protected $cinemas
        = [
            [
                'name'         => 'Cineplexx Big Beograd',
                'address'      => 'Višnjička 84',
                'city'         => 'Beograd',
                'phone'        => '+381 11 40 40 780',
                'crawler'      => 'CinaplexxCrawler',
                'page_url'     => 'http://www.cineplexx.rs/filmovi/u-bioskopu',
                'soon_url'     => 'http://www.cineplexx.rs/service/program.php?type=upcoming',
                'crawler_link' => 'http://www.cineplexx.rs/service/program.php?type=program&centerId=616&date=*&sorting=alpha&undefined=Svi&view=detail&page=1'
            ],
            [
                'name'         => 'Cineplexx Delta City',
                'address'      => 'Jurija Gagarina 16/16A',
                'city'         => 'Beograd',
                'phone'        => '+381 11 2203 400',
                'crawler'      => 'CinaplexxCrawler',
                'page_url'     => 'http://www.cineplexx.rs/filmovi/u-bioskopu',
                'soon_url'     => 'http://www.cineplexx.rs/service/program.php?type=upcoming',
                'crawler_link' => 'http://www.cineplexx.rs/service/program.php?type=program&centerId=611&date=*&sorting=alpha&undefined=Svi&view=detail&page=1'
            ],
            [
                'name'         => 'Cineplexx Kragujevac Plaza',
                'address'      => 'Bulevar kraljice Marije 56',
                'city'         => 'Kragujevac',
                'phone'        => '+381 34 619 50 30',
                'crawler'      => 'CinaplexxCrawler',
                'page_url'     => 'http://www.cineplexx.rs/filmovi/u-bioskopu',
                'soon_url'     => 'http://www.cineplexx.rs/service/program.php?type=upcoming',
                'crawler_link' => 'http://www.cineplexx.rs/service/program.php?type=program&centerId=612&date=*&sorting=alpha&undefined=Svi&view=detail&page=1'
            ],
            [
                'name'         => 'Cineplexx Niš',
                'address'      => 'Bulevar Medijana 21',
                'city'         => 'Niš',
                'phone'        => '+381 18 300 340',
                'crawler'      => 'CinaplexxCrawler',
                'page_url'     => 'http://www.cineplexx.rs/filmovi/u-bioskopu',
                'soon_url'     => 'http://www.cineplexx.rs/service/program.php?type=upcoming',
                'crawler_link' => 'http://www.cineplexx.rs/service/program.php?type=program&centerId=615&date=*&sorting=alpha&undefined=Svi&view=detail&page=1'
            ],
            [
                'name'         => 'Cineplexx Ušće Shopping Centar',
                'address'      => 'Bulevar Mihajla Pupina 4',
                'city'         => 'Beograd',
                'phone'        => '+381 11 311 33 70',
                'crawler'      => 'CinaplexxCrawler',
                'page_url'     => 'http://www.cineplexx.rs/filmovi/u-bioskopu',
                'soon_url'     => 'http://www.cineplexx.rs/service/program.php?type=upcoming',
                'crawler_link' => 'http://www.cineplexx.rs/service/program.php?type=program&centerId=614&date=*&sorting=alpha&undefined=Svi&view=detail&page=1'
            ],
            [
                'name'         => 'Tuckwood',
                'address'      => 'Kneza Miloša 7a',
                'city'         => 'Beograd',
                'phone'        => '+381 11 32 36 517',
                'crawler'      => 'TuckCrawler',
                'page_url'     => 'http://tuck.rs/cms/view.php?id=109',
                'soon_url'     => 'http://tuck.rs/cms/view.php?id=102',
                'crawler_link' => 'http://tuck.rs/cms/view.php?id=109'
            ],
            [
                'name'         => 'Fontana',
                'address'      => 'Pariske komune 13',
                'city'         => 'Beograd',
                'phone'        => '+381 11 2670469',
                'crawler'      => 'FontanaCrawler',
                'page_url'     => 'http://www.bioskopfontana.rs/#home',
                'soon_url'     => 'http://www.bioskopfontana.rs/#home',
                'crawler_link' => 'http://www.bioskopfontana.rs/#home'
            ],
            [
                'name'         => 'Roda Cineplex',
                'address'      => 'Požeška 83a',
                'city'         => 'Beograd',
                'phone'        => '+381 11 254 52 60',
                'crawler'      => 'RodaCrawler',
                'page_url'     => 'http://www.rodacineplex.com/repertoar',
                'soon_url'     => 'http://www.rodacineplex.com/uskoro',
                'crawler_link' => 'http://www.rodacineplex.com/repertoar'
            ]
        ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i<count($this->cinemas); $i++)
        {
             $this->cinemas[$i]['created_at'] = Carbon::now()->toDateTimeString();
             $this->cinemas[$i]['updated_at'] = Carbon::now()->toDateTimeString();
        }

        DB::table('cinemas')->insert($this->cinemas);
    }
}
