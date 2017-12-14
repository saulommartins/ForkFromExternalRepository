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
    * Data de Criação   : 01/09/2008

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

$stJs = '';
switch ($stCtrl) {

case "carregaDados":
    if ($_REQUEST['inCodNota']) {

        include_once CAM_GPC_TPB_MAPEAMENTO.'TCEPBNotaFiscal.class.php';

        $obTTCEPBNotaFiscal = new TCEPBNotaFiscal;
        $stFiltro  = " WHERE tcepb.nota_fiscal.cod_nota = ".$_REQUEST['inCodNota'];
        $obTTCEPBNotaFiscal->recuperaTodos($rsNotaFiscalEmpenho, $stFiltro);

        $arEmpenhos = array();
        $inCount = 0;

        include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenho.class.php';
        $obTEmpenhoEmpenho = new TEmpenhoEmpenho;

        while ( !$rsNotaFiscalEmpenho->eof()) {
            $stFiltro  = "   AND e.exercicio    = '".$rsNotaFiscalEmpenho->getCampo('exercicio')."'";
            $stFiltro .= "   AND e.cod_entidade =  ".$rsNotaFiscalEmpenho->getCampo('cod_entidade');
            $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenhoCgm($rsEmpenho, $stFiltro);
            $arEmpenhos[$inCount]['cod_nota_liquidacao'] = $rsNotaFiscalEmpenho->getCampo('cod_nota_liquidacao');
            $arEmpenhos[$inCount]['cod_entidade']        = $rsEmpenho->getCampo('cod_entidade');
            $arEmpenhos[$inCount]['cod_empenho']         = $rsEmpenho->getCampo('cod_empenho');
            $arEmpenhos[$inCount]['exercicio']           = $rsEmpenho->getCampo('exercicio');
            $arEmpenhos[$inCount]['numNota']             = $rsNotaFiscalEmpenho->getCampo('nro_nota');
            $arEmpenhos[$inCount]['nuVlAssociado']       = $rsNotaFiscalEmpenho->getCampo('vl_associado');
            $arEmpenhos[$inCount]['numSerie']            = $rsNotaFiscalEmpenho->getCampo('nro_serie');
            $arEmpenhos[$inCount]['data_emissao']        = $rsNotaFiscalEmpenho->getCampo('data_emissao');
            $arEmpenhos[$inCount]['cod_nota']            = $rsNotaFiscalEmpenho->getCampo('cod_nota');
            $inCount++;
            $rsNotaFiscalEmpenho->proximo();
        }
        $stJs .= "jq('#numEmpenho').val('" . $arEmpenhos[0]['cod_empenho']  . "');";
        $stJs .= "f.numEmpenho.value        = '".$arEmpenhos[0]['cod_empenho']                              ."';\n";
        $stJs .= "f.cod_entidade.value      = '".$arEmpenhos[0]['cod_entidade']                             ."';\n";
        $stJs .= "f.inCodEntidade.value     = '".$arEmpenhos[0]['cod_entidade']                             ."';\n";
        $stJs .= "jq('#labelTotalLiquidacao').html('".$arEmpenhos[0]['nuVlAssociado']                       ."');\n";
        $stJs .= "f.inNumSerie.value          ='".$arEmpenhos[0]['numSerie']                                 ."';\n";
        $stJs .= "f.data_emissao.value        ='".$arEmpenhos[0]['data_emissao']                             ."';\n";
        $stJs .= "f.dtEmissao.value           ='".$arEmpenhos[0]['data_emissao']                               ."';\n";
        Sessao::write('arEmpenhos', $arEmpenhos);

    }
    break;

case "incluirEmpenhoLista":
    $arRegistro = array();
    $arEmpenhos = array();
    $arRequest  = array();
    $arRequest  = explode('/', $_REQUEST['numEmpenho']);
    $boIncluir  = true;

    $arEmpenhos = Sessao::read('arEmpenhos');

    if ($_REQUEST['stExercicioEmpenho'] and $arRequest[0] != "") {

        include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
        $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
        $obTEmpenhoEmpenho->setDado( 'dt_emissao'  , $_REQUEST['dtEmissao']           );
        $obTEmpenhoEmpenho->setDado( 'cod_empenho' , $arRequest[0]                    );
        $obTEmpenhoEmpenho->setDado( 'exercicio'   , $_REQUEST['stExercicioEmpenho']  );
        $obTEmpenhoEmpenho->setDado( 'dt_final'    , $_REQUEST['dtEmissao']           );
        $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenho($rsRecordSet, $stFiltro);

        if ( $rsRecordSet->getNumLinhas() > 0 ) {

            if ( count( $arEmpenhos ) > 0 ) {
                foreach ($arEmpenhos as $key => $array) {
                    $stCod = $array['cod_empenho'];

                    if ($arRequest[0] == $stCod) {
                        $boIncluir = false;
                        $stJs .= "alertaAviso('Empenho já incluso na lista.','form','erro','".Sessao::getId()."');";
                        break;
                    }
                }
            }
            if ($boIncluir) {

                $arRegistro['cod_entidade']  = $rsRecordSet->getCampo('cod_entidade');
                $arRegistro['cod_empenho' ]  = $rsRecordSet->getCampo('cod_empenho');
                $arRegistro['data_empenho']  = $rsRecordSet->getCampo('dt_empenho');
                $arRegistro['nom_cgm'     ]  = $rsRecordSet->getCampo('credor');
                $arRegistro['exercicio'   ]  = $rsRecordSet->getCampo('exercicio');
                $arRegistro['nuVlAssociado'] = Sessao::read('nuVlassociado');
                $arRegistro['cod_nota']    =  $_REQUEST['comboLiquidacao'];
                $arEmpenhos[] = $arRegistro ;

                Sessao::write('arEmpenhos', $arEmpenhos);
                $stJs .= "f.cod_entidade.disabled = true; ";
                $stJs .= "f.stNomEntidade.disabled = true; ";
                $stJs .= 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                $stJs .= "f.stEmpenho.value = '';";
                $stJs .= "f.numEmpenho.value = '';";
                $stJs .= "f.numEmpenho.focus();";
                $stJs .= "f.dtEmissao.disabled = true; ";
                $stJs .= montaListaEmpenhos();
            }
        } else {
            $stJs .= "alertaAviso('Empenho informado inválido.','form','erro','".Sessao::getId()."');";
        }
    } else {
        if (!$_REQUEST['stExercicioEmpenho']) {
            $stJs .= "alertaAviso('Informe o exercício do empenho.','form','erro','".Sessao::getId()."');";
        }
        if (!$arRequest[0]) {
            $stJs .= "alertaAviso('Informe o número do empenho.','form','erro','".Sessao::getId()."');";
        }
    }
    break;

case "excluirEmpenhoLista":
    $arTempEmp = array();
    $arEmpenhos = Sessao::read('arEmpenhos');

    foreach ($arEmpenhos as $registro) {
        if ($registro['cod_empenho'].$registro['cod_entidade'].$registro['exercicio'] != $_REQUEST['codEmpenho'].$_REQUEST['codEntidade'].$_REQUEST['stExercicio']) {
            $arTempEmp[] = $registro;
        }
    }

    if (count($arTempEmp) <= 0) {

        $stJs  = "f.inCodEntidade.disabled = false; ";
        $stJs .= "f.stNomEntidade.disabled = false; ";
        $stJs .= "f.dtEmissao.disabled = false; ";

    }

    Sessao::write('arEmpenhos', $arTempEmp);
    $stJs .= montaListaEmpenhos();
    break;

case "limpar":
    $stJs  = 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
    $stJs .= "f.numEmpenho.value = '';";
    $stJs .= "f.numEmpenho.focus();";

    break;

case "preencheInner":

    if ($_REQUEST['stAcao'] == 'incluir') {
        $arRequest  = explode('/', $_REQUEST['numEmpenho']);

        if ($_REQUEST['inCodEntidade'] AND $_REQUEST['stExercicioEmpenho'] AND $_REQUEST['numEmpenho'] != '') {
            include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenho.class.php';

            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $obTEmpenhoEmpenho->setDado('dt_emissao'  , $_REQUEST['dtEmissao']);
            $obTEmpenhoEmpenho->setDado('cod_empenho' , $arRequest[0]);
            $obTEmpenhoEmpenho->setDado('exercicio'   , $_REQUEST['stExercicioEmpenho']);
            $obTEmpenhoEmpenho->setDado('dt_final'    , $_REQUEST['dtEmissao']);
            $stFiltro = " AND e.cod_entidade = ".$_REQUEST['inCodEntidade'];
            $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenho($rsRecordSet, $stFiltro);

            if ($rsRecordSet->getNumLinhas() > 0) {
                $stJs .= "jq('#numEmpenho').val('" . $rsRecordSet->getCampo('cod_empenho') . '/' . $rsRecordSet->getCampo('exercicio') . "');";
                $stJs .= "jq('#stEmpenho').html('" . $rsRecordSet->getCampo('dt_empenho') . '-' . $rsRecordSet->getCampo('credor') . "');";

                include_once CAM_GPC_TPB_MAPEAMENTO.'TCEPBNotaFiscalEmpenho.class.php';

                $rsLiquidacaoEmpenho =  new Recordset;
                $obTCEPBNotaFiscalEmpenho = new TCEPBNotaFiscalEmpenho();
                $stFiltro = "AND  cod_empenho =".$arRequest[0]."AND empenho.nota_liquidacao.exercicio = '".Sessao::getExercicio()."'";
                $stOrder =  "ORDER BY dt_liquidacao";
                $obTCEPBNotaFiscalEmpenho->liquidacaoEmpenho($rsLiquidacaoEmpenho,$stFiltro,$stOrder);

                $obTCEPBNotaFiscalEmpenho = new TCEPBNotaFiscalEmpenho();
                $obTCEPBNotaFiscalEmpenho->setDado('exercicio',$_REQUEST['stExercicioEmpenho']);
                $obTCEPBNotaFiscalEmpenho->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
                $obTCEPBNotaFiscalEmpenho->setDado('cod_empenho',$arRequest[0]);
                $obTCEPBNotaFiscalEmpenho->setDado('cod_nota',$_REQUEST['comboLiquidacao']);

                $obTCEPBNotaFiscalEmpenho->totalLiquidacaoEmpenho($rsLiquidacaoTotal);

                if ($rsLiquidacaoTotal->getCampo('total') == '0.00') {
                    $obForm = new Formulario;
                    $obCboLiquidacao = new Select();
                    $obCboLiquidacao->setName  ("comboLiquidacao");
                    $obCboLiquidacao->setId    ("comboLiquidacao");
                    $obCboLiquidacao->addOption( "", "Selecione" );
                    $obCboLiquidacao->setRotulo("*Liquidação ");
                    $obForm->addComponente($obCboLiquidacao);
                    $obForm->montaInnerHTML();
                    $stHTML = $obForm->getHTML();

                } else {
                    $obForm = new Formulario;
                    $obCboLiquidacao = new Select();
                    $obCboLiquidacao->setName          ("comboLiquidacao");
                    $obCboLiquidacao->setId            ("comboLiquidacao");
                    $obCboLiquidacao->setCampoId       ("cod_nota"       );
                    $obCboLiquidacao->setCampoDesc     ("[cod_nota] - [dt_liquidacao]");
                    $obCboLiquidacao->addOption        ( "", "Selecione" );
                    $obCboLiquidacao->setRotulo        ("*Liquidação ");
                    $obCboLiquidacao->preencheCombo    ($rsLiquidacaoEmpenho);
                    $obCboLiquidacao->obEvento->setOnChange ("montaParametrosGET('totalLiquidacao','comboLiquidacao,inCodEntidade,numEmpenho,stExercicioEmpenho,stAcao');");
                    $obForm->addComponente($obCboLiquidacao);
                    $obForm->montaInnerHTML();
                    $stHTML = $obForm->getHTML();
                }

                $stJs .= 'jq("#labelTotalLiquidacao").html("&nbsp;");';
                $stJs .= "jq('#spanLiquidacao').html('".$stHTML."');";

            } else {
                $stJs  = "alertaAviso('Empenho inválido para data de emissão da NF.','form','erro','".Sessao::getId()."');\n";
                $stJs .= 'jq("#numEmpenho").val("");';
                $stJs .= 'jq("#stEmpenho").html("&nbsp;");';
                $stJs .= 'jq("#spanLiquidacao").html("&nbsp;");';
                $stJs .= 'jq("#numEmpenho").focus();';
            }
        } else {
            $stJs .= "jq('input#numEmpenho').val('');";
            $stJs .= "jq('#stEmpenho').html('&nbsp;');";
            $stJs .= "jq('#spanLiquidacao').html('&nbsp;');";
            if (!$_REQUEST['inCodEntidade']) {
                $stJs .= "alertaAviso('Informe a entidade.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.inCodEntidade.focus();\n";
            }
            if (!$_REQUEST['stExercicioEmpenho']) {
                $stJs .= "alertaAviso('Informe o exercício do empenho.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.stExercicioEmpenho.focus();\n";
            }
        }

    } else {
        include_once(CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php");

        $arEmpenho = explode('/',$_REQUEST['numEmpenho']);
        $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
        $obTEmpenhoEmpenho->setDado('cod_empenho',  $arEmpenho[0]);
        $obTEmpenhoEmpenho->setDado('exercicio'  , $arEmpenho[1]);
        $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenho($rsRecordSet, $stFiltro);

        $stDescEmpenho  = $rsRecordSet->getCampo('cod_empenho') . '/' . $rsRecordSet->getCampo('exercicio');
        $stDescEmpenho .= ' - ' . $rsRecordSet->getCampo('dt_empenho') . '-' . $rsRecordSet->getCampo('credor') ;
        $stJs .= "jq('span#stEmpenho_label').html('" . $stDescEmpenho . "');";
        $stJs .= "jq('#labelTotalLiquidacao').html('" . number_format($rsRecordSet->getCampo('vl_saldo_anterior'),2,',','.') . "');";

        $obForm = new Formulario();
        $obLblLiquidacao = new Label();
        $obLblLiquidacao->setRotulo ('*Liquidação');
        $obLblLiquidacao->setValue  ($_REQUEST['inCodNotaLiquidacao'] . ' - ' . $_REQUEST['dtLiquidacao']);
        $obForm->addComponente($obLblLiquidacao);
        $obForm->montaInnerHTML();
        $stHTML = $obForm->getHTML();

        $stJs .= "jq('#spanLiquidacao').html('".$stHTML."');";
    }
    break;

case 'totalLiquidacao':
    include_once( CAM_GPC_TPB_MAPEAMENTO."TCEPBNotaFiscalEmpenho.class.php" );

    $arNotaLiquidacao = explode('/',$_REQUEST['numEmpenho']);

    $obForm = new Formulario();
    $rsLiquidacaoTotal = new RecordSet;
    $obTCEPBNotaFiscalEmpenho = new TCEPBNotaFiscalEmpenho();
    $obTCEPBNotaFiscalEmpenho->setDado('exercicio',$_REQUEST['stExercicioEmpenho']);
    $obTCEPBNotaFiscalEmpenho->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
    $obTCEPBNotaFiscalEmpenho->setDado('cod_empenho',$arNotaLiquidacao[0]);
    $obTCEPBNotaFiscalEmpenho->setDado('cod_nota',$_REQUEST['comboLiquidacao']);

    $obTCEPBNotaFiscalEmpenho->totalLiquidacaoEmpenho($rsLiquidacaoTotal);
    $obForm->montaInnerHTML();
    $stHTML = $obForm->getHTML();
    $stJs .= "jq('#labelTotalLiquidacao').html('".number_format($rsLiquidacaoTotal->getCampo('total'),2,',','.')."'); ";
    Sessao::write('nuVlassociado',$rsLiquidacaoTotal->getCampo('total'));
    break;

case 'habilitaIncluirEmpenho':
    if ($_REQUEST['dtEmissao']) {
        $stJs  = 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
        $stJs .= "f.numEmpenho.value = '".$_REQUEST['numEmpenho']."';";
        $stJs .= "f.data_emissao.value  = '".$_REQUEST['dtEmissao']."';";
        $stJs .= "f.btnLimpar.disabled  = false; ";
        $stJs .= "f.btnIncluir.disabled = false; ";
    }

    break;
}

echo $stJs;

function montaListaEmpenhos()
{
    $obLista = new Lista;
    $rsLista = new RecordSet;
    $rsLista->preenche ( Sessao::read('arEmpenhos') );

    while (!$rsLista->eof()) {
        $vlTotal = str_replace('.','',$rsLista->getCampo('nuVlAssociado'));
        $vlTotal = str_replace(',','.',$vlTotal);
        $vlSoma  = $vlSoma + $vlTotal;
        $rsLista->proximo();
    }

    $vlTotal = number_format($vlSoma,2,',','.');

    $rsLista->setPrimeiroElemento();

    $obLista->setRecordset( $rsLista );
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo ( 'Lista de empenhos' );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Empenho");
    $obLista->ultimoCabecalho->setWidth( 10);
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nota do Empenho");
    $obLista->ultimoCabecalho->setWidth( 10);
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome do Credor");
    $obLista->ultimoCabecalho->setWidth( 70 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor Associado");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio]" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_nota]" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nuVlAssociado" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('excluirEmpenhoLista');" );
    $obLista->ultimaAcao->addCampo("","&codEmpenho=[cod_empenho]&codEntidade=[cod_entidade]&stExercicio=[exercicio]");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    //$stJs  = 'd.getElementById("nuSoma").innerHTML = "'.$vlTotal.'";';
    $stJs .= "f.nuVlTotal.value = '".$vlTotal."';";
    $stJs .= "d.getElementById('spnLista').innerHTML = '';\n";
    $stJs .= "d.getElementById('spnLista').innerHTML = '".$html."';\n";

    return $stJs;

}

?>
