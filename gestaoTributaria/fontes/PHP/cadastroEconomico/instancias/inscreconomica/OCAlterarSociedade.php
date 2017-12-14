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
    * Página Oculto de Alteracao Sociedade
    * Data de Criação   : 10/05/2005

    * @author Tonismar Régis Bernardo
    * @ignore

    * $Id: OCAlterarSociedade.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.7  2006/09/15 14:33:07  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AlterarSociedade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function BuscaSocio()
{
    global $_REQUEST;
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
            $stJs .= "sistemaLegado::alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
           $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
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
        $obLista->ultimoDado->setAlinhamento   ( "DIREITA"              );
        $obLista->ultimoDado->setCampo         ( "flQuota"              );
        $obLista->commitDado                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "ALTERAR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink          ( "JavaScript:alterarDado('alterarSocio');" );
        $obLista->ultimaAcao->addCampo         ( "inIndice","inCodigoSocio"   );
        $obLista->commitAcao                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink          ( "JavaScript:excluirDado('excluirSocio');" );
        $obLista->ultimaAcao->addCampo         ( "inIndice","inCodigoSocio"   );
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
        $rsListaSocio->proximo();

    }
    $flCapital = number_format( $flCapital, 2, ',','');
    $stJs .= "d.getElementById('flCapitalSocial').innerHTML = '&nbsp;';\n";
    $stJs .= "d.getElementById('lsListaSocio').innerHTML = '".$stHTML."';\n";
    $stJs .= "d.getElementById('flCapitalSocial').innerHTML = '".$flCapital."';\n";

    return $stJs;
}

switch ($_REQUEST["stCtrl"]) {
    case "buscaSocio":
         sistemaLegado::executaFrameOculto( BuscaSocio() );
    break;
    case "montaSocio":
        $rsSocios = new RecordSet;
        $obRCGM = new RCGM;
        $obRCGM->setNumCGM( $_REQUEST["inCodigoSocio"] );
        $obRCGM->consultar( $rsCGM );

        $stMsg = "";

        //VERIFICA SE O CGM JA FOI INFORMADO
        $boErro = false;
        $boIgual = 'false';
        $arNovoSocios = array();
        $arSociosSessao = Sessao::read( "socios" );
        foreach ($arSociosSessao as  $inChave => $arSocios) {
            if ($arSocios["inCodigoSocio"] == $_REQUEST["inCodigoSocio"]) {
                $boIgual = 'true';
                $arSocios["flQuota"] = $_REQUEST["flQuota"];
                $arNovoSocios[] = $arSocios;
            } else {
                $arNovoSocios[] = $arSocios;
            }
        }

        if ($_REQUEST["flQuota"] == '0,00') {
            $boErro = true;
            $stMsg  = "Campo Quota Nulo!";
        }

        if ($boErro) {
            $stJs = "sistemaLegado::alertaAviso('".$stMsg."(".$_REQUEST["inNumCGM"].")','form','erro','".Sessao::getId()."', '../');";
        } else {
            $stJs  = "f.inCodigoSocio.value = '';\n";
            $stJs .= "f.inCodigoSocio.readOnly = false;\n";
            $stJs .= "d.getElementById('stNomeSocio').innerHTML = '&nbsp;';\n";
            $stJs .= "f.flQuota.value = '';\n";
            $stJs .= "f.btnIncluirSocio.value = 'Incluir';\n";

            if ($boIgual == 'true') {
                Sessao::write( "socios", $arNovoSocios );
                $arSociosSessao = $arNovoSocios;
            } else {
                $arSocio = array( "inCodigoSocio"  => $_REQUEST["inCodigoSocio"],
                                  "stNomeSocio"    => $rsCGM->getCampo('nom_cgm'),
                                  "flQuota"        => $_REQUEST["flQuota"] );
                $arSocio["inLinha"] = count( $arSociosSessao );
                $arSociosSessao[] = $arSocio;
                Sessao::write( "socios", $arSociosSessao );
            }
            $stJs .= "d.getElementById('lsListaSocio').innerHTML = '';\n";
            $rsSocios->preenche( $arSociosSessao );
            $stJs .= montaListaSocio( $rsSocios  );
        }
        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "alterarSocio":
        foreach ( Sessao::read( "socios" ) as  $inChave => $arSocios ) {
            if ($arSocios["inCodigoSocio"] == $_REQUEST["inLinha"]) {
                $arAtual = $arSocios;
                break;
            }
        }

        $stJs .= "f.inCodigoSocio.readOnly = true;\n";
        $stJs .= "f.inCodigoSocio.value = '".$arAtual["inCodigoSocio"]."';\n";
        $stJs .= "f.btnIncluirSocio.value = 'Alterar';\n";
        $stJs .= "d.getElementById('stNomeSocio').innerHTML = '".$arAtual["stNomeSocio"]."';\n";
        $stJs .= "f.flQuota.value = '".$arAtual["flQuota"]."';\n";
        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "excluirSocio":

        $inLinha = $_REQUEST["inLinha"] ? $_REQUEST["inLinha"] : 0;
        $arNovaListaSocio = array();
        $cont = 0; $contNovo = 0;
        $arSociosSessao = Sessao::read( "socios" );
        $nSocios = count ($arSociosSessao);

        while ($cont < $nSocios) {
            if ($inLinha != $arSociosSessao[$cont]['inCodigoSocio']) {
                $arNovaListaSocio[$contNovo]['inCodigoSocio'] = $arSociosSessao[$cont]['inCodigoSocio'];
                $arNovaListaSocio[$contNovo]['stNomeSocio'] = $arSociosSessao[$cont]['stNomeSocio'];
                $arNovaListaSocio[$contNovo]['flQuota'] = $arSociosSessao[$cont]['flQuota'];
                $contNovo++;
            }

            $cont++;
        }

        Sessao::write( "socios", $arNovaListaSocio );

        $rsListaSocio = new RecordSet;
        $rsListaSocio->preenche( $arNovaListaSocio );
        $stJs = montaListaSocio( $rsListaSocio );
        sistemaLegado::executaFrameOculto($stJs);
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

    case "ListaSocio":
        $rsListaSocio = new RecordSet;
        $rsListaSocio->preenche( Sessao::read( 'socios' ) );
        $stJs = montaListaSocio( $rsListaSocio );
        sistemaLegado::executaFrameOculto($stJs);
    break;

}

?>
