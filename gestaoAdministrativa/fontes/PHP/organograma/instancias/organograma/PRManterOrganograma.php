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
  * Arquivo de instância para manutenção de organograma
  * Data de Criação: 25/07/2005

  * @author Analista: Cassiano
  * @author Desenvolvedor: Cassiano

  $Id: PRManterOrganograma.php 59997 2014-09-24 19:54:15Z evandro $

  Casos de uso: uc-01.05.01

  */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrganograma.class.php";

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterOrganograma";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new ROrganogramaOrganograma;
$niveis = Sessao::read('niveis');

switch ($stAcao) {

    case "incluir":
        $obRegra->setDtImplantacao        ( $_POST['stDataImplantacao'] );
        $obRegra->obRNorma->setCodNorma   ( $_POST['inCodNorma'] );
        $obRegra->setPermissaoHierarquica ( $_POST['boPermissaoHierarquica'] );

        for ($inCount=0; $inCount<count($niveis); $inCount++) {
            $obRegra->addNivel();
            //$obRegra->obUltimoNivel->setNumNivel      ( $niveis[$inCount]['inNumNivel']     );
            $obRegra->obUltimoNivel->setDescricao     ( $niveis[$inCount]['stDescNivel']    );
            $obRegra->obUltimoNivel->setMascaraCodigo ( $niveis[$inCount]['stMascaraNivel'] );
            $obRegra->commitNivel();
        }

        $obErro = $obRegra->salvar();

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgForm,"Organograma: ".$_POST['stDataImplantacao'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;

    case "alterar":
        
        $obRegra->setCodOrganograma       ( $_REQUEST['inCodOrganograma'] );
        $obRegra->setDtImplantacao        ( $_POST['stDataImplantacao'] );
        $obRegra->obRNorma->setCodNorma   ( $_POST['inCodNorma'] );
        $obRegra->setPermissaoHierarquica ( $_POST['boPermissaoHierarquica'] );
        for ($inCount=0; $inCount<count($niveis); $inCount++) {
            $obRegra->addNivel();
            $obRegra->obUltimoNivel->setCodNivel      ( $niveis[$inCount]['inCodNivel']     );
            $obRegra->obUltimoNivel->setNumNivel      ( $niveis[$inCount]['inNumNivel']     );
            $obRegra->obUltimoNivel->setDescricao     ( $niveis[$inCount]['stDescNivel']    );
            $obRegra->obUltimoNivel->setMascaraCodigo ( $niveis[$inCount]['stMascaraNivel'] );
            $obRegra->commitNivel();
        }
        $obErro = $obRegra->salvar();

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgList.$stFiltro,"Organograma: ".$_POST['stDataImplantacao'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }

    break;

    case "excluir";
        $obRegra->setCodOrganograma( $_REQUEST['inCodOrganograma'] );
        $obRegra->consultar();        
        $obErro = $obRegra->excluir();        

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgList.$stFiltro,"Organograma: ".$obRegra->getDtImplantacao(),"excluir","aviso", Sessao::getId(), "../");
        } else {
            if (strpos($obErro->getDescricao(), 'fk_')) {
                $obErro->setDescricao('O organograma não pode ser excluído porque existem órgãos cadastrados.');
            }
            SistemaLegado::alertaAviso($pgList,urlencode( $obErro->getDescricao() ),"n_excluir","erro", Sessao::getId(), "../");
        }

    break;
}

?>
