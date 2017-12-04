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

include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php'     );
include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php'  );
include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConfiguracaoDDC.class.php' );
include_once ( CAM_GA_NORMAS_NEGOCIO."RNorma.class.php" );
include_once ('../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php');

$stPrograma = "ManterConfiguracaoArquivoDDC";
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
}

function incluirDividaLista(){
    GLOBAL $request;
    $arDividas  = Sessao::read('arDividas');
    $obErro = executaValidacao($_REQUEST);
    $arTmp = array();
    
    $obNorma = new RNorma;
    $obNorma->setCodNorma( $request->get('inCodLeiAutorizacao') );
    $obNorma->listarDecreto( $rsNorma );

    if(!$obErro->ocorreu()){
        $arTmp['id']                     = count($arDividas);
        $arTmp['inExercicio']            = $request->get('inExercicio');
        $arTmp['inMes']                  = $request->get('inMes');
        $arTmp['inCodEntidade']          = $request->get('inCodEntidade');
        $arTmp['inCodLeiAutorizacao']    = $request->get('inCodLeiAutorizacao');
        $arTmp['stLeiAutorizacao']       = $request->get('inCodLeiAutorizacao')." - ".$rsNorma->getCampo('nom_tipo_norma')." ".$rsNorma->getCampo('num_norma_exercicio')." - ".$rsNorma->getCampo('nom_norma');
        $arTmp['stNomeLeiAutorizacao']   = $request->get('stNomeLeiAutorizacao');
        $arTmp['inNumContratoDivida']    = $request->get('inNumContratoDivida');
        $arTmp['dtAssinaturaDivida']     = $request->get('dtAssinaturaDivida');
        $arTmp['stContratoDecLei']       = $request->get('stContratoDecLei');
        $arTmp['stObjetoContrato']       = $request->get('stObjetoContrato');
        $arTmp['stDescDivida']           = $request->get('stDescDivida');
        $arTmp['inTipoLancamento']       = $request->get('inTipoLancamento');
        $arTmp['inCGMCredor']            = $request->get('inCGMCredor');
        $arTmp['stNomeCGMCredor']        = $request->get('stNomeCGMCredor');
        $arTmp['stJustificativaCancelamento'] = $request->get('stJustificativaCancelamento');
        $arTmp['flValorSaldoAnterior']   = $request->get('flValorSaldoAnterior');
        $arTmp['flValorContratacaoMes']  = $request->get('flValorContratacaoMes');
        $arTmp['flValorAmortizacaoMes']  = $request->get('flValorAmortizacaoMes');
        $arTmp['flValorCancelamentoMes'] = $request->get('flValorCancelamentoMes');
        $arTmp['flValorEncampacaoMes']   = $request->get('flValorEncampacaoMes');
        $arTmp['flValorAtualizacaoMes']  = $request->get('flValorAtualizacaoMes');
        $arTmp['flValorSaldoAtual']      = $request->get('flValorSaldoAtual');
        
        $arDividas[] = $arTmp;
        
        Sessao::write('arDividas', $arDividas);
        echo montaListaDivida($arDividas);

    } else {
       $stJs = "alertaAviso('".$obErro->getDescricao()."!','form','erro','".Sessao::getId()."');\n";
       return $stJs;
    }
}

function montaListaDivida($arDividas){
    
    $rsLeisDDC = new RecordSet();
    $rsLeisDDC->preenche($arDividas);
    
    $obTable = new Table();
    $obTable->setRecordSet( $rsLeisDDC );
    $obTable->setSummary('Lista de Dívidas');
       
    $obTable->Head->addCabecalho( 'Lei de autorização' , 20 );
    $obTable->Head->addCabecalho( 'Nº do contrato' , 7 );
    $obTable->Head->addCabecalho( 'Data de assinatura' , 10 );
    $obTable->Head->addCabecalho( 'Nº do documento do Credor' , 7 );

    $obTable->Body->addCampo( 'stLeiAutorizacao', 'C' );
    $obTable->Body->addCampo( 'inNumContratoDivida', 'C' );
    $obTable->Body->addCampo( 'dtAssinaturaDivida', 'C' );
    $obTable->Body->addCampo( 'stNomeCGMCredor', 'C' );

    $obTable->Body->addAcao( 'alterar', "executaFuncaoAjax( 'montaAlterarDivida', '&id=%s');", array( 'id' ) );
    $obTable->Body->addAcao( 'excluir', "executaFuncaoAjax( 'excluirListaDivida', '&id=%s');", array( 'id' ) );

    $obTable->montaHTML();
    $stHTML = $obTable->getHtml();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs = "window.parent.frames['telaPrincipal'].document.getElementById('spnListaDividas').innerHTML = '".$stHTML."';";
    $stJs.= "window.parent.frames['telaPrincipal'].limparDivida();";
    
    return $stJs;    
}

