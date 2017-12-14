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
 * Oculto para Configuração Formato de Exportação
 * Data de Criação   : 21/10/2008

 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';

$stPrograma = "ManterFormatoExportacao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

function gerarSpansExportar()
{
    switch ($_GET["inCodTipo"]) {
        case "1":
        case "2":
        case "4":
            $stHtml = montaHTMLFormato($obFormulario);
            break;
        case "7":
            $stHtml = montaHTMLFaixasDiassExtrasCadastradas($obFormulario);
            break;
    }
    $stJs = "jQuery('#spnInformacaoPonto').html('".$stHtml."');";

    return $stJs;
}

function montaHTMLFaixasDiassExtrasCadastradas(&$obFormulario)
{
    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFaixasHorasExtra.class.php");
    $obTPontoFaixasHorasExtra = new TPontoFaixasHorasExtra();
    $obTPontoFaixasHorasExtra->recuperaRelacionamento($rsLista);

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFaixasDias.class.php");
    $obTPontoFaixasDias = new TPontoFaixasDias();

    $arFaixas = array();
    while (!$rsLista->eof()) {
        switch ($rsLista->getCampo("calculo_horas_extra")) {
            case "D":
                $stCalculo = "Diário";
                break;
            case "M":
                $stCalculo = "Mensal";
                break;
            case "S":
                $stCalculo = "Semanal";
                break;
        }
        $stFiltro  = " WHERE faixas_dias.cod_configuracao = ".$rsLista->getCampo("cod_configuracao");
        $stFiltro .= "   AND faixas_dias.timestamp = '".$rsLista->getCampo("timestamp")."'";
        $stFiltro .= "   AND faixas_dias.cod_faixa = ".$rsLista->getCampo("cod_faixa");
        $obTPontoFaixasDias->recuperaRelacionamento($rsDias,$stFiltro);
        $stDias = "";
        if ($rsDias->getNumLinhas() > 0) {
            while (!$rsDias->eof()) {
                $stDias .= $rsDias->getCampo("nom_dia")."/";
                $rsDias->proximo();
            }
            $stDias = substr($stDias,0,strlen($stDias)-1);
        }
        $arTemp["cod_configuracao"] = $rsLista->getCampo("cod_configuracao");
        $arTemp["cod_faixa"]        = $rsLista->getCampo("cod_faixa");
        $arTemp["percentual"]       = $rsLista->getCampo("percentual")." %";
        $arTemp["horas"]            = $rsLista->getCampo("horas");
        $arTemp["calculo"]          = $stCalculo;
        $arTemp["dias"]             = $stDias;
        $arFaixas[] = $arTemp;
        $rsLista->proximo();
    }
    $rsFaixas = new recordset();
    $rsFaixas->preenche($arFaixas);

    $obChkMarcar = new Checkbox();
    $obChkMarcar->setName("boMarcar_[cod_configuracao]_[cod_faixa]");
    $obChkMarcar->setValue("boMarcar_[cod_configuracao]_[cod_faixa]");
    $obChkMarcar->setTitle("Selecione para que as horas enquadradas nos percentuais selecionados sejam exportados para o evento indicado a seguir.");

    $obLista = new Table();
    $obLista->setRecordset($rsFaixas);
    $obLista->setSummary("Faixas de Horas Extras Cadastradas");

    $obLista->Head->addCabecalho("Conf",5);
    $obLista->Head->addCabecalho("Dias",50);
    $obLista->Head->addCabecalho("Percentual",10);
    $obLista->Head->addCabecalho("Qtd Horas",15);
    $obLista->Head->addCabecalho("Cálculo",10);
    $obLista->Head->addCabecalho("Marcar",10);

    $obLista->Body->addCampo( 'cod_configuracao', 'C' );
    $obLista->Body->addCampo( 'dias', 'E' );
    $obLista->Body->addCampo( 'percentual', 'C' );
    $obLista->Body->addCampo( 'horas', 'C' );
    $obLista->Body->addCampo( 'calculo', 'C' );
    $obLista->Body->addComponente( $obChkMarcar );

    $obLista->montaHTML(true);
    $stHtml = $obLista->getHtml();

    return $stHtml;
}

