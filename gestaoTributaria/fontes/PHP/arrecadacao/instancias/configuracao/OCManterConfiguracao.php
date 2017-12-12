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
    * Página de Frame Oculto para Configuração da Arrecadacao
    * Data de Criação   : 26/12/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCManterConfiguracao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.01
*/

/*
$Log$
Revision 1.6  2006/10/23 17:41:36  fabio
adicionado grupo de credito para escrituracao de receita

Revision 1.5  2006/09/15 11:02:28  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONConvenio.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );

function BuscarCredito($stParam1, $stParam2)
{
    $obRegra = new RARRGrupo;
    if ($_REQUEST[$stParam1]) {
        $arDados = explode("/", $_REQUEST[$stParam1]);
        $stMascara = "";
        $obRARRGrupo = new RARRGrupo;
        $obRARRGrupo->RecuperaMascaraGrupoCredito( $stMascara );
        $stMascara .= "/9999";

        $inCodGrupoMascarado = str_pad($arDados[0], $stMascara, '0', STR_PAD_LEFT);
        $valorMascarado = $inCodGrupoMascarado."/".$arDados[1];

        if ( strlen($valorMascarado) < strlen($stMascara) ) {
            $stJs = 'f.'.$stParam1.'.value= "";';
            $stJs .= 'f.'.$stParam1.'.focus();';
            $stJs .= 'd.getElementById("'.$stParam2.'").innerHTML = "&nbsp;";';
            $stJs .= "alertaAviso('@Código Grupo/Ano exercício incompleto. (".$_REQUEST[$stParam1].")', 'form','erro','".Sessao::getId()."');";
        } else {
            $obRARRGrupo->setCodGrupo( $arDados[0] );
            $obRARRGrupo->setExercicio( $arDados[1] );

            $obRARRGrupo->listarGrupos( $rsListaGrupo );

            if ( $rsListaGrupo->Eof() ) {
                $stJs = 'f.'.$stParam1.'.value= "";';
                $stJs .= 'f.'.$stParam1.'.focus();';
                $stJs .= 'd.getElementById("'.$stParam2.'").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Código Grupo/Ano exercício inválido. (".$_REQUEST[$stParam1].")', 'form','erro','".Sessao::getId()."');";
            } else {
                $stJs = 'd.getElementById("'.$stParam2.'").innerHTML = "'.$rsListaGrupo->getCampo("descricao").'";';
            }
        }
    } else {
        //$stJs = 'f.inCodGrupo.value= "";';
        $stJs .= 'd.getElementById("'.$stParam2.'").innerHTML = "&nbsp";';

    }

    return $stJs;
}

$stCtrl = $_REQUEST["stCtrl"];
switch ($stCtrl) {
    case "buscaCreditoAutAcrEco":
        $stJs = BuscarCredito( "inNumLancAutAcrEcoCredito", "stNomLancAutAcrEcoCredito" );
        SistemaLegado::executaFrameOculto( $stJs );
        break;

    case "buscaCreditoAutAcrImob":
        $stJs = BuscarCredito( "inNumLancAutAcrImobCredito", "stNomLancAutAcrImobCredito" );
        SistemaLegado::executaFrameOculto( $stJs );
        break;

    case "buscaCreditoAcrGeral":
        $stJs = BuscarCredito( "inNumLancAutAcrGeralCredito", "stNomLancAutAcrGeralCredito" );
        SistemaLegado::executaFrameOculto( $stJs );
        break;

    case "buscaCreditoAutEco":
        $stJs = BuscarCredito( "inNumLancAutEcoCredito", "stNomLancAutEcoCredito" );
        SistemaLegado::executaFrameOculto( $stJs );
        break;

    case "buscaCreditoAutImo":
        $stJs = BuscarCredito( "inNumLancAutImoCredito", "stNomLancAutImoCredito" );
        SistemaLegado::executaFrameOculto( $stJs );
        break;

    case "buscaCreditoAutGer":
        $stJs = BuscarCredito( "inNumLancAutGerCredito", "stNomLancAutGerCredito" );
        SistemaLegado::executaFrameOculto( $stJs );
        break;

    case "buscaCredito":
        $stJs = BuscarCredito( "inNumCredito", "stNomCredito" );
        SistemaLegado::executaFrameOculto( $stJs );
        break;

    case "buscaEscrituracaoReceita":
        $stJs = BuscarCredito( "inNumGrupoEscrituracao", "stNomGrupoEscrituracao" );
        SistemaLegado::executaFrameOculto( $stJs );
        break;

    case "buscaNotaAvulsa":
        $stJs = BuscarCredito( "inNumGrupoNotaAvulsa", "stNomGrupoNotaAvulsa" );
        SistemaLegado::executaFrameOculto( $stJs );
        break;

    case "buscaConvenio":
        $stJs = "";
        if ($_REQUEST['inNumConvenio']) {
            $obRMONConvenio = new RMONConvenio;
            $obRMONConvenio->setNumeroConvenio( $_REQUEST['inNumConvenio'] );
            $obRMONConvenio->listarConvenioBanco( $rsConvenios );
            if ( $rsConvenios->eof() ) {
                $stJs .= 'f.inNumConvenio.value = "";';
                $stJs .= 'f.inNumConvenio.focus();';
                $stJs .= "alertaAviso('@Número do convênio inválido. (".$_REQUEST['inNumConvenio'].")','form','erro','".Sessao::getId()."');";

                SistemaLegado::executaFrameOculto( $stJs );
            }
        }
        break;

    case "buscaTodos":

          $stJs .= BuscarCredito( "inNumCredito"               , "stNomCredito"                );

          $stJs .= BuscarCredito( "inNumCreditoIPTU"           , "stNomCreditoIPTU"            );

          if ($_REQUEST['inNumLancAutAcrEcoCredito'] != '') {
            $stJs .= BuscarCredito( "inNumLancAutAcrEcoCredito"  , "stNomLancAutAcrEcoCredito"   );
          }

          if ($_REQUEST['inNumLancAutAcrImobCredito'] != '') {
            $stJs .= BuscarCredito( "inNumLancAutAcrImobCredito" , "stNomLancAutAcrImobCredito"  );
          }

          if ($_REQUEST['inNumLancAutAcrGeralCredito'] != '') {
            $stJs .= BuscarCredito( "inNumLancAutAcrGeralCredito", "stNomLancAutAcrGeralCredito" );
          }

          if ($_REQUEST['inNumLancAutEcoCredito'] != '') {
             $stJs .= BuscarCredito( "inNumLancAutEcoCredito"     , "stNomLancAutEcoCredito"      );
          }

          if ($_REQUEST['inNumLancAutImoCredito'] != '') {
             $stJs .= BuscarCredito( "inNumLancAutImoCredito"     , "stNomLancAutImoCredito"      );
          }

          if ($_REQUEST['inNumLancAutGerCredito'] != '') {
             $stJs .= BuscarCredito( "inNumLancAutGerCredito"     , "stNomLancAutGerCredito"      );
          }

          if ($_REQUEST['inNumGrupoEscrituracao'] != '') {
            $stJs .= BuscarCredito( "inNumGrupoEscrituracao"     ,"stNomGrupoEscrituracao"       );
          }
          if ($_REQUEST['inNumGrupoNotaAvulsa'] != '') {
            $stJs .= BuscarCredito( "inNumGrupoNotaAvulsa"     ,"stNomGrupoNotaAvulsa"       );
          }
          // $stJs .= BuscarCredito( "inNumCredito"               , "stNomCredito"                );
       // $stJs  = BuscarCredito( "inNumLancAutAcrEcoCredito"  , "stNomLancAutAcrEcoCredito"   );
       // $stJs .= BuscarCredito( "inNumLancAutAcrImobCredito" , "stNomLancAutAcrImobCredito"  );
       // $stJs .= BuscarCredito( "inNumLancAutAcrGeralCredito", "stNomLancAutAcrGeralCredito" );
       // $stJs .= BuscarCredito( "inNumLancAutEcoCredito"     , "stNomLancAutEcoCredito"      );
       // $stJs .= BuscarCredito( "inNumLancAutImoCredito"     , "stNomLancAutImoCredito"      );
       // $stJs .= BuscarCredito( "inNumLancAutGerCredito"     , "stNomLancAutGerCredito"      );
       // $stJs .= BuscarCredito( "inNumGrupoEscrituracao"     ,"stNomGrupoEscrituracao"       );

        SistemaLegado::executaFrameOculto( $stJs );
        break;
}

?>
