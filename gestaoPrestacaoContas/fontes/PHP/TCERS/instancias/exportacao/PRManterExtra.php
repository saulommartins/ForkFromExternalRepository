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
    * Página de Processamento - Parâmetros do Arquivo UNIORCAM.
    * Data de Criação   : 11/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: PRManterExtra.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.08.04
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GPC_TCERS_NEGOCIO."RExportacaoTCERSArqRDExtra.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterExtra";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obRExportacaoTCERSArqRDExtra = new RExportacaoTCERSArqRDExtra;
$arRDExtra = Sessao::read('arRDExtra');
$stAcao = 'incluir';

if (sizeof($arRDExtra)) {
    foreach ($arRDExtra as $Recordset) {
        $obRExportacaoTCERSArqRDExtra->addRDExtra();
        $obRExportacaoTCERSArqRDExtra->roUltimaRDExtra->setCodEstrutural($Recordset["cod_estrutural"]);
        $obRExportacaoTCERSArqRDExtra->roUltimaRDExtra->setNomConta($Recordset["nom_conta"]);
        $obRExportacaoTCERSArqRDExtra->roUltimaRDExtra->setClassificacao($Recordset["classificacao"]);
        $obRExportacaoTCERSArqRDExtra->roUltimaRDExtra->setExercicio( Sessao::getExercicio() );
        }

    $obErro = $obRExportacaoTCERSArqRDExtra->salvar();
    if ( !$obErro->ocorreu() ) {
        SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " ".$cont." Renda Extra Orçamentária incluídos/alterados ", "incluir", "aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }
} else {
        SistemaLegado::exibeAviso("Lista de registros não pode ser vazia","n_incluir","erro");
}

SistemaLegado::LiberaFrames();

?>
