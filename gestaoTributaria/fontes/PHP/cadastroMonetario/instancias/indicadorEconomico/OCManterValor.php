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
  * Pagina Oculta para VALOR DO Indicador Economico
  * Data de criacao : 02/06/2005

    * @author Analista: Fabio Bertoldi
    * @author Programador: Diego Bueno Coelho

    * $Id: OCManterValor.php 63839 2015-10-22 18:08:07Z franver $

    Caso de uso: uc-05.05.08
**/

/*
$Log$
Revision 1.2  2006/09/15 14:57:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RFuncao.class.php"           );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"         );
include_once ( CAM_GT_ARR_NEGOCIO."RARRPermissao.class.php"     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php"         );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"              );
include_once ( CAM_GT_MON_NEGOCIO."RMONIndicadorEconomico.class.php" );

$stJs = "";

//Define o nome dos arquivos PHP
$stPrograma      = "ManterValorr";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgFormGrupo     = "FM".$stPrograma.".php";
$pgFormCredito   = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

$obRARRGrupo = new RARRGrupo ;
$obRFuncao   = new RFuncao   ;

function montaListaValores($rsLista, $boLimpar = false)
{
    if ( $rsLista->getNumLinhas() > 0 ) {
        $obLista = new Lista;

        $obLista->setRecordSet ( $rsLista );
        $obLista->setTitulo ( "Registros de Valores" );
        $obLista->setMostraPaginacao ( false );

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth    ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Vigência" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Valor" );
        $obLista->ultimoCabecalho->setWidth ( 45 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "dtVigencia" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "inValor" );
        $obLista->commitDado ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao   ( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao ( true );
        $obLista->ultimaAcao->setLink   ( "JavaScript:editarValor();" );
        $obLista->ultimaAcao->addCampo  ( "inIndice1", "dtVigencia" );
        $obLista->ultimaAcao->addCampo  ( "inIndice2", "inValor" );
        $obLista->commitAcao ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao   ( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao ( true );
        $obLista->ultimaAcao->setLink   ( "JavaScript:excluirValor();" );
        $obLista->ultimaAcao->addCampo  ( "inIndice1", "dtVigencia" );
        $obLista->ultimaAcao->addCampo  ( "inIndice2", "inValor" );
        $obLista->commitAcao ();

        $obLista->montaHTML ();
        $stHTML =  $obLista->getHtml ();
        $stHTML = str_replace ( "\n","",$stHTML );
        $stHTML = str_replace ( "  ","",$stHTML );
        $stHTML = str_replace ( "'","\\'",$stHTML );
    } else {
        $stHTML = "&nbsp";
    }

    if ($boLimpar) {
        $js = 'f.inValor.value = "";';
        $js .= 'f.dtVigencia.value = "";';
    }

    $js .= "d.getElementById('spnListaValores').innerHTML = '".$stHTML."';\n";

    sistemaLegado::executaFrameOculto($js);
}
/*
        FIM DAS FUN?ES
*/

