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
    * Processamento de Apostila de Contrato TCEMG
    * Data de Criação   : 06/05/2014
    
    * @author Analista      Silvia Martins Silva
    * @author Desenvolvedor Michel Teixeira
    
    * @package URBEM
    * @subpackage
    
    * @ignore
    
    $Id: PRManterApostilaContrato.php 59612 2014-09-02 12:00:51Z gelson $
*/
include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php'        );
include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php'  );
include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContrato.class.php'              );
include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoApostila.class.php'      );

$stPrograma = "ManterApostilaContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

switch( $_REQUEST['stAcao'] ){
	case "incluir":
		Sessao::setTrataExcecao ( true );
		
		$obErro = new Erro;

		$obTTCEMGContratoApostila = new TTCEMGContratoApostila ;

		$stFiltro  = " WHERE cod_contrato   	=  ".$_REQUEST['inCodContrato'];
		$stFiltro .= "   AND exercicio		= '".$_REQUEST['stExercicioContrato']."'";
		$stFiltro .= "   AND cod_entidade      	=  ".$_REQUEST['inCodEntidadeContrato'];
		$stFiltro .= "   AND cod_apostila      	=  ".$_REQUEST['inCodApostila'];
		$obTTCEMGContratoApostila->recuperaTodos($rsRecordSet, $stFiltro);

		if( $rsRecordSet->getNumLinhas() > 0 )
			$obErro->setDescricao('Número da Apostila já existe para o Contrato '.$_REQUEST['inNumContrato'  ] .'/'. $_REQUEST['stExercicioContrato'].'.');
		
		if( !$obErro->ocorreu() ){
			$obTTCEMGContratoApostila->setDado( 'cod_contrato'  , $_REQUEST['inCodContrato']		);
			$obTTCEMGContratoApostila->setDado( 'exercicio'     , $_REQUEST['stExercicioContrato']		);
			$obTTCEMGContratoApostila->setDado( 'cod_entidade'  , $_REQUEST['inCodEntidadeContrato']	);
			$obTTCEMGContratoApostila->setDado( 'cod_apostila'  , $_REQUEST['inCodApostila']           	);
			$obTTCEMGContratoApostila->setDado( 'cod_tipo'      , $_REQUEST['inCodTipoApostila']          	);
			$obTTCEMGContratoApostila->setDado( 'cod_alteracao' , $_REQUEST['inCodTipoAlteracaoApostila'] 	);
			$obTTCEMGContratoApostila->setDado( 'descricao'     , $_REQUEST['stDscApostila']     		);
			$obTTCEMGContratoApostila->setDado( 'data_apostila' , $_REQUEST['dtApostila']      		);
			
			$nuVlApostila=(isset($_REQUEST['nuVlApostila'])) ? $_REQUEST['nuVlApostila'] : 0;
			$obTTCEMGContratoApostila->setDado( 'valor_apostila', $nuVlApostila         			);
			
			$obErro = $obTTCEMGContratoApostila->inclusao();
		}

		if( $obErro->ocorreu() ){
			SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
		}else{
			sistemaLegado::alertaAviso($pgFilt."?stAcao=".$_REQUEST['stAcao'], "Contrato:".$_REQUEST['inNumContrato'  ] .'/'. $_REQUEST['stExercicioContrato']." - Apostila:".$_REQUEST['inCodApostila'] ,"incluir","aviso", Sessao::getId(), "../");
		}
		Sessao::encerraExcecao();
	
	break;

	case "alterar":
		Sessao::setTrataExcecao ( true );
		
		$obErro = new Erro;
		
		$obTTCEMGContratoApostila = new TTCEMGContratoApostila ;

		if($_REQUEST['inCodApostilaAtual']!=$_REQUEST['inCodApostila']){
			$stFiltro  = " WHERE cod_contrato   	=  ".$_REQUEST['inCodContrato'];
			$stFiltro .= "   AND exercicio		= '".$_REQUEST['stExercicioContrato']."'";
			$stFiltro .= "   AND cod_entidade      	=  ".$_REQUEST['inCodEntidadeContrato'];
			$stFiltro .= "   AND cod_apostila       =  ".$_REQUEST['inCodApostila'];
			$obTTCEMGContratoApostila->recuperaTodos($rsRecordSet, $stFiltro);
	
			if( $rsRecordSet->getNumLinhas() > 0 )
				$obErro->setDescricao('Número da Apostila já existe para o Contrato '.$_REQUEST['inNumContrato'  ] .'/'. $_REQUEST['stExercicioContrato'].'.');
		}
		
		if( !$obErro->ocorreu() ){
			$obTTCEMGContratoApostila->setDado( 'cod_contrato'  , $_REQUEST['inCodContrato']		);
			$obTTCEMGContratoApostila->setDado( 'exercicio'     , $_REQUEST['stExercicioContrato']		);
			$obTTCEMGContratoApostila->setDado( 'cod_entidade'  , $_REQUEST['inCodEntidadeContrato']	);
			$obTTCEMGContratoApostila->setDado( 'cod_apostila'  , $_REQUEST['inCodApostilaAtual']         	);
			
			$obErro = $obTTCEMGContratoApostila->exclusao();
			
			if( !$obErro->ocorreu() ){
				$obTTCEMGContratoApostila->setDado( 'cod_contrato'  , $_REQUEST['inCodContrato']		);
				$obTTCEMGContratoApostila->setDado( 'exercicio'     , $_REQUEST['stExercicioContrato']		);
				$obTTCEMGContratoApostila->setDado( 'cod_entidade'  , $_REQUEST['inCodEntidadeContrato']	);
				$obTTCEMGContratoApostila->setDado( 'cod_apostila'  , $_REQUEST['inCodApostila']           	);
				$obTTCEMGContratoApostila->setDado( 'cod_tipo'      , $_REQUEST['inCodTipoApostila']          	);
				$obTTCEMGContratoApostila->setDado( 'cod_alteracao' , $_REQUEST['inCodTipoAlteracaoApostila'] 	);
				$obTTCEMGContratoApostila->setDado( 'descricao'     , $_REQUEST['stDscApostila']     		);
				$obTTCEMGContratoApostila->setDado( 'data_apostila' , $_REQUEST['dtApostila']      		);
				
				$nuVlApostila=(isset($_REQUEST['nuVlApostila'])) ? $_REQUEST['nuVlApostila'] : 0;
				$obTTCEMGContratoApostila->setDado( 'valor_apostila', $nuVlApostila         			);
				
				$obErro = $obTTCEMGContratoApostila->inclusao();
			}
		}

		if( $obErro->ocorreu() ){
			SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
		}else{
			sistemaLegado::alertaAviso($pgFilt."?stAcao=".$_REQUEST['stAcao'], "Contrato:".$_REQUEST['inNumContrato'  ] .'/'. $_REQUEST['stExercicioContrato']." - Apostila:".$_REQUEST['inCodApostila'] ,"alterar","aviso", Sessao::getId(), "../");
		}
		
		Sessao::encerraExcecao();
		
	break;
	
	case "excluir":
		Sessao::setTrataExcecao ( true );
		
		$obErro = new Erro;
		
		$obTTCEMGContratoApostila = new TTCEMGContratoApostila ;
		$obTTCEMGContratoApostila->setDado( 'cod_contrato'  , $_REQUEST['inCodContrato']		);
		$obTTCEMGContratoApostila->setDado( 'exercicio'     , $_REQUEST['stExercicioContrato']		);
		$obTTCEMGContratoApostila->setDado( 'cod_entidade'  , $_REQUEST['inCodEntidadeContrato']	);
		$obTTCEMGContratoApostila->setDado( 'cod_apostila'  , $_REQUEST['inCodApostila']         	);
		
		$obErro = $obTTCEMGContratoApostila->exclusao();
		
		if( $obErro->ocorreu() ){
			SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
		}else{
			sistemaLegado::alertaAviso($pgFilt."?stAcao=".$_REQUEST['stAcao'], "Contrato:".$_REQUEST['inNumContrato'  ] .'/'. $_REQUEST['stExercicioContrato']." - Apostila:".$_REQUEST['inCodApostila'] ,"excluir","aviso", Sessao::getId(), "../");
		}
		Sessao::encerraExcecao();
	
	break; 
}
?>