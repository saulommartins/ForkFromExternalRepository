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
    * Arquivo de Processamento para configuração da exportação do CAGED
    * Data de Criação: 18/04/2008

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.08.20

    $Id: PRConfiguracaoCAGED.php 66444 2016-08-29 19:13:17Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoCaged.class.php";
include_once CAM_GRH_IMA_MAPEAMENTO."TIMACagedAutorizadoCei.class.php";
include_once CAM_GRH_IMA_MAPEAMENTO."TIMACagedAutorizadoCgm.class.php";
include_once CAM_GRH_IMA_MAPEAMENTO."TIMACagedEstabelecimento.class.php";
include_once CAM_GRH_IMA_MAPEAMENTO."TIMACagedEvento.class.php";
include_once CAM_GRH_IMA_MAPEAMENTO."TIMACagedSubDivisao.class.php";

$obTIMAConfiguracaoCaged = new TIMAConfiguracaoCaged();
$obTIMACagedAutorizadoCei = new TIMACagedAutorizadoCei();
$obTIMACagedAutorizadoCgm = new TIMACagedAutorizadoCgm();
$obTIMACagedEstabelecimento = new TIMACagedEstabelecimento();
$obTIMACagedEvento = new TIMACagedEvento();
$obTIMACagedSubDivisao = new TIMACagedSubDivisao();

$obTIMACagedAutorizadoCei->obTIMAConfiguracaoCaged = &$obTIMAConfiguracaoCaged;
$obTIMACagedAutorizadoCgm->obTIMAConfiguracaoCaged = &$obTIMAConfiguracaoCaged;
$obTIMACagedEstabelecimento->obTIMAConfiguracaoCaged = &$obTIMAConfiguracaoCaged;
$obTIMACagedEvento->obTIMAConfiguracaoCaged = &$obTIMAConfiguracaoCaged;
$obTIMACagedSubDivisao->obTIMAConfiguracaoCaged = &$obTIMAConfiguracaoCaged;

//Define o nome dos arquivos PHP
$stPrograma = "ConfiguracaoCAGED";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao(true);
Sessao::getTransacao()->setMapeamento( $obTIMAConfiguracaoCaged );

$stAcao = $request->get("stAcao");
switch ($stAcao) {
    case "configurar":
        $obTIMACagedAutorizadoCgm->excluirTodos();
        $obTIMACagedAutorizadoCei->excluirTodos();
        $obTIMACagedEstabelecimento->excluirTodos();
        $obTIMACagedSubDivisao->excluirTodos();        
        $obTIMACagedEvento->excluirTodos();
        $obTIMAConfiguracaoCaged->excluirTodos();

        $obTIMAConfiguracaoCaged->setDado("cod_cnae"       ,$request->get("HdninCodCnae"));
        $obTIMAConfiguracaoCaged->setDado("tipo_declaracao",$request->get("stPrimeiraDeclaracao"));
        $obTIMAConfiguracaoCaged->inclusao();

        if ($request->get("boInformarResponsavel")) {
            $obTIMACagedAutorizadoCgm->setDado("numcgm",$request->get("inCGM"));
            $obTIMACagedAutorizadoCgm->setDado("num_autorizacao",$request->get("inNumeroAutorizacao"));
            $obTIMACagedAutorizadoCgm->inclusao();
            if ($request->get("boInformarCEIAutorizado")) {
                $obTIMACagedAutorizadoCei->setDado("num_cei",$request->get("inNumeroCEIAutorizacao"));
                $obTIMACagedAutorizadoCei->inclusao();
            }
        }
        if ($request->get("boInformarCEI")) {
            $obTIMACagedEstabelecimento->setDado("num_cei",$request->get("inNumeroCEI"));
            $obTIMACagedEstabelecimento->inclusao();
        }
        $arCodSubDivisaoSelecionados = $request->get("inCodSubDivisaoSelecionados");        
        if (is_array($arCodSubDivisaoSelecionados)) {
            foreach ($arCodSubDivisaoSelecionados as $inCodSubDivisao) {
                $obTIMACagedSubDivisao->setDado("cod_sub_divisao",$inCodSubDivisao);
                $obTIMACagedSubDivisao->inclusao();                
            }
        }
        foreach ($request->get("inCodEventoSelecionados") as $inCodEvento) {
            $obTIMACagedEvento->setDado("cod_evento",$inCodEvento);
            $obTIMACagedEvento->inclusao();
        }

    break;
}
Sessao::encerraExcecao();
SistemaLegado::alertaAviso($pgForm,"Configuração do CAGED concluída com sucesso!","configurar","aviso", Sessao::getId(), "../");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
