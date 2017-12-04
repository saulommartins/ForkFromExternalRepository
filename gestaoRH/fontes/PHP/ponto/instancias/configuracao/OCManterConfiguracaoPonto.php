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
 * Oculto para configuração do ponto
 * Data de Criação   : 13/10/2008

 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoPonto";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

function onLoad()
{
    $stJs = "jQuery('#btLimparFaixasHorasExtras').attr('onClick', 'montaParametrosGET(\'limparFaixasHorasExtras\',\'\');' );";
    if (Sessao::read("stAcao") == "alterar") {
        if (Sessao::read("boSNArredondarTempo") == "SIM") {
            $_GET["boSNArredondarTempo"] = "S";
            $stJs .= gerarSpanArredondar();
        }

        include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFaixasHorasExtra.class.php");
        $obTPontoFaixasHorasExtra = new TPontoFaixasHorasExtra();
        $stFiltro  = " WHERE faixas_horas_extra.cod_configuracao = ".Sessao::read("inCodConfiguracao");
        $stFiltro .= "   AND faixas_horas_extra.timestamp = '".Sessao::read("stUltimoTimestamp")."'";
        $obTPontoFaixasHorasExtra->recuperaTodos($rsFaixasHorasExtra,$stFiltro);
        if ($rsFaixasHorasExtra->getNumLinhas() > 0) {
            include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFaixasDias.class.php");
            $obTPontoFaixasDias = new TPontoFaixasDias();

            $arFaixasHorasExtras = array();
            while (!$rsFaixasHorasExtra->eof()) {
                $arDias = array();
                $stDias = "";
                $stFiltro  = " WHERE faixas_dias.cod_configuracao = ".Sessao::read("inCodConfiguracao");
                $stFiltro .= "   AND faixas_dias.timestamp = '".Sessao::read("stUltimoTimestamp")."'";
                $stFiltro .= "   AND faixas_dias.cod_faixa = ".$rsFaixasHorasExtra->getCampo("cod_faixa");
                $obTPontoFaixasDias->recuperaRelacionamento($rsFaixasDias,$stFiltro);
                while (!$rsFaixasDias->eof()) {
                    $arDias[] = $rsFaixasDias->getCampo("cod_dia");
                    $stDias .= trim($rsFaixasDias->getCampo("nom_dia"))."/";
                    $rsFaixasDias->proximo();
                }
                $stDias = substr($stDias,0,strlen($stDias)-1);

                switch ($rsFaixasHorasExtra->getCampo("calculo_horas_extra")) {
                    case "D":
                        $stCalculoHorasExtras = "Diário";
                        break;
                    case "S":
                        $stCalculoHorasExtras = "Semanal";
                        break;
                    case "M":
                        $stCalculoHorasExtras = "Mensal";
                        break;
                }
                $arFaixaHorasExtras["inId"]                 = count($arFaixasHorasExtras)+1;
                $arFaixaHorasExtras["arDias"]               = $arDias;
                $arFaixaHorasExtras["stDias"]               = $stDias;
                $arFaixaHorasExtras["inPercentual"]         = (int) $rsFaixasHorasExtra->getCampo("percentual");
                $arFaixaHorasExtras["stQuantHoras"]         = substr($rsFaixasHorasExtra->getCampo("horas"),0,5);
                $arFaixaHorasExtras["stCalculoHorasExtras"] = $stCalculoHorasExtras;
                $arFaixasHorasExtras[] = $arFaixaHorasExtras;
                $rsFaixasHorasExtra->proximo();
            }

            Sessao::write("arFaixasHorasExtras",$arFaixasHorasExtras);
            $stJs .= gerarSpanFaixasHorasExtras();
        }
    }

    return $stJs;
}

function gerarSpanArredondar()
{
    $stHtml = "";
    $stEval = "";
    if ($_GET["boSNArredondarTempo"] == "S") {
        $obHorEntrada1 = new Hora();
        $obHorEntrada1->setRotulo("1° Entrada");
        $obHorEntrada1->setName("stEntrada1");
        $obHorEntrada1->setId("stEntrada1");
        $obHorEntrada1->setValue(Sessao::read("stEntrada1"));
        $obHorEntrada1->setNull(false);

        $obHorSaida1 = new Hora();
        $obHorSaida1->setRotulo("1° Saída");
        $obHorSaida1->setName("stSaida1");
        $obHorSaida1->setId("stSaida1");
        $obHorSaida1->setValue(Sessao::read("stSaida1"));
        $obHorSaida1->setNull(false);

        $obHorEntrada2 = new Hora();
        $obHorEntrada2->setRotulo("2° Entrada");
        $obHorEntrada2->setName("stEntrada2");
        $obHorEntrada2->setId("stEntrada2");
        $obHorEntrada2->setValue(Sessao::read("stEntrada2"));
        $obHorEntrada2->setNull(false);

        $obHorSaida2 = new Hora();
        $obHorSaida2->setRotulo("2° Saída");
        $obHorSaida2->setName("stSaida2");
        $obHorSaida2->setId("stSaida2");
        $obHorSaida2->setValue(Sessao::read("stSaida2"));
        $obHorSaida2->setNull(false);

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obHorEntrada1);
        $obFormulario->addComponente($obHorSaida1);
        $obFormulario->addComponente($obHorEntrada2);
        $obFormulario->addComponente($obHorSaida2);
        $obFormulario->montaInnerHTML();
        $obFormulario->obJavaScript->montaJavaScript();
        $stHtml = $obFormulario->getHTML();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    }
    $stJs  = "jQuery('#spnArredondarTempo').html('".$stHtml."');";
    $stJs .= "jQuery('#hdnArredondarTempo').attr('value','".$stEval."');";

    return $stJs;
}

