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
    * Página de Processamento do Registro de Evento de Décimo Terceiro
    * Data de Criação: 06/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30711 $
    $Name$
    $Author: souzadl $
    $Date: 2008-04-09 09:28:09 -0300 (Qua, 09 Abr 2008) $

    * Casos de uso: uc-04.05.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoDecimo.class.php"                   );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoDecimo.class.php"             );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php"                                 );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorPeriodo.class.php"                );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoDecimoParcela.class.php"            );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoDecimoCalculado.class.php"                  );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoDecimoCalculadoDependente.class.php"        );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoDecimo.class.php"                   );

$stAcao = $request->get("stAcao");
$link = Sessao::read("link");
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEventoDecimo";
$pgForm = "FM".$stPrograma.".php";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obTFolhaPagamentoRegistroEventoDecimo          = new TFolhaPagamentoRegistroEventoDecimo;
$obTFolhaPagamentoUltimoRegistroEventoDecimo    = new TFolhaPagamentoUltimoRegistroEventoDecimo;
$obTFolhaPagamentoEvento                        = new TFolhaPagamentoEvento;
$obTFolhaPagamentoContratoServidorPeriodo       = new TFolhaPagamentoContratoServidorPeriodo;
$obTFolhaPagamentoRegistroEventoDecimoParcela   = new TFolhaPagamentoRegistroEventoDecimoParcela;
$obTFolhaPagamentoEventoDecimoCalculado         = new TFolhaPagamentoEventoDecimoCalculado;
$obTFolhaPagamentoEventoDecimoCalculadoDependente = new TFolhaPagamentoEventoDecimoCalculadoDependente;
$obTFolhaPagamentoLogErroCalculoDecimo          = new TFolhaPagamentoLogErroCalculoDecimo;

