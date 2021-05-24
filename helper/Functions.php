<?php 
class Functions {

	public const EXPLIMPIARIDFISCAL = [ '/[^0-9 -]/', '/[ -]+/', '/^-|-$/' ];

	public static $diasFeriados = [
													'01-01', //Año nuevo
													'04-19', //Declaración de la Independencia,
													'05-01', //Día del trabajador
													'06-24', //Batalla de Carabobo
													'07-05', //Día de la independencia
													'07-24', //Natalicio de Simón Bolívar
													'10-12', //Día de la Resistencia Indígena
													'12-24', //Víspera de Navidad
													'12-25', //Navidad
													'12-31', //Fiesta de Fin de Año
												 ];
	
	/**
	 * [formatearFecha Formatea la fecha Y-m-d]
	 * @param  [string] $fecha [description]
	 * @return [string]        [description]
	 */
	public static function formatearFecha($fecha) {

		if ( strpos($fecha, '/') ) {

			$fechaArray = explode('/', $fecha);

			$dia  = $fechaArray[0];
			$mes  = $fechaArray[1];
			$year = $fechaArray[2];

			$fecha = $year . '-' . $mes . '-' . $dia;

		}

		return $fecha;

	}

	/**
	 * [diferenciaEntreFechas Obtiene la diferencia entre 2 fechas]
	 * @param  [string] $fechaInicial    [description]
	 * @param  [string] $fechaFinal      [description]
	 * @param  string $formatoDiferencia [ % = Literal %; %
	 *                                   	 Y = Años, numérico, al menos 2 dígitos empezando con 0; 01, 03
	 *                                   	 y = Años, numérico; 1, 3
	 *                                   	 M = Meses, numérico, al menos 2 dígitos empezando con 0; 01, 03, 12
	 *                                   	 m = Meses, numérico; 1, 3, 12
	 *                                   	 D = Días, numérico, al menos 2 dígitos empezando con 0; 01, 03, 31
	 *                                   	 d = Días, numérico; 1, 3, 31
	 *                                   	 a = Número total de días como resultado de una operación con DateTime::diff(), o de lo contrario (unknown); 4, 18, 8123
	 *                                   	 H = Horas, numérico, al menos 2 dígitos empezando con 0; 01, 03, 23
	 *                                   	 h = Horas, numérico; 1, 3, 23
	 *                                   	 I = Minutos, numérico, al menos 2 dígitos empezando con 0; 01, 03, 59
	 *                                   	 i = Minutos, numérico; 1, 3, 59
	 *                                   	 S = Segundos, numérico, al menos 2 dígitos empezando con 0; 01, 03, 57
	 *                                   	 s = Segundos, numérico; 1, 3, 57
	 *                                   	 R = Signo "-" cuando es negativo, "+" cuando es positivo; -, +
	 *                                   	 r = Signo "-" cuando es negativo, vacío cuando es positivo; - ]
	 * @return [string]                  [description]
	 */
	public static function diferenciaEntreFechas($fechaInicial, $fechaFinal, $formatoDiferencia = 'a') {

		$fechaInicial = new \DateTime( self::formatearFecha($fechaInicial) );
		$fechaFinal   = new \DateTime( self::formatearFecha($fechaFinal) );

		//Diferencia de fechas y formato
		return $fechaInicial->diff($fechaFinal)->format("%$formatoDiferencia%");

	}

	/**
	 * [contarDiasNoHabiles Cuenta los días NO hábiles entre 2 fechas]
	 * @param  [string] $fechaInicial [description]
	 * @param  [string] $fechaFinal   [description]
	 * @return [array]  $arrayDias    [Número de días No hábiles (Sábados y Domingos)]
	 */
	public static function contarDiasNoHabiles($fechaInicial, $fechaFinal/*, $fechaHoy*/) {

		//Contadores de días
		$feriados = 0;
		$sabados  = 0;
		$domingos = 0;

		$msg          = '';
		$diasFeriados = [];

		$fechaInicial = new \DateTime( self::formatearFecha($fechaInicial) );
		$fechaFinal   = new \DateTime( self::formatearFecha($fechaFinal) );

		while ( $fechaInicial <= $fechaFinal ) {

			( $fechaInicial->format('l') === 'Saturday' ) ? $sabados++  : $sabados  += 0;
			( $fechaInicial->format('l') === 'Sunday' )   ? $domingos++ : $domingos += 0;

			//Si está en el array y es sábado o domingo (Se restan esos días)
			if ( in_array($fechaInicial->format('m-d'), self::$diasFeriados ) ) {

				$feriados++;

				if ( $fechaInicial->format('l') === 'Saturday' ) {
					$sabados--;
					$msg .= '* El sábado ' . $fechaInicial->format('Y-m-d') . ' fue feriado ';
				}

				if ( $fechaInicial->format('l') === 'Sunday' ) {
					$domingos--;
					$msg .= '* El domingo ' . $fechaInicial->format('Y-m-d') . ' fue feriado ';
				}

				array_push($diasFeriados, $fechaInicial->format('Y-m-d'));

			}

			$fechaInicial->modify('+1 days'); //Incremento de la fecha inicial

		}

		return $arrayDias = ['feriados'     => (int)$feriados,
												 'sabados'      => (int)$sabados, 
												 'domingos'     => (int)$domingos,
												 'totalDias'    => (int)($feriados + $sabados + $domingos),
												 'diasFeriados' => [$diasFeriados],
												 'msg'          => $msg];

	}

	public static function checkHoliday($date){
        if(date('l', strtotime($date)) == 'Saturday'){
            return false;
        //   return "Saturday";
        }else if(date('l', strtotime($date)) == 'Sunday'){
            return true;
        //   return "Sunday";
        }else{
          $receivedDate = date('d M', strtotime($date));
      
          $holiday = array(
            '01 Jan' => 'New Year Day',
            '02 Apr' => 'Viernes Santo',
            '03 Apr' => 'Sabado Santo',
            '01 May' => 'Dia del Trabajador',
            '15 May' => 'Eleccion Alcaldes',
            '16 May' => 'Eleccion Alcaldes',
            '13 Jun' => 'Segunda Vuelta Gobernadores Regionales',
            '28 Jun' => 'San Pedro y San Pablo',
            '16 Jul' => 'Dia de la Virgen del Carmen',
            '18 Jul' => 'Elecciones Primarias Presidenciales',
            '15 Ago' => 'Asuncion de la Virgen', 
            '17 Sep' => 'Feriado Adicional',
            '18 Sep' => 'Independencia Nacional',
            '19 Sep' => 'Dia de las Glorias del Ejercito',
            '11 Oct' => 'Dia de la Raza',
            '31 Oct' => 'Dia de las Iglesias Evangélicas', 
            '01 Nov' => 'Dia de Todos los Santos',
            '21 Nov' => 'Elecciones Presidenciales y Parlamentarias',
            '08 Dec' => 'Inmaculada Concepcion',
            '19 Dec' => 'Segunda Vuelta Elecciones Presidenciales',
            '25 Dec' => 'Navidad',
            '31 Dec' => 'Feriado Bancario'
          );
      
          foreach($holiday as $key => $value){
            if($receivedDate == $key){
                return true;
            //   return $value;
            }
          }
        }
      }

	

}

// var_dump(Functions::contarDiasNoHabiles('01/01/2017', '31/12/2017'));

?>