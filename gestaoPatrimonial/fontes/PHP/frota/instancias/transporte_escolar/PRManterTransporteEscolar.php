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
    * Data de Criação: 11/04/2014

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Michel Teixeira

    $Id: PRManterTransporteEscolar.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaTransporteEscolar.class.php" );


//Define o nome dos arquivos PHP
$stPrograma = "ManterTransporteEscolar";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$inCodVeiculo = (isset($_REQUEST['inCodVeiculo'])&&$_REQUEST['inCodVeiculo']!='') ? $_REQUEST['inCodVeiculo'] : 0;
$inCgmEscola = (isset($_REQUEST['inCgmEscola'])&&$_REQUEST['inCgmEscola']!='') ? $_REQUEST['inCgmEscola'] : 0;
	
$arMes = array();
for($i=1;$i<13;$i++){
	$arMes[($i-1)]['mes'] = $i;
	$arMes[($i-1)]['passageiros'] = $_REQUEST['inPassageiros_'.$i];
	$arMes[($i-1)]['distancia'] = $_REQUEST['inDistancia_'.$i];
	$arMes[($i-1)]['dias_rodados'] = $_REQUEST['inDias_'.$i];
	$arMes[($i-1)]['cod_turno'] = $_REQUEST['inNumTurno_'.$i];
}


Sessao::setTrataExcecao ( true );
$obErro = new Erro();

if ($inCodVeiculo==0)
	$obErro->setDescricao('Código do Veículo inválido!');

if ($inCgmEscola==0&&!$obErro->ocorreu())
	$obErro->setDescricao('CGM Escola inválido!');

if (!$obErro->ocorreu()) {
	$count = 0;
	for($i=0;$i<count($arMes);$i++){
		if($arMes[$i]['passageiros']!=''||$arMes[$i]['distancia']!=''||$arMes[$i]['dias_rodados']!=''||$arMes[$i]['cod_turno']!=''){
			if($arMes[$i]['passageiros']==''||$arMes[$i]['distancia']==''||$arMes[$i]['dias_rodados']==''||$arMes[$i]['cod_turno']==''){
				$obErro->setDescricao('Preencha todos os itens do mês('.$arMes[$i]['mes'].')!');
				break;
			}
			else{
				$ItemMes=true;
				$arMesFiltrado[$count]['mes'] = $arMes[$i]['mes'];
				$arMesFiltrado[$count]['passageiros'] = $arMes[$i]['passageiros'];
				$arMesFiltrado[$count]['distancia'] = $arMes[$i]['distancia'];
				$arMesFiltrado[$count]['dias_rodados'] = $arMes[$i]['dias_rodados'];
				$arMesFiltrado[$count]['cod_turno'] = $arMes[$i]['cod_turno'];
				$count++;
			}
		}
	}
}

if(!$ItemMes&&!$obErro->ocorreu())
	$obErro->setDescricao('Nenhum mês preenchido!');

if (!$obErro->ocorreu()) {
	$obTFrotaTransporteEscolar = new TFrotaTransporteEscolar();
	$obTFrotaTransporteEscolar->setDado( 'cod_veiculo'	, $inCodVeiculo 			);
	$obTFrotaTransporteEscolar->setDado( 'cgm_escola'	, $inCgmEscola 				);
	$obTFrotaTransporteEscolar->setDado( 'exercicio' 	, Sessao::getExercicio() 		);
	
	$obErro = $obTFrotaTransporteEscolar->exclusao();
	
	if(!$obErro->ocorreu()){
		for($i=0;$i<count($arMesFiltrado);$i++){
			$obTFrotaTransporteEscolar->setDado( 'cod_veiculo'	, $inCodVeiculo 			);
			$obTFrotaTransporteEscolar->setDado( 'cgm_escola'	, $inCgmEscola 				);
			$obTFrotaTransporteEscolar->setDado( 'exercicio' 	, Sessao::getExercicio() 		);
			$obTFrotaTransporteEscolar->setDado( 'mes' 		, $arMesFiltrado[$i]['mes'] 		);
			$obTFrotaTransporteEscolar->setDado( 'passageiros' 	, $arMesFiltrado[$i]['passageiros'] 	);
			$obTFrotaTransporteEscolar->setDado( 'distancia' 	, $arMesFiltrado[$i]['distancia'] 	);
			$obTFrotaTransporteEscolar->setDado( 'dias_rodados' 	, $arMesFiltrado[$i]['dias_rodados']	);
			$obTFrotaTransporteEscolar->setDado( 'cod_turno' 	, $arMesFiltrado[$i]['cod_turno'] 	);
		
			$obErro = $obTFrotaTransporteEscolar->inclusao();
		}
	}
}

if( $obErro->ocorreu() )
	sistemaLegado::exibeAviso( urlencode($obErro->getDescricao()), "unica","erro" );
else
	sistemaLegado::alertaAviso($pgFilt."?stAcao=manter", "Veículo:".$inCodVeiculo.' / CGM Escola:'.$inCgmEscola , 'incluir',"aviso", Sessao::getId(), "../");

Sessao::encerraExcecao();

?>
