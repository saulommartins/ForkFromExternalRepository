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
    * Página de Processamento de Ajustar Historico
    * Data de Criação   : 21/02/2006

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: PRAjustarHistEncerramento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.31
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AjustarHistEncerramento";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obRContabilidadeLancamento = new RContabilidadeLancamento;

switch ($stAcao) {

    case "alterar":

        $obErro = new Erro;

         $stFiltro = "";
         $filtro = Sessao::read('filtro');
         foreach ($filtro as $stCampo => $stValor) {
             if (is_array($stValor)) {
                 foreach ($stValor as $stCampo2 => $stValor2) {
                      $stFiltro .= $stCampo2."=".urlencode( $stValor2 )."&";
                 }
             } else {
                 $stFiltro .= $stCampo."=".urlencode( $stValor )."&";
             }
         }

        $stFiltro .= "pg=".Sessao::read('pg')."&";
        $stFiltro .= "pos=".Sessao::read('pos')."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

        foreach ($_REQUEST as $key => $valor) {
            if (substr($key,0,12) == "boLancamento") {
                if ($valor=="on") {
                    list($boLote,$inCodLote,$stTipo,$inCodEntidade,$inSequencia,$stExercicio) = explode("|",$key);
                    $stExercicio = substr($stExercicio,0,4);

                    $obRContabilidadeLancamento->setSequencia ( $inSequencia );
                    $obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( $stExercicio );
                    $obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $inCodLote );
                    $obRContabilidadeLancamento->obRContabilidadeLote->setTipo( $stTipo );
                    $obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );

                    $obErro = $obRContabilidadeLancamento->alterarCodHistorico();

                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgFilt, "Historicos alterados", "alterar", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
}
?>
