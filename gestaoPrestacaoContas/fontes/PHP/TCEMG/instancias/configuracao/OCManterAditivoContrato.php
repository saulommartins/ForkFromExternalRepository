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
    * Oculto de Aditivo de Contrato TCEMG
    * Data de Criação   : 30/04/2014
    
    * @author Analista      Silvia Martins Silva
    * @author Desenvolvedor Michel Teixeira
    
    * @package URBEM
    * @subpackage
    
    * @ignore
    
    $Id: OCManterAditivoContrato.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stPrograma = "ManterAditivoContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

function MontaItensAditivo($inCodTermoAditivo){
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoItemPreEmpenho.class.php" );
    
    $arEmpenhos = Sessao::read('arEmpenhos');
    $inCount=0;
    
    if(count( $arEmpenhos ) > 0){
        $obTEmpenhoEmpenho = new TEmpenhoEmpenho();
        
        for($i=0;$i<count( $arEmpenhos );$i++){            
            $stFiltro  = "   WHERE exercicio    = '".$arEmpenhos[$i]['exercicio']."'";
            $stFiltro .= "   AND cod_entidade = ".$arEmpenhos[$i]['cod_entidade']."";
            $stFiltro .= "   AND cod_empenho  = ".$arEmpenhos[$i]['cod_empenho']."";
            $obTEmpenhoEmpenho->recuperaTodos($rsEmpenho, $stFiltro);

            $obTEmpenhoItemPreEmpenho  = new TEmpenhoItemPreEmpenho;
            $stFiltro  = "   WHERE exercicio    = '".$rsEmpenho->getCampo('exercicio')."'";
            $stFiltro .= "   AND cod_pre_empenho = ".$rsEmpenho->getCampo('cod_pre_empenho')."";
            $obTEmpenhoItemPreEmpenho->setDado( "cod_pre_empenho", $rsEmpenho->getCampo('cod_pre_empenho') );
            $obTEmpenhoItemPreEmpenho->setDado( "exercicio"      , $rsEmpenho->getCampo('exercicio')  );
            $obTEmpenhoItemPreEmpenho->recuperaTodos($rsItem, $stFiltro, ' num_item');
            
            while( !$rsItem->eof()){
                $rsItensFiltrado[$inCount]['empenho'] = $rsEmpenho->getCampo('cod_empenho')."_".$rsEmpenho->getCampo('exercicio')."_".$rsItem->getCampo('num_item')."_".($rsItem->getCampo('vl_total')/$rsItem->getCampo('quantidade')."_".$rsEmpenho->getCampo('cod_entidade'));
                $rsItensFiltrado[$inCount]['cod_empenho'] = $rsEmpenho->getCampo('cod_empenho')."/".$rsEmpenho->getCampo('exercicio');
                $rsItensFiltrado[$inCount]['nom_item'] = $rsItem->getCampo('nom_item');
                $rsItensFiltrado[$inCount]['quantidade'] = str_replace("." ,"," ,$rsItem->getCampo('quantidade'));
                $rsItensFiltrado[$inCount]['vl_unitario'] = ($rsItem->getCampo('vl_total')/$rsItem->getCampo('quantidade'));
                $rsItensFiltrado[$inCount]['vl_total'] = $rsItem->getCampo('vl_total');
    
                $inCount++;
                $rsItem->proximo();
            }
        }
            
        $rsLista = new RecordSet;
        $rsLista->preenche( $rsItensFiltrado );
        $rsLista->addFormatacao('vl_total'   , 'NUMERIC_BR');
        $rsLista->addFormatacao('vl_unitario', 'NUMERIC_BR');

        $obLista = new Lista;
        $obLista->setMostraPaginacao(false);
        $obLista->setRecordSet($rsLista);
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(3);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Empenho');
        $obLista->ultimoCabecalho->setWidth(7);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Descrição');
        $obLista->ultimoCabecalho->setWidth(50);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Quantidade Original');
        $obLista->ultimoCabecalho->setWidth(13);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Valor Unitário');
        $obLista->ultimoCabecalho->setWidth(9);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Valor Total');
        $obLista->ultimoCabecalho->setWidth(8);
        $obLista->commitCabecalho();
    
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('cod_empenho');
        $obLista->ultimoDado->setAlinhamento('CENTRO');
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('nom_item');
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo('quantidade');
        $obLista->ultimoDado->setAlinhamento('DIREITA');
        $obLista->commitDado();
        
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('vl_unitario');
        $obLista->ultimoDado->setAlinhamento('DIREITA');
        $obLista->commitDado();
        
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('vl_total');
        $obLista->ultimoDado->setAlinhamento('DIREITA');
        $obLista->commitDado();
        
        //Define objeto Quantidade Nova
        $obTxtQuantidade = new Numerico;
        $obTxtQuantidade->setName           ( 'inQuantidade' );
        $obTxtQuantidade->setSize           ( 10 );
        $obTxtQuantidade->setMaxLength      ( 10 );
        $obTxtQuantidade->setInteiro        ( true );
        $obTxtQuantidade->setNegativo	    ( false );
        $obTxtQuantidade->setDecimais 	    ( 4 );
    
        $obLista->addCabecalho('Quantidade Nova', 10);
        $obLista->addDadoComponente( $obTxtQuantidade );
        $obLista->commitDadoComponente();
    
        $obHdnIdentificador = new Hidden;
        $obHdnIdentificador->setName ( "Identificador" );
        $obHdnIdentificador->setValue( '[empenho]' );
        
        $obCmbAcrescimoDecrescimo= new Select;
        $obCmbAcrescimoDecrescimo->setName          ( "inAcrescimoDecrescimo"	);
        $obCmbAcrescimoDecrescimo->setId            ( "inAcrescimoDecrescimo" 	);
        $obCmbAcrescimoDecrescimo->setStyle         ( "width: 100px"	        );
        $obCmbAcrescimoDecrescimo->addOption  	    ( "", "Selecione"	        );
        $obCmbAcrescimoDecrescimo->addOption  	    ( "1", "Acréscimo"	        );
        $obCmbAcrescimoDecrescimo->addOption  	    ( "2", "Decréscimo"	        );
        $obCmbAcrescimoDecrescimo->setObrigatorio   ( true			);        
        
        if($inCodTermoAditivo==14||$inCodTermoAditivo==11){
            $obLista->addCabecalho('Tipo', 5);
            $obLista->addDadoComponente( $obCmbAcrescimoDecrescimo );
            $obLista->commitDadoComponente();
        }
        
        $obLista->addCabecalho('', 0);
        $obLista->addDadoComponente( $obHdnIdentificador );
        $obLista->commitDadoComponente();
        
    
        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace("\n" ,"" ,$stHTML );
        $stHTML = str_replace(chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace("  " ,"" ,$stHTML );
        $stHTML = str_replace("'","\\'",$stHTML );
        $stHTML = str_replace("\\\'","\\'",$stHTML );
        
        $stJs = "f.qtd_Itens.value = ".$inCount.";";   

        echo $stJs;
                
        return $stHTML;
    }
}

function MontaTermoAditivo($inCodTermoAditivo){
    
    $obDtTerminoAditivo = new Data;
    $obDtTerminoAditivo->setName    ( "dtTerminoAditivo"                            );
    $obDtTerminoAditivo->setRotulo  ( "Nova Data de Término do Contrato"            );
    $obDtTerminoAditivo->setTitle   ( 'Informe a Nova Data de Término do Contrato.' );
    $obDtTerminoAditivo->setNull    ( false                                         );
    
    $obTxtDescricao = new TextArea;
    $obTxtDescricao->setName         	( "stDescricaoAditivo"        	);
    $obTxtDescricao->setId           	( "stDescricaoAditivo"        	);
    $obTxtDescricao->setRotulo       	( "Descrição do Termo Aditivo"	);
    $obTxtDescricao->setNull         	( false                       	);
    $obTxtDescricao->setRows         	( 2                           	);
    $obTxtDescricao->setCols         	( 100                       	);
    $obTxtDescricao->setMaxCaracteres	( 250                       	);
    
        //Valor do aditivo
    $obTxtVlAditivo = new Moeda;
    $obTxtVlAditivo->setName     ( "nuVlAditivo"      );
    $obTxtVlAditivo->setRotulo   ( "Valor do Termo de Aditivo" );
    $obTxtVlAditivo->setAlign    ( 'RIGHT'             );
    $obTxtVlAditivo->setTitle    ( ""                  );
    $obTxtVlAditivo->setMaxLength( 19                  );
    $obTxtVlAditivo->setSize     ( 21                  );
    $obTxtVlAditivo->setValue    ( ''                  );
    $obTxtVlAditivo->setNull     ( false               );
    
    $obCmbAcrescimoDecrescimo2= new Select;
    $obCmbAcrescimoDecrescimo2->setName          ( "inVlAcrescimoDecrescimo"	);
    $obCmbAcrescimoDecrescimo2->setId            ( "inVlAcrescimoDecrescimo" 	);
    $obCmbAcrescimoDecrescimo2->setRotulo        ( "Tipo de Alteração do Valor" 	);
    $obCmbAcrescimoDecrescimo2->setStyle         ( "width: 100px"	        );
    $obCmbAcrescimoDecrescimo2->addOption  	     ( "", "Selecione"	        );
    $obCmbAcrescimoDecrescimo2->addOption  	     ( "1", "Acréscimo"	        );
    $obCmbAcrescimoDecrescimo2->addOption  	     ( "2", "Decréscimo"	        );
    $obCmbAcrescimoDecrescimo2->setObrigatorio   ( true			);        
    
    //Início do Formulário
    $obFormulario = new Formulario;
    $obFormulario->obForm->setName("adit");
    
    $SpnItensAditivo= new Span;
    $SpnItensAditivo->SetId('spnItensAditivo');
    
    if($inCodTermoAditivo==7||$inCodTermoAditivo==13){
        $obFormulario->addComponente ( $obDtTerminoAditivo );
    }
    
    if($inCodTermoAditivo==6||$inCodTermoAditivo==14){
        $obFormulario->addComponente ( $obTxtDescricao );
    }
    
    if($inCodTermoAditivo==9||$inCodTermoAditivo==10||$inCodTermoAditivo==11||$inCodTermoAditivo==14){
        $obFormulario->addSpan ( $SpnItensAditivo );
        $aditItens=true;
    }
    
    if($inCodTermoAditivo==4||$inCodTermoAditivo==5){
        $obFormulario->addComponente ( $obTxtVlAditivo );
        $obFormulario->addComponente ( $obCmbAcrescimoDecrescimo2 );

    }
    
    $obFormulario->montaInnerHTML();
    
    $stJs= "jQuery('#spnTermoAditivo').html('".$obFormulario->getHTML()."');";
    
    if(isset($aditItens)){
        $obItens = MontaItensAditivo($inCodTermoAditivo);
        $stJs .= "jQuery('#spnItensAditivo').html('".$obItens."');";
    }
    
    return $stJs;
    
}

switch( $stCtrl ){
    case "MontaTermoAditivo":
        $stJs = MontaTermoAditivo($_REQUEST["inCodTermoAditivo"]);
            
        echo $stJs;
    break;

    case "carregaLista":
	if($_REQUEST['inNumContrato']!=''&&$_REQUEST['inCodEntidade']!=''&&$_REQUEST['stExercicioContrato']!=''&&$_REQUEST['inNumeroAditivo']!=''&&$_REQUEST['stExercicioAditivo']!=''){
	    include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContrato.class.php' );
            $obTTCEMGContrato = new TTCEMGContrato;
            $stFiltro  = " WHERE nro_contrato   =  ".$_REQUEST['inNumContrato'];
            $stFiltro .= " AND exercicio        = '".$_REQUEST['stExercicioContrato']."'";
            $stFiltro .= " AND cod_entidade     = '".$_REQUEST['inCodEntidade']."'";
            $obTTCEMGContrato->recuperaTodos($rsContrato, $stFiltro);

	    if($rsContrato->inNumLinhas==1){
		include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoAditivo.class.php' );
		include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoAditivoItem.class.php' );
		$obTTCEMGContratoAditivo 	= new TTCEMGContratoAditivo;
		$obTTCEMGContratoAditivoItem	= new TTCEMGContratoAditivoItem;
		
		$stFiltro  = " WHERE cod_contrato	= ".$rsContrato->getCampo('cod_contrato');
		$stFiltro .= " AND exercicio_contrato	= '".$rsContrato->getCampo('exercicio')."'"; 
		$stFiltro .= " AND cod_entidade_contrato= ".$_REQUEST['inCodEntidade'];
		$stFiltro .= " AND nro_aditivo		= ".$_REQUEST['inNumeroAditivo'];
		$stFiltro .= " AND exercicio       	= '".$_REQUEST['stExercicioAditivo']."'"; 
		$obTTCEMGContratoAditivo->recuperaTodos($rsAditivo, $stFiltro);

		if($rsAditivo->inNumLinhas==1){
		    $stJs .= "f.inCodAditivo.value		= '".$rsAditivo->getCampo('cod_contrato_aditivo')."';	\n";
		    $stJs .= "f.stExercicioAditivoAtual.value	= '".$rsAditivo->getCampo('exercicio')."';		\n";
		    $stJs .= "f.stExercicioAditivo.value	= '".$rsAditivo->getCampo('exercicio')."';		\n";
		    $stJs .= "f.inNumAditivo.value		= '".$rsAditivo->getCampo('nro_aditivo')."';		\n";
		    $stJs .= "f.dtAssinaturaAditivo.value	= '".$rsAditivo->getCampo('data_assinatura')."';	\n";
		    $stJs .= "f.inCodTermoAditivo.value		= '".$rsAditivo->getCampo('cod_tipo_aditivo')."';	\n";
		    $stJs .= "f.inVeiculoAditivo.value		= '".$rsAditivo->getCampo('cgm_publicacao')."';		\n";
		    $stJs .= "f.dtPublicacaoAditivo.value	= '".$rsAditivo->getCampo('data_publicacao')."';	\n";
		    
		    $where = " WHERE numcgm=".$rsAditivo->getCampo('cgm_publicacao');
		    $NomPublicacao = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm', $where);
		    $stJs .= "d.getElementById('stNomCgmVeiculoPublicadadeAditivo').innerHTML = '".$NomPublicacao."';	\n";
		    
		    $inCodTermoAditivo = $rsAditivo->getCampo('cod_tipo_aditivo');
		    $stJs .= MontaTermoAditivo($inCodTermoAditivo);
		    
		    if($inCodTermoAditivo==7||$inCodTermoAditivo==13)
			$stJs .= "f.dtTerminoAditivo.value	= '".$rsAditivo->getCampo('data_termino')."';		\n";
		    else if($inCodTermoAditivo==6||$inCodTermoAditivo==14)
			$stJs .= "f.stDescricaoAditivo.value	= '".$rsAditivo->getCampo('descricao')."';		\n";
		    
		    if($inCodTermoAditivo==9||$inCodTermoAditivo==10||$inCodTermoAditivo==11||$inCodTermoAditivo==14){
			$stFiltro  = " WHERE cod_contrato_aditivo   = ".$rsAditivo->getCampo('cod_contrato_aditivo');
			$stFiltro .= " AND exercicio                = '".$rsAditivo->getCampo('exercicio')."'"; 
			$stFiltro .= " AND cod_entidade             = ".$rsAditivo->getCampo('cod_entidade');
			$obTTCEMGContratoAditivoItem->recuperaTodos($rsAditivoItem, $stFiltro);
			
			$stJs .= " var count = d.getElementById('hdnNumLinhas').value;					\n";
			while( !$rsAditivoItem->eof()){
			    $where  = "  WHERE exercicio    = '".$rsAditivoItem->getCampo('exercicio_pre_empenho');
			    $where .= "' AND cod_pre_empenho=  ".$rsAditivoItem->getCampo('cod_pre_empenho');
			    $where .= "  AND num_item       =  ".$rsAditivoItem->getCampo('num_item');
			    $vlTotalItem = SistemaLegado::pegaDado('vl_total', 'empenho.item_pre_empenho', $where);
			    $quantidadeItem = SistemaLegado::pegaDado('quantidade', 'empenho.item_pre_empenho', $where);
			    $vlUnitario = $vlTotalItem/$quantidadeItem;
			    
			    $Aditivo  = $rsAditivoItem->getCampo('cod_empenho')."_".$rsAditivoItem->getCampo('exercicio_empenho')."_";
			    $Aditivo .= $rsAditivoItem->getCampo('num_item')."_".$vlUnitario."_".$rsAditivoItem->getCampo('cod_entidade');
			    
			    $stJs .= " for(i=1;i<=count;i++){
				var Identificador = d.getElementById('Identificador_'+i).value;
				var Aditivo = '".$Aditivo."';
				var TipoAcrescDecresc = '".$rsAditivoItem->getCampo('tipo_acresc_decresc')."';
				if(Identificador==Aditivo){
				    d.getElementById('inQuantidade_'+i).value='".str_replace("." ,"," ,$rsAditivoItem->getCampo('quantidade') )."';
				    if(TipoAcrescDecresc!=''){
					d.getElementsByName('inAcrescimoDecrescimo_'+i)[0].value=TipoAcrescDecresc;
				    }
				}
			    }";
			    
			    $rsAditivoItem->proximo();
			}
		    }
		}
	    }
	}

	echo $stJs;
    break;
}
?>
