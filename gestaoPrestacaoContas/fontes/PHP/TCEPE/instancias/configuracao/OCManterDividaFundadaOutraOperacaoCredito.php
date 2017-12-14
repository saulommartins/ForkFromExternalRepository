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
 * Página de Oculto de Manter Inventario
 * Data de Criação: 12/03/2014

 * @author Analista:      Eduardo Paculski Schitz
 * @author Desenvolvedor: Arthur Cruz
 */

include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php');
include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php' );
include_once ('../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php');
include_once ( CAM_GA_NORMAS_NEGOCIO."RNorma.class.php");
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php");

$stPrograma = "ManterDividaFundadaOutraOperacaoCredito";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');
$arDividas = Sessao::read('arDividas');

switch ($stCtrl) {
    case 'incluirDividaLista':
        $stJs = incluirDividaLista();
    break;
    
    case 'excluirListaDivida':
        $stJs = excluirListaDivida();
    break;

    case 'montaAlterarDivida':        
        $stJs = montaAlterarDivida();
    break;

    case 'alterarListaDivida':        
        $stJs = alterarListaDivida();
    break;

    case 'enviarFormulario':        
        $stJs = enviarFormulario();
    break;

    case 'montaListaDivida':        
        $stJs = montaListaDivida();
    break;

    case 'buscaLeiAutorizacao':
        buscaLei('inCodLeiAutorizacao', 'stNomeLeiAutorizacao', $_REQUEST['inCodLeiAutorizacao']);
    break;
}

function buscaLei($nomCodLei, $nomNomeLei, $inCodLei){
    $stJs  = "jq('#".$nomCodLei."').val('');\n";
    $stJs .= "jq('#".$nomNomeLei."').html('&nbsp;');";
    if($inCodLei){
        $obTNorma = new TNorma;
        $stFiltro = ' WHERE N.cod_norma='.$inCodLei.' ';
        $obTNorma->recuperaNormasDecreto($rsLei, $stFiltro);
        
        if($rsLei->getNumLinhas()>0){
            $stLei  = $rsLei->getCampo('nom_tipo_norma').' '.$rsLei->getCampo('num_norma_exercicio').' - '.$rsLei->getCampo('nom_norma');
            $stJs  = "jq('#".$nomCodLei."').val('".$inCodLei."');\n";
            $stJs .= "jq('#".$nomNomeLei."').html('".$stLei."');";
        }else
            $stJs .= "alertaAviso('@Código informado não existe. (".$inCodLei.")','form','erro','".Sessao::getId()."');";
    }
    
    echo $stJs;
}

function montaListaDivida(){
    $arDividas = Sessao::read('arDividas');
    
    $rsLeisDDC = new RecordSet();
    $rsLeisDDC->preenche($arDividas);
    
    $obTable = new Table();
    $obTable->setRecordSet($rsLeisDDC);
    $obTable->setSummary('Lista de Dívidas');
    
    $obTable->Head->addCabecalho( 'CGM - Nome'               , 20);
    $obTable->Head->addCabecalho( 'Lei de autorização'       , 20);
    $obTable->Head->addCabecalho( 'Nº do contrato'           , 7 );
    $obTable->Head->addCabecalho( 'Data da lei autorizativa' , 10);

    $obTable->Body->addCampo( '[inCodCredor] - [stNomCGM]', 'C');
    $obTable->Body->addCampo( 'stLeiAutorizacao' , 'C');
    $obTable->Body->addCampo( 'inNumeroContrato' , 'C');
    $obTable->Body->addCampo( 'stDataNorma'      , 'C');

    $obTable->Body->addAcao( 'alterar', "executaFuncaoAjax( 'montaAlterarDivida', '&id=%s');", array( 'id' ));
    $obTable->Body->addAcao( 'excluir', "executaFuncaoAjax( 'excluirListaDivida', '&id=%s');", array( 'id' ));

    $obTable->montaHTML();
    $stHTML = $obTable->getHtml();
    $stHTML = str_replace( "\n" ,"" ,$stHTML);
    $stHTML = str_replace( "  " ,"" ,$stHTML);
    $stHTML = str_replace( "'","\\'",$stHTML);

    $stJs = "window.parent.frames['telaPrincipal'].document.getElementById('spnListaDividas').innerHTML = '".$stHTML."';";
    $stJs.= "window.parent.frames['telaPrincipal'].limparDivida();";
    
    return $stJs;    
}

