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
    * Página de Processamento do Termo de Autuação de Edital
    * Data de Criação: 13/01/2009

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Grasiele Torres

    * @ignore

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoAdjudicacao.class.php" );
include_once ( TLIC.'TLicitacaoLicitacao.class.php');
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoAdjudicacaoAnulada.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "TermoAutuacaoEdital";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgGera     = "OCGeraTermoAutuacaoEdital.php";

Sessao::setTrataExcecao( true );

$rsProcesso = Sessao::read('rsProcesso');
$arAssinaturas = Sessao::read('assinaturas');

if (count($arAssinaturas['selecionadas']) == 0 ) {
    $stMensagem = 'Selecione uma assinatura!';
}

if (count($arAssinaturas['selecionadas']) > 1 ) {
    $stMensagem = 'Selecione apenas uma assinatura!';
}

if (count($arAssinaturas['selecionadas']) == 1) {
    foreach ($arAssinaturas['selecionadas'] as $arSelecionadas) {
        $stEntidade  = $arSelecionadas['inCodEntidade'];
        $stTimestamp = "'".$arSelecionadas['timestamp']."'";
        $inCGM       = $arSelecionadas['inCGM'];
        $stNomCGM    = $arSelecionadas['stNomCGM'];
    }

    $obTLicitacaoLicitacao = new TLicitacaoLicitacao();
    $obTLicitacaoLicitacao->setDado( 'numcgm', $inCGM);
    $obTLicitacaoLicitacao->recuperaNorma( $rsNorma );

    if ( $rsNorma->EOF() ) {
       $stMensagem = 'Este servidor não participa de uma comissão de licitação!';
    }
}

if ($stMensagem == '') {
    SistemaLegado::exibeAviso("Termo de Autuação de Edital ","incluir","aviso");
    Sessao::write('request', $_REQUEST);
    SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=".$_REQUEST['stAcao'].Sessao::read('stFiltroLista'), '', "incluir", "aviso", Sessao::getId(), "../");
    SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId());
} else {
    sistemaLegado::exibeAviso(urlencode($stMensagem),'erro');
}

Sessao::encerraExcecao();

?>