function excluirListaDivida(){
    GLOBAL $request;
    $arTmp   = array();
    $inCount = 0;
    $arDividas = Sessao::read('arDividas');

    foreach ($arDividas as $key => $value) {
        if ($value['id'] != $request->get('id')) {
    
            $arTmp[$inCount]['id']                     = $inCount;
            $arTmp[$inCount]['inExercicio']            = $value['inExercicio'];
            $arTmp[$inCount]['inMes']                  = $value['inMes'];
            $arTmp[$inCount]['inCodEntidade']          = $value['inCodEntidade'];
            $arTmp[$inCount]['inCodLeiAutorizacao']    = $value['inCodLeiAutorizacao'];
            $arTmp[$inCount]['stLeiAutorizacao']       = $value['stLeiAutorizacao'];
            $arTmp[$inCount]['stNomeLeiAutorizacao']   = $value['stNomeLeiAutorizacao'];
            $arTmp[$inCount]['inNumContratoDivida']    = $value['inNumContratoDivida'];
            $arTmp[$inCount]['dtAssinaturaDivida']     = $value['dtAssinaturaDivida'];
            $arTmp[$inCount]['stContratoDecLei']       = $value['stContratoDecLei'];
            $arTmp[$inCount]['stObjetoContrato']       = $value['stObjetoContrato'];
            $arTmp[$inCount]['stDescDivida']           = $value['stDescDivida'];
            $arTmp[$inCount]['inTipoLancamento']       = $value['inTipoLancamento'];
            $arTmp[$inCount]['inCGMCredor']            = $value['inCGMCredor'];
            $arTmp[$inCount]['stNomeCGMCredor']        = $value['stNomeCGMCredor'];
            $arTmp[$inCount]['stJustificativaCancelamento'] = $value['stJustificativaCancelamento'];
            $arTmp[$inCount]['flValorSaldoAnterior']   = $value['flValorSaldoAnterior'];
            $arTmp[$inCount]['flValorContratacaoMes']  = $value['flValorContratacaoMes'];
            $arTmp[$inCount]['flValorAmortizacaoMes']  = $value['flValorAmortizacaoMes'];
            $arTmp[$inCount]['flValorCancelamentoMes'] = $value['flValorCancelamentoMes'];
            $arTmp[$inCount]['flValorEncampacaoMes']   = $value['flValorEncampacaoMes'];
            $arTmp[$inCount]['flValorAtualizacaoMes']  = $value['flValorAtualizacaoMes'];
            $arTmp[$inCount]['flValorSaldoAtual']      = $value['flValorSaldoAtual'];
            
            $inCount++;
        }
    }
    
    Sessao::write('arDividas', $arTmp);
    echo montaListaDivida( $arTmp );
}

function montaAlterarDivida(){
    GLOBAL $request;
    
    $arTmp   = array();
    $inCount = 0;
    $arDividas = Sessao::read('arDividas');

    $stJs = "window.parent.frames['telaPrincipal'].limparDivida();";
    
    foreach ($arDividas as $key => $value) {
        
        if ($value['id'] == $_REQUEST['id']) {
            $stJs .= " document.getElementById( 'inHdnId' ).value                     = '".$value['id']."'; ";
            $stJs .= " document.getElementById( 'inCodLeiAutorizacao' ).value         = '".$value['inCodLeiAutorizacao']."'; ";
            $stJs .= " document.getElementById( 'stNomeLeiAutorizacao' ).innerHTML    = '".$value['stLeiAutorizacao']."'; ";
            $stJs .= " document.getElementById( 'inNumContratoDivida' ).value         = '".$value['inNumContratoDivida']."'; ";
            $stJs .= " document.getElementById( 'dtAssinaturaDivida' ).value          = '".$value['dtAssinaturaDivida']."'; ";
            $stJs .= " document.getElementById( 'stContratoDecLei' ).value            = '".$value['stContratoDecLei']."'; ";
            $stJs .= " document.getElementById( 'stObjetoContrato' ).value            = '".$value['stObjetoContrato']."'; ";
            $stJs .= " document.getElementById( 'stDescDivida' ).value                = '".$value['stDescDivida']."'; ";
            $stJs .= " document.getElementById( 'inTipoLancamento' ).value            = '".$value['inTipoLancamento']."'; ";
            $stJs .= " document.getElementById( 'inCGMCredor' ).value                 = '".$value['inCGMCredor']."'; ";
            // $stJs .= " document.getElementById( 'stNomeCGMCredor' ).value             = '".$value['stNomeCGMCredor']."'; ";
            $stJs .= " document.getElementById( 'stJustificativaCancelamento' ).value = '".$value['stJustificativaCancelamento']."'; ";
            $stJs .= " document.getElementById( 'flValorSaldoAnterior' ).value        = '".$value['flValorSaldoAnterior']."'; ";
            $stJs .= " document.getElementById( 'flValorContratacaoMes' ).value       = '".$value['flValorContratacaoMes']."'; ";
            $stJs .= " document.getElementById( 'flValorAmortizacaoMes' ).value       = '".$value['flValorAmortizacaoMes']."'; ";
            $stJs .= " document.getElementById( 'flValorCancelamentoMes' ).value      = '".$value['flValorCancelamentoMes']."'; ";
            $stJs .= " document.getElementById( 'flValorEncampacaoMes' ).value        = '".$value['flValorEncampacaoMes']."'; ";
            $stJs .= " document.getElementById( 'flValorAtualizacaoMes' ).value       = '".$value['flValorAtualizacaoMes']."'; ";
            $stJs .= " document.getElementById( 'flValorSaldoAtual' ).value           = '".$value['flValorSaldoAtual']."'; ";
        }
    }
    $stJs .= "document.getElementById('btIncluirDivida').disabled = true;";
    $stJs .= "document.getElementById('btAlterarDivida').disabled = false;"; 
    return $stJs; 
}