function incluirDividaLista(){
    GLOBAL $request;
    $arDividas  = Sessao::read('arDividas');
    $arTmp = array();
    $obErro = executaValidacao();
    
    if(!$obErro->ocorreu()) {    
        $obNorma = new RNorma;
        $obNorma->setCodNorma($request->get('inCodLeiAutorizacao'));
        $obNorma->listarDecreto($rsNorma);

        $obTCGM = new TCGM;
        $obTCGM->setDado('numcgm', $request->get('inCodCredor'));
        $obTCGM->recuperaPorChave($rsCGM);
    
        $arTmp['id']                   = date('Ymdhisu').mt_rand(); //Gerador de ID aleatório
        $arTmp['stExercicio']          = $request->get('stExercicio');
        $arTmp['inCodEntidade']        = $request->get('inCodEntidade');
        $arTmp['inCodCredor']          = $request->get('inCodCredor');
        $arTmp['stNomCGM']             = $rsCGM->getCampo('nom_cgm');
        $arTmp['inCodLeiAutorizacao']  = $request->get('inCodLeiAutorizacao');
        $arTmp['stLeiAutorizacao']     = $request->get('inCodLeiAutorizacao')." - ".$rsNorma->getCampo('nom_tipo_norma')." ".$rsNorma->getCampo('num_norma_exercicio')." - ".$rsNorma->getCampo('nom_norma');
        $arTmp['stDataNorma']          = $rsNorma->getCampo('dt_assinatura');
        $arTmp['inNumeroContrato']     = $request->get('inNumeroContrato');
        $arTmp['vlSaldoAnterior']      = $request->get('vlSaldoAnterior');
        $arTmp['vlInscricaoExercicio'] = $request->get('vlInscricaoExercicio');
        $arTmp['vlBaixaExercicio']     = $request->get('vlBaixaExercicio');
    
        $arDividas[] = $arTmp;
    } else {
        echo 'alertaAviso("'.$obErro->getDescricao().'","n_incluir","erro","");';
        die;
    }
    
    Sessao::write('arDividas', $arDividas);
    echo montaListaDivida();
}

