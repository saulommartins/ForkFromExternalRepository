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
    * Página de Processamento do Registro de Evento de Férias
    * Data de Criação: 21/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30766 $
    $Name$
    $Author: souzadl $
    $Date: 2008-04-09 09:28:09 -0300 (Qua, 09 Abr 2008) $

    * Casos de uso: uc-04.05.53
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoFerias.class.php"                   );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoFerias.class.php"             );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php"                                 );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorPeriodo.class.php"                );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoFeriasParcela.class.php"            );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoFeriasCalculado.class.php"                  );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoFeriasCalculadoDependente.class.php"        );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoFerias.class.php"                   );

$link = Sessao::read("link");
$stAcao = $request->get("stAcao");
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEventoFerias";
$pgForm = "FM".$stPrograma.".php";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obTFolhaPagamentoRegistroEventoFerias          = new TFolhaPagamentoRegistroEventoFerias;
$obTFolhaPagamentoUltimoRegistroEventoFerias    = new TFolhaPagamentoUltimoRegistroEventoFerias;
$obTFolhaPagamentoEvento                        = new TFolhaPagamentoEvento;
$obTFolhaPagamentoContratoServidorPeriodo       = new TFolhaPagamentoContratoServidorPeriodo;
$obTFolhaPagamentoRegistroEventoFeriasParcela   = new TFolhaPagamentoRegistroEventoFeriasParcela;
$obTFolhaPagamentoEventoFeriasCalculado         = new TFolhaPagamentoEventoFeriasCalculado;
$obTFolhaPagamentoEventoFeriasCalculadoDependente = new TFolhaPagamentoEventoFeriasCalculadoDependente;
$obTFolhaPagamentoLogErroCalculoFerias          = new TFolhaPagamentoLogErroCalculoFerias;

