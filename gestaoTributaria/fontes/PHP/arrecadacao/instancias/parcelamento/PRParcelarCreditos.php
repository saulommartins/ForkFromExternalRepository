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
  * Página de processamento para Parcelamento
  * Data de criação : 28/03/2006

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Diego Bueno Coelho

    * $Id: PRParcelarCreditos.php 63839 2015-10-22 18:08:07Z franver $

    Caso de uso: uc-05.03.20
**/

/*
$Log$
Revision 1.3  2006/09/15 11:16:00  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRParametroCalculo.class.php"   );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php"            );
include_once ( CAM_GT_ARR_NEGOCIO."RARRLancamento.class.php"         );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"              );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma    = "ParcelarCreditos";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

$obErro = new Erro;

switch ($stAcao) {

    case "incluir":

        $cont = 0;
        $contParcelas = 0;
        foreach ($_REQUEST as $key => $valor) {
            $cont++;
            if ($cont > 9) {
                $arrayNovasParcelas[$contParcelas] = $valor;
                $contParcelas++;
            }
        }

        Sessao::write( 'arNovasParcelas', $arrayNovasParcelas );

        $obErro = new Erro;
        $obRegra =  new RARRLancamento($obRegra);

        $obRegra->boLancamento = TRUE;
        if ($obRegra->boLancamento == TRUE) {
            $obRegra->setPercentual                         ( $_REQUEST['stTipoDesconto']       );
            $obRegra->setDataVencimento                ( $_REQUEST['dtVencimento']         );
            $obRegra->setDataVencimentoDesconto ( $_REQUEST['dtVencimentoDesconto'] );
            $obRegra->setValorDesconto                   ( $_REQUEST['nuDesconto']           );
            $obRegra->setTotalParcelas                     ( $_REQUEST['inNumParcelas']        );
            $obRegra->setValor                                  ( $_REQUEST['flTotalApurado']);
            $obRegra->obRCgm->setNumCGM            ( $_REQUEST['inNumCGM'] );

        }
        $obErro = $obRegra->efetuarLancamentoParcelamento();

        if (!$obErro->ocorreu() && $obRegra->boLancamento == "true") {

            SistemaLegado::alertaAviso($pgForm."?stAcao=incluir&inCodContribuinte=".$_REQUEST[inNumCGM] ,"Contribuinte ".$_REQUEST["inNumCGM"],"incluir","aviso", Sessao::getId(), "../");
        } elseif (!$obErro->ocorreu() && $obRegra->boLancamento == "false") {

            $stPag = $pgFormRelatorioExecucao."?stAcao=incluir&stTipoCalculo=".$_REQUEST["stTipoCalculo"]."&inCodGrupo=".$_REQUEST["inCodGrupo"];
            SistemaLegado::alertaAviso($stPag,"Codigo do Grupo:".$_REQUEST["inCodGrupo"],"incluir","aviso", Sessao::getId(), "../");
        } else {

            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../");
        }
    break;
}
