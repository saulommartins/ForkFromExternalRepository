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
    * Página Oculto de Inclusao/Alteracao Inscrição Ecônomica
    * Data de Criação   : 22/12/2004

    * @author Tonismar Régis Bernardo
    * @ignore

    * $Id: OCManterInscricao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMCategoria.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNaturezaJuridica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RResponsavelTecnico.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomico.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManteriInscricao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function BuscaCGM()
{
    global $_REQUEST;

    if ($_REQUEST[ 'inCodigoEnquadramento' ] == 2) {
        $obRCGM = new RCGMPessoaJuridica;
    } elseif ($_REQUEST[ 'inCodigoEnquadramento' ] == "") {
        $obRCGM = new RCGM;
    } else {
        $obRCGM = new RCGMPessoaFisica;
    }

    $stText = "inNumCGM";
    $stSpan = "stNomCGM";

    if ($_REQUEST[ $stText ] != "") {
        $obRCGM->setNumCGM( $_REQUEST[ $stText ] );
        if ($_REQUEST[ 'inCodigoEnquadramento' ] != "") {
            $obRCGM->consultarCGM( $rsCGM );
        } else {
            $obRCGM->consultar( $rsCGM );
        }
        $stNull = "&nbsp;";

        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
           $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
        }
    } else {
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}

function BuscaCGMJuridica()
{
    global $_REQUEST;

    $obRCGM = new RCGMPessoaJuridica;

    $stText = "inNumCGMJuridica2";
    $stSpan = "stNomCGMJuridica2";

    if ($_REQUEST[ $stText ] != "") {
        $obRCGM->setNumCGM( $_REQUEST[ $stText ] );
        $obRCGM->consultarCGM( $rsCGM );
        $stNull = "&nbsp;";

        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('CGM de Pessoa Jurídica inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
           $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
        }
    } else {
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}

function BuscaSocio()
{
    global $_REQUEST;
    ;
    $obRCGM = new RCGM;

    $stText = "inCodigoSocio";
    $stSpan = "stNomeSocio";

    if ($_REQUEST[ $stText ] != "") {
        $obRCGM->setNumCGM( $_REQUEST[ $stText ] );
        $obRCGM->consultar( $rsCGM );
        $stNull = "&nbsp;";

        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
           $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
        }
    } else {
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}

function BuscaRespContabil()
{
    global $_REQUEST;
    ;
    $obRResponsavel = new RResponsavelTecnico;

    $stText = "inNumCGMResponsavel";
    $stSpan = "stNomCGMResponsavel";

    if ($_REQUEST[ $stText ] != "") {
        $obRResponsavel->setNumCGM( $_REQUEST[ $stText ] );
        $obRResponsavel->listarResponsavelContabil( $rsCGM );
        $stNull = "&nbsp;";
        if ( $rsCGM->getNumLinhas() <= 0 ) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
           $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';

           if ($rsCGM->getCampo('cod_profissao'))
                $stJs .= 'f.inCodProfissao.value = '.$rsCGM->getCampo('cod_profissao').';';

           $stJs .= 'f.inSequencia.value = '.$rsCGM->getCampo('sequencia').';';
        }
    } else {
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}
function BuscaDomicilio()
{
    global $_REQUEST;
    ;
    $obRCIMImovel = new RCIMImovel( new RCIMLote );
    $stText = "inCodigoDomicilio";
    $stNull = "&nbsp;";
    if ($_REQUEST[ $stText ] != "") {
        $obRCIMImovel->setNumeroInscricao( $_REQUEST[ $stText ] );
        $obRCIMImovel->addProprietario();
        $obRCIMImovel->listarImoveisConsulta( $rsImovel );

        if ( $rsImovel->getNumLinhas() <= 0 ) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'd.getElementById("stEndereco").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido para domicilio fiscal.(".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
            $stJs .= 'f.'.$stText.'.focus();';
        } else {
            $stJs .= 'f.inCodigoDomicilio.value = '.$rsImovel->getCampo('inscricao_municipal').';';

            $stEndereco = $rsImovel->getCampo('endereco');
            $stJs .= 'd.getElementById("stEndereco").innerHTML = "'.$stEndereco.'";';
        }
    } else {
        $stJs .= 'd.getElementById("stEndereco").innerHTML = "'.$stNull.'";';
        $stJs .= 'f.'.$stText.'.value = "";';
    }

    return $stJs;
}