function alterarListaDivida(){
    GLOBAL $request;
    $arTmp   = array();
    $inCount = 0;
    $arDividas = Sessao::read('arDividas');   
    $obNorma = new RNorma;
    $obNorma->setCodNorma( $request->get('inCodLeiAutorizacao') );
    $obNorma->listarDecreto( $rsNorma );

    foreach ($arDividas as $key => $value) {
        if ($value['id'] == $request->get('inHdnId')) {
            $arTmp[$inCount]['id']                     = $inCount;
            $arTmp[$inCount]['inExercicio']            = $request->get('inExercicio');
            $arTmp[$inCount]['inMes']                  = $request->get('inMes');
            $arTmp[$inCount]['inCodEntidade']          = $request->get('inCodEntidade');
            $arTmp[$inCount]['inCodLeiAutorizacao']    = $request->get('inCodLeiAutorizacao');
            $arTmp[$inCount]['stLeiAutorizacao']       = $request->get('inCodLeiAutorizacao')." - ".$rsNorma->getCampo('nom_tipo_norma')." ".$rsNorma->getCampo('num_norma_exercicio')." - ".$rsNorma->getCampo('nom_norma');
            $arTmp[$inCount]['stNomeLeiAutorizacao']   = $request->get('stNomeLeiAutorizacao');
            $arTmp[$inCount]['inNumContratoDivida']    = $request->get('inNumContratoDivida');
            $arTmp[$inCount]['dtAssinaturaDivida']     = $request->get('dtAssinaturaDivida');
            $arTmp[$inCount]['stContratoDecLei']       = $request->get('stContratoDecLei');
            $arTmp[$inCount]['stObjetoContrato']       = $request->get('stObjetoContrato');
            $arTmp[$inCount]['stDescDivida']           = $request->get('stDescDivida');
            $arTmp[$inCount]['inTipoLancamento']       = $request->get('inTipoLancamento');
            $arTmp[$inCount]['inCGMCredor']            = $request->get('inCGMCredor');
            $arTmp[$inCount]['stNomeCGMCredor']        = $request->get('stNomeCGMCredor');
            $arTmp[$inCount]['stJustificativaCancelamento'] = $request->get('stJustificativaCancelamento');
            $arTmp[$inCount]['flValorSaldoAnterior']   = $request->get('flValorSaldoAnterior');
            $arTmp[$inCount]['flValorContratacaoMes']  = $request->get('flValorContratacaoMes');
            $arTmp[$inCount]['flValorAmortizacaoMes']  = $request->get('flValorAmortizacaoMes');
            $arTmp[$inCount]['flValorCancelamentoMes'] = $request->get('flValorCancelamentoMes');
            $arTmp[$inCount]['flValorEncampacaoMes']   = $request->get('flValorEncampacaoMes');
            $arTmp[$inCount]['flValorAtualizacaoMes']  = $request->get('flValorAtualizacaoMes');
            $arTmp[$inCount]['flValorSaldoAtual']      = $request->get('flValorSaldoAtual');
        }else{
            $arTmp[$inCount]['id']                     = $inCount;
            $arTmp[$inCount]['inExercicio']            = $value['inExercicio'];
            $arTmp[$inCount]['inMes']                  = $value['inMes'];
            $arTmp[$inCount]['inCodEntidade']          = $value['inCodEntidade'];
            $arTmp[$inCount]['inCodLeiAutorizacao']    = $value['inCodLeiAutorizacao'];
            $arTmp[$inCount]['stLeiAutorizacao']       = $value['stLeiAutorizacao'];
            $arTmp[$inCount]['stNomeLeiAutorizacao']   = $value['stNomeLeiAutorizacao'];
            $arTmp[$inCount]['inNumContratoDivida']    = $value['inNumContratoDivida'];
            $arTmp[$inCount]['dtAssinaturaDivida']     = $value['dtAssinaturaDivida'];
            $arTmp[$inCount]['stContratoDecLei']       = $value['stContratoDecLei'];
            $arTmp[$inCount]['stObjetoContrato']       = $value['stObjetoContrato'];
            $arTmp[$inCount]['stDescDivida']           = $value['stDescDivida'];
            $arTmp[$inCount]['inTipoLancamento']       = $value['inTipoLancamento'];
            $arTmp[$inCount]['inCGMCredor']            = $value['inCGMCredor'];
            $arTmp[$inCount]['stNomeCGMCredor']        = $value['stNomeCGMCredor'];
            $arTmp[$inCount]['stJustificativaCancelamento'] = $value['stJustificativaCancelamento'];
            $arTmp[$inCount]['flValorSaldoAnterior']   = $value['flValorSaldoAnterior'];
            $arTmp[$inCount]['flValorContratacaoMes']  = $value['flValorContratacaoMes'];
            $arTmp[$inCount]['flValorAmortizacaoMes']  = $value['flValorAmortizacaoMes'];
            $arTmp[$inCount]['flValorCancelamentoMes'] = $value['flValorCancelamentoMes'];
            $arTmp[$inCount]['flValorEncampacaoMes']   = $value['flValorEncampacaoMes'];
            $arTmp[$inCount]['flValorAtualizacaoMes']  = $value['flValorAtualizacaoMes'];
            $arTmp[$inCount]['flValorSaldoAtual']      = $value['flValorSaldoAtual'];
        }
        $inCount++;
    }
    Sessao::write('arDividas', $arTmp);
    echo montaListaDivida( $arTmp );
    $stJs  = "document.getElementById('btIncluirDivida').disabled = false;";
    $stJs .= "document.getElementById('btAlterarDivida').disabled = true;";
    
    return $stJs;
}