switch ($stAcao) {
    case "alterar":
        Sessao::setTrataExcecao(true);
        $obTFolhaPagamentoContratoServidorPeriodo->setDado("cod_periodo_movimentacao"   ,$request->get("inCodPeriodoMovimentacao"));
        $obTFolhaPagamentoContratoServidorPeriodo->setDado("cod_contrato"               ,$request->get("inCodContrato"));
        $obTFolhaPagamentoContratoServidorPeriodo->recuperaPorChave($rsContratoServidorPeriodo);
        if ( $rsContratoServidorPeriodo->getNumLinhas() < 0 ) {
            $obTFolhaPagamentoContratoServidorPeriodo->inclusao();
        }
        $stFiltro = " AND cod_contrato = ".$request->get("inCodContrato");
        $stFiltro.= " AND cod_periodo_movimentacao = ".$request->get("inCodPeriodoMovimentacao");
        $obTFolhaPagamentoRegistroEventoDecimo->recuperaRelacionamento($rsRegistroEventos,$stFiltro);
        $rsRegistroEventos->addFormatacao("valor","NUMERIC_BR");
        $rsRegistroEventos->addFormatacao("quantidade","NUMERIC_BR");

        //Excluir todos os eventos de decimo do contrato
        while (!$rsRegistroEventos->eof()) {
            $inCodigoEvento  = $rsRegistroEventos->getCampo("codigo");
            $stDesdobramento = $rsRegistroEventos->getCampo("desdobramento");

            $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("cod_registro",$rsRegistroEventos->getCampo("cod_registro"));
            $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("cod_evento",$rsRegistroEventos->getCampo("cod_evento"));
            $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("desdobramento",$rsRegistroEventos->getCampo("desdobramento"));
            $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("timestamp",$rsRegistroEventos->getCampo("timestamp"));
            $obTFolhaPagamentoUltimoRegistroEventoDecimo->deletarUltimoRegistroEvento();

            $rsRegistroEventos->proximo();
        }

        $arEventos = Sessao::read("arEventos");
        foreach ($arEventos as $arEvento) {
            $stFiltro = " WHERE codigo = '".$arEvento['inCodigoEvento']."'";
            $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);
            $stFiltro = " AND cod_contrato = ".$request->get("inCodContrato");
            $obTFolhaPagamentoRegistroEventoDecimo->recuperaRelacionamento($rsRegistroEventos,$stFiltro);

            $obTFolhaPagamentoRegistroEventoDecimo->obTFolhaPagamentoContratoServidorPeriodo = &$obTFolhaPagamentoContratoServidorPeriodo;
            $obTFolhaPagamentoRegistroEventoDecimo->setDado("cod_registro"            ,$arEvento["inCodRegistro"]);
            $obTFolhaPagamentoRegistroEventoDecimo->setDado("cod_evento"              ,$rsEvento->getCampo("cod_evento"));
            $obTFolhaPagamentoRegistroEventoDecimo->setDado("valor"                   ,$arEvento["nuValorEvento"]);
            $obTFolhaPagamentoRegistroEventoDecimo->setDado("quantidade"              ,$arEvento["nuQuantidadeEvento"]);
            $obTFolhaPagamentoRegistroEventoDecimo->setDado("desdobramento"           ,$arEvento["stDesdobramento"]);
            $obTFolhaPagamentoRegistroEventoDecimo->setDado("timestamp"               ,"");
            $obTFolhaPagamentoUltimoRegistroEventoDecimo->obTFolhaPagamentoRegistroEventoDecimo = &$obTFolhaPagamentoRegistroEventoDecimo;
            $obTFolhaPagamentoEventoDecimoCalculado->obTFolhaPagamentoUltimoRegistroEventoDecimo = &$obTFolhaPagamentoUltimoRegistroEventoDecimo;
            $obTFolhaPagamentoEventoDecimoCalculadoDependente->obTFolhaPagamentoEventoDecimoCalculado = &$obTFolhaPagamentoEventoDecimoCalculado;
            $obTFolhaPagamentoLogErroCalculoDecimo->obTFolhaPagamentoUltimoRegistroEventoDecimo  = &$obTFolhaPagamentoUltimoRegistroEventoDecimo;
            $obTFolhaPagamentoRegistroEventoDecimoParcela->obTFolhaPagamentoUltimoRegistroEventoDecimo = &$obTFolhaPagamentoUltimoRegistroEventoDecimo;
            if ($arEvento["inCodRegistro"] != "") {
                $obTFolhaPagamentoLogErroCalculoDecimo->exclusao();
                $obTFolhaPagamentoEventoDecimoCalculadoDependente->exclusao();
                $obTFolhaPagamentoEventoDecimoCalculado->exclusao();
                $obTFolhaPagamentoRegistroEventoDecimoParcela->exclusao();
                $obTFolhaPagamentoUltimoRegistroEventoDecimo->exclusao();
            }
            $obTFolhaPagamentoRegistroEventoDecimo->inclusao();
            $obTFolhaPagamentoUltimoRegistroEventoDecimo->inclusao();
            if ($arEvento["nuQuantidadeParcelasEvento"] > 0) {
                $obTFolhaPagamentoRegistroEventoDecimoParcela->obTFolhaPagamentoUltimoRegistroEventoDecimo = &$obTFolhaPagamentoUltimoRegistroEventoDecimo;
                $obTFolhaPagamentoRegistroEventoDecimoParcela->setDado("parcela"      ,$arEvento["nuQuantidadeParcelasEvento"]);
                $obTFolhaPagamentoRegistroEventoDecimoParcela->inclusao();
            }
        }
        Sessao::encerraExcecao();
        SistemaLegado::LiberaFrames();
        $stMensagem = "Necessário recalcular a folha. Matrícula: ".Sessao::read('inContrato');        
        if ($request->get('stOkRetorno') == "lista") {
            sistemaLegado::alertaAviso($pgList,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgFilt,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
        }
    break;
}

?>
