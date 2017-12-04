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
    * Oculto
    * Data de Criação: 28/06/2006

    * @author Projetista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza
    *                        Vandré Miguel Ramos

    * @ignore

    $Revision: 32866 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-01-28 13:03:05 -0200 (Seg, 28 Jan 2008) $

    * Casos de uso: uc-04.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarFichaFinanceira";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function bloqueiaTela()
{
    $stJs ="document.body.scrollTop=0;";
    $stJs.="document.getElementById('fundo_carregando').style.visibility='visible';";
    SistemaLegado::executaFrameOculto($stJs);
}

function gerarSpan1($boExecuta=false)
{
    $obErro = new erro;
    $stJs = '';
    global $request;

    $arLink                   = Sessao::read("link");

    $inCodContrato            = $arLink["inCodContrato"];
    $inCodConfiguracao        = ($arLink["inCodConfiguracao"] != "")?$arLink["inCodConfiguracao"]:0;
    $inCodComplementar        = ($arLink["inCodComplementar"]) ? $arLink["inCodComplementar"] : 0;
    $inCodPeriodoMovimentacao = $arLink["inCodPeriodoMovimentacao"];
    $inCodMes                 = $arLink["inCodMes"];
    $inAno                    = $arLink["inAno"];

    if (trim($inCodPeriodoMovimentacao)=="") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);
        $inCodPeriodoMovimentacao = $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
    }

    if ($inCodContrato != "") {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato = new TPessoalContrato();
        $stFiltro = " AND cod_contrato = ".$inCodContrato;
        $obTPessoalContrato->recuperaCgmDoRegistro($rsCgm,$stFiltro);

        $rsValoresAcumuladosBase = new recordset();
        $rsRotuloValoresAcumuladosBase = new recordset();
        $rsValoresAcumuladosDesconto = new recordset();
        $rsRotuloValoresAcumuladosDesconto = new recordset();

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
        $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();
        $obTFolhaPagamentoEventoCalculado->setDado("cod_periodo_movimentacao",$inCodPeriodoMovimentacao);
        $obTFolhaPagamentoEventoCalculado->setDado("cod_contrato",$rsCgm->getCampo("cod_contrato"));
        $obTFolhaPagamentoEventoCalculado->setDado("cod_configuracao",$inCodConfiguracao);
        $obTFolhaPagamentoEventoCalculado->setDado("cod_complementar",$inCodComplementar);
        $obTFolhaPagamentoEventoCalculado->setDado("ordem","codigo");
        $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculadosFichaFinanceira($rsEventoCalculado);

        switch ($inCodConfiguracao) {
            case 0:
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
                $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado();

                $obTFolhaPagamentoEventoComplementarCalculado->setDado("cod_contrato",$rsCgm->getCampo("cod_contrato"));
                $obTFolhaPagamentoEventoComplementarCalculado->setDado("numcgm",$rsCgm->getCampo("numcgm"));
                $obTFolhaPagamentoEventoComplementarCalculado->setDado("cod_periodo_movimentacao",$inCodPeriodoMovimentacao);
                $obTFolhaPagamentoEventoComplementarCalculado->setDado("natureza",'B');
                $obTFolhaPagamentoEventoComplementarCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosBase,'',' order by codigo');
                $obTFolhaPagamentoEventoComplementarCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosBase);

                $obTFolhaPagamentoEventoComplementarCalculado->setDado("natureza",'D');
                $obTFolhaPagamentoEventoComplementarCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosDesconto,'',' order by codigo');
                $obTFolhaPagamentoEventoComplementarCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosDesconto);
                break;
            case 1:
                $obTFolhaPagamentoEventoCalculado->setDado("cod_contrato",$rsCgm->getCampo("cod_contrato"));
                $obTFolhaPagamentoEventoCalculado->setDado("numcgm",$rsCgm->getCampo("numcgm"));
                $obTFolhaPagamentoEventoCalculado->setDado("cod_periodo_movimentacao",$inCodPeriodoMovimentacao);
                $obTFolhaPagamentoEventoCalculado->setDado("natureza",'B');
                $obTFolhaPagamentoEventoCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosBase,'',' order by codigo');
                $obTFolhaPagamentoEventoCalculado->recuperaValoresAcumuladosCalculoSalarioFamilia($rsValoresAcumuladosBaseSalarioFamilia,'',' order by codigo');
                $obTFolhaPagamentoEventoCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosBase);
                $obTFolhaPagamentoEventoCalculado->recuperaRotuloValoresAcumuladosCalculoSalarioFamilia($rsRotuloValoresAcumuladosBaseSalarioFamilia);

                $obTFolhaPagamentoEventoCalculado->setDado("natureza",'D');
                $obTFolhaPagamentoEventoCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosDesconto,'',' order by codigo');
                $obTFolhaPagamentoEventoCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosDesconto);
                break;
            case 2:
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoFeriasCalculado.class.php");
                $obTFolhaPagamentoEventoFeriasCalculado = new TFolhaPagamentoEventoFeriasCalculado();

                $obTFolhaPagamentoEventoFeriasCalculado->setDado("cod_contrato",$rsCgm->getCampo("cod_contrato"));
                $obTFolhaPagamentoEventoFeriasCalculado->setDado("numcgm",$rsCgm->getCampo("numcgm"));
                $obTFolhaPagamentoEventoFeriasCalculado->setDado("cod_periodo_movimentacao",$inCodPeriodoMovimentacao);
                $obTFolhaPagamentoEventoFeriasCalculado->setDado("natureza",'B');
                $obTFolhaPagamentoEventoFeriasCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosBase,'',' order by codigo');
                $obTFolhaPagamentoEventoFeriasCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosBase);

                $obTFolhaPagamentoEventoFeriasCalculado->setDado("natureza",'D');
                $obTFolhaPagamentoEventoFeriasCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosDesconto,'',' order by codigo');
                $obTFolhaPagamentoEventoFeriasCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosDesconto);
                break;
            case 3:
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoDecimoCalculado.class.php");
                $obTFolhaPagamentoEventoDecimoCalculado = new TFolhaPagamentoEventoDecimoCalculado();

                $obTFolhaPagamentoEventoDecimoCalculado->setDado("cod_contrato",$rsCgm->getCampo("cod_contrato"));
                $obTFolhaPagamentoEventoDecimoCalculado->setDado("numcgm",$rsCgm->getCampo("numcgm"));
                $obTFolhaPagamentoEventoDecimoCalculado->setDado("cod_periodo_movimentacao",$inCodPeriodoMovimentacao);
                $obTFolhaPagamentoEventoDecimoCalculado->setDado("natureza",'B');
                $obTFolhaPagamentoEventoDecimoCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosBase,'',' order by codigo');
                $obTFolhaPagamentoEventoDecimoCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosBase);

                $obTFolhaPagamentoEventoDecimoCalculado->setDado("natureza",'D');
                $obTFolhaPagamentoEventoDecimoCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosDesconto,'',' order by codigo');
                $obTFolhaPagamentoEventoDecimoCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosDesconto);
                break;
            case 4:
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculado.class.php");
                $obTFolhaPagamentoEventoRescisaoCalculado = new TFolhaPagamentoEventoRescisaoCalculado();

                $obTFolhaPagamentoEventoRescisaoCalculado->setDado("cod_contrato",$rsCgm->getCampo("cod_contrato"));
                $obTFolhaPagamentoEventoRescisaoCalculado->setDado("numcgm",$rsCgm->getCampo("numcgm"));
                $obTFolhaPagamentoEventoRescisaoCalculado->setDado("cod_periodo_movimentacao",$inCodPeriodoMovimentacao);
                $obTFolhaPagamentoEventoRescisaoCalculado->setDado("natureza",'B');
                $obTFolhaPagamentoEventoRescisaoCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosBase,'',' order by codigo');
                $obTFolhaPagamentoEventoRescisaoCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosBase);

                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
                $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();
                $obTFolhaPagamentoEventoCalculado->setDado("cod_contrato",$rsCgm->getCampo("cod_contrato"));
                $obTFolhaPagamentoEventoCalculado->setDado("numcgm",$rsCgm->getCampo("numcgm"));
                $obTFolhaPagamentoEventoCalculado->setDado("cod_periodo_movimentacao",$inCodPeriodoMovimentacao);
                $obTFolhaPagamentoEventoCalculado->setDado("natureza",'B');
                $obTFolhaPagamentoEventoCalculado->recuperaValoresAcumuladosCalculoSalarioFamilia($rsValoresAcumuladosBaseSalarioFamilia,'',' order by codigo');
                $obTFolhaPagamentoEventoCalculado->recuperaRotuloValoresAcumuladosCalculoSalarioFamilia($rsRotuloValoresAcumuladosBaseSalarioFamilia);

                $obTFolhaPagamentoEventoRescisaoCalculado->setDado("natureza",'D');
                $obTFolhaPagamentoEventoRescisaoCalculado->recuperaValoresAcumuladosCalculo($rsValoresAcumuladosDesconto,'',' order by codigo');
                $obTFolhaPagamentoEventoRescisaoCalculado->recuperaRotuloValoresAcumuladosCalculo($rsRotuloValoresAcumuladosDesconto);
                break;
        }

        $stTabela0  = "<center>";
        $stTabela0 .= "<table border=0 width=100% >";
        $stTabela0 .= "<tr><td class=labelcenterCabecalho align=right width=20%><font size=-1>Período Consultado</font></td>";
        $stTabela0 .= "<td class=labelleftCabecalho width=80%><font size=-1>"."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".SistemaLegado::mesExtensoBR($inCodMes)." / ".$inAno."</font></td>";
        $stTabela0 .= "</table>";
        $stTabela0 .= "</center>";

        $rsEventos = processarEventos($rsEventoCalculado,1);
        $inCount1  = $rsEventos->getNumLinhas();
        $stTabela1  = "<center>";
        $stTabela1 .= "<table border=0 width=100% >";
        $stTabela1 .= "<tr><td class=labelcenterCabecalho align=right width=10%><font size=-1>Evento</font></td>";
        $stTabela1 .= "<td class=labelcenterCabecalho width=20%><font size=-1>Descrição</font></td>";
        $stTabela1 .= "<td class=labelcenterCabecalho width=10%><font size=-1>Desdobramento</font></td>";
        $stTabela1 .= "<td class=labelcenterCabecalho align=right width=20%><font size=-1>Quantidade</font></td>";
        $stTabela1 .= "<td class=labelcenterCabecalho align=right width=20%><font size=-1>Proventos</font></td>";
        $stTabela1 .= "<td class=labelcenterCabecalho align=right width=20%><font size=-1>Descontos</font></td></tr>";
        while ( !$rsEventos->eof() ) {
            $stTabela1 .= "<tr><td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('codigo')."</td>";
            $stTabela1 .= "<td class= fieldfinanceiro><font size=-1>".$rsEventos->getCampo('descricao')."</font></td>";
            $stTabela1 .= "<td class= fieldfinanceiro><font size=-1>".$rsEventos->getCampo('desdobramento_texto')."</font></td>";
            $stQuantidadeParc = $rsEventos->getCampo("quantidade_total_parcela") != "" ? '/'.$rsEventos->getCampo("quantidade_total_parcela") : "";
            $stTabela1 .= "<td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('quantidade_parcelas').$stQuantidadeParc."</font></td>";
            $stTabela1 .= "<td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('proventos')."</font></td>";
            $stTabela1 .= "<td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('descontos')."</font></td></tr>";
            $rsEventos->proximo();
        }
        $stTabela1 .= "</table>";
        $stTabela1 .= "</center>";

        $rsEventos = processarEventos($rsEventoCalculado,2);
        $inCount2  = $rsEventos->getNumLinhas();
        $stTabela2  = "<center>";
        $stTabela2 .= "<table border=0 width=100% >";
        $stTabela2 .= "<tr><td class=labelcentercabecalho align=right width=10%><font size=-1>Evento</font></td>";
        $stTabela2 .= "<td class=labelcentercabecalho width=20%><font size=-1>Descrição</font></td>";
        $stTabela2 .= "<td class=labelcentercabecalho width=10%><font size=-1>Desdobramento</font></td>";
        $stTabela2 .= "<td class=labelcentercabecalho align=right width=20%><font size=-1>Quantidade</font></td>";
        $stTabela2 .= "<td class=labelcentercabecalho align=right width=40%><font size=-1>Valor</font></td></tr>";
        while ( !$rsEventos->eof() ) {
            $stTabela2 .= "<tr  ><td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('codigo')."</font></td>";
            $stTabela2 .= "<td class= fieldfinanceiro><font size=-1>".$rsEventos->getCampo('descricao')."</font></td>";
            $stTabela2 .= "<td class= fieldfinanceiro><font size=-1>".$rsEventos->getCampo('desdobramento_texto')."</font></td>";
            $stTabela2 .= "<td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('quantidade')."</font></td>";
            $stTabela2 .= "<td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('descontos')."</font></td></tr>";
            $rsEventos->proximo();
        }
        $stTabela2 .= "</table>";
        $stTabela2 .= "</center>";

        $rsEventos = processarEventos($rsEventoCalculado,3);
        $inCount3  = $rsEventos->getNumLinhas();
        $stTabela3  = "<center>";
        $stTabela3 .= "<table border=0 width=100% >";
        $stTabela3 .= "<tr><td class=labelcentercabecalho align=right width=10%><font size=-1>Evento</font></td>";
        $stTabela3 .= "<td class=labelcentercabecalho width=20%><font size=-1>Descrição</font></td>";
        $stTabela3 .= "<td class=labelcentercabecalho width=10%><font size=-1>Desdobramento</font></td>";
        $stTabela3 .= "<td class=labelcentercabecalho align=right width=20%><font size=-1>Quantidade</font></td>";
        $stTabela3 .= "<td class=labelcentercabecalho align=right width=40%><font size=-1>Valor</font></td></tr>";
        while ( !$rsEventos->eof() ) {
            $stTabela3 .= "<tr><td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('codigo')."</font></td>";
            $stTabela3 .= "<td class= fieldfinanceiro ><font size=-1>".$rsEventos->getCampo('descricao')."</font></td>";
            $stTabela3 .= "<td class= fieldfinanceiro><font size=-1>".$rsEventos->getCampo('desdobramento_texto')."</font></td>";
            $stTabela3 .= "<td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('quantidade')."</font></td>";
            $stTabela3 .= "<td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('descontos')."</font></td></tr>";
            $rsEventos->proximo();
        }
        $stTabela3 .= "</table>";
        $stTabela3 .= "</center>";

        $rsEventos = processarEventos($rsEventoCalculado,4);
        $stTabela4  = "<center>";
        $stTabela4 .= "<table border=0 width=100%>";
        while ( !$rsEventos->eof() ) {
            $stTabela4 .= "<tr><td class= fieldfinanceiro><font size=-1>".$rsEventos->getCampo('descricao')."</font></td><td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('proventos')."</font></td><td class= fieldfinanceiro align=right><font size=-1>".$rsEventos->getCampo('descontos')."</font></td></tr>";
            $rsEventos->proximo();
        }
        $stTabela4 .= "</table>";
        $stTabela4 .= "</center>";

        $stTabela5  = "<center>";
        $stTabela5 .= "<table border=0 width=100%>";
        $stTabela5 .= "<tr><td class=labelcentercabecalho align=right width=10%><font size=-1>Evento</font></td><td class=labelcentercabecalho width=50%><font size=-1>Descrição</font></td><td class=labelcentercabecalho align=right width=40%><font size=-1>Valor</font></td></tr>";
        if ($inCount1 == -1 and $inCount2 == -1 and $inCount3 == -1) {
            $rsValoresAcumuladosBase = new RecordSet;
            $stRegistrosBase = "Não há contratos calculados";
        }
        while (!$rsValoresAcumuladosBase->eof()) {
            $stTabela5 .= "<tr><td class= fieldfinanceiro align=right><font size=-1>".$rsValoresAcumuladosBase->getCampo('codigo')."</font></td><td class= fieldfinanceiro><font size=-1>".$rsValoresAcumuladosBase->getCampo('descricao')."</font></td><td class= fieldfinanceiro align=right><font size=-1>".number_format($rsValoresAcumuladosBase->getCampo('valor'),2,',','.')."</font></td></tr>";
            $rsValoresAcumuladosBase->proximo();
        }
        $stTabela5 .= "</table>";
        $stTabela5 .= "</center>";

        if ($inCodConfiguracao == 1 or $inCodConfiguracao == 4) {
            $stTabelaSalarioFamilia  = "<center>";
            $stTabelaSalarioFamilia .= "<table border=0 width=100%>";
            $stTabelaSalarioFamilia .= "<tr><td class=labelcentercabecalho align=right width=10%><font size=-1>Evento</font></td><td class=labelcentercabecalho width=50%><font size=-1>Descrição</font></td><td class=labelcentercabecalho align=right width=40%><font size=-1>Valor</font></td></tr>";

            while (!$rsValoresAcumuladosBaseSalarioFamilia->eof()) {
                $stTabelaSalarioFamilia .= "<tr><td class= fieldfinanceiro align=right><font size=-1>".$rsValoresAcumuladosBaseSalarioFamilia->getCampo('codigo')."</font></td><td class= fieldfinanceiro><font size=-1>".$rsValoresAcumuladosBaseSalarioFamilia->getCampo('descricao')."</font></td><td class= fieldfinanceiro align=right><font size=-1>".number_format($rsValoresAcumuladosBaseSalarioFamilia->getCampo('valor'),2,',','.')."</font></td></tr>";
                $rsValoresAcumuladosBaseSalarioFamilia->proximo();
            }
            $stTabelaSalarioFamilia .= "</table>";
            $stTabelaSalarioFamilia .= "</center>";
        }

        $stTabela6  = "<center>";
        $stTabela6 .= "<table border=0 width=100%>";
        $stTabela6 .= "<tr><td  class=labelcentercabecalho align=right width=10%><font size=-1>Evento</font></td><td class=labelcentercabecalho width=50%><font size=-1>Descrição</font></td><td  class=labelcentercabecalho align=right width=40%><font size=-1>Valor</font></td></tr>";
        if ($inCount1 == -1 and $inCount2 == -1 and $inCount3 == -1) {
            $rsValoresAcumuladosDesconto = new RecordSet;
            $stRegistrosDesconto = "Não há contratos calculados";
        }
        while (!$rsValoresAcumuladosDesconto->eof()) {
            $stTabela6 .= "<tr><td class= fieldfinanceiro align=right><font size=-1>".$rsValoresAcumuladosDesconto->getCampo('codigo')."</font></td ><td class=fieldfinanceiro ><font size=-1>".$rsValoresAcumuladosDesconto->getCampo('descricao')."</font></td><td class= fieldfinanceiro align=right><font size=-1>".number_format($rsValoresAcumuladosDesconto->getCampo('valor'),2,',','.')."</font></td></tr>";
            $rsValoresAcumuladosDesconto->proximo();
        }
        $stTabela6 .= "</table>";
        $stTabela6 .= "</center>";

        $stTabela7  = "<center>";
        $stTabela7 .= "<table border=0 width=100% >";
        $stTabela7 .= "<tr><td class= fieldfinanceiro align=left width=100%><font size=-1>(S)Folha Salário</font></td></tr>";
        $stTabela7 .= "<tr><td class= fieldfinanceiro align=left width=100%><font size=-1>(C)Folha Complementar</font></td></tr>";
        $stTabela7 .= "<tr><td class= fieldfinanceiro align=left width=100%><font size=-1>(F)Folha Férias</font></td></tr>";
        $stTabela7 .= "</table>";
        $stTabela7 .= "</center>";

        $obSpnSpan1_0 = new Span;
        $obSpnSpan1_0->setId                            ( "SpnSpan1_0"                                  );

        $obSpnSpan1_1 = new Span;
        $obSpnSpan1_1->setId                            ( "SpnSpan1_1"                                  );

        $obSpnSpan1_2 = new Span;
        $obSpnSpan1_2->setId                            ( "SpnSpan1_2"                                  );

        $obSpnSpan1_3 = new Span;
        $obSpnSpan1_3->setId                            ( "SpnSpan1_3"                                  );

        $obSpnSpan1_4 = new Span;
        $obSpnSpan1_4->setId                            ( "SpnSpan1_4"                                  );

        $obSpnSpan1_5 = new Span;
        $obSpnSpan1_5->setId                            ( "SpnSpan1_5"                                  );

        $obSpnSpan1_6 = new Span;
        $obSpnSpan1_6->setId                            ( "SpnSpan1_6"                                  );

        $obSpnSpan1_7 = new Span;
        $obSpnSpan1_7->setId                            ( "SpnSpan1_7"                                  );

        $obSpnSalarioFamilia1 = new Span();
        $obSpnSalarioFamilia1->setId("spnSalarioFamilia");
        $obSpnSalarioFamilia1->setValue($stTabelaSalarioFamilia);

        $obFormulario = new Formulario;
        $obFormulario->addTitulo                        ( "Ficha Financeira"                            );
        $obFormulario->addSpan                          ( $obSpnSpan1_0                                 );
        $obFormulario->addTitulo                        ( "Eventos Calculados"                          );
        $obFormulario->addSpan                          ( $obSpnSpan1_1                                 );
        $obFormulario->addTitulo                        ( "Bases de Cálculo"                            );
        $obFormulario->addSpan                          ( $obSpnSpan1_2                                 );
        $obFormulario->addTitulo                        ( "Eventos Informativos"                        );
        $obFormulario->addSpan                          ( $obSpnSpan1_3                                 );
        $obFormulario->addTitulo                        ( "Totais Calculados"                           );
        $obFormulario->addSpan                          ( $obSpnSpan1_4                                 );
        if ( $request->get('inCodConfiguracao') != 3 ) {
            $obFormulario->addTitulo                        ( "Valores Acumulados com o Cálculo da Matrícula","center" );
            $obFormulario->addTitulo                        ( "Matrícula(s): ".$rsRotuloValoresAcumuladosBase->getCampo("rotulo")              ,"center" );
            $obFormulario->addSpan                          ( $obSpnSpan1_5                                 );
            if ($inCodConfiguracao == 1 or $inCodConfiguracao == 4) {
                $obFormulario->addTitulo                        ( "Valores Acumulados para Cálculo do Salário Família","center" );
                $obFormulario->addTitulo                        ( "Matrícula(s): ".$rsRotuloValoresAcumuladosBaseSalarioFamilia->getCampo("rotulo")              ,"center" );
                $obFormulario->addSpan                          ( $obSpnSalarioFamilia1                                 );
            }
            $obFormulario->addTitulo                        ( "Valores Acumulados até o Cálculo da Matrícula","center" );
            $obFormulario->addTitulo                        ( "Matrícula(s): ".$rsRotuloValoresAcumuladosDesconto->getCampo("rotulo")          ,"center" );
            $obFormulario->addSpan                          ( $obSpnSpan1_6                                 );
            $obFormulario->addSpan                          ( $obSpnSpan1_7                                 );
        }
        $obFormulario->montaInnerHtml();
        $stJs .= "d.getElementById('SpnSpan1').innerHTML = '".$obFormulario->getHTML()."';  \n";
        $stJs .= "d.getElementById('SpnSpan1_0').innerHTML = '".$stTabela0."';  \n";
        $stJs .= "d.getElementById('SpnSpan1_1').innerHTML = '".$stTabela1."';  \n";
        $stJs .= "d.getElementById('SpnSpan1_2').innerHTML = '".$stTabela2."';  \n";
        $stJs .= "d.getElementById('SpnSpan1_3').innerHTML = '".$stTabela3."';  \n";
        $stJs .= "d.getElementById('SpnSpan1_4').innerHTML = '".$stTabela4."';  \n";
        if ( $request->get('inCodConfiguracao') != 3 ) {
            $stJs .= "d.getElementById('SpnSpan1_5').innerHTML = '".$stTabela5."';  \n";
            $stJs .= "d.getElementById('SpnSpan1_6').innerHTML = '".$stTabela6."';  \n";
            $stJs .= "d.getElementById('SpnSpan1_7').innerHTML = '".$stTabela7."';  \n";
        }
    }

    $stJs.="document.getElementById('fundo_carregando').style.visibility='hidden';";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;

    }
}

