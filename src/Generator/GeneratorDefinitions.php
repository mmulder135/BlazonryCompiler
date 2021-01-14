<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Generator;

class GeneratorDefinitions
{
    const BASESHIELD = '
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="600" height="660" id="Shield">
            <defs id="Herald">
                <path d="M3,3 V260.637C3,369.135,46.339,452.459,99.763,514 C186.238,614.13,300,657,300,657 C300,657,413.762,614.13,500.237,514 C553.661,452.459,597,369.135,597,260.637V3Z" id="Shield1"/>
            </defs>
            <g>
                <use id="Background" xlink:href="#Shield1"/>
            </g></svg>
    ';

    const MASK = '
        <mask id="Mask">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#Shield1" fill="white"/>
        </mask>
    ';

    const SINISTERTRANSFORM = 'scale(-1,1) translate(-600,0)';

    private const COLORS = [
        'or' => '#e3d800',
        'argent' => '#fafafa',
        'azure' => '#0036b6',
        'purpure' => '#b6008f',
        'sable' => '#0a0a0a',
        'vert' => '#2ab600',
        'gules' => '#b60000',
        'error' => '#808080'
    ];

    private const FURS = [
        "vair" => '
            <path id="vair" fill="#0036b6" d="m62.875.5-31.187 29.95v59.9l-31.188 29.95h124.75l-31.188-29.95v-59.9z"/>
          ',
        "ermine" => '
            <g id="ermine">
                <circle cx="40" cy="55" r="10" />
                <circle cx="60" cy="55" r="10" />
                <circle cx="50" cy="37" r="10" />
                <path transform="scale(2)" d="M25,30c0,0,0,0-0.1,0v0.1l-3.8,22.365l-4.9,15.705l0,0l-3.3,7.058v0.1v0.099c0.1,0,0.1,0,0.1,0l3.9-1.889 l2.1-2.386l2.2-3.379l3.5,12.922l-0.1,0.497l0,0l0.1,0.1c0,0,0,0,0.1,0h0.2h0.1c0-0.1,0-0.1,0.1-0.1v-0.099l-0.2-0.398l3.7-12.922 l2.1,3.379l2.1,2.386h0.1l3.7,1.889l0,0c0.1-0.099,0.1-0.099,0.2-0.099v-0.1l-3.3-7.058l0,0l-4.9-15.705l-3.5-22.365l-0.1-0.1 H256.2z"/>
            </g>
        ',
    ];

    private const PARTITIONMASKS = [
        "bend" => '0,0 600,660 600,0',
        "pale" => '0,0 300,0 300,660 0,660',
    ];

    private const ORDINARIES = [
        "bend" => '0,0 150,0 600,510 600,660 450,660 0,150',
        "pale" => '200,0 400,0 400,660 200,660',
    ];

    private const CANBESINISTER = [
        "bend"
    ];

    public static function getColor(string $color): ?string
    {
        if (array_key_exists($color, self::COLORS)) {
            return self::COLORS[$color];
        }
        return null;
    }

    public static function getFurDefinition(string $fur): ?string
    {
        if (array_key_exists($fur, self::FURS)) {
            return self::FURS[$fur];
        }
        return null;
    }

    public static function getPartitionMaskPoints(string $partition): ?string
    {
        if (array_key_exists($partition, self::PARTITIONMASKS)) {
            return self::PARTITIONMASKS[$partition];
        }
        return null;
    }

    public static function getOrdinaryPoints(string $ordinary): ?string
    {
        if(array_key_exists($ordinary,self::ORDINARIES)) {
            return self::ORDINARIES[$ordinary];
        }
        return null;
    }

    public static function canBeSinister(string $partition): bool
    {
        return in_array($partition, self::CANBESINISTER);
    }
}
