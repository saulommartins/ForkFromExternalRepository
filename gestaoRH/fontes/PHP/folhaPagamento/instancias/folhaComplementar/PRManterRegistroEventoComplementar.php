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
    * Processamento de Manter Registro de Evento (Folha Complementar)
    * Data de Criação: 20/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30766 $
    $Name$
    $Author: souzadl $
    $Date: 2008-01-16 17:02:08 -0200 (Qua, 16 Jan 2008) $

    * Casos de uso: uc-04.05.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaComplementar.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                     );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"                                    );

$link = Sessao::read("link");
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=alterar";
//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEventoComplementar";
$pgFilt = "FL".$stPrograma.".php?stAcao=alterar";
$pgList = "LS".$stPrograma.".php?".$stLink;
$pgForm = "FM".$stPrograma.".php?";
$pgProc = "PR".$stPrograma.".php?";
$pgOcul = "OC".$stPrograma.".php?";
$pgJS   = "JS".$stPrograma.".js";
$stAcao = $request->get("stAcao");

$obRFolhaPagamentoFolhaComplementar = new RFolhaPagamentoFolhaComplementar( new RFolhaPagamentoPeriodoMovimentacao );
switch ($stAcao) {
    case "incluir":
        Sessao::setTrataExcecao(true);
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementar.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoComplementar.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoComplementarParcela.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculadoDependente.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculoComplementar.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
        $obTPessoalContrato = new TPessoalContrato;
        $stFiltro = " WHERE registro = ".Sessao::read('inContrato');
        $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorComplementar.class.php");
        $obTFolhaPagamentoContratoServidorComplementar = new TFolhaPagamentoContratoServidorComplementar;
        $obTFolhaPagamentoContratoServidorComplementar->setDado("cod_contrato"              ,$rsContrato->getCampo("cod_contrato"));
        $obTFolhaPagamentoContratoServidorComplementar->setDado("cod_complementar"          ,$request->get('inCodComplementar'));
        $obTFolhaPagamentoContratoServidorComplementar->setDado("cod_periodo_movimentacao"  ,$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
        $obTFolhaPagamentoContratoServidorComplementar->recuperaPorChave($rsContratoServidorComplementar);
        if ( $rsContratoServidorComplementar->getNumLinhas() < 0 ) {
            $obTFolhaPagamentoContratoServidorComplementar->inclusao();
        }

        //Excluir todos registros de evento complementar para o contrato
        $stFiltro  = " AND cod_contrato = ".$rsContrato->getCampo("cod_contrato");
        $stFiltro .= " AND cod_periodo_movimentacao = ".$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
        $stFiltro .= " AND cod_complementar = ".$request->get('inCodComplementar');

        $obTFolhaPagamentoUltimoRegistroEventoComplementar = new TFolhaPagamentoUltimoRegistroEventoComplementar;
        $obTFolhaPagamentoUltimoRegistroEventoComplementar->recuperaRelacionamento($rsRegistroEventoComplementar,$stFiltro);

        while ( !$rsRegistroEventoComplementar->eof() ) {
            $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_registro"      ,$rsRegistroEventoComplementar->getCampo("cod_registro"));
            $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_evento"        ,$rsRegistroEventoComplementar->getCampo("cod_evento"));
            $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("cod_configuracao"  ,$rsRegistroEventoComplementar->getCampo("cod_configuracao"));
            $obTFolhaPagamentoUltimoRegistroEventoComplementar->setDado("timestamp"         ,$rsRegistroEventoComplementar->getCampo("timestamp"));
            $obTFolhaPagamentoUltimoRegistroEventoComplementar->deletarUltimoRegistroEvento();

            $rsRegistroEventoComplementar->proximo();
        }

        $arCodigoEventos = array();
        $arEventos = Sessao::read("eventos");
        foreach ($arEventos as $arEvento) {
            $nuValor        = ( $arEvento['nuValor']        != "" ) ? $arEvento['nuValor']      : 0;
            $nuQuantidade   = ( $arEvento['nuQuantidade']   != "" ) ? $arEvento['nuQuantidade'] : 0;
            $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
            $stFiltro = " WHERE codigo = '".$arEvento['inCodigo']."'";
            $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);

            $obTFolhaPagamentoRegistroEventoComplementar = new TFolhaPagamentoRegistroEventoComplementar;
            $obTFolhaPagamentoRegistroEventoComplementarParcela = new TFolhaPagamentoRegistroEventoComplementarParcela;
            $obTFolhaPagamentoUltimoRegistroEventoComplementar = new TFolhaPagamentoUltimoRegistroEventoComplementar;
            $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado;
            $obTFolhaPagamentoEventoComplementarCalculadoDependente = new TFolhaPagamentoEventoComplementarCalculadoDependente();
            $obTFolhaPagamentoLogErroCalculoComplementar = new TFolhaPagamentoLogErroCalculoComplementar;
            $obTFolhaPagamentoRegistroEventoComplementar->obTFolhaPagamentoContratoServidorComplementar = &$obTFolhaPagamentoContratoServidorComplementar;
            $obTFolhaPagamentoUltimoRegistroEventoComplementar->obTFolhaPagamentoRegistroEventoComplementar = &$obTFolhaPagamentoRegistroEventoComplementar;
            $obTFolhaPagamentoRegistroEventoComplementarParcela->obTFolhaPagamentoUltimoRegistroEventoComplementar = &$obTFolhaPagamentoUltimoRegistroEventoComplementar;
            $obTFolhaPagamentoLogErroCalculoComplementar->obTFolhaPagamentoUltimoRegistroEventoComplementar = &$obTFolhaPagamentoUltimoRegistroEventoComplementar;
            $obTFolhaPagamentoEventoComplementarCalculado->obTFolhaPagamentoUltimoRegistroEventoComplementar = &$obTFolhaPagamentoUltimoRegistroEventoComplementar;
            $obTFolhaPagamentoEventoComplementarCalculadoDependente->obTFolhaPagamentoEventoComplementarCalculado = &$obTFolhaPagamentoEventoComplementarCalculado;

            $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_evento"      ,$rsEvento->getCampo("cod_evento"));
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("cod_configuracao",$arEvento['inCodConfiguracao']);
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("valor"           , $nuValor);
            $obTFolhaPagamentoRegistroEventoComplementar->setDado("quantidade"      , $nuQuantidade);
            $obTFolhaPagamentoRegistroEventoComplementar->inclusao();
            $obTFolhaPagamentoUltimoRegistroEventoComplementar->inclusao();
            if ($arEvento['inQuantidadeParc'] != "") {
                $obTFolhaPagamentoRegistroEventoComplementarParcela->setDado("parcela"  ,$arEvento['inQuantidadeParc']);
                $obTFolhaPagamentoRegistroEventoComplementarParcela->inclusao();
            }
        }

        Sessao::encerraExcecao();
        $stMensagem = "Necessário recalcular a folha. Matrícula: ".Sessao::read('inContrato');
        if ($request->get('stOkRetorno') == "lista") {
            sistemaLegado::alertaAviso($pgList,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgFilt,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
        }
    break;
}
?>
