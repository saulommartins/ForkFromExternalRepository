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
* Página do oculto do vale transporte
* Data de Criação: 08/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30880 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.06.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php"                  );
include_once( CAM_GRH_BEN_NEGOCIO."RBeneficioValeTransporte.class.php"         );

//Define o nome dos arquivos PHP
$stPrograma = "ManterValeTransporte";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

$obRBeneficioValeTransporte    = new RBeneficioValeTransporte;
$rsMunicipioOrigem    = $rsMunicipioDestino = new RecordSet;

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function listaCustos()
{
    global  $obRBeneficioValeTransporte;
    if ( Sessao::read('inCodValeTransporte') ) {
        $obRBeneficioValeTransporte->setCodValeTransporte( Sessao::read('inCodValeTransporte') );
    }

    $obRBeneficioValeTransporte->listarCusto( $rsListaCusto );

    $nuValorCusto = $rsListaCusto->getCampo('valor');
    $nuValorCusto = number_format( $nuValorCusto , 2 , ',' , '.' );

    if ($rsListaCusto->getNumLinhas() == 0) {
        $stHTML = " ";
        $js .= "window.parent.frames['telaPrincipal'].document.frm.boValidaCustos.value = '';";
    } else {
        $rsListaCusto->addFormatacao("valor", "NUMERIC_BR");

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Variações de Custo Unitário" );

        $obLista->setRecordSet( $rsListaCusto );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Vigência do Valor");
        $obLista->ultimoCabecalho->setWidth( 45 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor");
        $obLista->ultimoCabecalho->setWidth( 45 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inicio_vigencia" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "valor" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace("\n","",$stHTML);
        $stHTML = str_replace("  ","",$stHTML);
        $stHTML = str_replace("'","\\'",$stHTML);

    }

    // preenche a lista com innerHTML
    $js .= "d.getElementById('listaCustos').innerHTML = '".$stHTML."';";

    return $js;
}

function listarFaixasDesconto()
{
    global $obRBeneficioValeTransporte;
    $rsRecordSet = new Recordset;
    $obRBeneficioValeTransporte->obRBeneficioVigencia->roUltimoFaixaDesconto->listarUltimaVigencia( $rsRecordSet );
    $rsRecordSet->addFormatacao( "vl_inicial", "NUMERIC_BR" );
    $rsRecordSet->addFormatacao( "vl_final", "NUMERIC_BR" );
    $rsRecordSet->addFormatacao( "percentual_desconto", "NUMERIC_BR" );
    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Faixas de Desconto Salarial" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor Inicial" );
        $obLista->ultimoCabecalho->setWidth( 40 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor Final" );
        $obLista->ultimoCabecalho->setWidth( 40 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Percentual Desconto" );
        $obLista->ultimoCabecalho->setWidth( 40 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_inicial" );
        $obLista->ultimoDado->setAlinhamento( 'RIGHT' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_final" );
        $obLista->ultimoDado->setAlinhamento( 'RIGHT' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "percentual_desconto" );
        $obLista->ultimoDado->setAlinhamento( 'CENTER' );
        $obLista->commitDado();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('listaFaixaDesconto').innerHTML = '".$stHtml."';";

    return $stJs;
}

function preencheMunicipioIncluir()
{
    global $obRBeneficioValeTransporte;
    $rsMunicipioOrigem = new Recordset;
    $stFiltro = " WHERE cod_uf = ".$_REQUEST["inCodUFOrigem"];
    $js .= "limpaSelect(f.stNomeMunicipioOrigem,0); \n";
    $js .= "f.stNomeMunicipioOrigem[0] = new Option('Selecione','');\n";
    if ($_REQUEST["inCodUFOrigem"]) {
        $obRBeneficioValeTransporte->obRBeneficioItinerario->obTMunicipio->recuperaTodos( $rsMunicipioOrigem, $stFiltro,"", $boTransacao );
        $inContador = 1;
    }

    while ( !$rsMunicipioOrigem->eof() ) {
        if ( $_REQUEST["inCodMunicipioOrigem"] == $rsMunicipioOrigem->getCampo( "cod_municipio" ) ) {
            $js .= "f.stNomeMunicipioOrigem.options[$inContador] = new Option('".addslashes($rsMunicipioOrigem->getCampo( "nom_municipio" ))."','".$rsMunicipioOrigem->getCampo( "cod_municipio" )."' , 'selected'); \n";
        } else {
             $js .= "f.stNomeMunicipioOrigem.options[$inContador] = new Option('".addslashes($rsMunicipioOrigem->getCampo( "nom_municipio" ))."','".$rsMunicipioOrigem->getCampo( "cod_municipio" )."'); \n";
        }

        if ( $_REQUEST["inCodMunicipioDestino"] == $rsMunicipioOrigem->getCampo( "cod_municipio" ) ) {
            $js .= "f.stNomeMunicipioDestino.options[$inContador] = new Option('".addslashes($rsMunicipioOrigem->getCampo( "nom_municipio" ))."','".$rsMunicipioOrigem->getCampo( "cod_municipio" )."' , 'selected'); \n";
        } else {
            $js.= "f.stNomeMunicipioDestino.options[$inContador] = new Option('".addslashes($rsMunicipioOrigem->getCampo( "nom_municipio" ))."','".$rsMunicipioOrigem->getCampo( "cod_municipio" )."'); \n";
        }

        $inContador++;
        $rsMunicipioOrigem->proximo();
    }

    return $js;
}

function buscaCGM()
{
    global $obRBeneficioValeTransporte;
    if ($_POST["inNumCGM"] != "") {
        $obRBeneficioValeTransporte->obRBeneficioFornecedorValeTransporte->setNumCGM( $_POST["inNumCGM"] );
        $stWhere = " AND  sw_cgm.numcgm = ".$obRBeneficioValeTransporte->obRBeneficioFornecedorValeTransporte->getNumCGM();
        $null = "&nbsp;";
        $obRBeneficioValeTransporte->obRBeneficioFornecedorValeTransporte->listarFornecedorValeTransporte( $rsRecordSet, $stWhere,"", $boTransacao );
        $inNumLinhas = $rsRecordSet->getNumLinhas();
        if ($inNumLinhas <= 0) {
            $js .= 'f.inNumCGM.value = "";';
            $js .= 'f.inNumCGM.focus();';
            $js .= 'd.getElementById("campoInner").innerHTML = "'.$null.'";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST['inNumCGM'].")','form','erro','".Sessao::getId()."');\n";
        } else {
            $stNomCgm = $rsRecordSet->getCampo('nom_cgm');
            $js .= 'd.getElementById("stNomCGM").innerHTML = "'.$stNomCgm.'";';
        }

        return $js;
    } else {
        return "";
    }
}

//Monta a combo de municipio após o estado ter sido selecionado
switch ($_POST["stCtrl"]) {
    case "buscaCGM":
        $js .= buscaCGM();
        sistemaLegado::executaFrameOculto($js);
    break;

    case "preencheMunicipioIncluir":
        $js .= preencheMunicipioIncluir();
        sistemaLegado::executaFrameOculto($js);
    break;

    case "preencheMunicipioOrigem":
        $stFiltro = " WHERE cod_uf = ".$_REQUEST["inCodUFOrigem"];
        $js .= "f.inCodMunicipioOrigem.value = ''; \n";
        $js .= "limpaSelect(f.stNomeMunicipioOrigem,0); \n";
        $js .= "f.stNomeMunicipioOrigem[0] = new Option('Selecione','', 'selected');\n";
        if ($_POST["inCodUFOrigem"]) {
            $obRBeneficioValeTransporte->obRBeneficioItinerario->obTMunicipio->recuperaTodos( $rsMunicipioOrigem, $stFiltro,"", $boTransacao );
            $inContador = 1;
        }
        while ( !$rsMunicipioOrigem->eof() ) {
            $inCodMunicipio  = $rsMunicipioOrigem->getCampo( "cod_municipio" );
            $stNomeMunicipio = $rsMunicipioOrigem->getCampo( "nom_municipio" );
            $js .= "f.stNomeMunicipioOrigem.options[$inContador] = new Option('".addslashes($stNomeMunicipio)."','".$inCodMunicipio."'); \n";
            $inContador++;
            $rsMunicipioOrigem->proximo();
        }
        sistemaLegado::executaFrameOculto($js);
    break;

    case "preencheMunicipioDestino":
        $stFiltro = " WHERE cod_uf = ".$_REQUEST["inCodUFDestino"];
        $js .= "f.inCodMunicipioDestino.value = ''; \n";
        $js .= "limpaSelect(f.stNomeMunicipioDestino,0); \n";
        $js .= "f.stNomeMunicipioDestino[0] = new Option('Selecione','', 'selected');\n";
        if ($_POST["inCodUFDestino"]) {
            $obRBeneficioValeTransporte->obRBeneficioItinerario->obTMunicipio->recuperaTodos( $rsMunicipioDestino, $stFiltro,"", $boTransacao );
            $inContador = 1;
        }
        while ( !$rsMunicipioDestino->eof() ) {
            $inCodMunicipio  = $rsMunicipioDestino->getCampo( "cod_municipio" );
            $stNomeMunicipio = $rsMunicipioDestino->getCampo( "nom_municipio" );
            $js .= "f.stNomeMunicipioDestino.options[$inContador] = new Option('".addslashes($stNomeMunicipio)."','".$inCodMunicipio."'); \n";
            $inContador++;
            $rsMunicipioDestino->proximo();
        }
        sistemaLegado::executaFrameOculto($js);
    break;

    case "preencheCamposAlteracao":
        $js .= preencheMunicipioIncluir();
        $js .= buscaCGM();
        sistemaLegado::executaFrameOculto($js);
    break;

    case "preencheSpans":
        if ($_REQUEST['stAcao'] != 'incluir') {
            $js .= listaCustos();
        } else {
            $js .= preencheMunicipioIncluir();
        }
        $js .= listarFaixasDesconto();
        sistemaLegado::executaFrameOculto($js);
    break;
}
?>
