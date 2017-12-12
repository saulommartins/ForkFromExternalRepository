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
    * Página de Processamento de Reserva de Saldos
    * Data de Criação   : 04/05/2005

    * @author Analista: Diego Barbosa Victória
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2006-08-08 10:44:18 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso: uc-02.01.08
*/

/*
$Log$
Revision 1.8  2006/08/08 13:44:18  jose.eduardo
Bug #6691#

Revision 1.7  2006/08/08 13:18:46  jose.eduardo
Bug #6691#

Revision 1.6  2006/07/05 20:43:33  cleisson
Adicionada tag Log aos arquivos

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReservaSaldos.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "ManterReservaSaldos";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$pgProx    = "LSReservaSaldos.php";

$obROrcamentoReservaSaldos = new ROrcamentoReservaSaldos;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Trecho de código do filtro
/*
$stFiltro = '';
if ($stAcao != 'incluir') {
   foreach (//sessao->transf4 as $stCampo => $stValor) {
      if (is_array($stValor)) {
          foreach ($stValor as $stCampo2 => $stValor2) {
              $stFiltro .= "&".$stCampo2."=".@urlencode( $stValor2 );
          }
      } else {
          $stFiltro .= "&".$stCampo."=".@urlencode( $stValor );
      }
   }
}
*/

Sessao::write('stDtReserva',$_POST['dtDataReserva']);
//sessao->transf3['stDtReserva'] = $_POST['dtDataReserva'];

switch ($stAcao) {
    case "incluir":
        $obROrcamentoReservaSaldos->setExercicio                        ( Sessao::getExercicio()                );
        $obROrcamentoReservaSaldos->obROrcamentoDespesa->setCodDespesa  ( $_POST['inCodDespesa']            );
        $obROrcamentoReservaSaldos->setDtValidadeInicial                ( $_POST['dtDataReserva']           );
        $obROrcamentoReservaSaldos->setDtValidadeFinal                  ( $_POST['dtDataValidade']          );
        $obROrcamentoReservaSaldos->setDtInclusao                       ( $_POST['dtDataReserva']           );
        $obROrcamentoReservaSaldos->setVlReserva                        ( $_POST['flValor']                 );
        $obROrcamentoReservaSaldos->setMotivo                           ( $_POST['stMotivo']                );
        $obROrcamentoReservaSaldos->setTipo                             ( "M"                               );
        $obROrcamentoReservaSaldos->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $_POST["inCodigoEntidade"]     );
        $obErro = $obROrcamentoReservaSaldos->incluir();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm."?stAcao=incluir&".Sessao::getId().$stLink.$stFiltro,"Realizada a Reserva: ".$obROrcamentoReservaSaldos->getCodReserva(),"incluir","aviso", Sessao::getId(), "../");
            $stCaminho = CAM_GF_ORC_INSTANCIAS."reservaSaldos/OCRelatorioReservaSaldos.php";
            $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&inCodReserva=".$obROrcamentoReservaSaldos->getCodReserva();
            SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
        echo "<script type='text/javascript'>LiberaFrames(true,false);</script>";
    break;
    case "anular":
        $obROrcamentoReservaSaldos->setExercicio                        ( Sessao::getExercicio()                );
        $obROrcamentoReservaSaldos->setCodReserva                       ( $_POST['inCodReserva']            );
        $obROrcamentoReservaSaldos->setDtAnulacao                       ( $_POST['dtDataAnulacao']          );
        $obROrcamentoReservaSaldos->setDtValidadeInicial                ( $_POST['dtDataReserva']           );
        $obROrcamentoReservaSaldos->setDtValidadeFinal                  ( $_POST['dtDataValidade']          );
        $obROrcamentoReservaSaldos->setMotivoAnulacao                   ( $_POST['stMotivoAnulacao']        );
        $obErro = $obROrcamentoReservaSaldos->anular();
        $stFiltro = "&pg=".Sessao::read('pg')."&pos=".Sessao::read('pos');
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgProx."?stAcao=anular&".Sessao::getId().$stLink.$stFiltro,"Realizada a anulacão da reserva: ".$obROrcamentoReservaSaldos->getCodReserva(),"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
}
?>