switch ($_REQUEST ["stCtrl"]) {
    case "LimparSessao":
        Sessao::write( "valores", array() );
        Sessao::write( "editar", -1 );
        break;

    case "limparValores":
        $js .= 'f.inValor.value = "";';
        $js .= 'f.inValor.focus();';
        $js .= 'f.dtVigencia.value = "";';
        sistemaLegado::executaFrameOculto($js);
        break;

    case "carregaValores":
        $obRMONIndicador = new RMONIndicadorEconomico;
        $obRMONIndicador->setCodIndicador( $_REQUEST["inCodIndicador"] );
        $obRMONIndicador->ListarValoresExclusao( $rsLista );
        if ( !$rsLista->Eof() ) {
            $arTmpDados = array();
            $inX = 0;
            while ( !$rsLista->Eof() ) {
                $arTmpDados[$inX]["inValor"] = $rsLista->getCampo("valor");
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

    case "DefinirValores":
        $inNovoValor = $_REQUEST["inValor"];
        $dtVigencia = $_REQUEST["dtVigencia"];

        $arValoresSessao = Sessao::read( "valores" );
        $nregistros = count ( $arValoresSessao );
        $cont = 0;
        $insere = true;
        $inNovoValorFormatado = number_format( str_replace(',', '.', str_replace('.', '', $inNovoValor)), 2, '.', '' );
        if ($inNovoValorFormatado <= 0) {
           $js .= 'f.inValor.value = "";';
           $js .= 'f.inValor.focus();';
           $js .= "alertaAviso('@O valor deve ser maior que zero.','form','erro','".Sessao::getId()."');";
           sistemaLegado::executaFrameOculto( $js );
        } else {
            while ($cont < $nregistros) {
                if ( ($arValoresSessao[$cont]['dtVigencia'] == $dtVigencia) && (Sessao::read( "editar" ) != $cont) ) {
                    //codigo ja estava na lista!
                    $js .= 'f.inValor.value = "";';
                    $js .= 'f.inValor.focus();';
                    $js .= 'f.dtVigencia.value = "";';
                    $js .= "alertaAviso('@Vigência ".$dtVigencia." já está na lista.','form','erro','".Sessao::getId()."');";

                    sistemaLegado::executaFrameOculto( $js );
                    $insere = false;
                    break;
                } else {
                    $cont++;
                }
            }

            if ($insere) {
                if ( Sessao::read( "editar" ) >= 0) {
                    $arValoresSessao[Sessao::read( "editar" )]['inValor'] = $inNovoValor;
                    $arValoresSessao[Sessao::read( "editar" )]['dtVigencia'] = $dtVigencia;
                    Sessao::write( "editar", -1 );
                } else {
                    $arValoresSessao[$nregistros]['inValor'] = $inNovoValor;
                    $arValoresSessao[$nregistros]['dtVigencia'] = $dtVigencia;
                }

                Sessao::write( "valores", $arValoresSessao );
                $rsListaValores = new RecordSet;
                $rsListaValores->preenche ( $arValoresSessao );

                montaListaValores ( $rsListaValores, true );
            }
        }
        break;

    case "editarValor":
        $inNovoValor    = $_REQUEST['inIndice2'];
        $dtVigencia     = $_REQUEST['inIndice1'];
        $arValoresSessao = Sessao::read( "valores" );
        $nregistros = count ( $arValoresSessao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ( ( $arValoresSessao[$inCount]["inValor"] == $inNovoValor ) && ( $arValoresSessao[$inCount]["dtVigencia"] == $dtVigencia ) ) {
                Sessao::write( "editar", $inCount );
                break;
            }
        }
        break;

    case "excluirValor":
        $inNovoValor    = $_REQUEST['inIndice2'];
        $dtVigencia     = $_REQUEST['inIndice1'];
        $arTmpValor = array ();
        $inCountArray = 0;
        $arValoresSessao = Sessao::read( "valores" );
        $nregistros = count ( $arValoresSessao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ( ($arValoresSessao[$inCount]["inValor"] != $inNovoValor) || ($dtVigencia != $arValoresSessao[$inCount]["dtVigencia"]) ) {
                $arTmpValor[$inCountArray] = $arValoresSessao[$inCount];
                $inCountArray++;
            }
        }

        Sessao::write( "valores", $arTmpValor );

        $rsLista = new RecordSet;
        $rsLista->preenche ( $arTmpValor );

        montaListaValores ( $rsLista );
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
            $stJs .= "d.getElementById('stFormula').innerHTML = '".$inCodFuncao." - ".$stNomeFuncao."';\n";
        } else {
            $stJs .= "f.inCodFuncao.value ='';\n";
            $stJs .= "f.inCodFuncao.focus();\n";
            $stJs .= "d.getElementById('stFormula').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Função informada não existe. (".$_REQUEST["inCodFuncao"].")','form','erro','".Sessao::getId()."');";
        }
    break;

}

SistemaLegado::executaFrameOculto($stJs);
?>