function validarFaixasHorasExtras($stAcao,$inCodDiaValidar)
{
    $obErro = new erro();
    $arFaixasHorasExtras = Sessao::read("arFaixasHorasExtras");
    if (is_array($arFaixasHorasExtras)) {
        foreach ($arFaixasHorasExtras as $arFaixaHorasExtras) {
            foreach ($arFaixaHorasExtras["arDias"] as $inCodDia) {
                if ($inCodDia == $inCodDiaValidar and $arFaixaHorasExtras["inPercentual"] == $_GET["inPercentual"]) {
                    if ($arFaixaHorasExtras["inId"] != Sessao::read("inId")) {
                        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalDiasTurno.class.php");
                        $obTPessoalDiasTurno = new TPessoalDiasTurno();
                        $obTPessoalDiasTurno->setDado("cod_dia",$inCodDia);
                        $obTPessoalDiasTurno->recuperaPorChave($rsDia);
                        $obErro->setDescricao("O dia ".$rsDia->getCampo("nom_dia")." já foi inserido com o percentual de ".$_GET["inPercentual"]."%");
                    }
                }
            }
        }
    }

    return $obErro->getDescricao();
}

function validarDiasHorasExtras()
{
    $obErro = new erro();
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalDiasTurno.class.php");
    $obTPessoalDiasTurno = new TPessoalDiasTurno();
    $obTPessoalDiasTurno->recuperaTodos($rsDias);
    $obErro->setDescricao("Campo Dias Horas Extras da guia Horas Extras inválido!()");
    while (!$rsDias->eof()) {
        if ($_GET["boDiaUtilHorasExtras_".$rsDias->getCampo("cod_dia")] == "true") {
            $obErro->setDescricao("");
        }
        $rsDias->proximo();
    }

    return $obErro;
}

