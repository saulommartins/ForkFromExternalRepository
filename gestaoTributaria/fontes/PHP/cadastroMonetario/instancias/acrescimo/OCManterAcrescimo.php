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
  * Pagina Oculta para Calculo
  * Data de criacao : 02/06/2005

    * @author Analista: Fabio Bertoldi
    * @author Programador: Diego Bueno Coelho

    * $Id: OCManterAcrescimo.php 63839 2015-10-22 18:08:07Z franver $

    Caso de uso: uc-05.05.11
**/

/*
$Log$
Revision 1.11  2007/08/20 14:15:08  cercato
Bug#9958#

Revision 1.10  2007/04/25 20:32:18  cercato
alterando componente "acrescimo" para permitir criar mais de um objeto.

Revision 1.9  2007/03/01 12:36:47  cercato
Bug #8526#

Revision 1.8  2007/02/28 20:50:48  cercato
Bug #8525#

Revision 1.7  2006/09/15 14:57:21  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include_once ("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONAcrescimo.class.php"   );
include_once ( CAM_GA_ADM_NEGOCIO."RFuncao.class.php"           );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"         );
include_once ( CAM_GT_ARR_NEGOCIO."RARRPermissao.class.php"     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php"         );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"              );

$stCtrl = $_REQUEST['stCtrl'];

$stJs = "";

//Define o nome dos arquivos PHP
$stPrograma      = "ManterAcrescimo";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgFormGrupo     = "FM".$stPrograma.".php";
$pgFormCredito   = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

$obRARRGrupo = new RARRGrupo ;
$obRFuncao   = new RFuncao   ;

function montaListaValores($rsLista, $boLimparDados = false)
{
    if ( $rsLista->getNumLinhas() > 0 ) {
        $arNovo = array();
        $inX = 0;
        while ( !$rsLista->Eof() ) {
            $arData = explode( "/", $rsLista->getCampo( "dtVigencia" ) );
            $dtConvertida = $arData[2].$arData[1].$arData[0];
            $arNovo[ $inX ]["dtVigencia"] = $rsLista->getCampo( "dtVigencia" );
            $arNovo[ $inX ]["flValorAcrescimo"] = $rsLista->getCampo( "flValorAcrescimo" );
            $arNovo[ $inX ]["dtVigenciaAM"] = $dtConvertida;
            $rsLista->proximo();
            $inX++;
        }

        $rsLista->preenche( $arNovo );
        $rsLista->setPrimeiroElemento();
        $rsLista->ordena( "dtVigenciaAM", "DESC");

        $obLista = new Lista;
        $obLista->setRecordSet ( $rsLista );
        $obLista->setTitulo ( "Registro de Valores" );
        $obLista->setMostraPaginacao ( false );

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth    ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Vigência" );
        $obLista->ultimoCabecalho->setWidth ( 30 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Valor" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "dtVigencia" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "flValorAcrescimo" );
        $obLista->commitDado ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao ( true );
        $obLista->ultimaAcao->setLink ( "JavaScript:alterarValor();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1", "dtVigencia" );
        $obLista->ultimaAcao->addCampo ( "inIndice2", "flValorAcrescimo" );
        $obLista->commitAcao ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao ( true );
        $obLista->ultimaAcao->setLink ( "JavaScript:excluirValor();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1", "dtVigencia" );
        $obLista->ultimaAcao->addCampo ( "inIndice2", "flValorAcrescimo" );
        $obLista->commitAcao ();

        $obLista->montaHTML ();
        $stHTML =  $obLista->getHtml ();
        $stHTML = str_replace ( "\n","",$stHTML );
        $stHTML = str_replace ( "  ","",$stHTML );
        $stHTML = str_replace ( "'","\\'",$stHTML );
    } else {
        $stHTML = "&nbsp";
    }

    $js .= "d.getElementById('spnListaValor').innerHTML = '".$stHTML."';\n";
    if ($boLimparDados) {
        $js .= 'f.dtVigenciaValor.value = "";';
        $js .= 'f.flValorAcrescimo.value = "";';
    }

    sistemaLegado::executaFrameOculto($js);
}

/*
        FIM DAS FUNÇÕES
*/

