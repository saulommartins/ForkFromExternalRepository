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
    * Processaento de Rescisão de Contrato TCEMG
    * Data de Criação   : 05/05/2014
    
    * @author Analista      Silvia Martins Silva
    * @author Desenvolvedor Michel Teixeira
    
    * @package URBEM
    * @subpackage
    
    * @ignore
    
    $Id: PRRescindirContrato.php 59612 2014-09-02 12:00:51Z gelson $
*/
include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php'        );
include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php'  );
include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContrato.class.php'              );
include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoEmpenho.class.php'       );
include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoFornecedor.class.php'    );
include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoAditivo.class.php'       );
include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoAditivoItem.class.php'   );
include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoRescisao.class.php'      );
include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoApostila.class.php'      );
include_once ( CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenho.class.php'                );
include_once ( CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php'                );

$stPrograma = "RescindirContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

switch( $_REQUEST['stAcao'] ){
	case "rescindir":
		Sessao::setTrataExcecao ( true );
		
		$obErro = new Erro;
		
		$obTTCEMGContratoRescisao = new TTCEMGContratoRescisao;
		
		$obTTCEMGContratoRescisao->setDado( 'cod_contrato'  , $_REQUEST['inCodContrato']        );
		$obTTCEMGContratoRescisao->setDado( 'exercicio'     , $_REQUEST['stExercicioContrato']  );
		$obTTCEMGContratoRescisao->setDado( 'cod_entidade'  , $_REQUEST['inCodEntidadeContrato']);
		$obTTCEMGContratoRescisao->setDado( 'data_rescisao' , $_REQUEST['dtRescisao']           );
		$obTTCEMGContratoRescisao->setDado( 'valor_rescisao', $_REQUEST['nuVlRescisao']         );
		
		$obErro = $obTTCEMGContratoRescisao->inclusao();
		
		if( $obErro->ocorreu() ){
			SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
		}else{
			sistemaLegado::alertaAviso($pgFilt."?stAcao=".$_REQUEST['stAcao'], $_REQUEST['inNumContrato'  ] .'/'. $_REQUEST['stExercicioContrato']  ,"incluir","aviso", Sessao::getId(), "../");
		}
		Sessao::encerraExcecao();
	
	break;
	
	case "excluir":
		Sessao::setTrataExcecao ( true );
		
		$obErro = new Erro;
		
		$obTTCEMGContratoRescisao = new TTCEMGContratoRescisao;
		
		$obTTCEMGContratoRescisao->setDado( 'cod_contrato'  , $_REQUEST['inCodContrato']        );
		$obTTCEMGContratoRescisao->setDado( 'exercicio'     , $_REQUEST['stExercicioContrato']  );
		$obTTCEMGContratoRescisao->setDado( 'cod_entidade'  , $_REQUEST['inCodEntidadeContrato']);
		
		$obErro = $obTTCEMGContratoRescisao->exclusao();
		
		if( $obErro->ocorreu() ){
			SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
		}else{
			sistemaLegado::alertaAviso($pgFilt."?stAcao=".$_REQUEST['stAcao'], $_REQUEST['inNumContrato'  ] .'/'. $_REQUEST['stExercicioContrato']  ,"excluir","aviso", Sessao::getId(), "../");
		}
		Sessao::encerraExcecao();
	
	break; 
}
?>