function BuscaNatureza()
{
    global $_REQUEST;
    ;
    $obRCEMNaturezaJuridica = new RCEMNaturezaJuridica;

    $stText = "inCodigoNatureza";
    $stSpan = "stNomeNatureza";
    if ($_REQUEST[ $stText ] != "") {
        $inCodNatureza = str_replace("-","",$_REQUEST[ $stText ]);
        $obRCEMNaturezaJuridica->setCodigoNatureza( $inCodNatureza);
        $obRCEMNaturezaJuridica->listarNaturezaJuridica( $rsNatureza );
        $stNull = "&nbsp;";

        if ( $rsNatureza->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
           $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsNatureza->getCampo('nom_natureza')?$rsNatureza->getCampo('nom_natureza'):$stNull).'";';
        }
    } else {
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}

function montaListaSocio(&$rsListaSocio)
{
    GLOBAL $flCapital;
    if ( !$rsListaSocio->eof() ) {
        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsListaSocio          );
        $obLista->setTitulo                    ( "Lista de Sócios"      );
        $obLista->setMostraPaginacao           ( false                  );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Código"               );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Nome"                 );
        $obLista->ultimoCabecalho->setWidth    ( 75                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Quota"                );
        $obLista->ultimoCabecalho->setWidth    ( 75                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "inCodigoSocio"        );
        $obLista->commitDado                   (                        );
        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "stNomeSocio"          );
        $obLista->commitDado                   (                        );
        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flQuota"              );
        $obLista->commitDado                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirDado('excluirSocio');" );
        $obLista->ultimaAcao->addCampo         ( "1","inLinha"   );
        $obLista->commitAcao                   (                        );

        $obLista->montaHTML                    (                        );
        $stHTML =  $obLista->getHtml           (                        );
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );
    } else {
        $stHTML = "&nbsp;";
    }
    while ( !$rsListaSocio->eof() ) {

        $flAdd = str_replace('.','',$rsListaSocio->getCampo( "flQuota" ));
        $flAdd = number_format(str_replace(',','.',$flAdd),2,'.','');
        $flCapital = $flCapital + $flAdd;
        $flCapital = number_format( $flCapital, 2, ',','');
        $rsListaSocio->proximo();
    }
    $stJs .= "d.getElementById('flCapitalSocial').innerHTML = '&nbsp;';\n";
    $stJs .= "d.getElementById('lsListaSocio').innerHTML = '".$stHTML."';\n";
    $stJs .= "d.getElementById('flCapitalSocial').innerHTML = '".$flCapital."';\n";

    return $stJs;
}

