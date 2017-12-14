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
    * Página de Processamento para Concessão de Diárias
    * Data de Criação: 25/08/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: PRConcederDiarias.php 66258 2016-08-03 14:25:21Z evandro $

    * Casos de uso: uc-04.09.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalContratoServidor.class.php"                                 );
include_once ( CAM_GRH_DIA_MAPEAMENTO."TDiariasDiaria.class.php"                                        );
include_once ( CAM_GRH_DIA_MAPEAMENTO."TDiariasDiariaEmpenho.class.php"                                 );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php"                                              );

//Define o nome dos arquivos PHP
$stPrograma = "ConcederDiarias";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao        = $_REQUEST['stAcao'];
$inCodContrato = $_REQUEST['inCodContrato'];
$inRegistro    = $_REQUEST['inRegistro'];
$inCodDiaria   = $_REQUEST['inCodDiaria'];
$stTimestamp   = $_REQUEST['stTimestamp'];
$stRetorno     = $_REQUEST['stRetorno'];
$stLink        = "?stAcao=$stAcao&inCodContrato=$inCodContrato&inRegistro=$inRegistro";

switch ($stAcao) {
    case "conceder":
            Sessao::setTrataExcecao(true);

            $arConcessoes = Sessao::read('arConcessoes');

            //Verifica as diarias existem no banco. Caso não existam na sessão exclui
            $arCodDiarias = array();

            foreach ($arConcessoes as $arConcessoesTemp) {
                if ($arConcessoesTemp['inCodDiaria'] != "") {
                    $arCodDiarias[] = $arConcessoesTemp['inCodDiaria'];
                }
            }

            $obTDiariasDiaria = new TDiariasDiaria();
            $stFiltroDiarias = " WHERE cod_contrato = ".$inCodContrato;
            $obTDiariasDiaria->recuperaTodos($rsDiarias, $stFiltroDiarias);

            while (!$rsDiarias->eof()) {
                if (!in_array($rsDiarias->getCampo('cod_diaria'), $arCodDiarias)) {

                    $obTDiariasDiariaEmpenho = new TDiariasDiariaEmpenho();
                    $obTDiariasDiariaEmpenho->setDado('cod_diaria', $rsDiarias->getCampo('cod_diaria'));
                    $obTDiariasDiariaEmpenho->setDado('cod_contrato', $rsDiarias->getCampo('cod_contrato'));
                    $obTDiariasDiariaEmpenho->setDado('timestamp', $rsDiarias->getCampo('timestamp'));
                    $obTDiariasDiariaEmpenho->recuperaPorChave($rsEmpenho);

                    if ($rsEmpenho->getNumLinhas() < 1) {
                        $obTDiariasDiaria = new TDiariasDiaria();
                        $obTDiariasDiaria->setDado('cod_diaria', $rsDiarias->getCampo('cod_diaria'));
                        $obTDiariasDiaria->setDado('cod_contrato', $rsDiarias->getCampo('cod_contrato'));
                        $obTDiariasDiaria->setDado('timestamp', $rsDiarias->getCampo('timestamp'));
                        $obTDiariasDiaria->exclusao();
                    }

                    if (Sessao::getExcecao()->ocorreu()) {
                        break;
                    }
                }
                $rsDiarias->proximo();
            }

            //Verifica alteraçoes e inclusões de concessoes de diarias
            if (!Sessao::getExcecao()->ocorreu()) {
                foreach ($arConcessoes as $arConcessaoTemp) {

                    if (Sessao::getExcecao()->ocorreu()) {
                        break;
                    }

                    //Verifica Norma
                    $rsNorma    = new RecordSet();
                    $arCodNorma = ltrim($arConcessaoTemp['nuNormaExercicio'], "0");
                    if($arCodNorma[0] == "/")
                        $arCodNorma = "0".$arCodNorma;
                    $arCodNorma = explode("/",$arCodNorma);
                    $inCodNorma = "";
                    if (count($arCodNorma)>0) {
                        $obTNorma = new TNorma();
                        $stFiltroNorma = " WHERE num_norma = '".$arCodNorma[0]."' AND exercicio = '".$arCodNorma[1]."'";
                        $obTNorma->recuperaTodos($rsNorma, $stFiltroNorma);
                        if ($rsNorma->getNumLinhas() > 0) {
                            $inCodNorma  = $rsNorma->getCampo('cod_norma');
                        }
                    }

                    $boExisteEmpenho = false;
                    $obTDiariasDiaria = new TDiariasDiaria();
                    if ($arConcessaoTemp['inCodDiaria'] != "") {
                        $obTDiariasDiaria->setDado('cod_diaria', $arConcessaoTemp['inCodDiaria']);

                        $obTDiariasDiariaEmpenho = new TDiariasDiariaEmpenho();
                        $obTDiariasDiariaEmpenho->setDado('cod_diaria', $arConcessaoTemp['inCodDiaria']);
                        $obTDiariasDiariaEmpenho->setDado('cod_contrato', $arConcessaoTemp['inCodContrato']);
                        $obTDiariasDiariaEmpenho->setDado('timestamp', $arConcessaoTemp['stTimestamp']);
                        $obTDiariasDiariaEmpenho->recuperaPorChave($rsEmpenho);

                        if ($rsEmpenho->getNumLinhas() > 0) {
                            $boExisteEmpenho = true;
                        }
                    }

                    if (!$boExisteEmpenho) {
                        $obTDiariasDiaria->setDado('cod_contrato', $arConcessaoTemp['inCodContrato']);
                        $obTDiariasDiaria->setDado('cod_norma', $inCodNorma);
                        $obTDiariasDiaria->setDado('dt_inicio', $arConcessaoTemp['dtInicio']);
                        $obTDiariasDiaria->setDado('dt_termino', $arConcessaoTemp['dtTermino']);
                        $obTDiariasDiaria->setDado('hr_inicio', $arConcessaoTemp['hrInicio']);
                        $obTDiariasDiaria->setDado('hr_termino', $arConcessaoTemp['hrTermino']);
                        $obTDiariasDiaria->setDado('quantidade', $arConcessaoTemp['nuQuantidade']);
                        $obTDiariasDiaria->setDado('vl_total', $arConcessaoTemp['nuValorTotal']);
                        $obTDiariasDiaria->setDado('vl_unitario', $arConcessaoTemp['nuValorUnitario']);
                        $obTDiariasDiaria->setDado('cod_uf', $arConcessaoTemp['inCodEstado']);
                        $obTDiariasDiaria->setDado('cod_municipio', $arConcessaoTemp['inCodMunicipio']);
                        $obTDiariasDiaria->setDado('motivo', $arConcessaoTemp['stMotivo']);
                        $obTDiariasDiaria->setDado('cod_tipo', $arConcessaoTemp['inCodTipo']);
                        $obTDiariasDiaria->setDado('timestamp_tipo', $arConcessaoTemp['stTimestampTipo']);
                        $obTDiariasDiaria->setDado('numcgm', Sessao::read('numCgm'));

                        $obTDiariasDiaria->inclusao();
                    }

                }//foreach arTipoDiarias
            }//!Sessao::getExcecao()->ocorreu()

            Sessao::encerraExcecao();
            SistemaLegado::alertaAviso($pgFilt.$stLink,"Concessão de Diárias concluído. Matrícula $inRegistro","incluir","aviso", Sessao::getId(), "../");
        break;

    case "consultar":
        switch ($stRetorno) {
            case "recibo":

                $preview = new PreviewBirt(4,50,2);
                $preview->setVersaoBirt( '2.5.0' );
                $preview->setTitulo('Recibo de Diárias');
                $preview->setNomeArquivo('reciboDiarias');
                $preview->setReturnURL( CAM_GRH_DIA_INSTANCIAS."concessao/".$pgList.$stLink);
                $preview->addParametro("entidade",Sessao::getCodEntidade($boTransacao));
                $preview->addParametro("stEntidade",Sessao::getEntidade());
                $preview->addParametro("inCodContrato", $inCodContrato);
                $preview->addParametro("inCodDiaria",$inCodDiaria);
                $preview->addParametro("stTimestamp",$stTimestamp);
                $preview->addParametro("inCodMunicipio",SistemaLegado::pegaConfiguracao('cod_municipio', 2));
                $preview->addParametro("inCodEstado",SistemaLegado::pegaConfiguracao('cod_uf', 2));
                $preview->addParametro("stNomPrefeitura",SistemaLegado::pegaConfiguracao('nom_prefeitura', 2));
                $preview->preview();

                break;
            case "lista":
                $stMensagem = "Matrícula ".$inRegistro;
                sistemaLegado::alertaAviso($pgList,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
                break;
            case "filtro":
                $stMensagem = "Matrícula ".$inRegistro;
                sistemaLegado::alertaAviso($pgFilt.$stLink,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
                break;
        }
        break;
}
?>