function montaHTMLFormato()
{
    $obRdoHoras = new Radio();
    $obRdoHoras->setRotulo("Formato");
    $obRdoHoras->setName("stFormato");
    $obRdoHoras->setId("stFormato");
    $obRdoHoras->setLabel("Horas");
    $obRdoHoras->setValue("H");
    $obRdoHoras->setTitle("Marque para que a informação seja calculada em horas ou dias.");
    $obRdoHoras->setNullBarra(false);
    $obRdoHoras->setChecked(true);

    $obRdoDias = new Radio();
    $obRdoDias->setRotulo("Limites de Tolerância");
    $obRdoDias->setName("stFormato");
    $obRdoDias->setId("stFormato");
    $obRdoDias->setLabel("Dias");
    $obRdoDias->setValue("D");
    $obRdoDias->setTitle("Marque para que a informação seja calculada em horas ou dias.");
    $obRdoDias->setNullBarra(false);

    $obFormulario = new Formulario();
    $obFormulario->agrupaComponentes(array($obRdoHoras,$obRdoDias));
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();

    return $stHtml;
}

function gerarSpanConfiguracao()
{
    $rsLista = new RecordSet();
    $arLista = Sessao::read("arDadosExportacao");
    $rsLista->preenche($arLista);

    $obLista = new Table();
    $obLista->setRecordset($rsLista);
    $obLista->setSummary("Lista de Dados para Exportação");

    $obLista->Head->addCabecalho("Informação do Relógio Ponto",50);
    $obLista->Head->addCabecalho("Evento",50);

    $obLista->Body->addCampo( 'stDescTipo', 'E' );
    $obLista->Body->addCampo( '[inCodigoEvento]-[stEvento]', 'E' );

    $obLista->Body->addAcao("alterar","executaFuncaoAjax('%s','&inId=%s')",array('montaAlterarConfiguracao','inId'));
    $obLista->Body->addAcao("excluir","executaFuncaoAjax('%s','&inId=%s')",array('excluirConfiguracao','inId'));

    $obLista->montaHTML(true);
    $stHtml = $obLista->getHtml();

    $stJs  = "jQuery('#spnConfiguracao').html('".$stHtml."');\n";
    $stJs .= "jQuery('#spnInformacaoPonto').html('');\n";

    return $stJs;
}

function validarConfiguracao()
{
    $obErro = new erro;
    $arDadosExportacao = Sessao::read("arDadosExportacao");
    if (is_array($arDadosExportacao)) {
        foreach ($arDadosExportacao  as $arTemp) {
            if ($arTemp["inCodTipo"] == $_GET["inCodTipo"] and $_GET["inCodTipo"] != 7) {
                include_once(CAM_GRH_PON_MAPEAMENTO."TPontoTipoInformacao.class.php");
                $obTPontoTipoInformacao = new TPontoTipoInformacao();
                $obTPontoTipoInformacao->setDado("cod_tipo",$_GET["inCodTipo"]);
                $obTPontoTipoInformacao->recuperaPorChave($rsTipoInformacao);
                $obErro->setDescricao("A Informação do Relógio Ponto ".$rsTipoInformacao->getCampo("descricao")." já foi inserida na lista.");
            }
        }
    }

    return $obErro;
}

function montaAlterarConfiguracao()
{
    $arDadosExportacao = Sessao::read("arDadosExportacao");
    $arTemp = $arDadosExportacao[$_GET["inId"]];
    $_GET["inCodTipo"] = $arTemp["inCodTipo"];
    $stJs  = gerarSpansExportar();

    Sessao::write("inId",$_GET["inId"]);
    Sessao::write("inCodTipo",$arTemp["inCodTipo"]);
    $stJs .= "jQuery('#inCodTipo').val('".$arTemp["inCodTipo"]."');\n";
    $stJs .= "jQuery('#inCodTipo').attr('disabled','disabled');\n";
    $stJs .= "jQuery('#stEvento').html('".$arTemp["stEvento"]."');\n";
    $stJs .= "jQuery('#inCodigoEvento').val('".$arTemp["inCodigoEvento"]."');\n";
    if (in_array($arTemp["inCodTipo"],array(1,2,4))) {
        $stJs .= "jQuery('input[type=radio][name=stFormato][value=".$arTemp["stFormato"]."]').attr('checked', 'checked');\n";
    }
    if ($_GET["inCodTipo"] == 7) {
        foreach ($arTemp["arFaixas"] as $stCampo) {
            $stJs .= "jQuery('input[type=checkbox][value=".$stCampo."]').attr('checked', 'checked');\n";
        }
    }
    $stJs .= "jQuery('#btIncluirConfiguracao').attr('disabled','disabled');\n";
    $stJs .= "jQuery('#btAlterarConfiguracao').attr('disabled','');\n";

    return $stJs;
}

