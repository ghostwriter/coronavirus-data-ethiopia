<?php

declare(strict_types=1);

namespace COVID19Ethiopia;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

(static function () {
    if (file_exists($a = __DIR__ . '/vendor/autoload.php')) {
        require $a;
    } elseif (file_exists($a = __DIR__ . '/../../../autoload.php')) {
        require $a;
    } elseif (file_exists($a = __DIR__ . '/../vendor/autoload.php')) {
        require $a;
    } elseif (file_exists($a = __DIR__ . '/../autoload.php')) {
        require $a;
    } else {
        fwrite(STDERR, 'Cannot locate autoloader; please run "composer install"' . PHP_EOL);
        exit(1);
    }

    file_put_contents('./README.md', collect([
        '# Coronavirus cases in Ethiopia [![GitHub Actions status](https://github.com/COVID19Ethiopia/coronavirus-data/workflows/live/badge.svg)](https://github.com/COVID19Ethiopia/coronavirus-data/actions)',
            PHP_EOL . 'Collection of Coronavirus cases and patients data for Ethiopia.',
            
            PHP_EOL . '> On March 10, 2023, the [Johns Hopkins CSSE](https://github.com/CSSEGISandData/COVID-19) discontinued its collecting and reporting of global COVID-19 data. [commit](https://github.com/CSSEGISandData/COVID-19/commit/4360e50239b4eb6b22f3a1759323748f36752177)',
            
        PHP_EOL . '| Date | Active | Confirmed | Deaths | Recovered |',
        '|---|---|---|---|---|',
    ])->merge(
        collect(json_decode(file_get_contents('./live.json')))
            ->sortByDesc('Date')
            ->map(function ($item) {
                return collect($item)->only(
                    'Date',
                    'Active',
                    'Confirmed',
                    'Deaths',
                    'Recovered',
                )->mapWithKeys(function ($item, $key) {
                    if ($key === 'Date') {
                        return collect()->put(
                            $key,
                            Carbon::parse($item)->toDateString()
                        );
                    }

                    return collect()->put($key, $item);
                });
            })->map(function (Collection $item) {
                return sprintf(
                    '| %s | %s | %s | %s | %s |',
                    $item->get('Date'),
                    $item->get('Active'),
                    $item->get('Confirmed'),
                    $item->get('Deaths'),
                    $item->get('Recovered')
                );
            })
    )->join(PHP_EOL));
})();
