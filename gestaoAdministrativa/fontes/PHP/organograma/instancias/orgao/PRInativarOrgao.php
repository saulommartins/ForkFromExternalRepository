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
    * Arquivo de instância para manutenção de orgao
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    * $Id: PRInativarOrgao.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-01.05.02
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php");

//

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "InativarOrgao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new ROrganogramaOrgao;

$stFiltro = '';
$arDados = Sessao::read('filtro');
if ($arDados) {
    $stFiltro = '';
    foreach ($arDados as $stCampo => $stValor) {
        if (is_array($stValor)) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

switch ($stAcao) {
    case "inativar":
        $obRegra->setCodOrgao                               ( $_REQUEST['inCodOrgao'] );
        $obRegra->obROrganograma->setCodOrganograma         ( $_REQUEST['inCodOrganograma'] );
        $obRegra->setInativacao                             ( $_REQUEST['stDataInativacao'] );

        $obErro = $obRegra->inativar();

         if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList.$stFiltro."&inCodOrganograma=".$_REQUEST['inCodOrganograma'],"Órgão: ".$_REQUEST['inCodOrgao']." inativado","","aviso", Sessao::getId(), "../");
         } else {
            exibeAviso(urlencode($obErro->getDescricao()),"","erro");
         }
    break;
}

?>
