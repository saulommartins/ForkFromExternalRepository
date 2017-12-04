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
    * Página de Processamento para Permissoes de Autorizacao
    * Data de Criação   : 04/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: vitor $
    $Date: 2007-04-05 15:11:46 -0300 (Qui, 05 Abr 2007) $

    * Casos de uso: uc-02.03.01
*/

/*
$Log$
Revision 1.6  2007/04/05 18:11:46  vitor
8264

Revision 1.5  2006/07/05 20:47:34  cleisson
Adicionada tag Log aos arquivos

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_EMP_NEGOCIO."REmpenhoPermissaoAutorizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterPermissao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obRegra = new REmpenhoPermissaoAutorizacao;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stAcao = 'incluir';

switch ($stAcao) {
    case "incluir":
          $ptrPerm = &Sessao::read('arPermissoes');
          for ( $i=1; $i<=count($ptrPerm); $i++ ) {
                 $stPerm = explode("_", $_REQUEST['perm_'.($i)]);
                 $inNumOrgao = $stPerm[0];
                 $inNumUnidade = $stPerm[1];
                     if ($ptrPerm[($i-1)]['num_orgao'] == $inNumOrgao) {
                         if ($ptrPerm[($i-1)]['num_unidade'] == $inNumUnidade) {
                            if ($_REQUEST['perm_'.($i)]) {
                                 $ptrPerm[($i-1)]['permitido'] = true;
                             }
                         }
                     } else {
                                $ptrPerm[($i-1)]['permitido'] = false;
                          }
             }
        $obRegra->setExercicio( Sessao::getExercicio() );
        $obRegra->obRUsuario->obRCGM->setNumCGM( $_POST['inNumCGM'] );
        $obRegra->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $_POST['inCodOrgao'] );
        $obRegra->obROrcamentoUnidade->setNumeroUnidade( $_POST['inCodUnidade'] );

        $arAux = array();
        $inCount = 0;
        for ($i=0; $i < count($ptrPerm); $i++) {
            if ($ptrPerm[$i]['permitido'] === true) {
                $arAux[$inCount] = $ptrPerm[$i];
                unset($arAux[$inCount]['permitido']);
                $inCount++;
            }
        }

        Sessao::write('arPermissoes', $arAux);
        $obRegra->setPermissoes( Sessao::read('arPermissoes') );
        $obErro = $obRegra->incluir() ;
        if( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgFilt, $_POST['inNumCGM'], "incluir", "aviso", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    break;
}
?>
