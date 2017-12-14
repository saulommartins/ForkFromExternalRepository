<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
  * Página Oculta para gerar o arquivo Despesa total com pessoal
  * Data de Criação: 07/01/2015

  * @author Analista:      
  * @author Desenvolvedor: Arthur Cruz
  *
  * @ignore
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/Bimestre.class.php';

include_once CAM_GPC_TCEMG_NEGOCIO."RTCEMGRelatorioDespesaTotalPessoal.class.php";
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';
include_once CLA_MPDF;

$inEntidades = $_REQUEST['inCodEntidade'];

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio', Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaRelacionamentoNomes( $rsEntidade, $stCondicao );

$arEntidades = array();

foreach($inEntidades as $stCodEntidade) {
    $tipoEntidade = SistemaLegado::pegaDado("parametro"
                                           ,"administracao.configuracao"
                                           ,"WHERE cod_modulo = 8 
                                               AND parametro ilike 'cod_entidade%'
                                               AND exercicio = '".Sessao::getExercicio()."'
                                               AND valor = '".$stCodEntidade."';");
    
    $tipoEntidade = trim(substr($tipoEntidade,13,15));
    
    switch($tipoEntidade){
        case "prefeitura":
            $inPrefeitura = SistemaLegado::pegaDado("valor"
                                                   ,"administracao.configuracao"
                                                   ,"WHERE cod_modulo = 8 
                                                       AND parametro ilike 'cod_entidade_prefeitura'
                                                       AND exercicio = '".Sessao::getExercicio()."';");
            $arEntidades[] = $inPrefeitura;
        break;
            
        case "rpps":
            $rpps = SistemaLegado::pegaDado("valor"
                                           ,"administracao.configuracao"
                                           ,"WHERE cod_modulo = 8 
                                               AND parametro ilike 'cod_entidade_rpps'
                                               AND exercicio = '".Sessao::getExercicio()."';");
            $arEntidades[] = $rpps;

             break;
        
        case "camara":
            $inCamara = SistemaLegado::pegaDado("valor"
                                               ,"administracao.configuracao"
                                               ,"WHERE cod_modulo = 8 
                                                   AND parametro ilike 'cod_entidade_camara'
                                                   AND exercicio = '".Sessao::getExercicio()."';");
            $arEntidades[] = $inCamara;
            break;
    }
}

$inCodEntidades = implode(",", $arEntidades);

if(    (in_array($inPrefeitura, $arEntidades) && in_array($rpps, $arEntidades) && in_array($inCamara, $arEntidades))
    || (in_array($inPrefeitura, $arEntidades) && in_array($inCamara, $arEntidades))
    || (in_array($rpps, $arEntidades) && in_array($inCamara, $arEntidades))
){    
    require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    SistemaLegado::alertaAviso("FLRelatorioDespesaTotalPessoal.php?".Sessao::getId(),"Entidade Câmara Municipal deve ser selecionada sozinha.","form","erro", Sessao::getId(), "../");
    exit();
}

$obRTCEMGRelatorioDespesaTotalPessoal = new RTCEMGRelatorioDespesaTotalPessoal();
$stExercicio = Sessao::getExercicio();
$inPeriodo = $request->get('cmbPeriodo');

switch ($request->get('stPeriodicidade')) {
    case 'Mes':
        $stDataInicial = "01/".$inPeriodo."/".$stExercicio;
        $stDataFinal = SistemaLegado::retornaUltimoDiaMes($inPeriodo,$stExercicio);
        $stPeridiocidade = "Mensal";
    break;
    
    case 'Bimestre':
        $stDataInicial = Bimestre::getDataInicial($inPeriodo, $stExercicio);
        $stDataFinal   = Bimestre::getDataFinal($inPeriodo, $stExercicio);
        $stPeridiocidade = "Bimestral";
    break;

    case 'Trimestre':
        $stDataInicial = "01/".str_pad((string)(($inPeriodo*3)-2),2,'0',STR_PAD_LEFT)."/".$stExercicio;
        $stDataFinal   = SistemaLegado::retornaUltimoDiaMes(str_pad((string)($inPeriodo*3),2,'0',STR_PAD_LEFT),$stExercicio);
        $stPeridiocidade = "Trimestral";
    break;

    case 'Quadrimestre':
        $stDataInicial = "01/".str_pad((string)(($inPeriodo*4)-3),2,'0',STR_PAD_LEFT)."/".$stExercicio;
        $stDataFinal   = SistemaLegado::retornaUltimoDiaMes(str_pad((string)($inPeriodo*4),2,'0',STR_PAD_LEFT),$stExercicio);
        $stPeridiocidade = "Quadrimestral";
    break;
    
    case 'Semestre':
        $stDataInicial = "01/".str_pad((string)(($inPeriodo*6)-5),2,'0',STR_PAD_LEFT)."/".$stExercicio;
        $stDataFinal   = SistemaLegado::retornaUltimoDiaMes(str_pad((string)($inPeriodo*6),2,'0',STR_PAD_LEFT),$stExercicio);
        $stPeridiocidade = "Semestral";
    break;
}

$obRTCEMGRelatorioDespesaTotalPessoal->setExercicio    ($stExercicio);
$obRTCEMGRelatorioDespesaTotalPessoal->setCodEntidades ($inCodEntidades);
$obRTCEMGRelatorioDespesaTotalPessoal->setDataInicial  ($stDataInicial);
$obRTCEMGRelatorioDespesaTotalPessoal->setDataFinal    ($stDataFinal);
$obRTCEMGRelatorioDespesaTotalPessoal->setDataFinal    ($stDataFinal);
$obRTCEMGRelatorioDespesaTotalPessoal->setTipoSituacao ($request->get("stSituacao"));
$obRTCEMGRelatorioDespesaTotalPessoal->geraRecordSet   ($rsRecordSet);

$inMes = (int)substr($stDataFinal,3,2);
$inAno = substr($stDataFinal,8,2);
$arCabecalhoMeses = array();
$arMes = array(1 => "JANEIRO", 2 => "FEVEREIRO", 3 => "MARÇO", 4 => "ABRIL", 5 => "MAIO", 6 => "JUNHO", 7 => "JULHO", 8 => "AGOSTO", 9 => "SETEMBRO", 10 => "OUTUBRO", 11 => "NOVEMBRO", 12 => "DEZEMBRO");

for ( $inCount = 1; $inCount <= 12; $inCount++ ) {
        
    if(substr($stDataFinal,3,2) == $inMes){
        $arCabecalhoMeses["mes_".$inCount] = "MÊS BASE <br/>".$arMes[$inMes];
    }else{
        $arCabecalhoMeses["mes_".$inCount] = $arMes[$inMes];
    }
    
    $arCabecalhoMeses["ano_".$inCount] = $inAno;
    
    $inMes = $inMes - 1;
    if ( $inMes == 0 ) {
        $inMes = 12;
        $inAno = $inAno - 1;
    }
}

$arDados = array( "arDespesas"                    => $rsRecordSet["arDespesas"],
                  "arDespesasTotal"               => $rsRecordSet["arDespesasTotal"],
                  "arDespesasExclusoes"           => $rsRecordSet["arDespesasExclusoes"],
                  "arDespesasExclusoesTotal"      => $rsRecordSet["arDespesasExclusoesTotal"],
                  "arValorTotalDespesaPessoal"    => $rsRecordSet["arValorTotalDespesaPessoal"],
                  "arDespesaPessoal2013"    => $rsRecordSet["arDespesas2013"],
                  "arExercicio" => $stExercicio,
                  "arCabecalhoMes"                => $arCabecalhoMeses
                  );

$obMPDF = new FrameWorkMPDF(6,55,15);
$obMPDF->setCodEntidades($inCodEntidades);
$obMPDF->setDataInicio($stDataInicial);
$obMPDF->setDataFinal($stDataFinal." - ".$stPeridiocidade);
$obMPDF->setFormatoFolha("A4-L");
$obMPDF->setNomeRelatorio("Despesa Total com Pessoal");
$obMPDF->setConteudo($arDados);
$obMPDF->gerarRelatorio();

?>