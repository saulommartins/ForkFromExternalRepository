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
    * Página de Processamento do Registro de Evento de Rescisão
    * Data de Criação: 17/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: melo $
    $Date: 2007-07-24 14:47:15 -0300 (Ter, 24 Jul 2007) $

    * Casos de uso: uc-04.05.54
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoRescisao.class.php"                   );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoRescisao.class.php"             );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php"                                 );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorPeriodo.class.php"                );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoRescisaoParcela.class.php"            );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculado.class.php"                  );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculadoDependente.class.php"        );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoRescisao.class.php"                   );

$link = Sessao::read("link");
$stAcao = $request->get("stAcao");
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEventoRescisao";
$pgForm = "FM".$stPrograma.".php";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obTFolhaPagamentoRegistroEventoRescisao          = new TFolhaPagamentoRegistroEventoRescisao;
$obTFolhaPagamentoUltimoRegistroEventoRescisao    = new TFolhaPagamentoUltimoRegistroEventoRescisao;
$obTFolhaPagamentoEvento                          = new TFolhaPagamentoEvento;
$obTFolhaPagamentoContratoServidorPeriodo         = new TFolhaPagamentoContratoServidorPeriodo;
$obTFolhaPagamentoRegistroEventoRescisaoParcela   = new TFolhaPagamentoRegistroEventoRescisaoParcela;
$obTFolhaPagamentoEventoRescisaoCalculado         = new TFolhaPagamentoEventoRescisaoCalculado;
$obTFolhaPagamentoEventoRescisaoCalculadoDependente = new TFolhaPagamentoEventoRescisaoCalculadoDependente;
$obTFolhaPagamentoLogErroCalculoRescisao          = new TFolhaPagamentoLogErroCalculoRescisao;

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
        $obTFolhaPagamentoRegistroEventoRescisao->recuperaRelacionamento($rsRegistroEventos,$stFiltro);
        $rsRegistroEventos->addFormatacao("valor","NUMERIC_BR");
        $rsRegistroEventos->addFormatacao("quantidade","NUMERIC_BR");

        //Excluir todos os eventos de rescisão do contrato
        while (!$rsRegistroEventos->eof()) {
            $inCodigoEvento  = $rsRegistroEventos->getCampo("codigo");
            $stDesdobramento = $rsRegistroEventos->getCampo("desdobramento");

            $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("cod_registro",$rsRegistroEventos->getCampo("cod_registro"));
            $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("cod_evento",$rsRegistroEventos->getCampo("cod_evento"));
            $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("desdobramento",$rsRegistroEventos->getCampo("desdobramento"));
            $obTFolhaPagamentoUltimoRegistroEventoRescisao->setDado("timestamp",$rsRegistroEventos->getCampo("timestamp"));
            $obTFolhaPagamentoUltimoRegistroEventoRescisao->deletarUltimoRegistroEvento();

            $rsRegistroEventos->proximo();
        }

        $arEventos = Sessao::read("arEventos");
        foreach ($arEventos as $arEvento) {
            $stFiltro = " WHERE codigo = '".$arEvento['inCodigoEvento']."'";
            $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);
            $stFiltro = " AND cod_contrato = ".$request->get("inCodContrato");
            $obTFolhaPagamentoRegistroEventoRescisao->recuperaRelacionamento($rsRegistroEventos,$stFiltro);

            $obTFolhaPagamentoRegistroEventoRescisao->obTFolhaPagamentoContratoServidorPeriodo = &$obTFolhaPagamentoContratoServidorPeriodo;
            $obTFolhaPagamentoRegistroEventoRescisao->setDado("cod_registro"            ,$arEvento["inCodRegistro"]);
            $obTFolhaPagamentoRegistroEventoRescisao->setDado("cod_evento"              ,$rsEvento->getCampo("cod_evento"));
            $obTFolhaPagamentoRegistroEventoRescisao->setDado("valor"                   ,$arEvento["nuValorEvento"]);
            $obTFolhaPagamentoRegistroEventoRescisao->setDado("quantidade"              ,$arEvento["nuQuantidadeEvento"]);
            $obTFolhaPagamentoRegistroEventoRescisao->setDado("desdobramento"           ,$arEvento["stDesdobramento"]);
            $obTFolhaPagamentoRegistroEventoRescisao->setDado("timestamp"               ,"");

            $obTFolhaPagamentoUltimoRegistroEventoRescisao->obTFolhaPagamentoRegistroEventoRescisao = &$obTFolhaPagamentoRegistroEventoRescisao;
            $obTFolhaPagamentoEventoRescisaoCalculado->obTFolhaPagamentoUltimoRegistroEventoRescisao = &$obTFolhaPagamentoUltimoRegistroEventoRescisao;
            $obTFolhaPagamentoEventoRescisaoCalculadoDependente->obTFolhaPagamentoEventoRescisaoCalculado = &$obTFolhaPagamentoEventoRescisaoCalculado;
            $obTFolhaPagamentoLogErroCalculoRescisao->obTFolhaPagamentoUltimoRegistroEventoRescisao  = &$obTFolhaPagamentoUltimoRegistroEventoRescisao;
            $obTFolhaPagamentoRegistroEventoRescisaoParcela->obTFolhaPagamentoUltimoRegistroEventoRescisao = &$obTFolhaPagamentoUltimoRegistroEventoRescisao;

            $obTFolhaPagamentoRegistroEventoRescisao->inclusao();
            $obTFolhaPagamentoUltimoRegistroEventoRescisao->inclusao();

            if ($arEvento["nuQuantidadeParcelasEvento"] > 0) {
                $obTFolhaPagamentoRegistroEventoRescisaoParcela->obTFolhaPagamentoUltimoRegistroEventoRescisao = &$obTFolhaPagamentoUltimoRegistroEventoRescisao;
                $obTFolhaPagamentoRegistroEventoRescisaoParcela->setDado("parcela" ,$arEvento["nuQuantidadeParcelasEvento"]);
                $obTFolhaPagamentoRegistroEventoRescisaoParcela->inclusao();
            }
        }
        Sessao::encerraExcecao();
        $stMensagem = "Necessário recalcular a folha. Matrícula: ".$request->get('inRegistro');
        if ($request->get('stOkRetorno') == "lista") {
            sistemaLegado::alertaAviso($pgList,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgFilt,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
        }
    break;
}

?>