function limparConfiguracao()
{
    Sessao::remove("inId");
    Sessao::remove("inCodTipo");
    $stJs  = "jQuery('#inCodTipo').val('');\n";
    $stJs .= "jQuery('#inCodTipo').attr('disabled','');\n";
    $stJs .= "jQuery('#stEvento').html('&nbsp;');\n";
    $stJs .= "jQuery('#inCodigoEvento').val('');\n";
    $stJs .= "jQuery('#spnInformacaoPonto').html('');\n";
    $stJs .= "jQuery('#btIncluirConfiguracao').attr('disabled','');\n";
    $stJs .= "jQuery('#btAlterarConfiguracao').attr('disabled','disabled');\n";

    return $stJs;
}

function incluirConfiguracao()
{
    $obErro = validarConfiguracao();
    if (!$obErro->ocorreu()) {
        $arDadosExportacao = Sessao::read("arDadosExportacao");

        include_once(CAM_GRH_PON_MAPEAMENTO."TPontoTipoInformacao.class.php");
        $obTPontoTipoInformacao = new TPontoTipoInformacao();
        $obTPontoTipoInformacao->setDado("cod_tipo",$_GET["inCodTipo"]);
        $obTPontoTipoInformacao->recuperaPorChave($rsTipoInformacao);

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $stFiltro = " WHERE codigo = '".$_GET["inCodigoEvento"]."'";
        $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);

        $arTemp["inId"]             = count($arDadosExportacao);
        $arTemp["inCodTipo"]        = $_GET["inCodTipo"];
        $arTemp["stDescTipo"]       = $rsTipoInformacao->getCampo("descricao");
        $arTemp["inCodigoEvento"]   = $_GET["inCodigoEvento"];
        $arTemp["stEvento"]         = $rsEvento->getCampo("descricao");
        $arTemp["inCodEvento"]      = $rsEvento->getCampo("cod_evento");
        if (in_array($_GET["inCodTipo"],array(1,2,4))) {
            $arTemp["stFormato"]    = $_GET["stFormato"];
        }
        if ($_GET["inCodTipo"] == 7) {
            $arFaixas = array();
            foreach ($_GET as $stCampo=>$stValor) {
                if (strpos($stCampo,"boMarcar") === 0) {
                    $arFaixas[] = $stValor;
                }
            }
            $arTemp["arFaixas"]     = $arFaixas;
        }
        $arDadosExportacao[]        = $arTemp;
        Sessao::write("arDadosExportacao",$arDadosExportacao);
        $stJs = gerarSpanConfiguracao();
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function alterarConfiguracao()
{
    $arDadosExportacao = Sessao::read("arDadosExportacao");

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoTipoInformacao.class.php");
    $obTPontoTipoInformacao = new TPontoTipoInformacao();
    $obTPontoTipoInformacao->setDado("cod_tipo",Sessao::read("inCodTipo"));
    $obTPontoTipoInformacao->recuperaPorChave($rsTipoInformacao);

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stFiltro = " WHERE codigo = '".$_GET["inCodigoEvento"]."'";
    $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);

    $arTemp["inId"]             = Sessao::read("inId");
    $arTemp["inCodTipo"]        = Sessao::read("inCodTipo");
    $arTemp["stDescTipo"]       = $rsTipoInformacao->getCampo("descricao");
    $arTemp["inCodigoEvento"]   = $_GET["inCodigoEvento"];
    $arTemp["stEvento"]         = $rsEvento->getCampo("descricao");
    $arTemp["inCodEvento"]      = $rsEvento->getCampo("cod_evento");
    if (in_array(Sessao::read("inCodTipo"),array(1,2,4))) {
        $arTemp["stFormato"]    = $_GET["stFormato"];
    }
    if (Sessao::read("inCodTipo") == 7) {
        $arFaixas = array();
        foreach ($_GET as $stCampo=>$stValor) {
            if (strpos($stCampo,"boMarcar") === 0) {
                $arFaixas[] = $stValor;
            }
        }
        $arTemp["arFaixas"]     = $arFaixas;
    }
    $arDadosExportacao[Sessao::read("inId")] = $arTemp;
    Sessao::write("arDadosExportacao",$arDadosExportacao);
    $stJs  = gerarSpanConfiguracao();
    $stJs .= limparConfiguracao();

    return $stJs;
}

function excluirConfiguracao()
{
    $arDadosExportacao = Sessao::read("arDadosExportacao");
    $stJs .= limparConfiguracao();
    $arTemp = array();
    foreach ($arDadosExportacao as $arDados) {
        if ($arDados["inId"] != $_GET["inId"]) {
            $arDados["inId"] = count($arTemp)+1;
            $arTemp[] = $arDados;
        }
    }
    Sessao::write('arDadosExportacao',$arTemp);
    $stJs .= gerarSpanConfiguracao();

    return $stJs;
}

function limparFormulario()
{
    Sessao::remove('arDadosExportacao');
    $stJs = "document.frm.reset();";

    return $stJs;
}

