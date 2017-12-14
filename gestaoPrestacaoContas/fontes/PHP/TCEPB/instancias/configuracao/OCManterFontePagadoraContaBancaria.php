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
    * Página do Oculto
    * Data de Criação   : 25/03/2008

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Lucas Andrades Mendes

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TPB_MAPEAMENTO.'TTPBPlanoConta.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

$stPrograma = "ManterFontePagadoraContaBancaria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

function montaListaContas($stAcao)
{
    if ($stAcao == "mostrar") {
        include_once CAM_GPC_TPB_MAPEAMENTO.'TTPBPlanoConta.class.php';
        //Cabeçalho para a table.
        if (count(Sessao::read('arContas'))>0) {
            $arContas=Sessao::read('arContas');
            $codTipo=$arContas[0]['cod_tipo'];
            for ($i=1;$i<(count($arContas));$i++) {
                if ($codTipo==$arContas[$i]['cod_tipo']) {
                    $arContas[0]['Conta'][$i]=$arContas[$i]['Conta'][0];
                }
            }
            $count=count($arContas);
            for ($i=1;$i<$count;$i++) {
                unset($arContas[$i]);
            }

            Sessao::write('arContas',$arContas);

            $rsFontesPagadoras = new RecordSet;
            $rsFontesPagadoras->preenche($arContas);

            $obTableTree = new TableTree;
            $obTableTree->setArquivo('OCManterFontePagadoraContaBancaria.php');
            $obTableTree->setParametros( array("cod_tipo") );
            $obTableTree->setComplementoParametros( "stCtrl=detalharLista");
            $obTableTree->setRecordset($rsFontesPagadoras);
            $obTableTree->setSummary('Lista de Fonte(s) Pagadora(s)');
            $obTableTree->setConditional(true);
            $obTableTree->Head->AddCabecalho('Fontes Pagadoras',50);
            $obTableTree->Body->addCampo( 'descricao', 'E' );
            $obTableTree->Body->addAcao('excluir','executaFuncaoAjax(\'%s\',\'&inVlTipo=%s\')',array('excluirFontePagadora','cod_tipo'));
            $obTableTree->montaHTML(true);
            $stHTML = $obTableTree->getHtml();

            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );

            $rsFontesPagadoras->setPrimeiroElemento();

            $stJs .= "d.getElementById('spnLista').innerHTML = '".$stHTML."';\n";
        } else {
            $stJs .= "d.getElementById('spnLista').innerHTML = '';\n";
        }
    } else { //Incluir
        $arContasSessao = Sessao::read('arContas');

        for ($inConta = 0 ; $inConta < count($arContasSessao);$inConta++) {
            $arElementos[] = $arContasSessao[$inConta];
        }

        //TABLETREE
        $rsFontesPagadoras = new RecordSet;
        $rsFontesPagadoras->preenche ( $arElementos );
        $rsFontesPagadoras->setPrimeiroElemento();

        $obTableTree = new TableTree;

        $obTableTree->setArquivo('OCManterFontePagadoraContaBancaria.php');
        $obTableTree->setParametros( array("cod_tipo") );
        $obTableTree->setComplementoParametros( "stCtrl=detalharLista");
        $obTableTree->setRecordset($rsFontesPagadoras);
        $obTableTree->setSummary('Lista de Fonte(s) Pagadora(s)');
        $obTableTree->setConditional(true);
        $obTableTree->Head->addCabecalho('Fonte Pagadora',50);
        $obTableTree->Body->addCampo('descricao','E');
        $obTableTree->Body->addAcao('excluir','executaFuncaoAjax(\'%s\',\'&inVlTipo=%s\')',array('excluirFontePagadora','cod_tipo'));

        $obTableTree->montaHTML(true);
        $html = $obTableTree->getHtml();

        $stJs .= "d.getElementById('spnLista').innerHTML = '".$html."';\n";
    }

    return $stJs;
}

function listarArContas($tipo, $exercicio)
{
    $obTTPBPlanoConta= new TTPBPlanoConta();
    $filtro = "WHERE relacao_conta_corrente_fonte_pagadora.cod_tipo=".$tipo." AND relacao_conta_corrente_fonte_pagadora.exercicio='".$exercicio."' \n";
    $obTTPBPlanoConta->recuperaContasCodTipo($rsFontesPagadoras, $filtro);
    $inCounter=0;
    while (!$rsFontesPagadoras->eof()) {
        $obTTPBPlanoConta = new TTPBPlanoConta();
        $obTTPBPlanoConta->setDado('stExercicio', $exercicio );
        $obTTPBPlanoConta->setDado('arContasCorrente',$rsFontesPagadoras->getCampo('cod_conta_corrente'));
        $obTTPBPlanoConta->recuperaContaBancariaFontePagadoraContas($rsContasBancarias);

        $arContasCompleta[$inCounter]['id']           = $inCounter;
        $arContasCompleta[$inCounter]['descricao']    = $rsFontesPagadoras->getCampo('cod_tipo')." - ".$rsFontesPagadoras->getCampo('descricao');
        $arContasCompleta[$inCounter]['cod_tipo']     = $rsFontesPagadoras->getCampo('cod_tipo');
        $arContasCompleta[$inCounter]['Conta']        = $rsContasBancarias->arElementos;
        $inCounter++;
        $rsFontesPagadoras->proximo();
    }
    if (count($arContasCompleta) > 0) {
        Sessao::write('arContas',$arContasCompleta);
    }
}

