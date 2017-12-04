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
    * Processamento de Apostila de Contrato
    * Data de Criação   : 25/02/2016
    
    * @author Analista:      Gelson W. Gonçalves  <gelson.goncalves@cnm.org.br>
    * @author Desenvolvedor: Carlos Adriano       <carlos.silva@cnm.org.br>
    
    * @package URBEM
    * @subpackage
    
    * @ignore
    
    $Id: PRManterApostilaContrato.php 65151 2016-04-28 12:56:33Z jean $
*/
include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php'        );
include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php'  );
include_once ( TLIC.'TLicitacaoContrato.class.php'              );
include_once ( TLIC.'TLicitacaoContratoApostila.class.php'      );

$stPrograma = "ManterApostilaContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

switch( $request->get('stAcao') ){
	case "incluir":
		Sessao::setTrataExcecao ( true );
		
		$obErro = new Erro;

		$obTLicitacaoContratoApostila = new TLicitacaoContratoApostila ;
		$stFiltro  = " WHERE num_contrato  =  ".$request->get('inNumContrato');
		$stFiltro .= "   AND exercicio	   = '".$request->get('stExercicioContrato')."'";
		$stFiltro .= "   AND cod_entidade  =  ".$request->get('inCodEntidadeContrato');
		$stFiltro .= "   AND cod_apostila  =  ".$request->get('inCodApostila');
		$obTLicitacaoContratoApostila->recuperaTodos($rsRecordSet, $stFiltro);

		if( $rsRecordSet->getNumLinhas() > 0 )
			$obErro->setDescricao('Número da Apostila já existe para o Contrato '.$request->get('inNumContrato'  ) .'/'. $request->get('stExercicioContrato').'.');
		
		if( !$obErro->ocorreu() ){
            $obTLicitacaoContratoApostila->setDado( 'cod_apostila'  , $request->get('inCodApostila')           	  );
			$obTLicitacaoContratoApostila->setDado( 'num_contrato'  , $request->get('inNumContrato')		      );
			$obTLicitacaoContratoApostila->setDado( 'cod_entidade'  , $request->get('inCodEntidadeContrato')	  );
            $obTLicitacaoContratoApostila->setDado( 'exercicio'     , $request->get('stExercicioContrato')		  );
			$obTLicitacaoContratoApostila->setDado( 'cod_tipo'      , $request->get('inCodTipoApostila')          );
			$obTLicitacaoContratoApostila->setDado( 'cod_alteracao' , $request->get('inCodTipoAlteracaoApostila') );
			$obTLicitacaoContratoApostila->setDado( 'descricao'     , $request->get('stDscApostila')     		  );
			$obTLicitacaoContratoApostila->setDado( 'data_apostila' , $request->get('dtApostila')      		      );
			
			$nuVlApostila = str_replace(".", "", $request->get('nuVlApostila'));
			$nuVlApostila = str_replace(",", ".", $nuVlApostila);

			$nuVlApostila=(isset($nuVlApostila)) ? $nuVlApostila : 0;
			$obTLicitacaoContratoApostila->setDado( 'valor_apostila', $nuVlApostila );
			
			$obErro = $obTLicitacaoContratoApostila->inclusao();
		}

		if( $obErro->ocorreu() ){
			SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
		}else{
			sistemaLegado::alertaAviso($pgFilt."?stAcao=". $request->get('stAcao'), "Contrato:". $request->get('inNumContrato'  ) .'/'.  $request->get('stExercicioContrato')." - Apostila:". $request->get('inCodApostila') ,"incluir","aviso", Sessao::getId(), "../");
		}
		Sessao::encerraExcecao();
	
	break;

	case "alterar":
		Sessao::setTrataExcecao ( true );
		
		$obErro = new Erro;
		
		$obTLicitacaoContratoApostila = new TLicitacaoContratoApostila ;

		if( $request->get('inCodApostilaAtual')!= $request->get('inCodApostila')){
			$stFiltro  = " WHERE ( num_contrato  =  ". $request->get('inNumContrato');
			$stFiltro .= "   AND exercicio	   = '". $request->get('stExercicioContrato')."'";
			$stFiltro .= "   AND cod_entidade  =  ". $request->get('inCodEntidadeContrato');
			$stFiltro .= "   AND cod_apostila  =  ". $request->get('inCodApostila')." )";
            $stFiltro .= "   AND cod_apostila  !=  ". $request->get('inHdnCodApostila') ;
			$obTLicitacaoContratoApostila->recuperaTodos($rsRecordSet, $stFiltro);
	
			if( $rsRecordSet->getNumLinhas() > 0 )
				$obErro->setDescricao('Número da Apostila já existe para o Contrato '. $request->get('inNumContrato'  ) .'/'.  $request->get('stExercicioContrato').'.');
		}
		
		if( !$obErro->ocorreu() ){
			$obTLicitacaoContratoApostila->setDado( 'num_contrato'  ,  $request->get('inNumContrato')		 );
			$obTLicitacaoContratoApostila->setDado( 'exercicio'     ,  $request->get('stExercicioContrato')	 );
			$obTLicitacaoContratoApostila->setDado( 'cod_entidade'  ,  $request->get('inCodEntidadeContrato') );
			$obTLicitacaoContratoApostila->setDado( 'cod_apostila'  ,  $request->get('inHdnCodApostila')      );
			
			$obErro = $obTLicitacaoContratoApostila->exclusao();
			
			if( !$obErro->ocorreu() ){
                $obTLicitacaoContratoApostila->setDado( 'cod_apostila'  ,  $request->get('inCodApostila')           	  );
                $obTLicitacaoContratoApostila->setDado( 'num_contrato'  ,  $request->get('inNumContrato')		      );
                $obTLicitacaoContratoApostila->setDado( 'cod_entidade'  ,  $request->get('inCodEntidadeContrato')	  );
                $obTLicitacaoContratoApostila->setDado( 'exercicio'     ,  $request->get('stExercicioContrato')		  );
                $obTLicitacaoContratoApostila->setDado( 'cod_tipo'      ,  $request->get('inCodTipoApostila')          );
                $obTLicitacaoContratoApostila->setDado( 'cod_alteracao' ,  $request->get('inCodTipoAlteracaoApostila') );
                $obTLicitacaoContratoApostila->setDado( 'descricao'     ,  $request->get('stDscApostila')     		  );
                $obTLicitacaoContratoApostila->setDado( 'data_apostila' ,  $request->get('dtApostila')      		      );
				
				$nuVlApostila = str_replace(".", "", $request->get('nuVlApostila'));
				$nuVlApostila = str_replace(",", ".", $nuVlApostila);
				$nuVlApostila = ($nuVlApostila != "") ? $nuVlApostila : 0;

				$obTLicitacaoContratoApostila->setDado( 'valor_apostila', $nuVlApostila );
				
				$obErro = $obTLicitacaoContratoApostila->inclusao();
			}
		}

		if( $obErro->ocorreu() ){
			SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
		}else{
			sistemaLegado::alertaAviso($pgList."?stAcao=". $request->get('stAcao'), "Contrato:". $request->get('inNumContrato'  ) .'/'.  $request->get('stExercicioContrato')." - Apostila:". $request->get('inCodApostila') ,"alterar","aviso", Sessao::getId(), "../");
		}
		
		Sessao::encerraExcecao();
		
	break;
	
	case "excluir":
		Sessao::setTrataExcecao ( true );
		
		$obErro = new Erro;
		
		$obTLicitacaoContratoApostila = new TLicitacaoContratoApostila ;
		$obTLicitacaoContratoApostila->setDado( 'cod_contrato'  ,  $request->get('inCodContrato')		 );
		$obTLicitacaoContratoApostila->setDado( 'exercicio'     ,  $request->get('stExercicioContrato')	 );
		$obTLicitacaoContratoApostila->setDado( 'cod_entidade'  ,  $request->get('inCodEntidadeContrato') );
		$obTLicitacaoContratoApostila->setDado( 'cod_apostila'  ,  $request->get('inCodApostila')         );
		
		$obErro = $obTLicitacaoContratoApostila->exclusao();
		
		if( $obErro->ocorreu() ){
			SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
		}else{
			sistemaLegado::alertaAviso($pgFilt."?stAcao=". $request->get('stAcao'), "Contrato:". $request->get('inNumContrato'  ) .'/'.  $request->get('stExercicioContrato')." - Apostila:". $request->get('inCodApostila') ,"excluir","aviso", Sessao::getId(), "../");
		}
		Sessao::encerraExcecao();
	
	break; 
}
?>