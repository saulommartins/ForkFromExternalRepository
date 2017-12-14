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
	* Página do Oculto Operações de Crédito ARO
    * Data de Criação   : 10/03/2015
    * 
    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Michel Teixeira
    * 
    * @package URBEM
    * @subpackage
    * 
    * @ignore
    * 
    $Id: OCManterOperacoesCreditoARO.php 62529 2015-05-18 17:56:34Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGOperacoesCreditoARO.class.php";

$stPrograma = "ManterOperacoesCreditoARO";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

switch( $stCtrl ){    
    case "preenchePorEntidade":
		$stJs  = 'f.dtContratacao.value 		= "";';
		$stJs .= 'f.nuVlContratado.value		= "";';
		$stJs .= 'f.dtLiquidacaoPrincipal.value = "";';
		$stJs .= 'f.dtLiquidacaoJuros.value 	= "";';
		$stJs .= 'f.dtLiquidacaoEncargos.value 	= "";';
		$stJs .= 'f.nuVlLiquidacao.value 		= "";';

		if($_REQUEST["inCodEntidade"]){
			$obTTCEMGOperacoesCreditoARO =  new TTCEMGOperacoesCreditoARO();
			$obTTCEMGOperacoesCreditoARO->setDado( "exercicio"		, Sessao::getExercicio() 	);
			$obTTCEMGOperacoesCreditoARO->setDado( "cod_entidade"	, $_REQUEST["inCodEntidade"]);
			$obTTCEMGOperacoesCreditoARO->recuperaPorChave($rsOperacao);

			while (!$rsOperacao->eof()) {
				$stJs  = ' f.dtContratacao.value 			= "'.$rsOperacao->getCampo('dt_contratacao').'";';
				$stJs .= ' f.nuVlContratado.value 			= "'.number_format($rsOperacao->getCampo('vl_contratado'), 2, ',', '.').'";';
				$stJs .= ' f.dtLiquidacaoPrincipal.value 	= "'.$rsOperacao->getCampo('dt_principal').'";';
				$stJs .= ' f.dtLiquidacaoJuros.value 		= "'.$rsOperacao->getCampo('dt_juros').'";';
				$stJs .= ' f.dtLiquidacaoEncargos.value 	= "'.$rsOperacao->getCampo('dt_encargos').'";';
				$stJs .= ' f.nuVlLiquidacao.value 			= "'.number_format($rsOperacao->getCampo('vl_liquidacao'), 2, ',', '.').'";';

                $rsOperacao->proximo();
            }
		}
    break;
}

if (isset($stJs)) {
    echo ($stJs);
}
?>