switch ($stCtrl) {
    case "incluirContaLista":
        $boIncluir = true;
        unset($inCounter);

        $cmbFontePagadora     = $_REQUEST['cmbFontePagadora'];
        $arContasSelecionadas = $_REQUEST['arContasSelecionadas'];

        if ($cmbFontePagadora != "" && $arContasSelecionadas !="") {
            $inValorTipo = explode("-",$cmbFontePagadora);

            $arContasSessao = Sessao::read('arContas');

            if ($arContasSessao) {
                foreach ($arContasSessao as $arContasTmp) {
                    if ($inValorTipo[0] == $arContasTmp['cod_tipo']) {
                        $inCounter = $arContasTmp['id'];
                    }
                }
            }

            if ($inValorTipo[2]!="") {
                $cmbFontePagadora  = $inValorTipo[1] ."-".$inValorTipo[2];
            } else {
                $cmbFontePagadora = $inValorTipo[1];
            }

            for ($inCount=0;$inCount<count($arContasSelecionadas);$inCount++) {
                $aux = $arContasSelecionadas[$inCount];
                $arContasSelecionadas[$inCount] = "'".$aux."'";
            }

            if (isset($arContasSelecionadas)) {
                $arContasSelecionadas = implode(",",$arContasSelecionadas);
            }

            // Recuperando dados das contas.
            $obTTPBPlanoConta = new TTPBPlanoConta();
            $obTTPBPlanoConta->setDado('stExercicio', Sessao::getExercicio() );
            $obTTPBPlanoConta->setDado('arContasCorrente',$arContasSelecionadas);
            $obTTPBPlanoConta->setDado('arCodTipo',$inValorTipo[0]);
            $obTTPBPlanoConta->recuperaContaBancariaFontePagadora($rsContasBancarias);

            if (Sessao::read('arContas')=="") {
                $inCounter = 0;
                $inConta   = 0;

                while (!$rsContasBancarias->eof()) {
                    $arContasSessao[$inCounter]['id']           = $inCounter;
                    $arContasSessao[$inCounter]['descricao']    = $inValorTipo[0]." - ".$cmbFontePagadora;
                    $arContasSessao[$inCounter]['cod_tipo']     = $inValorTipo[0];
                    $arContasSessao[$inCounter]['Conta']        = $rsContasBancarias->arElementos;
                    $rsContasBancarias->proximo();
                }

                 Sessao::write('arContas',$arContasSessao);
            } else {
                if (!isset($inCounter)) {
                    $inCounter = count(Sessao::read('arContas'));
                }

                $obTTPBPlanoConta = new TTPBPlanoConta();
                $obTTPBPlanoConta->setDado('stExercicio', Sessao::getExercicio() );
                $obTTPBPlanoConta->setDado('arContasCorrente',$arContasSelecionadas);
                $obTTPBPlanoConta->recuperaContaBancariaFontePagadoraContas($rsContasBancarias);

                while (!$rsContasBancarias->eof()) {
                    $arContasSessao[$inCounter]['id']           = $inCounter;
                    $arContasSessao[$inCounter]['descricao']    = $inValorTipo[0]." - ".$cmbFontePagadora;
                    $arContasSessao[$inCounter]['cod_tipo']     = $inValorTipo[0];
                    $arContasSessao[$inCounter]['Conta']        = $rsContasBancarias->arElementos;
                    $rsContasBancarias->proximo();
                }

                Sessao::write('arContas',$arContasSessao);
            }

            $stJs  =  montaListaContas("incluir");
            $stJs .="JavaScript:passaItem('document.frm.arContasSelecionadas','document.frm.arCodContasDisponiveis','tudo');";
            $stJs .= "jq('#cmbFontePagadora').selectOptions('');";
            echo $stJs;
        } else {
           echo "alertaAviso('@Selecione uma Fonte Pagadora e pelo menos uma Conta.','form','erro','".Sessao::getId()."');";
        }
    break;

    case "excluirFontePagadora":
        $inCount = 0;
        foreach (Sessao::read('arContas') as $arContasTmp ) {
            if ($arContasTmp["cod_tipo"] != $_REQUEST["inVlTipo"]) {
                $arTmp[$inCount] = $arContasTmp;
                $inCount++;
            }
        }
        Sessao::write('arContas',$arTmp);

        $stJs = montaListaContas("mostrar");
        echo $stJs;
    break;

    case "excluirContaLista":
        $arContasSessao = Sessao::read('arContas');

        foreach ($arContasSessao AS $arContasTmp) {
            if ($arContasTmp['cod_tipo'] == $_REQUEST['cod_tipo']) {
                foreach ($arContasTmp['Conta'] AS $arContaTmp) {
                    if ($arContaTmp['cod_conta_corrente'] != $_REQUEST['cod_conta_corrente']) {
                        $arContaNova[] = $arContaTmp;
                    }
                }
                $arContasTmp['Conta'] = $arContaNova;
            }
            $arContasNovasSessao[] = $arContasTmp;
        }

        Sessao::write('arContas',$arContasNovasSessao);

        $stJs = montaListaContas("mostrar");
        echo $stJs;
    break;

    case "limparContasLista":
        $stJs ="JavaScript:passaItem('document.frm.arContasSelecionadas','document.frm.arCodContasDisponiveis','tudo');";
        $stJs .= "jq('#cmbFontePagadora').selectOptions('');";
        echo  $stJs;
    break;

    case "buscaContasFontePagadora":
        $stJs ="JavaScript:passaItem('document.frm.arContasSelecionadas','document.frm.arCodContasDisponiveis','tudo');";
        echo  $stJs;
        Sessao::remove('arContas');
        $arValorTipo = explode('-', $_REQUEST['cmbFontePagadora']);
        $inValorTipo = $arValorTipo[0];
        $arStringContas = '';
        $boListar = false;
        $exercicio=Sessao::getExercicio();
        $stJs = listarArContas($inValorTipo, $exercicio);
        echo $stJs;

        $arContas =  Sessao::read('arContas');
        $count=0;
        for ($inContador = 0 ;$inContador < count($arContas); $inContador++) {
            if ($inValorTipo == $arContas[$inContador]["cod_tipo"]) {
                if ($count==0) {
                 $rsContas = new RecordSet ;
                 $rsContas->preenche($arContas[$inContador]["Conta"]);
                } else {
                    $rsContas->arElementos[$count]=$arContas[$inContador]["Conta"][0];
                    $rsContas->inNumLinhas=$count+1;
                }
                $boListar = true;
                $count++;
            }

        }

        if ($boListar) {
            while (!$rsContas->EOF()) {
                if ($arStringContas != '') {
                    $arStringContas.= ',';
                }
                $arStringContas.= "'".$rsContas->getCampo('cod_conta_corrente')."'";
                $rsContas->proximo();
            }
        }

        $stJs = montaListaContas("mostrar");
        echo $stJs;

        if ($arStringContas != '') {
            $stJs = "
                    passaItem( f.arContasSelecionadas, f.arCodContasDisponiveis, 'tudo');

                    objDe = f.arCodContasDisponiveis;
                    objPara = f.arContasSelecionadas;
                    arContasSelecionadosTMP = new Array(".$arStringContas.");

                    for (i = 0; i<arContasSelecionadosTMP.length; i++) {

                        chaveArray = array_search(arContasSelecionadosTMP[i], objDe.options);

                        if (chaveArray > -1) {
                            valor = objDe.options[chaveArray].value;
                            texto = objDe.options[chaveArray].text;
                            var temp = new Option(texto,valor);
                            destino = objPara.length;
                            objPara.options[destino] = temp;
                            objDe.options[chaveArray] = null;
                        }
                    }";
        } else {
            $stJs = "passaItem( f.arContasSelecionadas, f.arCodContasDisponiveis, 'tudo');";
        }

        echo  $stJs;
    break;

    case "contasExistentes":
        $stJs = montaListaContas("mostrar");
        echo $stJs;
    break;

    case "detalharLista":
        $inCodTipo = $_REQUEST['cod_tipo'];

        $arContas =  Sessao::read('arContas');
        for ($inContador = 0 ;$inContador < count(Sessao::read('arContas')); $inContador++) {
            if ($inCodTipo == $arContas[$inContador]["cod_tipo"]) {
                $rsContas = new RecordSet ;
                $rsContas->preenche($arContas[$inContador]["Conta"]);
                break;
            }
        }

        while (!$rsContas->EOF()) {
            $rsContas->setCampo('cod_tipo', $inCodTipo);
            $rsContas->proximo();
        }

        $obTable = new Table;
        $obTable->setRecordset($rsContas);
        //$obTable->setConditional(true);
        $obTable->addLineNumber(false);
        $obTable->Head->addCabecalho('Contas', 50);
        $obTable->Body->addCampo('nom_conta', 'E');

        $stTableAction = 'excluir';
        $stFunctionJs  = "ajaxJavaScript(&quot;OCManterFontePagadoraContaBancaria.php?cod_conta_corrente=%s&cod_tipo=%s";
        $stFunctionJs .= "&quot;,&quot;excluirContaLista&quot;)";

        $obTable->Body->addAcao($stTableAction, $stFunctionJs, array( 'cod_conta_corrente', 'cod_tipo' ) );

        $obTable->montaHTML(true);
        $stHTML = $obTable->getHtml();

        echo  $stHTML;
    break;
}
