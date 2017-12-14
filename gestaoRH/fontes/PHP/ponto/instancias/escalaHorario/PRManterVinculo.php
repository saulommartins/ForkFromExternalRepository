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
    * Página de Processamento para Manter Vinculo de Escalas
    * Data de Criação: 13/10/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscala.class.php"                                          );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscalaTurno.class.php"                                     );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscalaContrato.class.php"                                  );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscalaContratoExclusao.class.php"                          );

include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalRegime.class.php"                                        );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php"                                    );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCargo.class.php"                                         );

include_once ( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgao.class.php"                                    );
include_once ( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaLocal.class.php"                                    );

include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"                                      );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterVinculo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stAcao      = $_REQUEST['stAcao'];
$inCodEscala = $_REQUEST['inCodEscala'];
$stLink      = "?stAcao=$stAcao";

$obTPontoEscala                 = new TPontoEscala();
$obTPontoEscalaContrato         = new TPontoEscalaContrato();
$obTPontoEscalaContratoExclusao = new TPontoEscalaContratoExclusao();

$obTPontoEscalaContrato->obTPontoEscala                 = &$obTPontoEscala;
$obTPontoEscalaContratoExclusao->obTPontoEscalaContrato = &$obTPontoEscalaContrato;

switch ($stAcao) {
    case "redirecionarLista":
        $pgList = str_replace($stAcao, "alterar", $pgList);
        SistemaLegado::alertaAviso($pgList,"Escala $inCodEscala","incluir","aviso", Sessao::getId(), "../");
        break;

    case "redirecionarFiltro":
        $pgFilt = str_replace($stAcao, "alterar", $pgFilt);
        SistemaLegado::alertaAviso($pgFilt,"Escala $inCodEscala","incluir","aviso", Sessao::getId(), "../");
        break;

    case "imprimir":
        $stChave     = $_REQUEST['stChave'];
        $arChave     = explode("_", $stChave);
        $stRetorno   = $_REQUEST['stRetorno'];

        switch ($stRetorno) {
            case "contrato":
                $obTPessoalContrato = new TPessoalContrato();
                $obTPessoalContrato->recuperaCgmDoRegistroServidor($rsDescricao, " AND contrato.cod_contrato = ".$arChave[1]);

                $stDescricao = "Matrícula: ".$rsDescricao->getCampo('registro')." - ".$rsDescricao->getCampo('nom_cgm');
                break;

            case "lotacao":
                $obTOrganogramaOrgao = new TOrganogramaOrgao();
                $obTOrganogramaOrgao->setDado('cod_orgao', $arChave[1]);
                $obTOrganogramaOrgao->listarOrgaoCodigoComposto($rsDescricao);

                $stDescricao = "Lotação: ".$rsDescricao->getCampo('orgao')." - ".$rsDescricao->getCampo('descricao');
                break;

            case "local":
                $obTOrganogramaLocal = new TOrganogramaLocal();
                $obTOrganogramaLocal->setDado('cod_local', $arChave[1]);
                $obTOrganogramaLocal->recuperaPorChave($rsDescricao);

                $rsDescricao->addStrPad('cod_local', 3);

                $stDescricao = "Local: ".$rsDescricao->getCampo('cod_local')." - ".$rsDescricao->getCampo('descricao');
                break;

            case "sub_divisao_funcao":
                $obTPessoalRegime = new TPessoalRegime();
                $obTPessoalRegime->setDado('cod_regime', $arChave[1]);
                $obTPessoalRegime->recuperaPorChave($rsDescricao);

                $stDescricao = "Regime / Subdivisão / Função: ".$rsDescricao->getCampo('descricao')." / ";

                $obTPessoalSubDivisao = new TPessoalSubDivisao();
                $obTPessoalSubDivisao->setDado('cod_regime', $arChave[1]);
                $obTPessoalSubDivisao->setDado('cod_sub_divisao', $arChave[2]);
                $obTPessoalSubDivisao->recuperaPorChave($rsDescricao);

                $stDescricao .=  $rsDescricao->getCampo('descricao')." / ";

                $obTPessoalCargo = new TPessoalCargo();
                $obTPessoalCargo->setDado('cod_cargo', $arChave[3]);
                $obTPessoalCargo->recuperaPorChave($rsDescricao);

                $stDescricao .=  $rsDescricao->getCampo('descricao');
                break;
        }

        $preview = new PreviewBirt(4,51,3);
        $preview->setVersaoBirt( '2.2.2' );
        $preview->setTitulo('Programação de Escalas');
        $preview->setNomeArquivo('programacaoDeEscalas');
        $preview->addParametro("entidade",Sessao::getCodEntidade($boTransacao));
        $preview->addParametro("stEntidade",Sessao::getEntidade());
        $preview->addParametro("stDescricao", $stDescricao);
        $preview->addParametro("inCodEscala", $inCodEscala);
        $preview->preview();
        break;

    case "incluir":
        Sessao::setTrataExcecao(true);

        $stFiltroEscala  = " AND NOT EXISTS ( SELECT 1
                                                  FROM ponto.escala_contrato
                                                 WHERE CONTRATO.cod_contrato = escala_contrato.cod_contrato
                                                   AND ponto.escala_contrato.cod_escala = ESCALA
                                                   AND NOT EXISTS (SELECT 1
                                                                     FROM ponto.escala_contrato_exclusao
                                                                    WHERE escala_contrato_exclusao.cod_escala   = escala_contrato.cod_escala
                                                                      AND escala_contrato_exclusao.cod_contrato = escala_contrato.cod_contrato
                                                                      AND escala_contrato_exclusao.timestamp    = escala_contrato.timestamp)
                                               )";

        $stFiltroAtivos  = " AND NOT EXISTS    (SELECT 1                                                                                   \n";
        $stFiltroAtivos .= "                      FROM pessoal.aposentadoria                                      \n";
        $stFiltroAtivos .= "                     WHERE aposentadoria.cod_contrato = CONTRATO.cod_contrato                                  \n";
        $stFiltroAtivos .= "                       AND NOT EXISTS (SELECT 1                                                                \n";
        $stFiltroAtivos .= "                                         FROM pessoal.aposentadoria_excluida          \n";
        $stFiltroAtivos .= "                                        WHERE aposentadoria_excluida.cod_contrato = aposentadoria.cod_contrato \n";
        $stFiltroAtivos .= "                                          AND aposentadoria_excluida.timestamp_aposentadoria = aposentadoria.timestamp)\n";
        $stFiltroAtivos .= "                   )                                                                                           \n";

        $stFiltroAtivos .= " AND NOT EXISTS    (SELECT 1                                                                                   \n";
        $stFiltroAtivos .= "                      FROM pessoal.contrato_servidor_caso_causa                       \n";
        $stFiltroAtivos .= "                     WHERE contrato_servidor_caso_causa.cod_contrato = CONTRATO.cod_contrato)                  \n";

        $inCodEscala = ltrim($_REQUEST['inCodEscala'], "0");
        $rsContratos = new Recordset();
        switch ($_REQUEST["stTipoFiltro"]) {
            case "contrato":
            case "cgm_contrato":
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
                foreach (Sessao::read("arContratos") as $arContrato) {
                    $stCodContratos .= $arContrato["cod_contrato"].",";
                }
                $stCodContratos = substr($stCodContratos,0,strlen($stCodContratos)-1);

                $stFiltro = " AND contrato.cod_contrato IN (".$stCodContratos.")";
                $stFiltro .= str_replace("CONTRATO", "contrato", str_replace("ESCALA", $inCodEscala, $stFiltroEscala));
                $stFiltro .= str_replace("CONTRATO", "contrato", $stFiltroAtivos);

                $obTPessoalContrato = new TPessoalContrato;
                $obTPessoalContrato->recuperaCgmDoRegistroServidor($rsContratos,$stFiltro);

                break;
            case "lotacao":
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php");
                $obTPessoalContratoServidorOrgao = new TPessoalContratoServidorOrgao();
                $stFiltro = " WHERE contrato_servidor_orgao.cod_orgao IN (".implode(",",$_POST["inCodLotacaoSelecionados"]).")";

                $stFiltro .= str_replace("CONTRATO", "contrato_servidor_orgao", str_replace("ESCALA", $inCodEscala, $stFiltroEscala));
                $stFiltro .= str_replace("CONTRATO", "contrato_servidor_orgao", $stFiltroAtivos);

                $obTPessoalContratoServidorOrgao->recuperaContratosDaLotacao($rsContratos,$stFiltro);
                break;
            case "local":
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorLocal.class.php");
                $obTPessoalContratoServidorLocal = new TPessoalContratoServidorLocal();
                $stFiltro = " WHERE contrato_servidor_local.cod_local IN (".implode(",",$_POST["inCodLocalSelecionados"]).")";

                $stFiltro .= str_replace("CONTRATO", "contrato_servidor_local", str_replace("ESCALA", $inCodEscala, $stFiltroEscala));
                $stFiltro .= str_replace("CONTRATO", "contrato_servidor_local", $stFiltroAtivos);

                $obTPessoalContratoServidorLocal->recuperaContratosDoLocal($rsContratos,$stFiltro);
                break;
            case "sub_divisao_funcao":
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFuncao.class.php");
                $obTPessoalContratoServidorFuncao = new TPessoalContratoServidorFuncao();
                $stFiltro  = " WHERE contrato_servidor_regime_funcao.cod_regime IN (".implode(",",$_POST["inCodRegimeSelecionadosFunc"]).")";
                $stFiltro .= "   AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao IN (".implode(",",$_POST["inCodSubDivisaoSelecionadosFunc"]).")";

                if ($_POST["inCodFuncaoSelecionados"] != "") {
                    $stFiltro .= " AND contrato_servidor_funcao.cod_cargo IN (".implode(",",$_POST["inCodFuncaoSelecionados"]).")";
                }

                $stFiltro .= str_replace("CONTRATO", "contrato_servidor_funcao", str_replace("ESCALA", $inCodEscala, $stFiltroEscala));
                $stFiltro .= str_replace("CONTRATO", "contrato_servidor_regime_funcao", $stFiltroAtivos);

                $obTPessoalContratoServidorFuncao->recuperaContratosDaFuncao($rsContratos,$stFiltro);
                break;
        }
        while (!$rsContratos->eof()) {
            $obTPontoEscalaContrato->setDado('cod_contrato', $rsContratos->getCampo("cod_contrato"));
            $obTPontoEscalaContrato->setDado('cod_escala', $inCodEscala);
            $obTPontoEscalaContrato->recuperaVerificaConflitoVincularEscalaContrato($rsConflito);

            if ($rsConflito->getNumLinhas() < 1) {
                $obTPontoEscalaContrato->setDado("cod_escala"  ,$inCodEscala);
                $obTPontoEscalaContrato->setDado("cod_contrato",$rsContratos->getCampo("cod_contrato"));
                $obTPontoEscalaContrato->inclusao();
            } else {
                SistemaLegado::exibeAviso("Ao efetuar o vínculo de escala, a matrícula ".$rsConflito->getCampo('registro').", foi ignorada por possuir turno em conflito com o novo vínculo - Escala ".$rsConflito->getCampo('cod_escala')." - ".$rsConflito->getCampo('dt_turno'), "n_incluir", "aviso");
            }
            $rsContratos->proximo();
        }
        Sessao::encerraExcecao();
        SistemaLegado::LiberaFrames();
        SistemaLegado::alertaAviso($pgFilt.$stLink,"Vínculos de Escala incluídos com sucesso.","incluir","aviso", Sessao::getId(), "../");
        break;

    case "excluir":
        Sessao::setTrataExcecao(true);

        $stFiltroEscala  = " AND EXISTS (       SELECT 1
                                                  FROM ponto.escala_contrato
                                                 WHERE CONTRATO.cod_contrato = escala_contrato.cod_contrato
                                                   AND escala_contrato.cod_escala = ESCALA
                                                   AND NOT EXISTS (SELECT 1
                                                                     FROM ponto.escala_contrato_exclusao
                                                                    WHERE escala_contrato_exclusao.cod_escala   = escala_contrato.cod_escala
                                                                      AND escala_contrato_exclusao.cod_contrato = escala_contrato.cod_contrato
                                                                      AND escala_contrato_exclusao.timestamp    = escala_contrato.timestamp)
                                         )";

        foreach ($_POST as $stCampo=>$stValor) {
            if (strpos($stCampo,"excluirVinculoEscala") === 0) {
                $arExcluirVinculoEscala = explode("_",$stCampo);
                $inCodEscala = $arExcluirVinculoEscala[1];

                switch ($_POST["stTipoFiltro"]) {
                    case "contrato":
                    case "cgm_contrato":
                    case "contrato_todos":
                    case "cgm_contrato_todos":
                        $arContratos[0]["cod_contrato"] = $arExcluirVinculoEscala[2];
                        $rsContratos = new recordset;
                        $rsContratos->preenche($arContratos);
                        break;
                    case "lotacao":
                        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php");
                        $obTPessoalContratoServidorOrgao = new TPessoalContratoServidorOrgao();
                        $stFiltro  = " WHERE contrato_servidor_orgao.cod_orgao = ".$arExcluirVinculoEscala[2];
                        $stFiltro .= str_replace("CONTRATO", "contrato_servidor_orgao", $stFiltroEscala);
                        $stFiltro  = str_replace("ESCALA", $inCodEscala, $stFiltro);

                        $obTPessoalContratoServidorOrgao->recuperaContratosDaLotacao($rsContratos,$stFiltro);
                        break;
                    case "local":
                        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorLocal.class.php");
                        $obTPessoalContratoServidorLocal = new TPessoalContratoServidorLocal();
                        $stFiltro  = " WHERE contrato_servidor_local.cod_local = ".$arExcluirVinculoEscala[2];
                        $stFiltro .= str_replace("CONTRATO", "contrato_servidor_local", $stFiltroEscala);
                        $stFiltro  = str_replace("ESCALA", $inCodEscala, $stFiltro);

                        $obTPessoalContratoServidorLocal->recuperaContratosDoLocal($rsContratos,$stFiltro);
                        break;
                    case "sub_divisao_funcao":
                        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFuncao.class.php");
                        $obTPessoalContratoServidorFuncao = new TPessoalContratoServidorFuncao();
                        $stFiltro  = " WHERE contrato_servidor_regime_funcao.cod_regime = ".$arExcluirVinculoEscala[2];
                        $stFiltro .= "   AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao = ".$arExcluirVinculoEscala[3];
                        $stFiltro .= "   AND contrato_servidor_funcao.cod_cargo = ".$arExcluirVinculoEscala[4];
                        $stFiltro .= str_replace("CONTRATO", "contrato_servidor_funcao", $stFiltroEscala);
                        $stFiltro  = str_replace("ESCALA", $inCodEscala, $stFiltro);

                        $obTPessoalContratoServidorFuncao->recuperaContratosDaFuncao($rsContratos,$stFiltro);
                        break;
                }
                Sessao::consultarDadosSessao();
                while (!$rsContratos->eof()) {
                    $stFiltro  = "  AND escala_contrato.cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltro .= "  AND escala_contrato.cod_escala = ".$inCodEscala;
                    $obTPontoEscalaContrato->recuperaContratosEscala($rsEscalaContratos,$stFiltro);
                    if ($rsEscalaContratos->getNumLinhas() == 1) {
                        $obTPontoEscalaContratoExclusao->setDado("cod_escala"  ,$rsEscalaContratos->getCampo("cod_escala"));
                        $obTPontoEscalaContratoExclusao->setDado("cod_contrato",$rsEscalaContratos->getCampo("cod_contrato"));
                        $obTPontoEscalaContratoExclusao->setDado("timestamp"   ,$rsEscalaContratos->getCampo("timestamp"));
                        $obTPontoEscalaContratoExclusao->setDado("numcgm"      ,Sessao::read("numCgm"));
                        $obTPontoEscalaContratoExclusao->inclusao();
                    }
                    $rsContratos->proximo();
                }
            }
        }

        Sessao::encerraExcecao();
        SistemaLegado::alertaAviso($pgList.$stLink,"Vínculos de Escala excluídos com sucesso.","excluir","aviso", Sessao::getId(), "../");
        break;
}

?>
