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
/*
    * Oculto de Apostila de Contrato TCEMG
    * Data de Criação   : 06/05/2014
    
    * @author Analista      Silvia Martins Silva
    * @author Desenvolvedor Michel Teixeira
    
    * @package URBEM
    * @subpackage
    
    * @ignore
    
    $Id: OCManterApostilaContrato.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stPrograma = "ManterApostilaContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

switch( $stCtrl ){
    case "carregaLista":
	if($_REQUEST['inNumContrato']!=''&&$_REQUEST['inCodEntidade']!=''&&$_REQUEST['stExercicioContrato']!=''&&$_REQUEST['inCodApostila']!=''&&$_REQUEST['stExercicioApostila']!=''){
	    include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContrato.class.php' );
            $obTTCEMGContrato = new TTCEMGContrato;
            $stFiltro  = " WHERE nro_contrato   =  ".$_REQUEST['inNumContrato'];
            $stFiltro .= " AND exercicio        = '".$_REQUEST['stExercicioContrato']."'";
            $stFiltro .= " AND cod_entidade     = '".$_REQUEST['inCodEntidade']."'";
            $obTTCEMGContrato->recuperaTodos($rsContrato, $stFiltro);

	    if($rsContrato->inNumLinhas==1){
		include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoApostila.class.php' );
		$obTTCEMGContratoApostila 	= new TTCEMGContratoApostila;
		
		$stFiltro  = " WHERE cod_contrato   	=  ".$rsContrato->getCampo('cod_contrato');
		$stFiltro .= "   AND exercicio		= '".$_REQUEST['stExercicioApostila']."'";
		$stFiltro .= "   AND cod_entidade      	=  ".$_REQUEST['inCodEntidade'];
		$stFiltro .= "   AND cod_apostila      	=  ".$_REQUEST['inCodApostila'];
		$obTTCEMGContratoApostila->recuperaTodos($rsApostila, $stFiltro);

		if($rsApostila->inNumLinhas==1){
		    $stJs .= "f.inCodApostilaAtual.value	= '".$rsApostila->getCampo('cod_apostila')."';					\n";
		    $stJs .= "f.inCodApostila.value		= '".$rsApostila->getCampo('cod_apostila')."';					\n";
		    $stJs .= "f.inCodTipoApostila.value		= '".$rsApostila->getCampo('cod_tipo')."';					\n";
		    $stJs .= "f.dtApostila.value		= '".$rsApostila->getCampo('data_apostila')."';					\n";
		    $stJs .= "f.inCodTipoAlteracaoApostila.value= '".$rsApostila->getCampo('cod_alteracao')."';					\n";
		    $stJs .= "f.stDscApostila.value		= '".$rsApostila->getCampo('descricao')."';					\n";
		    $stJs .= "f.nuVlApostila.value		= '".number_format($rsApostila->getCampo('valor_apostila'),2,',','.')."';	\n";
		    
		    if($rsApostila->getCampo('cod_alteracao')==3){
			$stJs .= "f.nuVlApostila.disabled = true;	\n";
			$stJs .= "f.nuVlApostila.value = '0,00';	\n"; 
		    }
		}
	    }
	}

	echo $stJs;
    break;

    case "liberaValorApostila":
	if(isset($_REQUEST['inCodTipoAlteracaoApostila'])&&$_REQUEST['inCodTipoAlteracaoApostila']==3){
	    $stJs  = "f.nuVlApostila.disabled = true; ";
	    $stJs .= "f.nuVlApostila.value = '0,00'; "; 
	}else{
	    $stJs  = "f.nuVlApostila.disabled = false; ";
	    $stJs .= "f.nuVlApostila.value = ''; "; 
	}
	
	echo $stJs;
    break;
}
?>
