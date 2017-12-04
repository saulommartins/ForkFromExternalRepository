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
* Página de Processamento Oculto
* Data de Criação: 30/06/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Rafael Almeida

* @package URBEM
* @subpackage

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-13 09:55:05 -0300 (Qua, 13 Jun 2007) $

* Casos de uso: uc-04.01.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_CON_NEGOCIO."RConcursoCandidato.class.php");
include_once( CAM_GA_ADM_NEGOCIO ."RAdministracaoConfiguracao.class.php");
include_once( CAM_GA_CGM_NEGOCIO ."RCGM.class.php");
include_once( CAM_GRH_CON_NEGOCIO."RConcursoConcurso.class.php");

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

// preenche combos de Localizacao a partir da MASCARA informada
$obRCandidato = new RConcursoCandidato;
$obRConcursoConcursoCargo = new RConcursoConcurso;

// Acoes por pagina
switch ($stCtrl) {
    case "buscaCGM":
    if ($_REQUEST["inNumCGM"] != "") {
        $obRCGM = new RCGMPessoaFisica;
        $obRCGM->setNumCGM( $_REQUEST["inNumCGM"] );
        $obRCGM->consultarCGM( $rsCGM );
        $null = "&nbsp;";
        $obRCandidato->setNumCGM($_REQUEST["inCodCandidatos"]);
        if ($rsCGM->getNumLinhas() <= 0) {
            $js .= 'f.inNumCGM.value = "";';
            $js .= 'f.inNumCGM.focus();';
            $js .= 'd.getElementById("nom_cgm").innerHTML = "'.$null.'";';
            $js .= "alertaAviso('@Valor inválido. (".$_REQUEST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
            $js .= "d.getElementById('stEndereco').innerHTML = '&nbsp;';\n";
            $js .= "d.getElementById('stEstado').innerHTML   = '&nbsp;';\n";
            $js .= "d.getElementById('stCidade').innerHTML   = '&nbsp;';\n";
            $js .= "d.getElementById('stBairro').innerHTML   = '&nbsp;';\n";
            $js .= "d.getElementById('stCep').innerHTML      = '&nbsp;';\n";
            $js .= "d.getElementById('stFoneRes').innerHTML  = '&nbsp;';\n";
            $js .= "d.getElementById('stFoneCel').innerHTML  = '&nbsp;';\n";
            $js .= "d.getElementById('stEmail').innerHTML    = '&nbsp;';\n";

        } else {
            $js .= 'd.getElementById("nom_cgm").innerHTML    = "'.$rsCGM->getCampo("nom_cgm").'";';
            $js .= 'd.getElementById("stEndereco").innerHTML = "'.$rsCGM->getCampo("tipo_logradouro_corresp")." ".$rsCGM->getCampo("logradouro_corresp").'";';
            $obRCandidato->obTUF->setDado("cod_uf", $rsCGM->getCampo("cod_uf_corresp"));
            $obRCandidato->recuperaUF( $rsUF );
            $js .= 'd.getElementById("stEstado").innerHTML   = "'.$rsUF->getCampo("sigla_uf").'";';
            $obRCandidato->obTMunicipio->setDado("cod_uf", $rsCGM->getCampo("cod_uf_corresp"));
            $obRCandidato->obTMunicipio->setDado("cod_municipio", $rsCGM->getCampo("cod_municipio_corresp"));
            $obRCandidato->obTMunicipio->recuperaPorChave( $rsMunicipio );
            $js .= 'd.getElementById("stCidade").innerHTML   = "'.$rsMunicipio->getCampo("nom_municipio").'";';
            $js .= 'd.getElementById("stBairro").innerHTML   = "'.$rsCGM->getCampo("bairro_corresp").'";';
            $js .= 'd.getElementById("stCep").innerHTML      = "'.$rsCGM->getCampo("cep_corresp").'";';
            $js .= 'd.getElementById("stFoneRes").innerHTML  = "'.$rsCGM->getCampo("fone_residencial").'";';
            $js .= 'd.getElementById("stFoneCel").innerHTML  = "'.$rsCGM->getCampo("fone_celular").'";';
            $js .= 'd.getElementById("stEmail").innerHTML    = "'.$rsCGM->getCampo("e_mail").'";';
        }
    } else {
        $js .= "d.getElementById('nom_cgm').innerHTML    = '&nbsp;';\n";
        $js .= "d.getElementById('stEndereco').innerHTML = '&nbsp;';\n";
        $js .= "d.getElementById('stEstado').innerHTML   = '&nbsp;';\n";
        $js .= "d.getElementById('stCidade').innerHTML   = '&nbsp;';\n";
        $js .= "d.getElementById('stBairro').innerHTML   = '&nbsp;';\n";
        $js .= "d.getElementById('stCep').innerHTML      = '&nbsp;';\n";
        $js .= "d.getElementById('stFoneRes').innerHTML  = '&nbsp;';\n";
        $js .= "d.getElementById('stFoneCel').innerHTML  = '&nbsp;';\n";
        $js .= "d.getElementById('stEmail').innerHTML    = '&nbsp;';\n";
    }

    SistemaLegado::executaFrameOculto($js);
    break;

    case 'preencheInner':
    $arCodMunUF = explode( '-' , $_REQUEST['inCodMunUF'] );
    $js  = "";
    $js .= 'd.getElementById("nom_cgm").innerHTML    = "'.$_REQUEST["stNomResponsavel"];
    SistemaLegado::executaFrameOculto($js);
    break;

    case "calculaMedia":
    $inValorMedia = 1;
    if ($_REQUEST['inHdnTipoProva'] == 1) {
        $stNotaProva  = $_REQUEST['stNotaProvaPratica'];

    } else {
        $stNotaProva  = $_REQUEST['stNotaProvaTeoricoPratica'];
    }

    $stMascara = $stNotaProva;
    $stNotaProva = str_replace( ",",".",$stNotaProva);

    if ($_REQUEST['inHdnProvaTitulacao'] == 't') {
        $stNotaTitulacao = $_REQUEST['stNotaTitulacao'];
        $stNotaTitulacao = str_replace( ",", ".", $stNotaTitulacao );
        if( $_REQUEST['stNotaTitulacao']<> '' )
        $inValorMedia    = 2;
    }

    $stNotaMedia = bcdiv(($stNotaProva + $stNotaTitulacao),$inValorMedia,2);
    $obRCandidato->obRConcursoConcurso->recuperaConfiguracao($arConfiguracao);

    foreach ($arConfiguracao as $key => $valor) {
        if ($key == 'mascara_nota'.Sessao::getEntidade()) {
            $stMascaraNota = $valor;
        }
    }

    $obMascara = New Mascara;
    $stNotaMedia = str_replace(".","",$stNotaMedia);
    $obMascara->preencheMascaraComZeros2( $stNotaMedia, $stMascaraNota );
    $stNotaMedia = $obMascara->getMascarado();

    if ($_REQUEST['stNotaProvaTeoricoPratica'] || $_REQUEST['stNotaProvaPratica']) {
        $js .= 'f.stMedia.value = "'.ltrim($stNotaMedia,"0").'";';
        $js .= 'f.inHdnMedia.value = "'.$stNotaMedia.'";';
    } else {
        $js .= 'f.stMedia.value = "";';
        $js .= 'f.inHdnMedia.value = "";';

    }

    SistemaLegado::executaFrameOculto($js);
    break;

    case 'preencheComboCargos':
    if ($_REQUEST['inCodEdital']!="") {
        $stFiltro = " AND C.cod_edital=".$_REQUEST['inCodEdital'];
        $obRConcursoConcursoCargo->obTConcursoConcursoCargo->listarCargos($rsCargosDisponiveis,$stFiltro);
        $obRConcursoConcursoCargo->setCodEdital($_REQUEST['inCodEdital']);
            $obRConcursoConcursoCargo->consultarConcurso( $rsConcurso, $rsCargosSelecionados );

        $inContador = 1;
        $js .= "limpaSelect(f.stCargo,0); \n";
        $js .= "f.stCargo.options[0] = new Option('Selecione o Cargo','', 'selected');\n";

        while (!$rsCargosDisponiveis->eof()) {
            $stCodCargo = $rsCargosDisponiveis->getCampo("cod_cargo");
            $stNomCargo = $rsCargosDisponiveis->getCampo("descricao");
            $js .= "f.stCargo.options[$inContador] = new Option('".$stNomCargo."','".$stCodCargo."'); \n";
            $inContador++;
            $rsCargosDisponiveis->proximo();
        }

        $js .= "d.getElementById('dtHomologacao').innerHTML = '".$rsConcurso->getCampo('dt_homologacao')."';\n";
    } else {
        $js .= "d.getElementById('dtHomologacao').innerHTML = '';\n";
    }

    SistemaLegado::executaFrameOculto($js);
    break;
}
?>