switch ($_REQUEST["stCtrl"]) {
    case "PreencheEmpresaIntervaloInicial":
        if ($_GET["inInscricaoEconomica"]) {
            $stFiltro = " AND CE.inscricao_economica = ".$_GET["inInscricaoEconomica"];
            $obTCEMCadastroEconomico = new TCEMCadastroEconomico;
            $obTCEMCadastroEconomico->recuperaInscricao( $rsEmpresas, $stFiltro );
            if ( $rsEmpresas->eof() ) {
                $js = "alertaAviso('@Número de inscrição inválido. (".$_GET["inInscricaoEconomica"].")','form','erro','".Sessao::getId()."');";
                $js .= "f.inNumInscricaoEconomicaInicial.value = '';\n";
                $js .= "f.inNumInscricaoEconomicaInicial.focus();\n";
            }
        } else {
            $js = "alertaAviso('@Número de inscrição inválido.','form','erro','".Sessao::getId()."');";
            $js .= "f.inNumInscricaoEconomicaInicial.value = '';\n";
            $js .= "f.inNumInscricaoEconomicaInicial.focus();\n";
        }
        echo $js;

        exit;
        break;

    case "PreencheEmpresaIntervaloFinal":
        if ($_GET["inInscricaoEconomica"]) {
            $stFiltro = " AND CE.inscricao_economica = ".$_GET["inInscricaoEconomica"];
            $obTCEMCadastroEconomico = new TCEMCadastroEconomico;
            $obTCEMCadastroEconomico->recuperaInscricao( $rsEmpresas, $stFiltro );
            if ( $rsEmpresas->eof() ) {
                $js = "alertaAviso('@Número de inscrição inválido. (".$_GET["inInscricaoEconomica"].")','form','erro','".Sessao::getId()."');";
                $js .= "f.inNumInscricaoEconomicaFinal.value = '';\n";
                $js .= "f.inNumInscricaoEconomicaFinal.focus();\n";
            }
        } else {
            $js = "alertaAviso('@Número de inscrição inválido.','form','erro','".Sessao::getId()."');";
            $js .= "f.inNumInscricaoEconomicaInicial.value = '';\n";
            $js .= "f.inNumInscricaoEconomicaInicial.focus();\n";
        }
        echo $js;

        exit;
        break;

    case "PreencheEmpresa":
        if ($_GET["inInscricaoEconomica"]) {
            $stFiltro = " AND CE.inscricao_economica = ".$_GET["inInscricaoEconomica"];
            $obTCEMCadastroEconomico = new TCEMCadastroEconomico;
            $obTCEMCadastroEconomico->recuperaInscricao( $rsEmpresas, $stFiltro );
            if ( $rsEmpresas->eof() ) {
                $js = "alertaAviso('@Número de inscrição inválido. (".$_GET["inInscricaoEconomica"].")','form','erro','".Sessao::getId()."');";
                $js .= "d.getElementById('stInscricaoEconomica').innerHTML = '&nbsp;';\n";
                $js .= "f.inInscricaoEconomica.value = '';\n";
                $js .= "f.inInscricaoEconomica.focus();\n";
            } else {
                $js = "d.getElementById('stInscricaoEconomica').innerHTML = '".preg_replace("/'/","\'",$rsEmpresas->getCampo("nom_cgm"))."';\n";
            }
        } else {
            $js = "d.getElementById('stInscricaoEconomica').innerHTML = '&nbsp;';\n";
            $js .= "f.inInscricaoEconomica.value = '';\n";
        }

        echo $js;
        exit;
        break;

    case "verificaInscricao":
        if ($_REQUEST["inNumeroInscricao"]) {
            $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
            $obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST["inNumeroInscricao"] );
            $obRCEMInscricaoEconomica->listarInscricao( $rsListaInscricao );
            if (!$rsListaInscricao->eof()) {
                $stJs .= "alertaAviso('@Inscrição economica já existe. (".$_REQUEST["inNumeroInscricao"].")', 'form','erro','".Sessao::getId()."');";

                $stJs .= "f.inNumeroInscricao.value = '';\n";
                $stJs .= "f.inNumeroInscricao.focus();\n";
            }
        }

        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "preencheEnquadramento":
        $stJs .= "d.frm.stEnquadramento.selectedIndex = ".$_REQUEST[ "inCodigoEnquadramento" ];
        sistemaLegado::executaFrameOculto( $stJs );
    break;
    case "preencheCodigoEnquadramento":
        $stJs .= "d.frm.inCodigoEnquadramento.value = d.frm.stEnquadramento.selectedIndex;";
        sistemaLegado::executaFrameOculto( $stJs );
    break;
    case "buscaCGM":
         sistemaLegado::executaFrameOculto( BuscaCGM() );
    break;
    case "buscaCGMJuridica":
         sistemaLegado::executaFrameOculto( BuscaCGMJuridica() );
    break;
    case "buscaCGMResponsavel":
         sistemaLegado::executaFrameOculto( BuscaCGM( "Responsavel" ) );
    break;
    case "buscaSocio":
         sistemaLegado::executaFrameOculto( BuscaSocio() );
    break;
    case "buscaNatureza":
         sistemaLegado::executaFrameOculto( BuscaNatureza() );
    break;
    case "buscaDomicilio":
         sistemaLegado::executaFrameOculto( BuscaDomicilio() );
    break;
    case "preencheCategoria":
        $obRCEMCategoria = new RCEMCategoria;

        $stJs .= "limpaSelect(f.stCategoria,0); \n";
        $stJs .= "f.stCategoria[0] = new Option('Selecione','', 'selected');\n";
        if ($_REQUEST["inCodigoCategoria"]) {
            $obRCEMCategoria->listarCategoria( $rsCategoria );
            $inContador = 1;
        }
        $obRCEMCategoria->setCodigoCategoria( $_REQUEST["inCodigoCategoria"] );
        while ( !$rsCategoria->eof() ) {
            if ( $_REQUEST["inCodigoCategoria"] == $rsCategoria->getCampo( "cod_categoria" ) ) {
                $stSelected = "selected";
            } else {
                $stSelected = "";
            }
            $inCodigoCategoria  = $rsCategoria->getCampo( "cod_categoria" );
            $stCategoria        = $rsCategoria->getCampo( "nom_categoria" );
            $stJs .= "f.stCategoria.options[$inContador] = new Option('".$stCategoria."','".$inCodigoCategoria."','".$stSelected."'); \n";
            $inContador++;
            $rsCategoria->proximo();
            sistemaLegado::executaFrameOculto( $stJs );
        }
    break;
    case "preencheCodigoCategoria":
        $stJs .= "d.frm.inCodigoCategoria.value = d.frm.stCategoria.selectedIndex;";
        sistemaLegado::executaFrameOculto( $stJs );
    break;
    case "montaSocio":
        $rsSocios = new RecordSet;
        $obRCGM = new RCGM;
        $obRCGM->setNumCGM( $_REQUEST["inCodigoSocio"] );
        $obRCGM->consultar( $rsCGM );

        $stMsg = "";

        //VERIFICA SE O CGM JA FOI INFORMADO
        $boErro = false;
        $arSociosSessao = Sessao::read( "socios" );
        if ($arSociosSessao!=null) {
            foreach ($arSociosSessao as  $inChave => $arSocios) {
                if ($arSocios["inCodigoSocio"] == $_REQUEST["inCodigoSocio"]) {
                    $boErro = true;
                    $stMsg  = "Sócio já informado!";
                    break;
                }
            }
        }

        if ($_REQUEST["flQuota"] == '0,00') {
            $boErro = true;
            $stMsg  = "Campo Quota Nulo!";
        }

        if ($boErro) {
            $stJs = "alertaAviso('".$stMsg."(".$_REQUEST["inNumCGM"].")','form','erro','".Sessao::getId()."', '../');";
        } else {
            $stJs  = "f.inCodigoSocio.value = '';\n";
            $stJs .= "d.getElementById('stNomeSocio').innerHTML = '&nbsp;';\n";
            $stJs .= "f.flQuota.value = '';\n";
            $arSocio = array( "inCodigoSocio"  => $_REQUEST["inCodigoSocio"],
                              "stNomeSocio"    => $rsCGM->getCampo('nom_cgm'),
                              "flQuota"        => $_REQUEST["flQuota"] );
            $arSocio["inLinha"] = count( $arSociosSessao );
            $arSociosSessao[] = $arSocio;
            Sessao::write( "socios", $arSociosSessao );
            $rsSocios->preenche( $arSociosSessao );
            $stJs .= montaListaSocio( $rsSocios  );
        }
        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "excluirSocio":
        $inLinha = $_REQUEST["inLinha"] ? $_REQUEST["inLinha"] : 0;
        $arNovaListaSocio = array();
        $inContLinha = 0;
        $arSociosSessao = Sessao::read( "socios" );
        if ($arSociosSessao!=null) {
            foreach ($arSociosSessao as $inChave => $arSocios) {
                if ($inChave != $inLinha) {
                    $arSocios["inLinha"] = $inContLinha++;
                    $arNovaListaSocio[] = $arSocios;
                }
            }
        }

        Sessao::write( "socios", $arNovaListaSocio );
        $rsListaSocio = new RecordSet;
        $rsListaSocio->preenche( $arNovaListaSocio );
        $stJs = montaListaSocio( $rsListaSocio );
        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaRespContabil":
        sistemaLegado::executaFrameOculto( buscaRespContabil() );
    break;
    case "limparSpan":
        if ($_REQUEST['inCodigoEnquadramento'] == 2) {
            Sessao::write( "socios", array() );
            $stJs .= "d.getElementById('stNomeNatureza').innerHTML = '&nbsp;';\n";
            $stJs .= "d.getElementById('stNomeSocio').innerHTML = '&nbsp;';\n";
            $stJs .= "d.getElementById('lsListaSocio').innerHTML = '';\n";
            $stJs .= "d.getElementById('flCapitalSocial').innerHTML = '&nbsp;';\n";
            $stJs .= "f.stCategoria.selectedIndex = 0;\n";
        }
        $stJs .= "d.getElementById('stNomCGMResponsavel').innerHTML = '&nbsp;';\n";
        $stJs .= "d.getElementById('stNomCGM').innerHTML = '&nbsp;';";
        sistemaLegado::executaFrameOculto( $stJs );
    break;

    case 'BuscaDados':
    case 'BuscaIndexacao':
        $obFormulario = new Formulario;
        if ($_REQUEST['boTipoDomicilio'] == 'IC') {
            $obBscDomicilio = new BuscaInner;
            $obBscDomicilio->setNull             ( true );
            $obBscDomicilio->setRotulo           ( "*Domicílio Fiscal"  );
            $obBscDomicilio->setId               ( "stEndereco"        );
            $obBscDomicilio->obCampoCod->setName ( "inCodigoDomicilio" );
            $obBscDomicilio->obCampoCod->setValue( $_REQUEST["inCodigoDomicilio"]  );
            $obBscDomicilio->obCampoCod->obEvento->setOnChange("buscaValor('buscaDomicilio');");
            $obBscDomicilio->setFuncaoBusca      ( "abrePopUp('".CAM_GT_CEM_POPUPS."domicilioFiscal/FLProcurarDomicilioFiscal.php','frm','inCodigoDomicilio','stEndereco','todos','".Sessao::getId()."','800','550');");

            //------------------------------------
            if ($_REQUEST['stAcao'] == "alterar" || $_REQUEST['stAcao'] == "converter" || $_REQUEST['stAcao'] == "domicilio") {

                $obRCIMImovel = new RCIMImovel( new RCIMLote );
                if ($_REQUEST['inCodDomicilioFiscal'] != "") {

                    $obRCIMImovel->setNumeroInscricao( $_REQUEST['inCodDomicilioFiscal'] );
                    $obRCIMImovel->addProprietario();
                    $obRCIMImovel->listarImoveisConsulta( $rsImovel );

                    $stEndereco = $rsImovel->getCampo('endereco');
                    if (!$stEndereco) { $stEndereco = '&nbsp;'; }
                    $jsNome = 'f.inCodigoDomicilio.value= "'.$_REQUEST['inCodDomicilioFiscal'].'"; ';
                    $jsNome .= 'd.getElementById("stEndereco").innerHTML = "'.$stEndereco.'";';
                }
            }
            //-----------------------------------

            $obFormulario->addComponente ( $obBscDomicilio );

//---------------------------------------------------------------------------------- FIM IMOVEL CADASTRADO
        } elseif ($_REQUEST['boTipoDomicilio'] == 'EI') {

            $obRCIMTrecho  = new RCIMTrecho;

            $obTxtNumero = new TextBox;
            $obTxtNumero->setName       ('inNumero');
            $obTxtNumero->setRotulo     ('Número');
            $obTxtNumero->setInteiro    ( true );
            $obTxtNumero->setMaxLength  ( 6 );
            $obTxtNumero->setSize       ( 8 );
            $obTxtNumero->setNull       ( false );

            $obTxtComplemento = new TextBox;
            $obTxtComplemento->setName       ('stComplemento');
            $obTxtComplemento->setRotulo     ('Complemento');
            $obTxtComplemento->setNull       ( true );
            $obTxtComplemento->setValue      ( $_REQUEST["stComplemento"] );
            $obTxtComplemento->setMaxLength  ( 160 );
            $obTxtComplemento->setSize       ( 50 );

            $obTxtCaixaPostal = new TextBox;
            $obTxtCaixaPostal->setName       ('stCaixaPostal');
            $obTxtCaixaPostal->setRotulo     ('Caixa Postal');
            $obTxtCaixaPostal->setNull       ( true );
            $obTxtCaixaPostal->setValue      ( $_REQUEST["stCaixaPostal"] );
            $obTxtCaixaPostal->setMaxLength  ( 6 );
            $obTxtCaixaPostal->setSize       ( 8 );

            $obLblMunicipio = new Label;
            $obLblMunicipio->setName       ('stMunicipio');
            $obLblMunicipio->setRotulo     ('Município');
            $obLblMunicipio->setValue      ( $_REQUEST["inCodigoMunicipio"].' - '.$_REQUEST["stMunicipio"] );
            $obLblMunicipio->setId         ('stMunicipio');

            $obLblEstado = new Label;
            $obLblEstado->setName       ('stEstado');
            $obLblEstado->setRotulo     ('Estado');
            $obLblEstado->setValue      ( $_REQUEST["inCodigoUF"].' - '.$_REQUEST["stUF"] );
            $obLblEstado->setId         ('stEstado');

            //-------------------------------------------------------- COMPONENTES BUSCA LOGRADOURO
            $obBscLogradouro = new BuscaInner;
            $obBscLogradouro->setRotulo ( "Logradouro"                               );
            $obBscLogradouro->setTitle  ( "Logradouro onde o trecho está localizado" );
            $obBscLogradouro->setId     ( "campoInnerLogr"                               );
            $obBscLogradouro->setNull   ( false                                      );
            $obBscLogradouro->obCampoCod->setName  ( "inNumLogradouro"               );
            $obBscLogradouro->obCampoCod->setValue ( $_REQUEST['HdninNumLogradouro'] );
            $obBscLogradouro->obCampoCod->obEvento->setOnChange( "buscaValor ('buscaLogradouro');" );
            $obBscLogradouro->obCampoCod->obEvento->setOnBlur   ( "buscaValor ('buscaLogradouro');" );
            $stBusca  = "abrePopUp('".CAM_GT_CIM_POPUPS."logradouro/FLProcurarLogradouro.php','frm','inNumLogradouro','campoInnerLogr',''";
            $stBusca .= " ,'".Sessao::getId()."&stCadastro=imovel','800','550')";
            $obBscLogradouro->setFuncaoBusca ( $stBusca );
            SistemaLegado::executaFramePrincipal("d.getElementById('campoInnerLogr').innerHTML = '&nbsp;';");

            $obHdnNomLogradouro = new Hidden;
            $obHdnNomLogradouro->setName ( "stNomeLogradouro" );
            $obHdnNomLogradouro->setValue( $_REQUEST ["stNomeLogradouro"] );

            $obHdnCodMunicipio = new Hidden;
            $obHdnCodMunicipio->setName ( "inCodMunicipio" );
            $obHdnCodMunicipio->setValue( $_REQUEST ["inCodigoMunicipio"] );

            $obHdnCodUF = new Hidden;
            $obHdnCodUF->setName ( "inCodUF" );
            $obHdnCodUF->setValue( $_REQUEST ["inCodigoUF"] );

            $obHdnCodLogradouro = new Hidden;
            $obHdnCodLogradouro->setName  ( "inCodLogradouro"            );
            $obHdnCodLogradouro->setValue ( $_REQUEST["inCodLogradouro"] );

            //-------------------------------------------------------- COMPONENTES BUSCA LOGRADOURO

            //----------------------------------------------------- BAIRRO

            if ($_REQUEST["HdninNumLogradouro"]) {

                $obRCIMTrecho->setCodigoLogradouro( $_REQUEST["HdninNumLogradouro"] ) ;
                $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro );
                $obRCIMTrecho->setCodigoLogradouro      ( $rsLogradouro->getCampo ('cod_logradouro') );
                $obRCIMTrecho->listarBairroLogradouro   ( $rsBairro );
                $obRCIMTrecho->listarCEP( $rsCep );

            } else {

                $rsBairro = new RecordSet;
                $rsCep = new RecordSet;

            }

            $stJs2 .= "limpaSelect(f.cmbBairro,0); \n\r";
            $stJs2 .= "limpaSelect(f.cmbCEP,0); \n\r";
            $stJs2 .= "f.cmbBairro[0] = new Option('Selecione','', 'selected');\n\r";
            $stJs2 .= "f.cmbCEP[0] = new Option('Selecione','', 'selected');\n\r";

            /* bairro ****************/
            $inContador = 1;
            while ( !$rsBairro->eof() ) {

                $inCodBairroTMP  = $rsBairro->getCampo( "cod_bairro" );
                $stNomeBairroTMP = $rsBairro->getCampo( "nom_bairro" );

                $stJs2 .= "f.cmbBairro.options[$inContador] = new Option('".$stNomeBairroTMP."','".$inCodBairroTMP."'); \n\r";
                $inContador++;
                $rsBairro->proximo();

            }

            /* cep *******************/
            $inContador = 1;
            while ( !$rsCep->eof() ) {
                $stCep = $rsCep->getCampo( "cep" );
                if ( $_REQUEST["stCEP"] == $stCep )
                    $stJs2 .= "f.cmbCEP.options[$inContador] = new Option('".$stCep."','".$stCep."', true); \n";
                else
                    $stJs2 .= "f.cmbCEP.options[$inContador] = new Option('".$stCep."','".$stCep."'); \n";
                $inContador++;
                $rsCep->proximo();
            }

            $obTxtCodBairro = new TextBox;
            $obTxtCodBairro->setRotulo    ( "Bairro"               );
            $obTxtCodBairro->setName      ( "inCodigoBairro"          );
            $obTxtCodBairro->setSize      ( 8                      );
            $obTxtCodBairro->setMaxLength ( 8                      );
            $obTxtCodBairro->setNull      ( false                  );
            $obTxtCodBairro->setInteiro   ( true                   );

            $obCmbBairro = new Select;
            $obCmbBairro->setRotulo       ( "Bairro"               );
            $obCmbBairro->setName         ( "cmbBairro"            );
            $obCmbBairro->addOption       ( "", "Selecione"        );
            $obCmbBairro->setCampoId      ( "cod_bairro"           );
            $obCmbBairro->setCampoDesc    ( "nom_bairro"           );
            $obCmbBairro->preencheCombo   ( $rsBairro              );
            $obCmbBairro->setNull         ( false                  );
            $obCmbBairro->setStyle        ( "width: 220px"         );
            //----------------------------------------------------- BAIRRO

            //----------------------------------------------------- CEP
            $rsCEP = new Recordset;

            $obCmbCep = new Select;
            $obCmbCep->setName         ( "cmbCEP"            );
            $obCmbCep->setRotulo       ( "CEP"               );
            $obCmbCep->addOption       ( "", "Selecione"     );
            $obCmbCep->setCampoId      ( "cod_cep"           );
            $obCmbCep->setCampoDesc    ( "num_cep"           );
            $obCmbCep->preencheCombo   ( $rsCEP              );
            $obCmbCep->setNull         ( False               );
            $obCmbCep->setStyle        ( "width: 220px"      );
            //----------------------------------------------------- CEP

            $obFormulario->addHidden     ( $obHdnNomLogradouro );
            $obFormulario->addHidden     ( $obHdnCodMunicipio );
            $obFormulario->addHidden     ( $obHdnCodUF );
            $obFormulario->addHidden     ( $obHdnCodLogradouro );
            $obFormulario->addComponente ( $obBscLogradouro );
            $obFormulario->addComponente ( $obTxtNumero );
            $obFormulario->addComponente ( $obTxtComplemento );
            $obFormulario->addComponenteComposto ( $obTxtCodBairro, $obCmbBairro );
            $obFormulario->addComponente ( $obCmbCep );
            $obFormulario->addComponente ( $obTxtCaixaPostal );
            $obFormulario->addComponente ( $obLblMunicipio );
            $obFormulario->addComponente ( $obLblEstado );

            $stJs2 .= "f.cmbBairro.value = '". $_REQUEST['HdninCodBairro'] ."';\n";
            $stJs2 .= "f.inCodigoBairro.value = '". $_REQUEST['HdninCodBairro'] ."';";
            $stJs2 .= "f.inNumero.value = '". $_REQUEST['HdninNumero'] ."';";

            if (!$_REQUEST['HdnstNomLogradouro']) {
                $nomeLogradouro = '&nbsp;';
            } else {
                $nomeLogradouro = $_REQUEST['HdnstNomLogradouro'];
            }

            $stJs2 .= "d.getElementById('campoInnerLogr').innerHTML = '". $nomeLogradouro . "';\n";
        }

        $obFormulario->montaInnerHTML();
        $js .= "d.getElementById('spnTipoDomicilio').innerHTML = '". $obFormulario->getHTML(). "';\n";

        SistemaLegado::executaFrameOculto($js);
        SistemaLegado::executaFrameOculto($stJs2);
        SistemaLegado::executaFrameOculto($jsNome);

    break;

    case 'buscaLogradouro':
        $obRCIMTrecho       = new RCIMTrecho;
        $rsLogradouro       = new RecordSet;

        if ( empty( $_REQUEST["inNumLogradouro"] ) ) {
            $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
            $stJs .= 'd.getElementById("stMunicipio").innerHTML = "-";';
            $stJs .= 'd.getElementById("stEstado").innerHTML = "-";';
        } else {

            $obRCIMTrecho->setCodigoLogradouro( $_REQUEST["inNumLogradouro"] ) ;
            $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro );

            if ( $rsLogradouro->eof() ) {
                $stJs .= 'f.inNumLogradouro.value = "";';
                $stJs .= 'f.inNumLogradouro.focus();';
                $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
                $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inNumLogradouro"].")', 'form','erro','".Sessao::getId()."');";

            } else {
                $stNomeLogradouro = $rsLogradouro->getCampo ("tipo_nome");
                $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "'.$stNomeLogradouro.'";';

                $stNomeMunicipio = $rsLogradouro->getCampo ("cod_municipio") . ' - ' .$rsLogradouro->getCampo ("nom_municipio");
                $stNomeEstado = $rsLogradouro->getCampo ("cod_uf") . ' - ' .$rsLogradouro->getCampo ("nom_uf");

                $stJs .= "f.stNomeLogradouro.value = '". $stNomeLogradouro."';";
                $stJs .= 'd.getElementById("stMunicipio").innerHTML = "'.$stNomeMunicipio.'";';
                $stJs .= 'd.getElementById("stEstado").innerHTML = "'.$stNomeEstado.'";';
                $stJs .= 'f.inCodMunicipio.value = "'.$rsLogradouro->getCampo ("cod_municipio").'";';
                $stJs .= 'f.inCodUF.value = "'.$rsLogradouro->getCampo ("cod_uf").'";';

                $obRCIMTrecho->setCodigoLogradouro( $rsLogradouro->getCampo ("cod_logradouro") );
                $obRCIMTrecho->listarBairroLogradouro( $rsBairro );
                $obRCIMTrecho->listarCEP( $rsCep );

                $stJs2 .= "limpaSelect(f.cmbBairro,0); \n\r";
                $stJs2 .= "limpaSelect(f.cmbCEP,0); \n\r";
                $stJs2 .= "f.cmbBairro[0] = new Option('Selecione','', 'selected');\n\r";
                $stJs2 .= "f.cmbCEP[0] = new Option('Selecione','', 'selected');\n\r";
                /* bairro ****************/
                $inContador = 1;
                while ( !$rsBairro->eof() ) {

                    $inCodBairroTMP  = $rsBairro->getCampo( "cod_bairro" );
                    $stNomeBairroTMP = $rsBairro->getCampo( "nom_bairro" );

                    $stJs2 .= "f.cmbBairro.options[$inContador] = new Option('".$stNomeBairroTMP."','".$inCodBairroTMP."'); \n\r";
                    $inContador++;
                    $rsBairro->proximo();

                }
                $stJs .= 'f.inCodigoBairro.value = "";';

                /* cep *******************/
                $inContador = 1;
                while ( !$rsCep->eof() ) {
                    $stCep = $rsCep->getCampo( "cep" );
                    $stJs2 .= "f.cmbCEP.options[$inContador] = new Option('".$stCep."','".$stCep."'); \n";
                    $inContador++;
                    $rsCep->proximo();
                }
                SistemaLegado::executaFrameOculto($stJs2);
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaBairro":

        $obRCIMBairro       = new RCIMBairro;
        $obRCIMConfiguracao = new RCIMConfiguracao;
        $obRCIMConfiguracao->listaDadosMunicipio( $arConfiguracao );

        $inCodUF        = $arConfiguracao["cod_uf"];
        $inCodMunicipio = $arConfiguracao["cod_municipio"];

        $obRCIMBairro->setCodigoBairro    ( $_REQUEST["inCodigoBairroLote"] );
        $obRCIMBairro->setCodigoUF        ( $arConfiguracao["cod_uf"] );
        $obRCIMBairro->setCodigoMunicipio ( $arConfiguracao["cod_municipio"] );
        $obErro = $obRCIMBairro->consultarBairro();

        if ( $obRCIMBairro->getNomeBairro() ) {
            $stJs = 'f.inCodigoUF.value = '.$inCodUF.';';
            $stJs .= 'f.inCodigoMunicipio.value = '.$inCodMunicipio.';';
            $stJs .= 'd.getElementById("innerBairroLote").innerHTML = "'.$obRCIMBairro->getNomeBairro().'";';
            SistemaLegado::LiberaFrames();
        } else {
            $stJs .= 'f.inCodigoUF.value = "";';
            $stJs .= 'f.inCodigoMunicipio.value = "";';
            $stJs .= 'd.getElementById("innerBairroLote").innerHTML = "&nbsp;";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inCodigoBairroLote"].")','form','erro','".Sessao::getId()."');";
            SistemaLegado::LiberaFrames();
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "buscaProcesso":
        $obRProcesso  = new RProcesso;
        if ($_POST['inNumProcesso'] != '') {
            list($inProcesso,$inExercicio) = explode("/",$_POST['inNumProcesso']);
            $obRProcesso->setCodigoProcesso( $inProcesso  );
            $obRProcesso->setExercicio     ( $inExercicio );
            $obErro = $obRProcesso->validarProcesso();

            if ( $obErro->ocorreu() ) {
                $stJs .= 'f.inNumProcesso.value = "";';
                $stJs .= 'f.inNumProcesso.focus();';
                $stJs .= "alertaAviso('@Processo nao encontrado. (".$_POST["inNumProcesso"].")','form','erro','".Sessao::getId()."');";
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "Download":
        $content_type   = 'application/sxw';
        $arDadosArquivos = Sessao::read( "dados" );
        $stDocumento    = $arDadosArquivos[2];
        $download       = $arDadosArquivos[1];

        header ("Content-Length: " . filesize( $stDocumento ));
        header("Content-type: $content_type");
        header("Content-Disposition: attachment; filename=\"$download\"");
        readfile( $stDocumento );
        break;

}
sistemaLegado::LiberaFrames();
?>