switch ($_REQUEST ["stCtrl"]) {

    case "buscaAcrescimo":
        $stDescricao = '&nbsp;';

        if ($_REQUEST[$_GET["stNomCampoCod"]]) {
            $obRMONAcrescimo = new RMONAcrescimo;

            $arDados = explode( ".", $_REQUEST[$_GET["stNomCampoCod"]] );
            if ( count( $arDados ) != 2 ) {
                $stJs = "alertaAviso('@Código do Acréscimo inválido (".$_REQUEST[$_GET["stNomCampoCod"]].").','form','erro','".Sessao::getId()."');";
                $stJs .= 'f.'.$_GET["stNomCampoCod"].'.value = "";';
            } else {
                $obRMONAcrescimo->setCodTipo( $arDados[1] );
                $obRMONAcrescimo->setCodAcrescimo ( $arDados[0] );
                $obRMONAcrescimo->listarAcrescimos ( $rsListaAcrescimo );
                if ( $rsListaAcrescimo->Eof() ) {
                    $stJs = "alertaAviso('@Código do Acréscimo inválido (".$_REQUEST[$_GET["stNomCampoCod"]].").','form','erro','".Sessao::getId()."');";
                    $stJs .= 'f.'.$_GET["stNomCampoCod"].'.value = "";';
                    $stJs .= "d.getElementById('".$_REQUEST['stIdCampoDesc']."').innerHTML = '&nbsp;';\n";
                } else {
                    $stDescricao = $rsListaAcrescimo->getCampo("descricao_acrescimo");
                    $stJs = "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_REQUEST['stIdCampoDesc']."', 'frm', '".$stDescricao."');";
                }
            }
        } else {
            $stJs  = 'f.'.$_GET["stNomCampoCod"].'.value = "";';
            $stJs .= "d.getElementById('".$_REQUEST['stIdCampoDesc']."').innerHTML = '&nbsp;';\n";
            if ($_REQUEST['inCodAcrescimo'] == '0') {
                $stJs .= "alertaAviso('@Código do Acréscimo inválido (".$_REQUEST['inCodAcrescimo'].").','form','erro','".Sessao::getId()."');";
            }
        }

        if ($stJs) echo $stJs;
        exit;

    break;
    case "carregaValores":

        $obRMONAcrescimo = new RMONAcrescimo;
        $obRMONAcrescimo->setCodTipo( $_REQUEST["inCodTipo"] );
        $obRMONAcrescimo->setCodAcrescimo( $_REQUEST["inCodAcrescimo"] );
        $obRMONAcrescimo->ListarValorAcrescimo( $rsLista );

        if ( !$rsLista->Eof() ) {
            $arTmpDados = array();
            $inX = 0;
            while ( !$rsLista->Eof() ) {
                $arTmpDados[$inX]["flValorAcrescimo"] = $rsLista->getCampo("valor");
                $arTmpDados[$inX]["dtVigencia"] = $rsLista->getCampo("inicio_vigencia");
                $inX++;
                $rsLista->proximo();
            }

            Sessao::write( "valores", $arTmpDados );

            $rsListaValores = new RecordSet;
            $rsListaValores->preenche ( $arTmpDados );

            montaListaValores ( $rsListaValores );
        }
        break;

    case "alterarValor":
        $flNovoValor    = $_REQUEST['inIndice2'];
        $dtVigencia     = $_REQUEST['inIndice1'];
        $arValoresSessao = Sessao::read( "valores" );
        $nregistros = count ( $arValoresSessao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ( ( $arValoresSessao[$inCount]["flValorAcrescimo"] == $flNovoValor ) && ( $arValoresSessao[$inCount]["dtVigencia"] == $dtVigencia ) ) {
                Sessao::write( "alterar", $inCount );
                break;
            }
        }
        break;

    case "excluirValor":
        $dtNovoVigencia = $_REQUEST['inIndice1'];
        $flNovoValorAcrescimo = $_REQUEST['inIndice2'];
        $arTmpValores = array ();
        $inCountArray = 0;
        $arValoresSessao = Sessao::read( "valores" );
        $nregistros = count ( $arValoresSessao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ($arValoresSessao[$inCount]["dtVigencia"] != $dtNovoVigencia || $arValoresSessao[$inCount]["flValorAcrescimo"] != $flNovoValorAcrescimo) {
                $arTmpValores[$inCountArray] = $arValoresSessao[$inCount];
                $inCountArray++;
            }
        }

        Sessao::write( "valores", $arTmpValores );
        $rsLista = new RecordSet;
        $rsLista->preenche ( $arTmpValores );

        montaListaValores ( $rsLista );
        break;

    case "limparValor":
        $js .= 'f.flValorAcrescimo.value = "";';
        $js .= 'f.flValorAcrescimo.focus();';
        $js .= 'f.dtVigenciaValor.value = "";';
        sistemaLegado::executaFrameOculto( $js );
        break;

    case "definirValor":
        $flNovoValorAcrescimo = $_REQUEST['flValorAcrescimo'];
        $dtNovoVigencia = $_REQUEST['dtVigenciaValor'];
        $arValoresSessao = Sessao::read( "valores" );
        $nregistros = count ( $arValoresSessao );
        $insere = true;

        $flNovoValorFormatado = number_format( str_replace(',', '.', str_replace('.', '', $flNovoValorAcrescimo)), 2, '.', '' );
        if ($_REQUEST['flValorAcrescimo'] == "") {
           $js .= 'f.flValorAcrescimo.value = "";';
           $js .= 'f.flValorAcrescimo.focus();';
           $js .= "alertaAviso('@O valor está vazio.','form','erro','".Sessao::getId()."');";
           sistemaLegado::executaFrameOculto( $js );
        } else {
            for ($inX=0; $inX<$nregistros; $inX++) {
                if ( $arValoresSessao[$inX]['dtVigencia'] == $dtNovoVigencia && (Sessao::read("alterar") != $inX) ) {
                    //codigo ja estava na lista!
                    $js .= 'f.dtVigenciaValor.value = "";';
                    $js .= 'f.dtVigenciaValor.focus();';
                    $js .= "alertaAviso('@Data de vigência já está na lista. (".$dtNovoVigencia.")','form','erro','".Sessao::getId()."');";

                    sistemaLegado::executaFrameOculto( $js );
                    $insere = false;
                    break;
                }
            }

            if ($insere) {
                if (Sessao::read("alterar") >= 0) {
                    $arValoresSessao[Sessao::read("alterar")]['flValorAcrescimo'] = $flNovoValorAcrescimo;
                    $arValoresSessao[Sessao::read("alterar")]['dtVigencia'] = $dtNovoVigencia;
                    Sessao::write( "alterar", -1 );
                } else {
                    $arValoresSessao[$nregistros]['flValorAcrescimo'] = $flNovoValorAcrescimo;
                    $arValoresSessao[$nregistros]['dtVigencia'] = $dtNovoVigencia;
                }

                Sessao::write( "valores", $arValoresSessao );
                $rsLista = new RecordSet;
                $rsLista->preenche ( $arValoresSessao );

                montaListaValores ( $rsLista, true );
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaFuncao":
        $arCodFuncao = explode('.',$_REQUEST["inCodFuncao"]);
        $obRFuncao->setCodFuncao                           ( $arCodFuncao[2] );
        $obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
        $obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );
        $obRFuncao->consultar();

        $inCodFuncao = $obRFuncao->getCodFuncao () ;
        $stDescricao = "&nbsp;";
        $stDescricao = $obRFuncao->getComentario() ;
        $stNomeFuncao = $obRFuncao->getNomeFuncao();
        if ( !empty($inCodFuncao) ) {
            $stJs .= "d.getElementById('stFormula').innerHTML = '".$stNomeFuncao."';\n";
        } else {
            $stJs .= "f.inCodFuncao.value ='';\n";
            $stJs .= "f.inCodFuncao.focus();\n";
            $stJs .= "d.getElementById('stFormula').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Função informada não existe. (".$_REQUEST["inCodFuncao"].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "buscaNorma":
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/negocio/RNorma.class.php';

        $obRMONNorma = new RNorma;
        $obRMONNorma->setCodNorma ( trim ($_REQUEST["inCodNorma"]) );
        $obRMONNorma->Consultar( $rsDados );

        $inCodigo = $obRMONNorma->getCodNorma();
        $stNomeTipoNorma = $obRMONNorma->obRTipoNorma->getNomeTipoNorma();
        $stNumNorma = $obRMONNorma->getNumNorma();
        $stExercicio = $obRMONNorma->getExercicio();
        $stNomeNorma = $obRMONNorma->getNomeNorma();

        $frase= $stNomeTipoNorma.' '.$stNumNorma.'/'.$stExercicio.' - '.$stNomeNorma;

        if ( !$rsDados->Eof() ) {
            $stJs .= "d.getElementById('stNorma').innerHTML = '".$frase."';\n";
        } else {
            $stJs .= "f.inCodNorma.value ='';\n";
            $stJs .= "f.inCodNorma.focus();\n";
            $stJs .= "d.getElementById('stNorma').innerHTML = '&nbsp;';\n";
            if ( $_REQUEST["inCodNorma"] )
                $stJs .= "alertaAviso('@Norma informada não existe. (".$_REQUEST["inCodNorma"].")','form','erro','".Sessao::getId()."');";
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

}

?>