function processarEventos($rsEventos,$inNatureza)
{
    $arEventos = ( $rsEventos->getNumLinhas() > 0 ) ? $rsEventos->getElementos() : array();
    $arTemp = array();
    $stNatureza1 = '';
    $stNatureza2 = '';
    $inCodEvento = 0;
    $boTodos = false;
    switch ($inNatureza) {
        case 1:
            $stNatureza1 = 'P';
            $stNatureza2 = 'D';
        break;
        case 2:
            $stNatureza1 = 'B';
            $stNatureza2 = 'B';
        break;
        case 3:
            $stNatureza1 = 'I';
            $stNatureza2 = 'I';
        break;
        case 4:
            $boTodos = true;
            $nuTotalProventos = 0;
            $nuTotalDescontos = 0;
        break;
    }
    foreach ($arEventos as $arEvento) {
        if( ($arEvento['natureza'] == $stNatureza1 or $arEvento['natureza'] == $stNatureza2)
            and ($arEvento['cod_evento'] != $inCodEvento or $arEvento['desdobramento'] != $stDesdobramento or $arEvento['cod_configuracao'] != $inCodConfiguracao) ){
            if ($arEvento['natureza'] == 'P') {
                $arEvento['proventos'] = $arEvento['valor'];
            } else {
                $arEvento['proventos'] = "0,00";
            }
            if ($arEvento['natureza'] == 'D' or $arEvento['natureza'] == 'B' or $arEvento['natureza'] == 'I') {
                $arEvento['descontos'] = $arEvento['valor'];
            } else {
                $arEvento['descontos'] = "0,00";
            }
            $arTemp[] = $arEvento;
            $stTimestamp        = isset($arEvento['timestamp_registro']) ? $arEvento['timestamp_registro'] : '' ;
            $inCodEvento        = $arEvento['cod_evento'];
            $stDesdobramento    = $arEvento['desdobramento'] ;
            $inCodConfiguracao  = isset($arEvento['cod_configuracao']) ? $arEvento['cod_configuracao'] : '' ;
        }
        if ($boTodos) {
            if ($arEvento['natureza'] == 'P') {
                $nuTotalProventos += $arEvento['valor'];
            }
            if ($arEvento['natureza'] == 'D') {
                $nuTotalDescontos += $arEvento['valor'];
            }
        }
    }
    if ($boTodos) {
        $arTemp[] = array("descricao"=>"Soma dos Proventos","proventos"=>$nuTotalProventos);
        $arTemp[] = array("descricao"=>"Soma dos Descontos","descontos"=>$nuTotalDescontos);
        $arTemp[] = array("descricao"=>"Líquido","proventos"=>$nuTotalProventos-$nuTotalDescontos);

    }
    $rsEventos = new recordset;
    $rsEventos->preenche($arTemp);

    if (!$boTodos) {
        $rsEventos->addFormatacao("quantidade","NUMERIC_BR");
    }
    $rsEventos->addFormatacao("proventos","NUMERIC_BR");
    $rsEventos->addFormatacao("descontos","NUMERIC_BR");

    return $rsEventos;
}

switch ($_REQUEST["stCtrl"]) {
    case "gerarSpan1":
        $stJs = gerarSpan1();
        break;
}
if ($stJs) {
//     sistemaLegado::executaFrameOculto($stJs);
    echo $stJs;
}

?>
