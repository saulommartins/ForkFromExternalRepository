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
    * Arquivo oculto com funcionalidades ref. ao vinculo do contrato aos empenhos.
    * Data de Criação: 05/03/2008

    * @author Alexandre Melo

    * Casos de uso: uc-02.03.37

    $Id: OCManterVinculoEmpenhoContrato.php 66418 2016-08-25 21:02:27Z michel $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterVinculoEmpenhoContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');

switch ($stCtrl) {
    case "incluirEmpenho":
        $rsRecordSet = new Recordset;
        $rsEmpenhos  = new Recordset;
        $arElementos = array();
        $arElementos = Sessao::read('elementos');
        $inProxId    = 0;
        $inCount     = count($arElementos);
        $boExecuta   = false;

        list($inCodEmpenho, $stExercicioEmpenho) = explode('/', $request->get('numEmpenho'));
        list($inNumAditivo, $stExercicioAditivo) = explode('/', $request->get('inNumAditivo'));

        if ($inCodEmpenho && strlen($stExercicioEmpenho) == 4) {
            if( $request->get('inExercicio') <= $stExercicioEmpenho ){
                include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php";
                $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
                $stFiltro  = " AND e.cod_empenho       =  ".$inCodEmpenho;
                $stFiltro .= " AND e.exercicio         =  '".$stExercicioEmpenho."'";
                $stFiltro .= " AND pe.cgm_beneficiario =  ".$request->get('cgm_credor');
                $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenho($rsRecordSet, $stFiltro);

                include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenhoContrato.class.php';
                $obTEmpenhoEmpenhoContrato = new TEmpenhoEmpenhoContrato;
                $stFiltro  = " AND e.cod_empenho       =  ".$inCodEmpenho;
                $stFiltro .= " AND e.exercicio         =  '".$stExercicioEmpenho."'";
                $stFiltro .= " AND ec.cod_entidade     =  ".$request->get('inCodEntidade');
                $stFiltro .= " AND ec.num_contrato||'/'||ec.exercicio_contrato <> '".$request->get('inNumContrato').'/'.$request->get('inExercicio')."'";
                $obTEmpenhoEmpenhoContrato->recuperaRelacionamentoEmpenhoContrato($rsEmpenhoContrato, $stFiltro, "");

                if ($rsRecordSet->getNumLinhas() > 0) {
                    if ($rsEmpenhoContrato->getNumLinhas() <= 0) {
                        if (Sessao::read('elementos') != "") {
                            //Define proximo ID
                            $rsEmpenhos->preenche(Sessao::read('elementos'));
                            $rsEmpenhos->setUltimoElemento();
                            $inUltimoId = $rsEmpenhos->getCampo("inId");
                            $inProxId   = $inUltimoId + 1;

                            $stChaveAtual  = $rsRecordSet->getCampo('cod_empenho');
                            $stChaveAtual .= '.'.$rsRecordSet->getCampo('exercicio');

                            //Verifica a existencia do empenho na lista
                            $rsEmpenhos->setPrimeiroElemento();
                            while (!$rsEmpenhos->eof()) {
                                $stChaveAnterior  = $rsEmpenhos->getCampo('cod_empenho');
                                $stChaveAnterior .= '.'.$rsEmpenhos->getCampo('exercicio');

                                if ($stChaveAtual === $stChaveAnterior) {
                                    $boExecuta = true;
                                    $stJs .= "alertaAviso('Empenho já incluso na lista.','form','erro','".Sessao::getId()."');";
                                }
                                $rsEmpenhos->proximo();
                            }
                        }
                        if (!$boExecuta) {
                            while (!$rsRecordSet->eof()) {
                                $arElementos[$inCount]['inId']              = $inProxId;
                                $arElementos[$inCount]['cod_empenho']       = $rsRecordSet->getCampo('cod_empenho');
                                $arElementos[$inCount]['exercicio']         = $rsRecordSet->getCampo('exercicio');
                                $arElementos[$inCount]['dt_empenho']        = $rsRecordSet->getCampo('dt_empenho');
                                $arElementos[$inCount]['vl_saldo_anterior'] = number_format($rsRecordSet->getCampo('vl_saldo_anterior'), 2,',','.');
                                $arElementos[$inCount]['num_aditivo']       = $inNumAditivo;
                                $arElementos[$inCount]['exercicio_aditivo'] = $stExercicioAditivo;
                                $arElementos[$inCount]['aditivo']           = $request->get('inNumAditivo');

                                $inCount = $inCount + 1;
                                $rsRecordSet->proximo();
                            }

                            Sessao::write('elementos', $arElementos);
                            $stJs .= listarEmpenho();
                        }
                    }else{
                        $stContrato = $rsEmpenhoContrato->getCampo('num_contrato').'/'.$rsEmpenhoContrato->getCampo('exercicio_contrato');
                        $stJs .= "alertaAviso('Empenho já está vinculado ao contrato ".$stContrato."!','form','erro','".Sessao::getId()."');";
                    }
                } else {
                    $stJs .= "alertaAviso('Empenho inexistente para o credor selecionado! ','form','erro','".Sessao::getId()."');";
                }
            }else{
                $stJs .= "alertaAviso('Exercício do Empenho precisa ser igual ou superior ao exercício do contrato! ','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "alertaAviso('Informe o código de empenho no formato: \'Número do empenho/Exercício do empenho\'.','form','erro','".Sessao::getId()."');";
        }

        $stJs .= "d.getElementById('spnAditivo').innerHTML = '';";
        $stJs .= "f.inNumAditivo.value = '';";
        $stJs .= "f.numEmpenho.value = '';";
        $stJs .= "f.numEmpenho.focus();";

        echo $stJs;
    break;

    case "excluirEmpenho":
        $arElementosSessao = Sessao::read('elementos');
        $arExcluidosSessao = Sessao::read('elementos_excluidos');

        $id = $request->get('inId');
        $inCount = 0;
        $inCountExcluidos = count(Sessao::read('elementos_excluidos'));

        foreach ($arElementosSessao AS $arElementosTMP) {
            if ($arElementosTMP["inId"] != $id) {
                $arElementos[$inCount]         = $arElementosTMP;
                $arElementos[$inCount]['inId'] = $inCount;
                $inCount= $inCount + 1;
            } else {
                $arExcluidosSessao[$inCountExcluidos]         = $arElementosTMP;
                $arExcluidosSessao[$inCountExcluidos]['inId'] = $inCount;
                $inCountExcluidos = $inCountExcluidos + 1;
            }
        }
        Sessao::write('elementos_excluidos', $arExcluidosSessao);
        Sessao::write('elementos', $arElementos);

        $stJs .= listarEmpenho();
        echo $stJs;
    break;

    case "consultaContratoEmpenho":
        $rsEmpenhos  = new Recordset;
        $arElementos = array();
        $inCount     = 0;

        Sessao::remove('elementos_excluidos');

        include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenhoContrato.class.php';
        $obTEmpenhoEmpenhoContrato = new TEmpenhoEmpenhoContrato;
        $stFiltro .= "   AND ec.exercicio_contrato = '".$request->get('inExercicio')."'";
        $stFiltro .= "   AND ec.cod_entidade =  ".$request->get('inCodEntidade');
        $stFiltro .= "   AND ec.num_contrato =  ".$request->get('inNumContrato');
        $obTEmpenhoEmpenhoContrato->recuperaRelacionamentoEmpenhoContrato($rsEmpenhos, $stFiltro, "");

        if ($rsEmpenhos->getNumLinhas() > 0) {
            while (!$rsEmpenhos->eof()) {
                $arElementos['inId']                = $inCount;
                $arElementos['cod_empenho']         = $rsEmpenhos->getCampo('cod_empenho');
                $arElementos['exercicio']           = $rsEmpenhos->getCampo('exercicio');
                $arElementos['dt_empenho']          = $rsEmpenhos->getCampo('dt_empenho');
                $arElementos['vl_saldo_anterior']   = number_format($rsEmpenhos->getCampo('vl_saldo_anterior'), 2,',','.');
                $arElementos['num_aditivo']         = $rsEmpenhos->getCampo('num_aditivo');
                $arElementos['exercicio_aditivo']   = $rsEmpenhos->getCampo('exercicio_aditivo');
                $arElementos['aditivo']             = $rsEmpenhos->getCampo('aditivo');
                $arTMP[] = $arElementos;

                $inCount= $inCount + 1;
                $rsEmpenhos->proximo();
            }
            Sessao::write('elementos', $arTMP);

            $stJs .= listarEmpenho();
            $stJs .= "f.numEmpenho.focus();";
        } else {
            Sessao::remove('elementos');
        }
        echo $stJs;
    break;

    case "limpar":
        $stJs .= "d.getElementById('spnAditivo').innerHTML = '';";
        $stJs .= "f.inNumAditivo.value = '';";
        $stJs .= "f.numEmpenho.value = '';";
        $stJs .= "f.numEmpenho.focus();";

        echo $stJs;
    break;

    case 'buscaAditivo':
        require_once CAM_GP_LIC_MAPEAMENTO.'TLicitacaoContratoAditivos.class.php';
        $obTLicitacaoContratoAditivos = new TLicitacaoContratoAditivos;

        list($inNumAditivo, $stExercicioAditivo) = explode('/', $request->get('inNumAditivo'));
        $inCodContrato = $request->get('inNumContrato');
        $stExercicioContrato = $request->get('inExercicio');
        $inCodEntidade = $request->get('inCodEntidade');

        $stHTML = "";
        if(!empty($inCodContrato) && !empty($stExercicioContrato) && !empty($inCodEntidade) && !empty($inNumAditivo) && !empty($stExercicioAditivo)){
            $stFiltro  = " WHERE num_contrato      = ".$inCodContrato;
            $stFiltro .= " AND exercicio_contrato  = '".$stExercicioContrato."'";
            $stFiltro .= " AND cod_entidade        = ".$inCodEntidade;
            $stFiltro .= " AND num_aditivo         = ".$inNumAditivo;
            $stFiltro .= " AND exercicio           = '".$stExercicioAditivo."'";

            $stOrdem = " ORDER BY exercicio, num_aditivo";
            $obTLicitacaoContratoAditivos->recuperaTodos($rsAditivo, $stFiltro, $stOrdem);

            if($rsAditivo->getNumLinhas()==1){
                $obLblData = new Label;
                $obLblData->setRotulo('Data do Aditivo');
                $obLblData->setValue($rsAditivo->getCampo('dt_assinatura'));

                $obLblValor = new Label;
                $obLblValor->setRotulo('Valor do Aditivo');
                $obLblValor->setValue(number_format($rsAditivo->getCampo('valor_contratado'),2,',','.'));

                $obForm = new Form;
                $obForm->setName("frm2");

                $obFormulario = new Formulario;
                $obFormulario->addForm  ($obForm);
                $obFormulario->addComponente($obLblData);
                $obFormulario->addComponente($obLblValor);
                $obFormulario->montaInnerHTML();

                $stHTML = $obFormulario->getHTML();
                $stHTML = str_replace( "\n"     , ""    , $stHTML );
                $stHTML = str_replace( chr(13)  , "<br>", $stHTML );
                $stHTML = str_replace( "  "     , ""    , $stHTML );
                $stHTML = str_replace( "'"      , "\\'" , $stHTML );
                $stHTML = str_replace( "\\\'"   , "\\'" , $stHTML );
            }
        }

        $stJs  = "if(d.getElementById('spnAditivo')){                            \n";
        $stJs .= "  d.getElementById('spnAditivo').innerHTML = '".$stHTML."';    \n";
        $stJs .= "}                                                              \n";

        echo $stJs;
    break;
}

function listarEmpenho()
{
    $rsRecordSet = new RecordSet;

    if (Sessao::read('elementos') != "")
        $rsRecordSet->preenche(Sessao::read('elementos'));

    if ($rsRecordSet->getNumLinhas() > 0) {
        $rsRecordSet->ordena('cod_empenho');
        $rsRecordSet->ordena('exercicio');

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Empenhos do Contrato" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Empenho" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Emissão" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Aditivo" );
        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio]" );
        $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[dt_empenho]" );
        $obLista->ultimoDado->setAlinhamento('CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[vl_saldo_anterior]" );
        $obLista->ultimoDado->setAlinhamento('DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "aditivo" );
        $obLista->ultimoDado->setAlinhamento('CENTRO' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "Excluir" );
        $obLista->ultimaAcao->setFuncaoAjax( true );

        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirEmpenho');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace( "\n"    , ""     , $stHtml );
        $stHtml = str_replace( chr(13) , "<br>" , $stHtml );
        $stHtml = str_replace( "  "    , ""     , $stHtml );
        $stHtml = str_replace( "'"     , "\\'"  , $stHtml );
        $stHtml = str_replace( "\\\'"  , "\\'"  , $stHtml );
    }

    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnListaEmpenhos').innerHTML = '".$stHtml."';";

    return $stJs;
}

?>
