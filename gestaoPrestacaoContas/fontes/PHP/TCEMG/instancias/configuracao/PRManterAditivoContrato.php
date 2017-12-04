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
    * Processamento de Aditivo de Contrato TCEMG
    * Data de Criação   : 30/04/2014
    
    * @author Analista      Silvia Martins Silva
    * @author Desenvolvedor Michel Teixeira
    
    * @package URBEM
    * @subpackage
    
    * @ignore
    
    $Id: PRManterAditivoContrato.php 59612 2014-09-02 12:00:51Z gelson $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContrato.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoEmpenho.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoFornecedor.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoAditivo.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoAditivoItem.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoRescisao.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoApostila.class.php';
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenho.class.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php';

$stPrograma = "ManterAditivoContrato";
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
		
		//Verificação de existencia de Aditivo
		$obTTCEMGContratoAditivo = new TTCEMGContratoAditivo;
		$stFiltro  = "   WHERE exercicio_contrato 	= '".$_REQUEST['stExercicioContrato']."'";
		$stFiltro .= "   AND nro_aditivo 		=  ".$_REQUEST['inNumAditivo'];
		$stFiltro .= "   AND cod_entidade_contrato 	=  ".$_REQUEST['inCodEntidadeContrato'];
		$stFiltro .= "   AND cod_contrato 		=  ".$_REQUEST['inCodContrato'];
		
		$obTTCEMGContratoAditivo->recuperaTodos($rsListaAditivo, $stFiltro);
		
		if($rsListaAditivo->inNumLinhas>0)
			$obErro->setDescricao('Aditivo('.$_REQUEST['inNumAditivo'].') já vinculado com o contrato('.$_REQUEST['inNumContrato']."/".$_REQUEST['stExercicioContrato'].').');
		
		//Verificando Existencia de Descrição
		if(!$obErro->ocorreu()&&($_REQUEST['inCodTermoAditivo']==6||$_REQUEST['inCodTermoAditivo']==14)){
			if(!isset($_REQUEST['stDescricaoAditivo'])||$_REQUEST['stDescricaoAditivo']=='')
				$obErro->setDescricao('Descrição do Termo Aditivo inválido!()');
		}
		
		//Verificando Existencia de Valor de termo de aditivo
		if(!$obErro->ocorreu()&&($_REQUEST['inCodTermoAditivo']==4||$_REQUEST['inCodTermoAditivo']==5)){
			if(!isset($_REQUEST['nuVlAditivo'])||$_REQUEST['nuVlAditivo']=='')
				$obErro->setDescricao('Valor do Termo Aditivo inválido!()');
		}
		
		//Verificando Existencia de Nova Data de Término de Contrato
		if(!$obErro->ocorreu()&&($_REQUEST['inCodTermoAditivo']==7||$_REQUEST['inCodTermoAditivo']==13)){
			if(!isset($_REQUEST['dtTerminoAditivo'])||$_REQUEST['dtTerminoAditivo']=='')
				$obErro->setDescricao('Nova Data de Término do Contrato inválido!()');
		}
		
		//Verificando se Existe Empenho para Alteração de Itens
		if(($_REQUEST['inCodTermoAditivo']>=9&&$_REQUEST['inCodTermoAditivo']<=11)||($_REQUEST['inCodTermoAditivo']==14)){
			$arEmpenhos = Sessao::read('arEmpenhos');
			if(count( $arEmpenhos ) == 0)
				$obErro->setDescricao('Não há Empenho vinculado com o Contrato('.$_REQUEST['inNumContrato']."/".$_REQUEST['stExercicioContrato'].')!');
		}
		
		//Se não ocorreu nenhum erro, faz a Inclusão do Aditivo
		if ( !$obErro->ocorreu() ){
			$obTTCEMGContratoAditivo = new TTCEMGContratoAditivo;
			$obTTCEMGContratoAditivo->setDado( 'cod_contrato'         	, $_REQUEST['inCodContrato']        	);
			$obTTCEMGContratoAditivo->setDado( 'exercicio_contrato'		, $_REQUEST['stExercicioContrato']  	);
			$obTTCEMGContratoAditivo->setDado( 'cod_entidade_contrato'	, $_REQUEST['inCodEntidadeContrato']	);
			
			$obTTCEMGContratoAditivo->recuperaProximoContratoAditivo($rsCodContratoAditivo);
			$inCodContratoAditivo = $rsCodContratoAditivo->getCampo('cod_contrato_aditivo');
			if(!$inCodContratoAditivo)
				$inCodContratoAditivo = 1;  
			
			$obTTCEMGContratoAditivo->setDado( 'cod_contrato_aditivo'	, $inCodContratoAditivo             	);
			$obTTCEMGContratoAditivo->setDado( 'nro_aditivo'         	, $_REQUEST['inNumAditivo']         	);
			$obTTCEMGContratoAditivo->setDado( 'exercicio'           	, $_REQUEST['stExercicioAditivo']   	);
			$obTTCEMGContratoAditivo->setDado( 'cod_entidade'        	, $_REQUEST['inCodEntidadeContrato']	);
			$obTTCEMGContratoAditivo->setDado( 'num_orgao'           	, $_REQUEST['inNumOrgao']           	);
			$obTTCEMGContratoAditivo->setDado( 'num_unidade'         	, $_REQUEST['inNumUnidade']         	);
			$obTTCEMGContratoAditivo->setDado( 'data_assinatura'     	, $_REQUEST['dtAssinaturaAditivo']  	);
			$obTTCEMGContratoAditivo->setDado( 'cod_tipo_aditivo'     	, $_REQUEST['inCodTermoAditivo']    	);
			$obTTCEMGContratoAditivo->setDado( 'data_publicacao'     	, $_REQUEST['dtPublicacaoAditivo']  	);
			$obTTCEMGContratoAditivo->setDado( 'cgm_publicacao'       	, $_REQUEST['inVeiculoAditivo']     	);
			
			//Se inCodTermoAditivo = 6 ou 14, Tem Descrição
			if($_REQUEST['inCodTermoAditivo']==6||$_REQUEST['inCodTermoAditivo']==14)
				$obTTCEMGContratoAditivo->setDado( 'descricao'      	, $_REQUEST['stDescricaoAditivo']	);
			
			//Se inCodTermoAditivo = 7 ou 13, Tem Nova Data de Término de Contrato
			if($_REQUEST['inCodTermoAditivo']==7||$_REQUEST['inCodTermoAditivo']==13)
				$obTTCEMGContratoAditivo->setDado( 'data_termino'   	, $_REQUEST['dtTerminoAditivo']		);
				
		    //Se inCodTermoAditivo = 6 ou 14, Tem Descrição
			if($_REQUEST['inCodTermoAditivo']==4||$_REQUEST['inCodTermoAditivo']==5)
				$Total = $_REQUEST['nuVlAditivo'];
				
			//Se inCodTermoAditivo = 9, 10, 11 ou 14, Tem Itens alterados
			if(($_REQUEST['inCodTermoAditivo']>=9&&$_REQUEST['inCodTermoAditivo']<=11)||($_REQUEST['inCodTermoAditivo']==14)){
				$countItens = (isset($_REQUEST['qtd_Itens'])) ? $_REQUEST['qtd_Itens'] : 0;
				$count=0;
				$listaItens = array();
				
				for($i=1;$i<=$countItens;$i++){
					if($_REQUEST['inQuantidade_'.$i]!=''){
						if(isset($_REQUEST['inAcrescimoDecrescimo_'.$i])&&($_REQUEST['inAcrescimoDecrescimo_'.$i]=='')){
						    $obErro->setDescricao('Informe o Tipo do Item('.$i.')!');
						    break;
						}
						else{
						    $Identificador = explode('_', $_REQUEST['Identificador_'.$i]);
						    
						    $listaItens[$count]['cod_empenho'] 	= $Identificador[0];
						    $listaItens[$count]['exercicio']   	= $Identificador[1];
						    $listaItens[$count]['cod_entidade']	= $Identificador[4];
						    $listaItens[$count]['num_item']    	= $Identificador[2];
						    $listaItens[$count]['vl_unitario'] 	= $Identificador[3];
						    $listaItens[$count]['quantidade']  	= $_REQUEST['inQuantidade_'.$i];
						    
						    if(isset($_REQUEST['inAcrescimoDecrescimo_'.$i])){
							$listaItens[$count]['tipo']    	= $_REQUEST['inAcrescimoDecrescimo_'.$i];
							if($_REQUEST['inAcrescimoDecrescimo_'.$i]==1){
							    $Total = $Total+($Identificador[3]*$_REQUEST['inQuantidade_'.$i]);
							}else{
							    $Total = $Total-($Identificador[3]*$_REQUEST['inQuantidade_'.$i]);    
							}
						    }else{
							$Total = $Total+($Identificador[3]*$_REQUEST['inQuantidade_'.$i]);
						    }
						    $count++;
						}
					}	
				}
				if( !$obErro->ocorreu() && $count==0)
					$obErro->setDescricao('Nenhum Item do(s) Empenho(s) foi Alterado!');  
			}
			
			//Verificando Total e Atribuindo-o.
			$Total=(isset($Total)) ? $Total : 0;
			$obTTCEMGContratoAditivo->setDado( 'valor'          , str_replace("-","",$Total) );
			
			//Verificar Tipo Valor e Atribuindo-o
			$inTipoValor=3;
			if($_REQUEST['inCodTermoAditivo']==9){
				$inTipoValor = 	1;
			}
			if($_REQUEST['inCodTermoAditivo']==4||$_REQUEST['inCodTermoAditivo']==5){
				$inTipoValor = 	$_REQUEST['inVlAcrescimoDecrescimo'];
			}
			else if($_REQUEST['inCodTermoAditivo']==10){
				$inTipoValor = 	2;
			}
			else if(($_REQUEST['inCodTermoAditivo']==11||$_REQUEST['inCodTermoAditivo']==14)&&$Total>0){
				$inTipoValor = 	1;	
			}
			elseif(($_REQUEST['inCodTermoAditivo']==11||$_REQUEST['inCodTermoAditivo']==14)&&$Total<0){
				$inTipoValor = 	2;
			}
			$obTTCEMGContratoAditivo->setDado( 'cod_tipo_valor' , $inTipoValor               );		
			
			//Se não contem erros, faz a inclusão do Aditivo.
			if( !$obErro->ocorreu() )
				$obErro = $obTTCEMGContratoAditivo->inclusao();
			
			//Se não ocorreu erro ao incluir o Aditivo, verifica se há itens alterados no mesmo.
			if( !$obErro->ocorreu() ){
				$obTTCEMGContratoAditivoItem = new TTCEMGContratoAditivoItem;
				$obTEmpenhoEmpenho = new TEmpenhoEmpenho();
				
				for($i=0;$i<count($listaItens);$i++){
					$obTTCEMGContratoAditivoItem->recuperaProximoContratoAditivoItem($rsCodContratoAditivoItem);
					$inCodContratoAditivoItem = $rsCodContratoAditivoItem->getCampo('cod_contrato_aditivo_item');
					if(!$inCodContratoAditivoItem)
						$inCodContratoAditivoItem = 1;
					
					$obTTCEMGContratoAditivoItem->setDado( 'cod_contrato_aditivo_item'	, $inCodContratoAditivoItem         		);
					$obTTCEMGContratoAditivoItem->setDado( 'cod_contrato_aditivo'     	, $inCodContratoAditivo             		);
					$obTTCEMGContratoAditivoItem->setDado( 'exercicio'                	, $_REQUEST['stExercicioAditivo']   		);
					$obTTCEMGContratoAditivoItem->setDado( 'cod_entidade'             	, $_REQUEST['inCodEntidadeContrato']		);
					$obTTCEMGContratoAditivoItem->setDado( 'cod_empenho'              	, $listaItens[$i]['cod_empenho']    		);
					$obTTCEMGContratoAditivoItem->setDado( 'exercicio_empenho'        	, $listaItens[$i]['exercicio']      		);
					$obTTCEMGContratoAditivoItem->setDado( 'num_item'                 	, $listaItens[$i]['num_item']       		);
					$obTTCEMGContratoAditivoItem->setDado( 'quantidade'               	, str_replace("," ,"." , $listaItens[$i]['quantidade']));
					
					$tipoAcrescDecresc = (isset($listaItens[$i]['tipo'])) ? $listaItens[$i]['tipo'] : ""; 
					$obTTCEMGContratoAditivoItem->setDado( 'tipo_acresc_decresc'      	, $tipoAcrescDecresc                		);
					
					$stFiltro  = "   WHERE exercicio    = '".$listaItens[$i]['exercicio']."'";
					$stFiltro .= "   AND cod_entidade   = ".$_REQUEST['inCodEntidadeContrato']."";
					$stFiltro .= "   AND cod_empenho    = ".$listaItens[$i]['cod_empenho']."";
					$obTEmpenhoEmpenho->recuperaTodos($rsEmpenho, $stFiltro);
					
					$obTTCEMGContratoAditivoItem->setDado( 'cod_pre_empenho'         	, $rsEmpenho->getCampo('cod_pre_empenho')	);
					$obTTCEMGContratoAditivoItem->setDado( 'exercicio_pre_empenho'   	, $rsEmpenho->getCampo('exercicio')      	); 

					$obErro = $obTTCEMGContratoAditivoItem->inclusao();
				}
			}
		}

		if( !$obErro->ocorreu() ){
			Sessao::remove( 'arEmpenhos' );
			sistemaLegado::alertaAviso($pgFilt."?stAcao=".$_REQUEST['stAcao'], "Contrato:".$_REQUEST['inNumContrato'  ] .'/'. $_REQUEST['stExercicioContrato']." - Aditivo:".$_REQUEST['inNumAditivo'].'/'.$_REQUEST['stExercicioAditivo'] ,"incluir","aviso", Sessao::getId(), "../");
		}else
			sistemaLegado::exibeAviso( urlencode($obErro->getDescricao()),"unica","erro" );
		
		Sessao::encerraExcecao();
	break;

	case 'alterar' :
		Sessao::setTrataExcecao ( true );
		
		$obErro = new Erro();
		
		//Verificação de existencia de Aditivo
		$obTTCEMGContratoAditivo = new TTCEMGContratoAditivo;
		$stFiltro  = "   WHERE exercicio_contrato 	= '".$_REQUEST['stExercicioContrato']."'";
		$stFiltro .= "   AND nro_aditivo          	=  ".$_REQUEST['inNumAditivo'];
		$stFiltro .= "   AND cod_entidade_contrato	=  ".$_REQUEST['inCodEntidadeContrato'];
		$stFiltro .= "   AND cod_contrato         	=  ".$_REQUEST['inCodContrato'];
		$stFiltro .= "   AND cod_contrato_aditivo	!= ".$_REQUEST['inCodAditivo'];
		
		$obTTCEMGContratoAditivo->recuperaTodos($rsListaAditivo, $stFiltro);
		
		if($rsListaAditivo->inNumLinhas>0)
			$obErro->setDescricao('Aditivo('.$_REQUEST['inNumAditivo'].') já vinculado com o contrato('.$_REQUEST['inNumContrato']."/".$_REQUEST['stExercicioContrato'].').');
		
		//Verificando Existencia de Descrição
		if(!$obErro->ocorreu()&&($_REQUEST['inCodTermoAditivo']==6||$_REQUEST['inCodTermoAditivo']==14)){
			if(!isset($_REQUEST['stDescricaoAditivo'])||$_REQUEST['stDescricaoAditivo']=='')
				$obErro->setDescricao('Descrição do Termo Aditivo inválido!()');
		}
		
		//Verificando Existencia de Nova Data de Término de Contrato
		if(!$obErro->ocorreu()&&($_REQUEST['inCodTermoAditivo']==7||$_REQUEST['inCodTermoAditivo']==13)){
			if(!isset($_REQUEST['dtTerminoAditivo'])||$_REQUEST['dtTerminoAditivo']=='')
				$obErro->setDescricao('Nova Data de Término do Contrato inválido!()');
		}
		
		//Verificando se Existe Empenho para Alteração de Itens
		if(($_REQUEST['inCodTermoAditivo']>=9&&$_REQUEST['inCodTermoAditivo']<=11)||($_REQUEST['inCodTermoAditivo']==14)){
			$arEmpenhos = Sessao::read('arEmpenhos');
			if(count( $arEmpenhos ) == 0)
				$obErro->setDescricao('Não há Empenho vinculado com o Contrato('.$_REQUEST['inNumContrato']."/".$_REQUEST['stExercicioContrato'].')!');
		}
		
		//Se não ocorreu nenhum erro, faz a Alteração do Aditivo
		if ( !$obErro->ocorreu() ){
			$obTTCEMGContratoAditivoItem = new TTCEMGContratoAditivoItem;
			$obTTCEMGContratoAditivoItem->setDado( 'cod_contrato_aditivo'		, $_REQUEST['inCodAditivo']		);
			$obTTCEMGContratoAditivoItem->setDado( 'exercicio'                  	, $_REQUEST['stExercicioAditivo']	);
			$obTTCEMGContratoAditivoItem->setDado( 'cod_entidade'               	, $_REQUEST['inCodEntidadeContrato']	);
			
			$obErro = $obTTCEMGContratoAditivoItem->exclusao();
			
			if ( !$obErro->ocorreu() ){
				$obTTCEMGContratoAditivo = new TTCEMGContratoAditivo;
				$obTTCEMGContratoAditivo->setDado( 'cod_contrato_aditivo'	, $_REQUEST['inCodAditivo']		);
				$obTTCEMGContratoAditivo->setDado( 'exercicio'			, $_REQUEST['stExercicioAditivo']	);
				$obTTCEMGContratoAditivo->setDado( 'cod_entidade'		, $_REQUEST['inCodEntidadeContrato']	);

				$obErro = $obTTCEMGContratoAditivo->exclusao();
				
				if ( !$obErro->ocorreu() ){
					$obTTCEMGContratoAditivo->setDado( 'cod_contrato'		, $_REQUEST['inCodContrato']		);
					$obTTCEMGContratoAditivo->setDado( 'exercicio_contrato'		, $_REQUEST['stExercicioContrato']	);
					$obTTCEMGContratoAditivo->setDado( 'cod_entidade_contrato'	, $_REQUEST['inCodEntidadeContrato']	);
					$obTTCEMGContratoAditivo->setDado( 'cod_contrato_aditivo'	, $_REQUEST['inCodAditivo']		);
					$obTTCEMGContratoAditivo->setDado( 'nro_aditivo'		, $_REQUEST['inNumAditivo']		);
					$obTTCEMGContratoAditivo->setDado( 'exercicio'              	, $_REQUEST['stExercicioAditivo']	);
					$obTTCEMGContratoAditivo->setDado( 'cod_entidade'           	, $_REQUEST['inCodEntidadeContrato']	);
					$obTTCEMGContratoAditivo->setDado( 'num_orgao'              	, $_REQUEST['inNumOrgao']		);
					$obTTCEMGContratoAditivo->setDado( 'num_unidade'            	, $_REQUEST['inNumUnidade']		);
					$obTTCEMGContratoAditivo->setDado( 'data_assinatura'        	, $_REQUEST['dtAssinaturaAditivo']	);
					$obTTCEMGContratoAditivo->setDado( 'cod_tipo_aditivo'       	, $_REQUEST['inCodTermoAditivo']	);
					$obTTCEMGContratoAditivo->setDado( 'data_publicacao'        	, $_REQUEST['dtPublicacaoAditivo']	);
					$obTTCEMGContratoAditivo->setDado( 'cgm_publicacao'         	, $_REQUEST['inVeiculoAditivo']		);
					
					//Se inCodTermoAditivo = 6 ou 14, Tem Descrição
					if($_REQUEST['inCodTermoAditivo']==6||$_REQUEST['inCodTermoAditivo']==14)
						$obTTCEMGContratoAditivo->setDado( 'descricao'      	, $_REQUEST['stDescricaoAditivo']	);
					
					//Se inCodTermoAditivo = 7 ou 13, Tem Nova Data de Término de Contrato
					if($_REQUEST['inCodTermoAditivo']==7||$_REQUEST['inCodTermoAditivo']==13)
						$obTTCEMGContratoAditivo->setDado( 'data_termino'   	, $_REQUEST['dtTerminoAditivo']		);
		
					//Se inCodTermoAditivo = 9, 10, 11 ou 14, Tem Itens alterados
					if(($_REQUEST['inCodTermoAditivo']>=9&&$_REQUEST['inCodTermoAditivo']<=11)||($_REQUEST['inCodTermoAditivo']==14)){
						$countItens = (isset($_REQUEST['qtd_Itens'])) ? $_REQUEST['qtd_Itens'] : 0;
						$count=0;
						$listaItens = array();
						
						for($i=1;$i<=$countItens;$i++){
							if($_REQUEST['inQuantidade_'.$i]!=''){
								if(isset($_REQUEST['inAcrescimoDecrescimo_'.$i])&&($_REQUEST['inAcrescimoDecrescimo_'.$i]=='')){
								    $obErro->setDescricao('Informe o Tipo do Item('.$i.')!');
								    break;
								}
								else{
								    $Identificador  = explode('_', $_REQUEST['Identificador_'.$i]);
								    $listaItens[$count]['cod_empenho'] = $Identificador[0];
								    $listaItens[$count]['exercicio'] = $Identificador[1];
								    $listaItens[$count]['cod_entidade'] = $Identificador[4];
								    $listaItens[$count]['num_item'] = $Identificador[2];
								    $listaItens[$count]['vl_unitario'] = $Identificador[3];
								    $listaItens[$count]['quantidade'] = $_REQUEST['inQuantidade_'.$i];
								    if(isset($_REQUEST['inAcrescimoDecrescimo_'.$i])){
									$listaItens[$count]['tipo'] = $_REQUEST['inAcrescimoDecrescimo_'.$i];
									if($_REQUEST['inAcrescimoDecrescimo_'.$i]==1){
									    $Total = $Total+($Identificador[3]*$_REQUEST['inQuantidade_'.$i]);
									}else{
									    $Total = $Total-($Identificador[3]*$_REQUEST['inQuantidade_'.$i]);    
									}
								    }else{
									$Total = $Total+($Identificador[3]*$_REQUEST['inQuantidade_'.$i]);
								    }
								    $count++;
								}
							}	
						}
						if( !$obErro->ocorreu() && $count==0)
							$obErro->setDescricao('Nenhum Item do(s) Empenho(s) foi Alterado!');  
					}
					
					//Verificando Total e Atribuindo-o.
					$Total=(isset($Total)) ? $Total : 0;
					$obTTCEMGContratoAditivo->setDado( 'valor'                  , str_replace("-","",$Total));
					
					//Verificar Tipo Valor e Atribuindo-o
					$inTipoValor=3;
					if($_REQUEST['inCodTermoAditivo']==9){
						$inTipoValor = 	1;
					}
					else if($_REQUEST['inCodTermoAditivo']==10){
						$inTipoValor = 	2;
					}
					else if(($_REQUEST['inCodTermoAditivo']==11||$_REQUEST['inCodTermoAditivo']==14)&&$Total>0){
						$inTipoValor = 	1;	
					}
					elseif(($_REQUEST['inCodTermoAditivo']==11||$_REQUEST['inCodTermoAditivo']==14)&&$Total<0){
						$inTipoValor = 	2;
					}
					$obTTCEMGContratoAditivo->setDado( 'cod_tipo_valor'         , $inTipoValor		);		
					
					//Se não contem erros, faz a inclusão do Aditivo.
					
					if( !$obErro->ocorreu() )
						$obErro = $obTTCEMGContratoAditivo->inclusao();

					//Se não ocorreu erro ao incluir o Aditivo, verifica se há itens alterados no mesmo.
					if( !$obErro->ocorreu() ){
						$obTTCEMGContratoAditivoItem = new TTCEMGContratoAditivoItem;
						$obTEmpenhoEmpenho = new TEmpenhoEmpenho();
						
						for($i=0;$i<count($listaItens);$i++){
							$obTTCEMGContratoAditivoItem->recuperaProximoContratoAditivoItem($rsCodContratoAditivoItem);
							$inCodContratoAditivoItem = $rsCodContratoAditivoItem->getCampo('cod_contrato_aditivo_item');
							if(!$inCodContratoAditivoItem)
								$inCodContratoAditivoItem = 1;
							
							$obTTCEMGContratoAditivoItem->setDado( 'cod_contrato_aditivo_item'  	, $inCodContratoAditivoItem		);
							$obTTCEMGContratoAditivoItem->setDado( 'cod_contrato_aditivo'		, $_REQUEST['inCodAditivo']		);
							$obTTCEMGContratoAditivoItem->setDado( 'exercicio'                  	, $_REQUEST['stExercicioAditivo']	);
							$obTTCEMGContratoAditivoItem->setDado( 'cod_entidade'               	, $_REQUEST['inCodEntidadeContrato']	);
							$obTTCEMGContratoAditivoItem->setDado( 'cod_empenho'                	, $listaItens[$i]['cod_empenho']	);
							$obTTCEMGContratoAditivoItem->setDado( 'exercicio_empenho'          	, $listaItens[$i]['exercicio']		);
							$obTTCEMGContratoAditivoItem->setDado( 'num_item'                   	, $listaItens[$i]['num_item']		);
							$obTTCEMGContratoAditivoItem->setDado( 'quantidade'             	, str_replace("," ,"." , $listaItens[$i]['quantidade']));
							
							$tipoAcrescDecresc = (isset($listaItens[$i]['tipo'])) ? $listaItens[$i]['tipo'] : ""; 
							$obTTCEMGContratoAditivoItem->setDado( 'tipo_acresc_decresc'		, $tipoAcrescDecresc			);
							
							$stFiltro  = "   WHERE exercicio    = '".$listaItens[$i]['exercicio']."'";
							$stFiltro .= "   AND cod_entidade   = ".$_REQUEST['inCodEntidadeContrato']."";
							$stFiltro .= "   AND cod_empenho    = ".$listaItens[$i]['cod_empenho']."";
							$obTEmpenhoEmpenho->recuperaTodos($rsEmpenho, $stFiltro);
							
							$obTTCEMGContratoAditivoItem->setDado( 'cod_pre_empenho'	, $rsEmpenho->getCampo('cod_pre_empenho')	);
							$obTTCEMGContratoAditivoItem->setDado( 'exercicio_pre_empenho'	, $rsEmpenho->getCampo('exercicio')		); 
		
							$obErro = $obTTCEMGContratoAditivoItem->inclusao();
						}
					}
				}
			}
		}
		
		if( !$obErro->ocorreu() ){
			Sessao::remove( 'arEmpenhos' );
			sistemaLegado::alertaAviso($pgFilt."?stAcao=".$_REQUEST['stAcao'], "Contrato:".$_REQUEST['inNumContrato'  ] .'/'. $_REQUEST['stExercicioContrato']." - Aditivo:".$_REQUEST['inNumAditivo'].'/'.$_REQUEST['stExercicioAditivo'] ,"alterar","aviso", Sessao::getId(), "../");
		}else
			sistemaLegado::exibeAviso( urlencode($obErro->getDescricao()),"unica","erro" );
			
		Sessao::encerraExcecao();
	break;

	case 'excluir':
		Sessao::setTrataExcecao ( true );
		
		$obErro = new Erro();
		
		$obTTCEMGContrato = new TTCEMGContrato;
		$stFiltro  = "   WHERE contrato.exercicio = '".$_REQUEST['stExercicioContrato']."'";
		$stFiltro .= "   AND contrato.nro_contrato = ".$_REQUEST['inNumContrato'];
		$stFiltro .= "   AND contrato.cod_entidade = ".$_REQUEST['inCodEntidade'];
		$stFiltro .= "   AND contrato.cod_objeto >= 1 ";
		$stFiltro .= "   AND contrato.cod_objeto <= 3 ";
		if($_REQUEST['stExercicioContrato']!=''&&$_REQUEST['inNumContrato']!=''&&$_REQUEST['inCodEntidade']!='')
		    $obTTCEMGContrato->recuperaContrato($rsContrato, $stFiltro, $stOrder);

		if($rsContrato->inNumLinhas==1){
			$obTTCEMGContratoAditivo 	= new TTCEMGContratoAditivo;
			$stFiltro  = " WHERE cod_contrato	= ".$rsContrato->getCampo('cod_contrato');
			$stFiltro .= " AND exercicio_contrato	= '".$rsContrato->getCampo('exercicio')."'"; 
			$stFiltro .= " AND cod_entidade_contrato= ".$_REQUEST['inCodEntidade'];
			$stFiltro .= " AND nro_aditivo		= ".$_REQUEST['inNumeroAditivo'];
			$stFiltro .= " AND exercicio       	= '".$_REQUEST['stExercicioAditivo']."'"; 
			$obTTCEMGContratoAditivo->recuperaTodos($rsAditivo, $stFiltro);
			
			if($rsAditivo->inNumLinhas==1){
				$obTTCEMGContratoAditivoItem = new TTCEMGContratoAditivoItem;
				$obTTCEMGContratoAditivoItem->setDado( 'cod_contrato_aditivo'	, $rsAditivo->getCampo('cod_contrato_aditivo')	);
				$obTTCEMGContratoAditivoItem->setDado( 'exercicio'              , $_REQUEST['stExercicioAditivo']		);
				$obTTCEMGContratoAditivoItem->setDado( 'cod_entidade'           , $_REQUEST['inCodEntidade']			);
				
				$obErro = $obTTCEMGContratoAditivoItem->exclusao();
				
				if ( !$obErro->ocorreu() ){
					$obTTCEMGContratoAditivo = new TTCEMGContratoAditivo;
					$obTTCEMGContratoAditivo->setDado( 'cod_contrato_aditivo'	, $rsAditivo->getCampo('cod_contrato_aditivo')	);
					$obTTCEMGContratoAditivo->setDado( 'exercicio'			, $_REQUEST['stExercicioAditivo']		);
					$obTTCEMGContratoAditivo->setDado( 'cod_entidade'		, $_REQUEST['inCodEntidade']			);
		
					$obErro = $obTTCEMGContratoAditivo->exclusao();
				}
			}
		}
		
		if( !$obErro->ocorreu() ){
			sistemaLegado::alertaAviso($pgFilt."?stAcao=".$_REQUEST['stAcao'], "Contrato:".$_REQUEST['inNumContrato'  ] .'/'. $_REQUEST['stExercicioContrato']." - Aditivo:".$_REQUEST['inNumeroAditivo'].'/'.$_REQUEST['stExercicioAditivo'] ,"excluir","aviso", Sessao::getId(), "../");
		}else
			sistemaLegado::exibeAviso( urlencode($obErro->getDescricao()),"unica","erro" );
			
		Sessao::encerraExcecao();
	break;
}
?>