switch ($stAcao) {
    case "alterar":
        Sessao::setTrataExcecao(true);
        $obTFolhaPagamentoContratoServidorPeriodo->setDado("cod_periodo_movimentacao"   ,$request->get("inCodPeriodoMovimentacao"));
        $obTFolhaPagamentoContratoServidorPeriodo->setDado("cod_contrato"               ,$request->get("inCodContrato"));
        $obTFolhaPagamentoContratoServidorPeriodo->recuperaPorChave($rsContratoServidorPeriodo);
        if ( $rsContratoServidorPeriodo->getNumLinhas() < 0 ) {
            $obTFolhaPagamentoContratoServidorPeriodo->inclusao();
        }

        //Busca todos os registros de evento de férias do servidor para o periodo de movimentação
        $stFiltro .= " AND cod_contrato = ".$request->get("inCodContrato");
        $stFiltro .= " AND cod_periodo_movimentacao = ".$request->get("inCodPeriodoMovimentacao");
        $obTFolhaPagamentoRegistroEventoFerias->recuperaRelacionamento($rsRegistroEventos,$stFiltro);
        $rsRegistroEventos->addFormatacao("valor","NUMERIC_BR");
        $rsRegistroEventos->addFormatacao("quantidade","NUMERIC_BR");

        // Excluir todos os ultimos registros de evento de ferias do servidor cadastrados
        while (!$rsRegistroEventos->eof()) {
            $inCodigoEvento  = $rsRegistroEventos->getCampo("codigo");
            $stDesdobramento = $rsRegistroEventos->getCampo("desdobramento");

            $obTFolhaPagamentoUltimoRegistroEventoFerias->setDado("cod_registro",$rsRegistroEventos->getCampo("cod_registro"));
            $obTFolhaPagamentoUltimoRegistroEventoFerias->setDado("cod_evento",$rsRegistroEventos->getCampo("cod_evento"));
            $obTFolhaPagamentoUltimoRegistroEventoFerias->setDado("desdobramento",$rsRegistroEventos->getCampo("desdobramento"));
            $obTFolhaPagamentoUltimoRegistroEventoFerias->setDado("timestamp",$rsRegistroEventos->getCampo("timestamp"));
            $obTFolhaPagamentoUltimoRegistroEventoFerias->deletarUltimoRegistroEvento();

            $rsRegistroEventos->proximo();
        }

        $arEventos = Sessao::read("arEventos");
        foreach ($arEventos as $arEvento) {
            $stFiltro = " WHERE codigo = '".$arEvento['inCodigoEvento']."'";
            $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);
            $stFiltro = " AND cod_contrato = ".$request->get("inCodContrato");
            $stFiltro .= " AND cod_periodo_movimentacao = ".$request->get("inCodPeriodoMovimentacao");
            $obTFolhaPagamentoRegistroEventoFerias->recuperaRelacionamento($rsRegistroEventos,$stFiltro);

            $obTFolhaPagamentoRegistroEventoFerias->obTFolhaPagamentoContratoServidorPeriodo = &$obTFolhaPagamentoContratoServidorPeriodo;
            $obTFolhaPagamentoRegistroEventoFerias->setDado("cod_registro"            ,$arEvento["inCodRegistro"]);
            $obTFolhaPagamentoRegistroEventoFerias->setDado("cod_evento"              ,$rsEvento->getCampo("cod_evento"));
            $obTFolhaPagamentoRegistroEventoFerias->setDado("valor"                   ,$arEvento["nuValorEvento"]);
            $obTFolhaPagamentoRegistroEventoFerias->setDado("quantidade"              ,$arEvento["nuQuantidadeEvento"]);
            $obTFolhaPagamentoRegistroEventoFerias->setDado("desdobramento"           ,$arEvento["stDesdobramento"]);
            $obTFolhaPagamentoRegistroEventoFerias->setDado("timestamp"               ,"");
            $obTFolhaPagamentoUltimoRegistroEventoFerias->obTFolhaPagamentoRegistroEventoFerias = &$obTFolhaPagamentoRegistroEventoFerias;
            $obTFolhaPagamentoEventoFeriasCalculado->obTFolhaPagamentoUltimoRegistroEventoFerias = &$obTFolhaPagamentoUltimoRegistroEventoFerias;
            $obTFolhaPagamentoEventoFeriasCalculadoDependente->obTFolhaPagamentoEventoFeriasCalculado = &$obTFolhaPagamentoEventoFeriasCalculado;
            $obTFolhaPagamentoLogErroCalculoFerias->obTFolhaPagamentoUltimoRegistroEventoFerias  = &$obTFolhaPagamentoUltimoRegistroEventoFerias;
            $obTFolhaPagamentoRegistroEventoFeriasParcela->obTFolhaPagamentoUltimoRegistroEventoFerias = &$obTFolhaPagamentoUltimoRegistroEventoFerias;
            if ($arEvento["inCodRegistro"] != "") {
                $obTFolhaPagamentoLogErroCalculoFerias->exclusao();
                $obTFolhaPagamentoEventoFeriasCalculadoDependente->exclusao();
                $obTFolhaPagamentoEventoFeriasCalculado->exclusao();
                $obTFolhaPagamentoRegistroEventoFeriasParcela->exclusao();
                $obTFolhaPagamentoUltimoRegistroEventoFerias->exclusao();
            }
            $obTFolhaPagamentoRegistroEventoFerias->inclusao();
            $obTFolhaPagamentoUltimoRegistroEventoFerias->inclusao();
            if ($arEvento["nuQuantidadeParcelasEvento"] > 0) {
                $obTFolhaPagamentoRegistroEventoFeriasParcela->obTFolhaPagamentoUltimoRegistroEventoFerias = &$obTFolhaPagamentoUltimoRegistroEventoFerias;
                $obTFolhaPagamentoRegistroEventoFeriasParcela->setDado("parcela"      ,$arEvento["nuQuantidadeParcelasEvento"]);
                $obTFolhaPagamentoRegistroEventoFeriasParcela->inclusao();
            }
        }
        Sessao::encerraExcecao();
        SistemaLegado::LiberaFrames();
        $stMensagem = "Necessário recalcular a folha. Matrícula: ".$request->get('inRegistro');
        if ($request->get('stOkRetorno') == "lista") {
            sistemaLegado::alertaAviso($pgList,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgFilt,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
        }
    break;
}

?>