function alterarListaDivida(){
    GLOBAL $request;
    $arTmp   = array();
    $inCount = 0;
    $arDividas = Sessao::read('arDividas');

    $obErro = executaValidacao();
    if(!$obErro->ocorreu()) {
        foreach ($arDividas as $key => $value) {
            
            if ($value['id'] === $request->get('id')) {
                $obNorma = new RNorma;
                $obNorma->setCodNorma($request->get('inCodLeiAutorizacao'));
                $obNorma->listarDecreto($rsNorma);
                
                $obTCGM = new TCGM;
                $obTCGM->setDado('numcgm', $request->get('inCodCredor'));
                $obTCGM->recuperaPorChave($rsCGM);
                
                $arTmp[$inCount]['id']                   = $request->get('id');
                $arTmp[$inCount]['stExercicio']          = $request->get('stExercicio');
                $arTmp[$inCount]['inCodEntidade']        = $request->get('inCodEntidade');
                $arTmp[$inCount]['inCodCredor']          = $request->get('inCodCredor');
                $arTmp[$inCount]['stNomCGM']             = $rsCGM->getCampo('nom_cgm');
                $arTmp[$inCount]['inCodLeiAutorizacao']  = $request->get('inCodLeiAutorizacao');
                $arTmp[$inCount]['stLeiAutorizacao']     = $request->get('inCodLeiAutorizacao')." - ".$rsNorma->getCampo('nom_tipo_norma')." ".$rsNorma->getCampo('num_norma_exercicio')." - ".$rsNorma->getCampo('nom_norma');
                $arTmp[$inCount]['stDataNorma']          = $rsNorma->getCampo('dt_assinatura');
                $arTmp[$inCount]['inNumeroContrato']     = $request->get('inNumeroContrato');
                $arTmp[$inCount]['vlSaldoAnterior']      = $request->get('vlSaldoAnterior');
                $arTmp[$inCount]['vlInscricaoExercicio'] = $request->get('vlInscricaoExercicio');
                $arTmp[$inCount]['vlBaixaExercicio']     = $request->get('vlBaixaExercicio');
                
            }else{
                $obNorma = new RNorma;
                $obNorma->setCodNorma($value['inCodLeiAutorizacao']);
                $obNorma->listarDecreto($rsNorma);
                
                $obTCGM = new TCGM;
                $obTCGM->setDado('numcgm', $value['inCodCredor']);
                $obTCGM->recuperaPorChave($rsCGM);
                
                $arTmp[$inCount]['id']                   = $value['id'];
                $arTmp[$inCount]['stExercicio']          = $value['stExercicio'];
                $arTmp[$inCount]['inCodEntidade']        = $value['inCodEntidade'];
                $arTmp[$inCount]['inCodCredor']          = $value['inCodCredor'];
                $arTmp[$inCount]['stNomCGM']             = $rsCGM->getCampo('nom_cgm');
                $arTmp[$inCount]['inCodLeiAutorizacao']  = $value['inCodLeiAutorizacao'];
                $arTmp[$inCount]['stLeiAutorizacao']     = $value['inCodLeiAutorizacao']." - ".$rsNorma->getCampo('nom_tipo_norma')." ".$rsNorma->getCampo('num_norma_exercicio')." - ".$rsNorma->getCampo('nom_norma');
                $arTmp[$inCount]['stDataNorma']          = $rsNorma->getCampo('dt_assinatura');
                $arTmp[$inCount]['inNumeroContrato']     = $value['inNumeroContrato'];
                $arTmp[$inCount]['vlSaldoAnterior']      = $value['vlSaldoAnterior'];
                $arTmp[$inCount]['vlInscricaoExercicio'] = $value['vlInscricaoExercicio'];
                $arTmp[$inCount]['vlBaixaExercicio']     = $value['vlBaixaExercicio'];
            }
            $inCount++;
        }
    } else {
        echo 'alertaAviso("'.$obErro->getDescricao().'","n_incluir","erro","");';
        die;
    }
    
    Sessao::write('arDividas', $arTmp);
    echo montaListaDivida();
    $stJs  = "document.getElementById('btIncluirDivida').disabled = false;";
    $stJs .= "document.getElementById('btAlterarDivida').disabled = true;";
    
    return $stJs;
}

function montaAlterarDivida(){
    $arTmp   = array();
    $arDividas = Sessao::read('arDividas');

    $stJs = "window.parent.frames['telaPrincipal'].limparDivida();";
    
    foreach ($arDividas as $key => $value) {
        if ($value['id'] === $_REQUEST['id']) {
            $stJs .= "jQuery('#id').val('".$value['id']."'); ";
            $stJs .= "jQuery('#stExercicio').val('".$value['stExercicio']."'); ";
            $stJs .= "jQuery('#inCodEntidade').val('".$value['inCodEntidade']."'); ";
            
            $stJs .= "jQuery('#inCGM').val('".$value['inCodCredor']."'); ";
            $stJs .= "jQuery('#stNomCredor').html('".$value['stNomCGM']."'); ";
            
            $stJs .= "jQuery('#inCodLeiAutorizacao').val('".$value['inCodLeiAutorizacao']."'); ";
            $stJs .= "jQuery('#stNomeLeiAutorizacao').html('".$value['stLeiAutorizacao']."'); ";
            $stJs .= "jQuery('#stDataNorma').html('".$value['stDataNorma']."'); ";
            $stJs .= "jQuery('#inNumeroContrato').val('".$value['inNumeroContrato']."'); ";
            $stJs .= "jQuery('#vlSaldoAnterior').val('".$value['vlSaldoAnterior']."'); ";
            $stJs .= "jQuery('#vlInscricaoExercicio').val('".$value['vlInscricaoExercicio']."'); ";
            $stJs .= "jQuery('#vlBaixaExercicio').val('".$value['vlBaixaExercicio']."'); ";
        }
    }
    
    $stJs .= "jQuery('#btIncluirDivida').attr('disabled', true);";
    $stJs .= "jQuery('#btAlterarDivida').attr('disabled', false);"; 
    return $stJs; 
}

