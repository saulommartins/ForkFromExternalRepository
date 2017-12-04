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
	* Arquivo de Processamento do Formulario Contratos TCEMG
	* Data de Criação   : 21/02/2014

	* @author Analista      Sergio Luiz dos Santos
	* @author Desenvolvedor Michel Teixeira

	* @package URBEM
	* @subpackage

	* @ignore

	$Id: PRManterContrato.php 59612 2014-09-02 12:00:51Z gelson $
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

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

switch( $_REQUEST['stAcao'] ){
	case 'incluir' :
		Sessao::setTrataExcecao ( true );

		$obErro = new Erro();
		if (count(Sessao::read('arEmpenhos')) <= 0) {
			$obErro->setDescricao('Nenhum empenho incluso na lista!');
		}

		if (count(Sessao::read('arFornecedores')) <= 0) {
			$obErro->setDescricao('Nenhum fornecedor incluso na lista!');
		}

		if ( !$obErro->ocorreu() ){   
			$obTTCEMGContrato = new TTCEMGContrato;
			$stFiltro  = " WHERE nro_contrato   = ".$_REQUEST['inNumContrato'];
			$stFiltro .= "   AND exercicio      = '".$_REQUEST['stExercicioContrato']."'";
			$obTTCEMGContrato->recuperaTodos($rsRecordSet, $stFiltro);

			if( $rsRecordSet->getNumLinhas() < 0 ){
				$obTTCEMGContrato->recuperaProximoContrato($rsRecordSet);
				$inCodContrato = $rsRecordSet->getCampo('cod_contrato');
				if(!$inCodContrato)
				    $inCodContrato = 1;            
				
                            $obTTCEMGContrato->setDado( 'cod_contrato'             , $inCodContrato                         );
                            $obTTCEMGContrato->setDado( 'cod_entidade'             , $_REQUEST['cod_entidade']              );
                            $obTTCEMGContrato->setDado( 'num_orgao'                , $_REQUEST['inCodOrgao']                );
                            $obTTCEMGContrato->setDado( 'num_unidade'              , $_REQUEST['inCodUnidade']              );
                            $obTTCEMGContrato->setDado( 'nro_contrato'             , $_REQUEST['inNumContrato']             );
                            $obTTCEMGContrato->setDado( 'exercicio'                , $_REQUEST['stExercicioContrato']       );
                            $obTTCEMGContrato->setDado( 'data_assinatura'          , $_REQUEST['dtAssinatura']              );
                            $obTTCEMGContrato->setDado( 'cod_modalidade_licitacao' , $_REQUEST['cod_modalidade']            );
                            $obTTCEMGContrato->setDado( 'cod_entidade_modalidade'  , $_REQUEST['inCodEntidadeModalidade']   );
                            $obTTCEMGContrato->setDado( 'num_orgao_modalidade'     , $_REQUEST['inCodOrgaoModalidade']      );
                            $obTTCEMGContrato->setDado( 'num_unidade_modalidade'   , $_REQUEST['inCodUnidadeModalidade']    );
                            $obTTCEMGContrato->setDado( 'nro_processo'             , $_REQUEST['inNumProcesso']             );
                            $obTTCEMGContrato->setDado( 'exercicio_processo'       , $_REQUEST['stExercicioProcesso']       );
                            $obTTCEMGContrato->setDado( 'cod_tipo_processo'        , $_REQUEST['cod_tipo_processo']         );
                            $obTTCEMGContrato->setDado( 'cod_objeto'               , $_REQUEST['cod_objeto']                );
                            $obTTCEMGContrato->setDado( 'objeto_contrato'          , $_REQUEST['stObjContrato']             );
                            $obTTCEMGContrato->setDado( 'cod_instrumento'          , $_REQUEST['cod_instrumento']           );
                            $obTTCEMGContrato->setDado( 'data_inicio'              , $_REQUEST['dtInicial']                 );
                            $obTTCEMGContrato->setDado( 'data_final'               , $_REQUEST['dtFinal']                   );
                            $obTTCEMGContrato->setDado( 'vl_contrato'              , $_REQUEST['nuVlContrato']              );
                            $obTTCEMGContrato->setDado( 'fornecimento'             , $_REQUEST['stFormaFornecimento']       );
                            $obTTCEMGContrato->setDado( 'pagamento'                , $_REQUEST['stFormaPagamento']          );
                            $obTTCEMGContrato->setDado( 'execucao'                 , $_REQUEST['stFormaPrazo']              );
                            $obTTCEMGContrato->setDado( 'multa'                    , $_REQUEST['stFormaMulta']              );
                            $obTTCEMGContrato->setDado( 'multa_inadimplemento'     , $_REQUEST['stMultaInadimplemento']     );
                            $obTTCEMGContrato->setDado( 'cod_garantia'             , $_REQUEST['cod_garantia']              );
                            $obTTCEMGContrato->setDado( 'data_publicacao'          , $_REQUEST['dtPublicacao']              );
                            $obTTCEMGContrato->setDado( 'numcgm_publicidade'       , $_REQUEST['inVeiculo']                 );
                            $obTTCEMGContrato->setDado( 'cgm_signatario'           , $_REQUEST['cgmSignatario']             );                               
				
                            $obREntidade =  new ROrcamentoEntidade();
                            $obREntidade->setExercicio      ( $_REQUEST['stExercicioContrato']  );
                            $obREntidade->setCodigoEntidade ( $_REQUEST['cod_entidade']         );
                            $obREntidade->listar($rsLista);
                            
                            if(count($rsLista->arElementos)>0){
                                $obTTCEMGContrato->setDado( 'numcgm_contratante'   , $rsLista->arElementos[0]['numcgm']     );
                            }else{
                                $obErro->setDescricao('Entidade não localizada.');
                            }
				
                            if(!$obErro->ocorreu()){
                                $obTTCEMGContrato->inclusao();

                                $obTTCEMGContratoEmpenho = new TTCEMGContratoEmpenho ;
                                $obTTCEMGContratoEmpenho->setDado( 'cod_contrato' , $inCodContrato                      );
                                $obTTCEMGContratoEmpenho->setDado( 'exercicio'    , $_REQUEST['stExercicioContrato']    );
                                $obTTCEMGContratoEmpenho->setDado( 'cod_entidade' , $_REQUEST['cod_entidade']           );

                                $rsRecordSet = new RecordSet;
                                $rsRecordSet->preenche(Sessao::read('arEmpenhos'));

                                while( !$rsRecordSet->eof() ){
                                    $obTTCEMGContratoEmpenho->setDado( 'exercicio_empenho' , $rsRecordSet->getCampo('exercicio')    );
                                    $obTTCEMGContratoEmpenho->setDado( 'cod_empenho'       , $rsRecordSet->getCampo('cod_empenho')  );
                                    $obTTCEMGContratoEmpenho->inclusao();
                                    $rsRecordSet->proximo();
                                }
                                
                                $obTTCEMGContratoFornecedor = new TTCEMGContratoFornecedor ;
                                $obTTCEMGContratoFornecedor->setDado( 'cod_contrato' , $inCodContrato                      );
                                $obTTCEMGContratoFornecedor->setDado( 'exercicio'    , $_REQUEST['stExercicioContrato']    );
                                $obTTCEMGContratoFornecedor->setDado( 'cod_entidade' , $_REQUEST['cod_entidade']           );

                                $rsRecordSet = new RecordSet;
                                $rsRecordSet->preenche(Sessao::read('arFornecedores'));

                                while( !$rsRecordSet->eof() ){
                                    $obTTCEMGContratoFornecedor->setDado( 'cgm_fornecedor'      , $rsRecordSet->getCampo('inCodFornecedor') );
                                    $obTTCEMGContratoFornecedor->setDado( 'cgm_representante'   , $rsRecordSet->getCampo('cgmRepLegal')     );
                                    $obTTCEMGContratoFornecedor->inclusao();
                                    $rsRecordSet->proximo();
                                }
                            }
			}else{
				$obErro->setDescricao('Número do contrato já existe para este exercício.');
			}
		}

		if( !$obErro->ocorreu() ){
			Sessao::write   ( 'arEmpenhos', ''      );
			Sessao::remove  ( 'arEmpenhos'          );
			Sessao::write   ( 'arFornecedores', ''  );
			Sessao::remove  ( 'arFornecedores'      );
			sistemaLegado::alertaAviso($pgForm."?stAcao=".$_REQUEST['stAcao'], $_REQUEST['inNumContrato'] .'/'. $_REQUEST['stExercicioContrato'] ,"incluir","aviso", Sessao::getId(), "../");
		}else{
			sistemaLegado::exibeAviso( urlencode($obErro->getDescricao()),"n_incluir","erro" );
		}
		Sessao::encerraExcecao();

    break;

    case "alterar":                                                
		Sessao::setTrataExcecao ( true );
  
		$obErro = new Erro();
                
                if ($_REQUEST['hdnNumContrato'] != '') {
                    $numContrato = $_REQUEST['hdnNumContrato'];
                } else {
                    $numContrato = $_REQUEST['inNumContrato'];
                }

		$arEmpenhos = Sessao::read('arEmpenhos');
		if (count(Sessao::read('arEmpenhos')) <= 0) {
		    $obErro->setDescricao('Nenhum empenho incluso na lista!');
		}

		$arFornecedores = Sessao::read('arFornecedores');
		if (count(Sessao::read('arFornecedores')) <= 0) {
		    $obErro->setDescricao('Nenhum fornecedor incluso na lista!');
		}

		if ( !$obErro->ocorreu() ){
                    $obTTCEMGContrato = new TTCEMGContrato;
                    $stFiltro  = " WHERE exercicio     = '".$_REQUEST['exercicio_contrato']."'";
                    $stFiltro .= "   AND cod_entidade  =  ".$_REQUEST['cod_entidade'];
                    $stFiltro .= "   AND nro_contrato  =  ".$numContrato;
                    $stFiltro .= "   AND cod_contrato !=  ".$_REQUEST['inCodContrato'];   
                    $obTTCEMGContrato->recuperaTodos($rsRecordSet, $stFiltro);
		    
		    if($rsRecordSet->getNumLinhas() == 1){
			$obErro->setDescricao('Número do contrato já existe para este exercício.');
		    }
		    
		    if ( !$obErro->ocorreu() ){
			$stFiltro  = " WHERE exercicio     = '".$_REQUEST['exercicio_contrato']."'";
			$stFiltro .= "   AND cod_entidade  =  ".$_REQUEST['cod_entidade'];
			$stFiltro .= "   AND cod_contrato  =  ".$_REQUEST['inCodContrato'];   
			$obTTCEMGContrato->recuperaTodos($rsRecordSet, $stFiltro);
    
			if($rsRecordSet->getNumLinhas() == 1){
			    $obTTCEMGContrato->setDado( 'cod_contrato'             , $_REQUEST['inCodContrato']             );
			    $obTTCEMGContrato->setDado( 'cod_entidade'             , $_REQUEST['cod_entidade']              );
			    $obTTCEMGContrato->setDado( 'num_orgao'                , $_REQUEST['inCodOrgao']                );
			    $obTTCEMGContrato->setDado( 'num_unidade'              , $_REQUEST['inCodUnidade']              );
			    $obTTCEMGContrato->setDado( 'nro_contrato'             , $numContrato                           );
			    $obTTCEMGContrato->setDado( 'exercicio'                , $_REQUEST['exercicio_contrato']        );
			    $obTTCEMGContrato->setDado( 'data_assinatura'          , $_REQUEST['dtAssinatura']              );
			    $obTTCEMGContrato->setDado( 'cod_modalidade_licitacao' , $_REQUEST['cod_modalidade']            );
			    $obTTCEMGContrato->setDado( 'cod_entidade_modalidade'  , $_REQUEST['inCodEntidadeModalidade']   );
			    $obTTCEMGContrato->setDado( 'num_orgao_modalidade'     , $_REQUEST['inCodOrgaoModalidade']      );
			    $obTTCEMGContrato->setDado( 'num_unidade_modalidade'   , $_REQUEST['inCodUnidadeModalidade']    );
			    $obTTCEMGContrato->setDado( 'nro_processo'             , $_REQUEST['inNumProcesso']             );
			    $obTTCEMGContrato->setDado( 'exercicio_processo'       , $_REQUEST['stExercicioProcesso']       );
			    $obTTCEMGContrato->setDado( 'cod_tipo_processo'        , $_REQUEST['cod_tipo_processo']         );
			    $obTTCEMGContrato->setDado( 'cod_objeto'               , $_REQUEST['cod_objeto']                );
			    $obTTCEMGContrato->setDado( 'objeto_contrato'          , $_REQUEST['stObjContrato']             );
			    $obTTCEMGContrato->setDado( 'cod_instrumento'          , $_REQUEST['cod_instrumento']           );
			    $obTTCEMGContrato->setDado( 'data_inicio'              , $_REQUEST['dtInicial']                 );
			    $obTTCEMGContrato->setDado( 'data_final'               , $_REQUEST['dtFinal']                   );
			    $obTTCEMGContrato->setDado( 'vl_contrato'              , $_REQUEST['nuVlContrato']              );
			    $obTTCEMGContrato->setDado( 'fornecimento'             , $_REQUEST['stFormaFornecimento']       );
			    $obTTCEMGContrato->setDado( 'pagamento'                , $_REQUEST['stFormaPagamento']          );
			    $obTTCEMGContrato->setDado( 'execucao'                 , $_REQUEST['stFormaPrazo']              );
			    $obTTCEMGContrato->setDado( 'multa'                    , $_REQUEST['stFormaMulta']              );
			    $obTTCEMGContrato->setDado( 'multa_inadimplemento'     , $_REQUEST['stMultaInadimplemento']     );
			    $obTTCEMGContrato->setDado( 'cod_garantia'             , $_REQUEST['cod_garantia']              );
			    $obTTCEMGContrato->setDado( 'data_publicacao'          , $_REQUEST['dtPublicacao']              );
			    $obTTCEMGContrato->setDado( 'numcgm_publicidade'       , $_REQUEST['inVeiculo']                 );
			    $obTTCEMGContrato->setDado( 'cgm_signatario'           , $_REQUEST['cgmSignatario']             );
			    
			    $obREntidade =  new ROrcamentoEntidade();
			    $obREntidade->setExercicio      ( $_REQUEST['stExercicioContrato']  );
			    $obREntidade->setCodigoEntidade ( $_REQUEST['cod_entidade']         );
			    $obREntidade->listar($rsLista);
			    
			    if(count($rsLista->arElementos)>0){
				$obTTCEMGContrato->setDado( 'numcgm_contratante'   , $rsLista->arElementos[0]['numcgm']     );
    
			    }else{
				$obErro->setDescricao('Entidade não localizada.');
			    }
			    
			    if(!$obErro->ocorreu()){
				$obErro = $obTTCEMGContrato->alteraContrato();
    
				if(!$obErro->ocorreu()){
				    $obErro = $obTTCEMGContrato->alteracao();
				}
			    }
    
			    if( !$obErro->ocorreu() ){                                                                   
				$obTTCEMGContratoEmpenho = new TTCEMGContratoEmpenho;
				$obTTCEMGContratoEmpenho->setDado( 'cod_contrato' , $_REQUEST['inCodContrato']              );
				$obTTCEMGContratoEmpenho->setDado( 'exercicio'    , $_REQUEST['exercicio_contrato']         );
				$obTTCEMGContratoEmpenho->setDado( 'cod_entidade' , $_REQUEST['cod_entidade']               );
				$obErro = $obTTCEMGContratoEmpenho->exclusao();
				
				if( !$obErro->ocorreu() ){
				    $obTTCEMGContratoEmpenho->setDado( 'cod_contrato'   , $_REQUEST['inCodContrato']        );
				    $obTTCEMGContratoEmpenho->setDado( 'exercicio'      , $_REQUEST['exercicio_contrato']   );
				    $obTTCEMGContratoEmpenho->setDado( 'cod_entidade'   , $_REQUEST['cod_entidade']         );
				    foreach( $arEmpenhos as $registro ){
					$obTTCEMGContratoEmpenho->setDado( 'exercicio_empenho' , $registro['exercicio']     );  
					$obTTCEMGContratoEmpenho->setDado( 'cod_empenho'       , $registro['cod_empenho']   );
					$obErro = $obTTCEMGContratoEmpenho->inclusao();
				    } 
				}
			    }
			    
			    if( !$obErro->ocorreu() ){                                                                   
				$obTTCEMGContratoFornecedor = new TTCEMGContratoFornecedor ;
				$obTTCEMGContratoFornecedor->setDado( 'cod_contrato' , $_REQUEST['inCodContrato']          );
				$obTTCEMGContratoFornecedor->setDado( 'exercicio'    , $_REQUEST['exercicio_contrato']     );
				$obTTCEMGContratoFornecedor->setDado( 'cod_entidade' , $_REQUEST['cod_entidade']           );
				$obErro = $obTTCEMGContratoFornecedor->exclusao();
				
				if( !$obErro->ocorreu() ){
				    $obTTCEMGContratoFornecedor->setDado( 'cod_contrato'   , $_REQUEST['inCodContrato']        );
				    $obTTCEMGContratoFornecedor->setDado( 'exercicio'      , $_REQUEST['exercicio_contrato']   );
				    $obTTCEMGContratoFornecedor->setDado( 'cod_entidade'   , $_REQUEST['cod_entidade']         );
				    foreach( $arFornecedores as $registro ){
					$obTTCEMGContratoFornecedor->setDado( 'cgm_fornecedor'      , $registro['inCodFornecedor']  );
					$obTTCEMGContratoFornecedor->setDado( 'cgm_representante'   , $registro['cgmRepLegal']      );
					$obErro = $obTTCEMGContratoFornecedor->inclusao();
				    } 
				}
			    }
			}
		    }
            
                    if( $obErro->ocorreu() ){
                        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                    }else{
                        Sessao::write   ( 'arEmpenhos', ''      );
                        Sessao::remove  ( 'arEmpenhos'          );
                        Sessao::write   ( 'arFornecedores', ''  );
                        Sessao::remove  ( 'arFornecedores'      );
                        sistemaLegado::alertaAviso($pgFilt."?stAcao=".$_REQUEST['stAcao'], $numContrato .'/'. $_REQUEST['exercicio_contrato'] ,"incluir","aviso", Sessao::getId(), "../");
                    }

		}else{
			$obErro = sistemaLegado::exibeAviso( urlencode($obErro->getDescricao()),"n_incluir","erro" );
		}  
		Sessao::encerraExcecao();

    break;

    case "excluir":
        Sessao::setTrataExcecao ( true );
        
        $obErro = new Erro;
	
	$obTTCEMGContratoAditivo = new TTCEMGContratoAditivo ;
	$stFiltro  = " WHERE cod_contrato           =  ".$_REQUEST['inCodContrato'];
	$stFiltro .= " AND exercicio_contrato       = '".$_REQUEST['stExercicioContrato']."'"; 
	$stFiltro .= " AND cod_entidade_contrato    =  ".$_REQUEST['inCodEntidade'];
	$obTTCEMGContratoAditivo->recuperaTodos($rsAditivo, $stFiltro);
	

	$obTTCEMGContratoApostila = new TTCEMGContratoApostila;
	$stFiltro  = " WHERE cod_contrato	=  ".$_REQUEST['inCodContrato'];
	$stFiltro .= " AND exercicio		= '".$_REQUEST['stExercicioContrato']."'"; 
	$stFiltro .= " AND cod_entidade    	=  ".$_REQUEST['inCodEntidade'];
	$obTTCEMGContratoApostila->recuperaTodos($rsApostila, $stFiltro);


	if( $rsAditivo->inNumLinhas>0 ){
		sistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'],"Contrato(".$_REQUEST['inCodContrato']."/".$_REQUEST['stExercicioContrato'].") vinculado com um ou mais aditivo(s)","n_excluir","erro", Sessao::getId(), "../");
	}else if( $rsApostila->inNumLinhas>0 ){
		sistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'],"Contrato(".$_REQUEST['inCodContrato']."/".$_REQUEST['stExercicioContrato'].") vinculado com uma ou mais apostila(s)","n_excluir","erro", Sessao::getId(), "../");
	}else{
		if( !$obErro->ocorreu() ){   
			$obTTCEMGContratoEmpenho = new TTCEMGContratoEmpenho;
			$obTTCEMGContratoEmpenho->setDado( 'cod_contrato' , $_REQUEST['inCodContrato']          );
			$obTTCEMGContratoEmpenho->setDado( 'exercicio'    , $_REQUEST['stExercicioContrato']    );
			$obTTCEMGContratoEmpenho->setDado( 'cod_entidade' , $_REQUEST['inCodEntidade']          );
			$obErro = $obTTCEMGContratoEmpenho->exclusao();
		}
		
		if( !$obErro->ocorreu() ){                                                                   
		    $obTTCEMGContratoFornecedor = new TTCEMGContratoFornecedor ;
		    $obTTCEMGContratoFornecedor->setDado( 'cod_contrato' , $_REQUEST['inCodContrato']       );
		    $obTTCEMGContratoFornecedor->setDado( 'exercicio'    , $_REQUEST['stExercicioContrato'] );
		    $obTTCEMGContratoFornecedor->setDado( 'cod_entidade' , $_REQUEST['inCodEntidade']       );
		    $obErro = $obTTCEMGContratoFornecedor->exclusao();
		}		
		
		if( !$obErro->ocorreu() ){
		    $obTTCEMGContrato = new TTCEMGContrato;
		    $obTTCEMGContrato->setDado('cod_contrato' , $_REQUEST['inCodContrato']       );  
		    $obTTCEMGContrato->setDado('exercicio'    , $_REQUEST['stExercicioContrato'] );      
		    $obTTCEMGContrato->setDado('cod_entidade' , $_REQUEST['inCodEntidade']       ); 
		    $obErro = $obTTCEMGContrato->exclusao();
		}
		
		if( $obErro->ocorreu() ){
		    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
		}else{
		    sistemaLegado::alertaAviso($pgFilt."?stAcao=".$_REQUEST['stAcao'], $_REQUEST['inNumContrato'  ] .'/'. $_REQUEST['stExercicioContrato']  ,"excluir","aviso", Sessao::getId(), "../");
		}
	}
        
        Sessao::encerraExcecao();

    break;
}
?>