function onLoad()
{
    $stJs  = "jQuery('#btLimparConfiguracao').attr('onClick', 'montaParametrosGET(\'limparConfiguracao\');' );";
    $stJs .= "jQuery('#limpar').attr('onClick', 'montaParametrosGET(\'limparFormulario\')' );";
    if (sessao::read("stAcao") == "alterar") {
        include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFormatoInformacao.class.php");
        include_once(CAM_GRH_PON_MAPEAMENTO."TPontoDadosExportacao.class.php");
        include_once(CAM_GRH_PON_MAPEAMENTO."TPontoTipoInformacao.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
        include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFormatoFaixasHorasExtras.class.php");

        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $obTPontoTipoInformacao = new TPontoTipoInformacao();
        $obTPontoDadosExportacao = new TPontoDadosExportacao();
        $obTPontoFormatoInformacao = new TPontoFormatoInformacao();
        $obTPontoFormatoFaixasHorasExtras = new TPontoFormatoFaixasHorasExtras();

        $stFiltro = " WHERE cod_formato = ".Sessao::read("inCodFormato");
        $obTPontoDadosExportacao->recuperaTodos($rsDadosExportacao,$stFiltro);
        $arDadosExportacao = array();
        while (!$rsDadosExportacao->eof()) {
            $obTPontoTipoInformacao->setDado("cod_tipo",$rsDadosExportacao->getCampo("cod_tipo"));
            $obTPontoTipoInformacao->recuperaPorChave($rsTipoInformacao);

            $stFiltro = " WHERE cod_evento = ".$rsDadosExportacao->getCampo("cod_evento");
            $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);

            $arTemp["inId"]             = count($arDadosExportacao);
            $arTemp["inCodTipo"]        = $rsDadosExportacao->getCampo("cod_tipo");
            $arTemp["stDescTipo"]       = $rsTipoInformacao->getCampo("descricao");
            $arTemp["inCodigoEvento"]   = $rsEvento->getCampo("codigo");
            $arTemp["stEvento"]         = $rsEvento->getCampo("descricao");
            $arTemp["inCodEvento"]      = $rsEvento->getCampo("cod_evento");
            if (in_array($rsDadosExportacao->getCampo("cod_tipo"),array(1,2,4))) {
                $obTPontoFormatoInformacao->setDado("cod_formato",$rsDadosExportacao->getCampo("cod_formato"));
                $obTPontoFormatoInformacao->setDado("cod_dado",$rsDadosExportacao->getCampo("cod_dado"));
                $obTPontoFormatoInformacao->recuperaPorChave($rsFormatoInformacao);
                $arTemp["stFormato"]    = $rsFormatoInformacao->getCampo("formato");
            }
            if ($rsDadosExportacao->getCampo("cod_tipo") == 7) {
                $stFiltro  = " AND cod_formato = ".$rsDadosExportacao->getCampo("cod_formato");
                $stFiltro .= " AND cod_dado = ".$rsDadosExportacao->getCampo("cod_dado");
                $obTPontoFormatoFaixasHorasExtras->recuperaRelacionamento($rsFormatoFaixasHorasExtras,$stFiltro);
                $arFaixas = array();
                while (!$rsFormatoFaixasHorasExtras->eof()) {
                    $stCampo = "boMarcar_".$rsFormatoFaixasHorasExtras->getCampo("cod_configuracao")."_".$rsFormatoFaixasHorasExtras->getCampo("cod_faixa");
                    $arFaixas[] = $stCampo;
                    $rsFormatoFaixasHorasExtras->proximo();
                }
                $arTemp["arFaixas"]     = $arFaixas;
            }
            $arDadosExportacao[] = $arTemp;
            $rsDadosExportacao->proximo();
        }
        Sessao::write("arDadosExportacao",$arDadosExportacao);
        $stJs  .= gerarSpanConfiguracao();
    }

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "gerarSpansExportar":
        $stJs = gerarSpansExportar();
        break;
    case "incluirConfiguracao":
        $stJs = incluirConfiguracao();
        break;
    case "alterarConfiguracao":
        $stJs = alterarConfiguracao();
        break;
    case "excluirConfiguracao":
        $stJs = excluirConfiguracao();
        break;
    case "limparConfiguracao":
        $stJs = limparConfiguracao();
        break;
    case "montaAlterarConfiguracao":
        $stJs =  montaAlterarConfiguracao();
        break;
    case "limparFormulario":
        $stJs = limparFormulario();
        break;
    case "onLoad":
        $stJs = onLoad();
        break;
}

if ($stJs) {
   echo($stJs);
}
?>