function excluirListaDivida(){
    GLOBAL $request;
    $arTmp   = array();
    $inCount = 0;
    $arDividas = Sessao::read('arDividas');

    foreach ($arDividas as $key => $value) {
        if ($value['id'] != $request->get('id')) {
    
            $obNorma = new RNorma;
            $obNorma->setCodNorma($value['inCodLeiAutorizacao']);
            $obNorma->listarDecreto($rsNorma);
            
            $obTCGM = new TCGM;
            $obTCGM->setDado('numcgm', $value['inCodCredor']);
            $obTCGM->recuperaPorChave($rsCGM);
            
            $arTmp[$inCount]['id']                   = $value['id'];
            $arTmp[$inCount]['stExercicio']          = $value['stExercicio'];
            $arTmp[$inCount]['inCodEntidade']        = $value['inCodEntidade'];
            $arTmp[$inCount]['inCodCredor']          = $value['inCodCredor'];
            $arTmp[$inCount]['stNomCGM']             = $rsCGM->getCampo('nom_cgm');
            $arTmp[$inCount]['inCodLeiAutorizacao']  = $value['inCodLeiAutorizacao'];
            $arTmp[$inCount]['stLeiAutorizacao']     = $value['inCodLeiAutorizacao']." - ".$rsNorma->getCampo('nom_tipo_norma')." ".$rsNorma->getCampo('num_norma_exercicio')." - ".$rsNorma->getCampo('nom_norma');
            $arTmp[$inCount]['stDataNorma']          = $value['stDataNorma'];
            $arTmp[$inCount]['inNumeroContrato']     = $value['inNumeroContrato'];
            $arTmp[$inCount]['vlSaldoAnterior']      = $value['vlSaldoAnterior'];
            $arTmp[$inCount]['vlInscricaoExercicio'] = $value['vlInscricaoExercicio'];
            $arTmp[$inCount]['vlBaixaExercicio']     = $value['vlBaixaExercicio'];
            
            $inCount++;
        }
    }
    
    Sessao::write('arDividas', $arTmp);
    echo montaListaDivida();
}

function executaValidacao() {
    
    $obErro = new Erro;
    $arDividas = Sessao::read('arDividas');

    if(is_array($arDividas)){
        foreach ($arDividas as $key => $value) {
            if(!($value['id'] === $_REQUEST['id'])){
                if ($value['inNumeroContrato'] === $_REQUEST['inNumeroContrato'] ) {
                    $obErro->setDescricao("Não foi possível inserir o Contrato de dívida. O número de contrato ".$_REQUEST['inNumeroContrato']." já existe na lista");
                    return $obErro;
                }
            }
        }
    }
    
    if ($_REQUEST['inCodCredor'] == '') {
        $obErro->setDescricao("CGM do Credor é inválido");

    } elseif ($_REQUEST['inCodLeiAutorizacao'] == '') {
        $obErro->setDescricao("Lei de autorização inválida");

    } elseif ($_REQUEST['inNumeroContrato'] == '') {
        $obErro->setDescricao("Nº contrato de dívida inválido");

    } elseif ($_REQUEST['vlSaldoAnterior'] == '') {
        $obErro->setDescricao("Saldo anterior inválido");

    } elseif ($_REQUEST['vlInscricaoExercicio'] ==  '') {
        $obErro->setDescricao("Inscrições no exercício  inválido");
    
    } elseif ($_REQUEST['vlBaixaExercicio'] ==  '') {
        $obErro->setDescricao("Baixa no exercício inválido");
    }

    return $obErro;
}

function enviarFormulario(){
    GLOBAL $request;
    $arDividas = Sessao::read('arDividas');
    
    if(is_array($arDividas)){
        $stJs = "document.frm.submit();";
    }else if($request->get('inNumContratoDivida') != ""){
        $stJs = "document.frm.submit();";
    }else{
       $stJs = "Valida()";
    }
    
    return $stJs;
}

if ($stJs) {
    echo $stJs;
}