function processarFaixasHorasExtras($stAcao)
{
    $obErro = validarDiasHorasExtras();
    if (!$obErro->ocorreu()) {
        $arFaixasHorasExtras = Sessao::read("arFaixasHorasExtras");
        $arDias = array();
        $stDias = "";
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalDiasTurno.class.php");
        $obTPessoalDiasTurno = new TPessoalDiasTurno();
        $obErro = new erro();
        foreach ($_GET as $stNome=>$stValor) {
            if (strpos($stNome,"boDiaUtilHorasExtras") === 0 and $stValor === "true") {
                $arTemp = explode("_",$stNome);
                $stMensagem = validarFaixasHorasExtras($stAcao,$arTemp[1]);
                if (trim($stMensagem) == "") {
                    $arDias[] = $arTemp[1];
                    $obTPessoalDiasTurno->setDado("cod_dia",$arTemp[1]);
                    $obTPessoalDiasTurno->recuperaPorChave($rsDia);
                    $stDias .= trim($rsDia->getCampo("nom_dia"))."/";
                } else {
                    $obErro->setDescricao($obErro->getDescricao()."@".$stMensagem);
                }
            }
        }
        $stDias = substr($stDias,0,strlen($stDias)-1);
        if (count($arDias) > 0) {
            $arFaixaHorasExtras["arDias"]               = $arDias;
            $arFaixaHorasExtras["stDias"]               = $stDias;
            $arFaixaHorasExtras["inPercentual"]         = $_GET["inPercentual"];
            $arFaixaHorasExtras["stQuantHoras"]         = $_GET["stQuantHoras"];
            $arFaixaHorasExtras["stCalculoHorasExtras"] = $_GET["stCalculoHorasExtras"];
            if ($stAcao == "incluir") {
                $arFaixaHorasExtras["inId"]                 = count($arFaixasHorasExtras)+1;
                $arFaixasHorasExtras[] = $arFaixaHorasExtras;
            } else {
                $arFaixaHorasExtras["inId"]                 = Sessao::read("inId");
                $arFaixasHorasExtras[Sessao::read("inId")-1] = $arFaixaHorasExtras;
            }

            Sessao::write('arFaixasHorasExtras',$arFaixasHorasExtras);
            $stJs  = gerarSpanFaixasHorasExtras();
        }
        if ($obErro->ocorreu()) {
            $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
        }
        $stJs .= limparFaixasHorasExtras();
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function incluirFaixasHorasExtras()
{
    $stJs = processarFaixasHorasExtras("incluir");

    return $stJs;
}

function alterarFaixasHorasExtras()
{
    $stJs = processarFaixasHorasExtras("alterar");

    return $stJs;
}

function excluirFaixasHorasExtras()
{
    $arFaixasHorasExtras = Sessao::read("arFaixasHorasExtras");
    $stJs .= limparFaixasHorasExtras();
    Sessao::write("inId",$_GET["inId"]);
    $arTemp = array();
    foreach ($arFaixasHorasExtras as $arFaixaHorasExtras) {
        if ($arFaixaHorasExtras["inId"] != $_GET["inId"]) {
            $arFaixaHorasExtras["inId"] = count($arTemp)+1;
            $arTemp[] = $arFaixaHorasExtras;
        }
    }
    Sessao::write('arFaixasHorasExtras',$arTemp);
    $stJs .= gerarSpanFaixasHorasExtras();

    return $stJs;
}

function limparFaixasHorasExtras()
{
    Sessao::remove("inId");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalDiasTurno.class.php");
    $obTPessoalDiasTurno = new TPessoalDiasTurno();
    $obTPessoalDiasTurno->recuperaTodos($rsDias);
    while (!$rsDias->eof()) {
        $stJs .= "jQuery('#boDiaUtilHorasExtras_".$rsDias->getCampo("cod_dia")."').attr('checked','');\n";
        $stJs .= "jQuery('#boDiaUtilHorasExtras_".$rsDias->getCampo("cod_dia")."').attr('value','false');\n";
        $rsDias->proximo();
    }
    $stJs .= "jQuery('#inPercentual').attr('value','');\n";
    $stJs .= "jQuery('#stQuantHoras').attr('value','');\n";
    $stJs .= "jQuery('#stCalculoHorasExtras').get(0).checked = true;\n";
    $stJs .= "jQuery('input[type=radio][value=Diário]').attr('checked', 'checked');";
    $stJs .= "jQuery('#btIncluirFaixasHorasExtras').attr('disabled','');\n";
    $stJs .= "jQuery('#btAlterarFaixasHorasExtras').attr('disabled','disabled');\n";

    return $stJs;
}

function gerarSpanFaixasHorasExtras()
{
    $rsRecordSet = new recordset();
    $rsRecordSet->preenche(Sessao::read("arFaixasHorasExtras"));

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Lista de Faixas de Horas Extras" );
    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Dias" );
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Percentual (%)" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Qtd Horas" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Cálculo" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Ação" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stDias");
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inPercentual");
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stQuantHoras");
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stCalculoHorasExtras");
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setLinkId("alterar");
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('montarAlterarFaixasHorasExtras');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirFaixasHorasExtras');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stJs = "jQuery('#spnFaixasHorasExtras').html('".$stHtml."');";

    return $stJs;
}

function montarAlterarFaixasHorasExtras()
{
    $arFaixasHorasExtras = Sessao::read("arFaixasHorasExtras");
    $stJs .= limparFaixasHorasExtras();
    Sessao::write("inId",$_GET["inId"]);
    foreach ($arFaixasHorasExtras as $arFaixaHorasExtras) {
        if ($arFaixaHorasExtras["inId"] == $_GET["inId"]) {
            //echo "alert('".$arFaixaHorasExtras["inPercentual"]."');";
            foreach ($arFaixaHorasExtras["arDias"] as $inCodDia) {
                $stJs .= "jQuery('#boDiaUtilHorasExtras_".$inCodDia."').attr('checked','checked');\n";
                $stJs .= "jQuery('#boDiaUtilHorasExtras_".$inCodDia."').attr('value','true');\n";
            }
            $stJs .= "jQuery('#inPercentual').attr('value','".$arFaixaHorasExtras["inPercentual"]."');\n";
            $stJs .= "jQuery('#stQuantHoras').attr('value','".$arFaixaHorasExtras["stQuantHoras"]."');\n";
            $stJs .= "jQuery('input[type=radio][value=".$arFaixaHorasExtras["stCalculoHorasExtras"]."]').attr('checked', 'checked');";
        }
    }
    $stJs .= "jQuery('#btIncluirFaixasHorasExtras').attr('disabled','disabled');\n";
    $stJs .= "jQuery('#btAlterarFaixasHorasExtras').attr('disabled','');\n";

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "gerarSpanArredondar":
        $stJs = gerarSpanArredondar();
        break;
    case "incluirFaixasHorasExtras":
        $stJs = incluirFaixasHorasExtras();
        break;
    case "alterarFaixasHorasExtras":
        $stJs = alterarFaixasHorasExtras();
        break;
    case "excluirFaixasHorasExtras":
        $stJs = excluirFaixasHorasExtras();
        break;
    case "limparFaixasHorasExtras":
        $stJs = limparFaixasHorasExtras();
        break;
    case "montarAlterarFaixasHorasExtras":
        $stJs = montarAlterarFaixasHorasExtras();
        break;
    case "onLoad":
        $stJs = onLoad();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
