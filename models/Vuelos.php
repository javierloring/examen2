<?php

namespace app\models;

/**
 * This is the model class for table "vuelos".
 *
 * @property int $id
 * @property string $codigo
 * @property int $origen_id
 * @property int $destino_id
 * @property int $compania_id
 * @property string $salida
 * @property string $llegada
 * @property string $plazas
 * @property string $precio
 *
 * @property Reservas[] $reservas
 * @property Aeropuertos $origen
 * @property Aeropuertos $destino
 * @property Companias $compania
 */
class Vuelos extends \yii\db\ActiveRecord
{
    public $plazas_libres;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vuelos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['origen_id', 'destino_id', 'compania_id', 'salida', 'llegada', 'plazas', 'precio'], 'required'],
            [['origen_id', 'destino_id', 'compania_id'], 'default', 'value' => null],
            [['origen_id', 'destino_id', 'compania_id', 'plazas'], 'integer', 'min' => 0],
            [['salida', 'llegada'], 'safe'],
            [['precio'], 'number'],
            [['codigo'], 'string', 'max' => 6],
            [['codigo'], 'match', 'pattern' => '/^[A-Z]{2}\d{4}$/'],
            [['codigo'], 'unique'],
            [['origen_id'], 'exist', 'skipOnError' => true, 'targetClass' => Aeropuertos::className(), 'targetAttribute' => ['origen_id' => 'id']],
            [['destino_id'], 'exist', 'skipOnError' => true, 'targetClass' => Aeropuertos::className(), 'targetAttribute' => ['destino_id' => 'id']],
            [['compania_id'], 'exist', 'skipOnError' => true, 'targetClass' => Companias::className(), 'targetAttribute' => ['compania_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Código',
            'origen_id' => 'Origen ID',
            'destino_id' => 'Destino ID',
            'compania_id' => 'Compania ID',
            'salida' => 'Salida',
            'llegada' => 'Llegada',
            'plazas' => 'Plazas',
            'precio' => 'Precio',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReservas()
    {
        return $this->hasMany(Reservas::className(), ['vuelo_id' => 'id'])->inverseOf('vuelo');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrigen()
    {
        return $this->hasOne(Aeropuertos::className(), ['id' => 'origen_id'])->inverseOf('vuelosOrigen');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestino()
    {
        return $this->hasOne(Aeropuertos::className(), ['id' => 'destino_id'])->inverseOf('vuelosDestino');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompania()
    {
        return $this->hasOne(Companias::className(), ['id' => 'compania_id'])->inverseOf('vuelos');
    }

    public static function find()
    {
        return parent::find()
        ->select([
            'vuelos.*',
            'plazas - COUNT(r.id) AS plazas_libres',
            ])
        ->joinwith(['reservas r'])
        ->groupBy('vuelos.id');
    }

    public function getAsientosLibres()
    {
        $ocupados = $this->getReservas()->select('asiento')->column();
        $total = range(1, $this->plazas);
        $libres = array_diff($total, $ocupados);
        return array_combine($libres, $libres);
    }
}
