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
    * Frame Ocult de Definição de Calendário Fiscal
    * Data de Criação   : 18/05/2005

    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: OCManterCalendario.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.03
*/

/*
$Log$
Revision 1.12  2006/09/15 11:50:32  fabio
corrigidas tags de caso de uso

Revision 1.11  2006/09/15 11:02:23  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalendarioFiscal.class.php");
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCalendario";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

function montaListaGrupos(&$rsListaGrupo)
{
    if ( !$rsListaGrupo->eof() ) {
        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsListaGrupo          );
        $obLista->setTitulo                    ( "Lista de Grupos de Vencimentos" );
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
        $obLista->ultimoCabecalho->addConteudo ( "Data Vencimento"      );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              ( 15                     );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Descrição"            );
        $obLista->ultimoCabecalho->setWidth    ( 75                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Limite Inicial"       );
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Limite Final"         );
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Cota Única"           );
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );

        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "inCodigo"             );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "dtDataVencimento"     );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "stDescricao"          );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "inLimiteInicial"      );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "inLimiteFinal"        );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "stUtilizarCotaUnica"  );
        $obLista->commitDado                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "ALTERAR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink          ("JavaScript:alterarDado('alterarGrupo');" );
        $obLista->ultimaAcao->addCampo         ( "1","inLinha"   );
        $obLista->commitAcao                   (    );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink          ( "JavaScript:excluirDado('excluirGrupo');" );
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

    $stJs .= "f.stDescricao.value = '';\n";
    $stJs .= "f.dtDataVencimento.value = '';\n";
    $stJs .= "d.getElementById('lsGrupo').innerHTML = '".$stHTML."';\n";
    $stJs .= "f.inLimiteInicial.value = '';\n";
    $stJs .= "f.inLimiteFinal.value = '';\n";

    return $stJs;
}

function BuscarCredito($stParam1, $stParam2)
{
    $obRegra = new RARRGrupo;

    if ($_REQUEST[$stParam1]) {
        $arDados = explode("/", $_REQUEST[$stParam1]);
        $stMascara = "";
        $obRARRGrupo = new RARRGrupo;
        $obRARRGrupo->RecuperaMascaraGrupoCredito( $stMascara );
        $stMascara .= "/9999";

        if ( strlen($_REQUEST[$stParam1]) < strlen($stMascara) ) {
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
        $stJs = 'f.inCodGrupo.value= "";';
        $stJs .= 'd.getElementById("'.$stParam2.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}
switch ($_REQUEST["stCtrl"]) {
    case "BuscaCodCredito":
        $stJs = BuscarCredito( "inCodGrupo", "stGrupo" );
        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "montaGrupo":
        $stMsg = "";
        //VERIFICA SE JA FOI INFORMADO UMA DESCRICAO
        $boErro = false;
        $arGrupos = Sessao::read( 'grupos' );

        if ($_REQUEST['flagEditar'] == 'incluir') {
        foreach ($arGrupos as  $inChave => $arGrupo) {
            if ( strtoupper($arGrupo["stDescricao"]) == strtoupper($_REQUEST["stDescricao"]) ) {
                $boErro = true;
                $stMsg  = "Descrição já informada!";
                break;
            }
        }

        if ($boErro) {
            $stJs = "alertaAviso('".$stMsg."(".$_REQUEST["stDescricao"].")','form','erro','".Sessao::getId()."', '../');";
        } else {
            if ($_REQUEST['boUtilizarCotaUnica'])
                $stCota = "Sim";
            else
                $stCota = "Não";

            $rsGrupo = new RecordSet;
            $stJs .= "f.stDescricao.value = '';\n";

            $limiteInicial = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST['inLimiteInicial'] ) );
            $limiteInicial = number_format ( $limiteInicial, 2, '.', '' );
            $limiteFinal = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST['inLimiteFinal'] ) );
            $limiteFinal = number_format ( $limiteFinal, 2, '.', '' );

            $arGrupos[] = array( "stDescricao"      => $_REQUEST['stDescricao'],
                              "inCodigo"         => count( $arGrupos ) + 1,
                              "dtDataVencimento" => $_REQUEST['dtDataVencimento'],
                              "inLimiteInicial"  => $limiteInicial,
                              "inLimiteFinal"    => $limiteFinal ,
                              "stUtilizarCotaUnica" => $stCota,
                              "inLinha" => count( $arGrupos ) +1
                            );
            }
            } else {//editar campo
                  if ($_REQUEST['boUtilizarCotaUnica'])
                      $stCota = "Sim";
                  else
                $stCota = "Não";

            $rsGrupo = new RecordSet;

  //          $limiteInicial = str_replace ( ',', '.', str_replace ( '.', '',$_REQUEST['inLimiteInicial'] ) );
            $limiteInicial = $_REQUEST['inLimiteInicial'];
//            $limiteInicial = number_format ( $limiteInicial, 2, '.', '' );
    //        $limiteFinal = str_replace ( ',', '.', str_replace ( '.', '',$_REQUEST['inLimiteFinal'] ) );
            $limiteFinal = $_REQUEST['inLimiteFinal'];
//            $limiteFinal = number_format ( $limiteFinal, 2, '.', '' );

            $arGruposAux = array( "stDescricao"      => $_REQUEST['stDescricao'],
                              "inCodigo"         => count( $arGrupos ) + 1,
                              "dtDataVencimento" => $_REQUEST['dtDataVencimento'],
                              "inLimiteInicial"  => $limiteInicial,
                              "inLimiteFinal"    => $limiteFinal ,
                              "stUtilizarCotaUnica" => $stCota,
                              "inLinha" => $_REQUEST['inLinhaAux']
                            );

             $arGrupos[$_POST["inLinhaAux"]-1] = $arGruposAux;
            }
            Sessao::write( 'grupos', $arGrupos );
            $rsGrupo->preenche( $arGrupos );

            $stJs .= "f.flagEditar.value = 'incluir';\n";
            $stJs .= "f.inLinhaAux.value = '';\n";
            $stJs .= montaListaGrupos( $rsGrupo );

        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "excluirGrupo":
        $inLinha = $_REQUEST["inLinha"] ? $_REQUEST["inLinha"] : 0;
        $inLinha = $inLinha -1;
        $arNovaListaGrupo = array();
        $inContLinha = 0;
        $arGrupos = Sessao::read( 'grupos' );
        foreach ($arGrupos as $inChave => $arGrupo) {
            if ($inChave != $inLinha) {
                $arGrupo["inLinha"] = ++$inContLinha;
                $arNovaListaGrupo[] = $arGrupo;
            }
        }

        Sessao::write( 'grupos', $arNovaListaGrupo );
        $rsListaGrupo = new RecordSet;
        $rsListaGrupo->preenche( $arNovaListaGrupo );
        $stJs = montaListaGrupos( $rsListaGrupo );
        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "limparGrupos":
        Sessao::write( 'grupos', array() );
        $stJs = "d.getElementById('lsGrupo').innerHTML = '&nbsp;';\n";
        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "listaGrupo":
        $rsGrupos = new RecordSet;
        $arGrupos = Sessao::read( 'grupos' );
        $rsGrupos->preenche( $arGrupos );
        $stJs .= montaListaGrupos( $rsGrupos );
        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "alterarGrupo":
           $arGrupo = Sessao::read( 'grupos' );
           $arGrupo = $arGrupo[$_REQUEST['inLinha']-1] ;

           if ($arGrupo) {
               $stJs .= "f.inLinhaAux.value          = '".$_REQUEST['inLinha']."' ;\n";
               $stJs .= "f.flagEditar.value = 'editar';\n";
               $stJs .= "f.stDescricao.value      = '".$arGrupo['stDescricao']."';\n";
               $stJs .= "f.dtDataVencimento.value = '".$arGrupo['dtDataVencimento']."';\n";
               $stJs .= "f.inLimiteInicial.value  = '".$arGrupo['inLimiteInicial']."';\n";
               $stJs .= "f.inLimiteFinal.value    = '".$arGrupo['inLimiteFinal']."';\n";

               if ($arGrupo['stUtilizarCotaUnica'] == "Sim") {
                    $stJs .= "d.getElementById('boUtilizarCotaUnicaSim').checked = true ;\n";
               } else {
                    $stJs .= "d.getElementById('boUtilizarCotaUnicaNao').checked = true ;\n";
               }

           } else {
               $stJs = "sistemaLegado::alertaAviso('@Valor inválido.($stMensagem)','form','erro','".Sessao::getId()."');";
           }
           SistemaLegado::executaFrameOculto($stJs);
       break;

}
