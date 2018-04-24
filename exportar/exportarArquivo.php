<?php

// define("CAM_GA", "gestaoAdministrativa/fontes/");
// define("CAM_FRAMEWORK", CAM_GA."PHP/framework/");
// define("CAM_FW_EXPORTACAO", CAM_FRAMEWORK."exportacao/");
// define("CLA_EXPORTADOR", CAM_FW_EXPORTACAO."Exportador.class.php" );
 define("CLA_EXPORTADOR","Exportador.class.php" );

 define( "CAM_GPC", "../gestaoPrestacaoContas/fontes/");
 define( "CAM_GPC_TCEMG", CAM_GPC."PHP/TCEMG/");
 define( "CAM_GPC_TCEMG_INSTANCIAS", CAM_GPC_TCEMG."instancias/");


 define( "CAM_GA", "../gestaoAdministrativa/fontes/");
 define( "CAM_ADMINISTRACAO",CAM_GA."PHP/administracao/");
 define( "CAM_GA_ADM_CLASSES",CAM_ADMINISTRACAO."classes/");
 define( "CAM_GA_ADM_MAPEAMENTO",CAM_GA_ADM_CLASSES."mapeamento/");

 define("CAM_FRAMEWORK", CAM_GA."PHP/framework/");
 define("CAM_FW_ARQUIVOS", CAM_FRAMEWORK."arquivos/");
 define("CLA_ARQUIVO_ZIP", CAM_FW_ARQUIVOS."ArquivoZip.class.php");

 define("CAM_FW_ARQUIVOS", CAM_FRAMEWORK."arquivos/");
 define("CLA_ZIP_FILE", CAM_FW_ARQUIVOS."zipfile.class.php");

 define("CAM_FW_EXCECAO", CAM_FRAMEWORK."excecao/");
 // define("CLA_ERRO", CAM_FW_EXCECAO."Erro.class.php");
 define("CLA_ERRO","Erro.class.php");
 define("CLA_EXCECAO", CAM_FW_EXCECAO."Excecao.class.php");

 define("CAM_FW_OBJETO", CAM_FRAMEWORK."objeto/");
 define("CLA_OBJETO", CAM_FW_OBJETO."Objeto.class.php");

define("BD_HOST","localhost");
define("BD_PORT","2345");
define("BD_NAME","urbem");
define("BD_USER","urbem");
define("BD_PASS","j31t0urbem");

define( "CAM_GPC",  "../../../../../../gestaoPrestacaoContas/fontes/" );
define( "CAM_GPC_TCEMG",              CAM_GPC."PHP/TCEMG/" );
define( "CAM_GPC_TCEMG_CLASSES"   ,       CAM_GPC_TCEMG."classes/"             );
define( "CAM_GPC_TCEMG_MAPEAMENTO",       CAM_GPC_TCEMG_CLASSES."mapeamento/"  );


try {
	$parametros = array();
	foreach ($argv as $value) {
		$newValue = explode("=",$value);
		@$parametros[$newValue[0]] =$newValue[1];
	}
	$arFiltro = $parametros;
	validateParameter($parametros, "entidade");
	validateParameter($parametros, "stMes");
	validateParameter($parametros, "exercicio");
	validateParameter($parametros, "arq");

}catch (Exception $e) {
	print($e->getMessage());
}

include_once ("../config.php");
include_once  CLA_ERRO;
include_once CLA_EXPORTADOR;
include_once  CLA_OBJETO;
include_once "Conexao.class.php";
include_once CAM_FRAMEWORK."bancoDados/postgreSQL/CampoTabela.class.php";
include_once CAM_FRAMEWORK."bancoDados/postgreSQL/RecordSet.class.php";
include_once CAM_FRAMEWORK."URBEM/SessaoLegada.class.php";
include_once CAM_FRAMEWORK."URBEM/Sessao.class.php";
include_once CAM_FRAMEWORK."bancoDados/postgreSQL/Persistente.class.php";
include_once "Transacao.class.php";
include_once CAM_FRAMEWORK."bancoDados/postgreSQL/Auditoria.class.php";

Sessao::setExercicio($parametros["exercicio"]);


//Recebe as entidades selecionadas no filtro e concatena elas separando por ','
//$stEntidades    = implode(",",$parametros["entidade"]);
$stEntidades    = $parametros["entidade"];
$stDataFinal= retornaUltimoDiaMes($parametros['stMes'], $parametros["exercicio"]);
if ($parametros['stMes'] < 10) {
   $parametros['stMes']=  str_pad( $parametros['stMes'], 2, '0', STR_PAD_LEFT );
}
$stDataInicial = '01/'.$parametros['stMes'].'/'.$parametros["exercicio"];

$stMes = $parametros['stMes'];


$arArquivosDownload = explode(",",$parametros['arq']);
print_r($arArquivosDownload);
$obExportador = new Exportador();

foreach ($arArquivosDownload  as $stArquivo) {
   $boAddArquivo = TRUE;
   
   if($boAddArquivo){
      $obExportador->addArquivo($stArquivo);
      $stNomeArquivo = trim($stArquivo, '.csv');

      include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".$parametros["exercicio"]
	      ."/".$stArquivo.".inc.php");
   }
}
$obExportador->show();



function validateParameter ($parametros, $name) {
    if (!isset($parametros[$name]) || empty($parametros[$name])) {
        throw new Exception("Invalid parameter for ".$name, 1);
    }
    return;
}

/* Retorna último dia do mês em formato dd/mm/yyyy */
function retornaUltimoDiaMes($inMes, $exercicio)
{
    switch ($inMes) {
        case '01':
            $dt = '31/01/'.$exercicio;
        break;

        case '02':
            $dt = date('d/m/Y', strtotime("-1 days",strtotime('01-03-'.$exercicio)) );
        break;

        case '03':
            $dt = '31/03/'.$exercicio;
        break;

        case '04':
            $dt = '30/04/'.$exercicio;
        break;

        case '05':
            $dt = '31/05/'.$exercicio;
        break;

        case '06':
            $dt = '30/06/'.$exercicio;
        break;

        case '07':
            $dt = '31/07/'.$exercicio;
        break;

        case '08':
            $dt = '31/08/'.$exercicio;
        break;

        case '09':
            $dt = '30/09/'.$exercicio;
        break;

        case '10':
            $dt = '31/10/'.$exercicio;
        break;

        case '11':
            $dt = '30/11/'.$exercicio;
        break;

        case '12':
            $dt = '31/12/'.$exercicio;
        break;
    }

    return $dt;
}

