<?php

namespace Ttc\GlobalConfig\Http\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use App\Traits\StringHelpers;

class GlobalConfig extends Model
{
    use HasFactory;
    use CentralConnection;
    use StringHelpers;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "global_configs";

    protected $fillable = ['key', 'value'];

    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    protected static function booted() {

        static::saving(function($config_value) {

            $config_value->value = self::maybe_serialize($config_value->value);

            return true;
        });
    }

    public function getValueAttribute($value)
    {

        return self::maybe_unserialize($value);
    }


}