function executaValidacao($array)
{
    $obErro = new Erro;
    $arDividas = Sessao::read('arDividas');

    if(is_array($arDividas)){
        foreach ($arDividas as $key => $value) {
            if ($value['inNumContratoDivida'] == $_REQUEST['inNumContratoDivida']) {
                $obErro->setDescricao("Não foi possível inserir o Contrato de dívida. O número de contrato ".$_REQUEST['inNumContratoDivida']." já existe na lista");
                return $obErro;
            }
        }
    }
    
    if ($array['inCodEntidade'] == '') {
        $obErro->setDescricao("Entidade inválida");

    } elseif ($array['inCodLeiAutorizacao'] == '') {
        $obErro->setDescricao("Lei de autorização inválida");

    } elseif ($array['inNumContratoDivida'] == '') {
        $obErro->setDescricao("Nº contrato de dívida inválido");

    } elseif ($array['dtAssinaturaDivida'] == '') {
        $obErro->setDescricao("Data de assinatura inválida");

    } elseif ($array['stObjetoContrato'] == '') {
        $obErro->setDescricao("Descrição de objeto do contrato inválida");

    } elseif ($array['stDescDivida'] ==  '') {
        $obErro->setDescricao("Descrição da dívida inválida");
    
    } elseif ($array['inTipoLancamento'] ==  '') {
        $obErro->setDescricao("Tipo de lançamento inválido");
    
    } elseif ($array['inCGMCredor'] ==  '') {
        $obErro->setDescricao("CGM inválido");
    
    } elseif ($array['flValorSaldoAnterior'] ==  '') {
        $obErro->setDescricao("Valor do saldo anterior inválido");
    
    } elseif ($array['flValorContratacaoMes'] ==  '') {
        $obErro->setDescricao("Valor de contratação inválido");
    
    } elseif ($array['flValorAmortizacaoMes'] ==  '') {
        $obErro->setDescricao("Valor de amortização inválido");
    
    } elseif ($array['flValorCancelamentoMes'] ==  '') {
        $obErro->setDescricao("Valor de cancelamento inválido");
    
    } elseif ($array['flValorEncampacaoMes'] ==  '') {
        $obErro->setDescricao("Valor de emcampação inválido");
    
    } elseif ($array['flValorAtualizacaoMes'] ==  '') {
        $obErro->setDescricao("Valor da atualização inválido");
    
    } elseif ($array['flValorSaldoAtual'] ==  '') {
        $obErro->setDescricao("Valor de saldo atual inválido");
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

?>
