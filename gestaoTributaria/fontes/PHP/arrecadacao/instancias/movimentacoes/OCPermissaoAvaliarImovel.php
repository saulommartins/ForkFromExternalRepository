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
  * Página de Formulario Oculto
  * Data de criação : 20/04/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

  * @ignore

    * $Id: OCPermissaoAvaliarImovel.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.06
**/

/*
$Log$
Revision 1.3  2007/02/22 18:06:24  rodrigo
Bug #8425#

Revision 1.2  2006/09/15 11:14:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RUsuario.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "PermissaoAvaliarImovel";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function montaListaUsuarios($rsListaUsuarios)
{
    if ( $rsListaUsuarios->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsListaUsuarios       );
        $obLista->setTitulo                    ( "Lista de Usuários"    );
        $obLista->setMostraPaginacao           ( false                  );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "CGM"                  );
        $obLista->ultimoCabecalho->setWidth    ( 20                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Nome"                 );
        $obLista->ultimoCabecalho->setWidth    ( 60                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "inNumCGM"             );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "stNomCGM"             );
        $obLista->commitDado                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink          ( "JavaScript:excluirUsuario();" );
        $obLista->ultimaAcao->addCampo         ( "inIndice", "inNumCGM" );
        $obLista->commitAcao                   (                        );

        $obLista->montaHTML                    (                        );
        $stHTML =  $obLista->getHtml           (                        );
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );
    } else {
        $stHTML = "&nbsp";
    }

    $js .= "d.getElementById('spnListaUsuarios').innerHTML = '".$stHTML."';\n";
    $js .= "d.getElementById('stNomCGM').innerHTML = '&nbsp;';\n";
    $js .= "f.inNumCGM.value = '';\n";
    $js .= "f.inNumCGM.focus();\n";

    sistemaLegado::executaFrameOculto( $js );
}

switch ($_REQUEST["stCtrl"]) {
    case "buscaUsuario":
        if ($_REQUEST["inNumCGM"]) {
            $obRUsuario = new RUsuario;
            $obRUsuario->obRCGM->setNumCGM ( $_REQUEST["inNumCGM"] );
            $obRUsuario->consultar( $rsUsuario );

            if ( $rsUsuario->eof() ) {
                $stJs .= 'f.inNumCGM.value = "";';
                $stJs .= 'f.inNumCGM.focus();';
                $stJs .= 'd.getElementById("stNomCGM").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@CGM inválido. (".$_REQUEST[ "inNumCGM" ].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("stNomCGM").innerHTML = "'.$obRUsuario->getUsername().'";';
            }
        } else {
            $stJs .= 'd.getElementById("stNomCGM").innerHTML = "&nbsp;";';
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "incluirUsuario":
      if (trim($_REQUEST["inNumCGM"]!="")) {
        $obRUsuario = new RUsuario;
        $obRUsuario->obRCGM->setNumCGM ( $_REQUEST["inNumCGM"] );
        $obRUsuario->consultar( $rsUsuario );
        if ( !$rsUsuario->eof() ) {
          $arUsuarios = Sessao::read( 'usuarios' );
          $inNregistros = count ( $arUsuarios );
          $inCont = 0;
          $boInsereUsuario = true;
          while ($inCont < $inNregistros) {
            if ($arUsuarios[$inCont]['inNumCGM'] == $_REQUEST[ "inNumCGM" ]) {
              //Usuario jah existente
             $js="alertaAviso('@Usuário já está na lista!(CGM:".$_REQUEST["inNumCGM"].")','form','erro','".Sessao::getId()."');";

             SistemaLegado::executaFrameOculto($js);

             $boInsereUsuario = false;
             break;
            }

            $inCont++;
          }

          if ($boInsereUsuario) {
              $arUsuarios[$inNregistros]['inNumCGM'] = $_REQUEST[ "inNumCGM" ];
              $arUsuarios[$inNregistros]['stNomCGM'] = $obRUsuario->getUsername();

              Sessao::write( 'usuarios', $arUsuarios );
          }
        } else {
            $stJs = 'f.inNumCGM.value = "";';
            $stJs.= 'f.inNumCGM.focus();';
            $stJs.= 'd.getElementById("stNomCGM").innerHTML = "&nbsp;";';
            $stJs.= "alertaAviso('@CGM inválido. (".$_REQUEST[ "inNumCGM" ].")','form','erro','".Sessao::getId()."');";

            SistemaLegado::executaFrameOculto($stJs);
        }

        $rsListaUsuarios = new RecordSet;
        $rsListaUsuarios->preenche ( $arUsuarios );
        $rsListaUsuarios->ordena("inNumCGM");
        montaListaUsuarios ( $rsListaUsuarios );
     } else {
        $js="alertaAviso('@Campo Usuário inválido!','form','aviso','".Sessao::getId()."');";
        SistemaLegado::executaFrameOculto($js);
        $boInsereUsuario = false;
     }
    break;

    case "excluirUsuario":
        $arTmpUsuarios = array ();
        $arUsuarios = Sessao::read( 'usuarios' );
        $inCountSessao = count ( $arUsuarios );
        $inCountArray = 0;
        for ($inCount = 0; $inCount < $inCountSessao; $inCount++) {
            if ($arUsuarios[$inCount][ "inNumCGM" ] != $_REQUEST[ "inExcluirCGM" ]) {
                $arTmpUsuarios[$inCountArray]["inNumCGM"] = $arUsuarios[$inCount][ "inNumCGM" ];
                $arTmpUsuarios[$inCountArray]["stNomCGM"] = $arUsuarios[$inCount][ "stNomCGM" ];
                $inCountArray++;
            }
        }

        Sessao::write( 'usuarios', $arTmpUsuarios );

        $rsListaUsuarios = new RecordSet;
        $rsListaUsuarios->preenche ( $arTmpUsuarios );
        $rsListaUsuarios->ordena("inNumCGM");

        montaListaUsuarios ( $rsListaUsuarios );
        break;

    case "limparUsuario":
        $stJs = 'f.inNumCGM.value = "";';
        $stJs .= 'f.inNumCGM.focus();';
        $stJs .= 'd.getElementById("stNomCGM").innerHTML = "&nbsp;";';

        SistemaLegado::executaFrameOculto($stJs);
        break;
}
