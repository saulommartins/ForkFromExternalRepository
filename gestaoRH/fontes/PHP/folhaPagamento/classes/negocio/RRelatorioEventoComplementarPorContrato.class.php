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
* Classe de regra de relatório para registros de  evento na complementar por contrato
* Data de Criação: 10/03/2006

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: André Almeida

* @package URBEM
* @subpackage Regra de Relatório

$Revision: 30896 $
$Name$
$Author: souzadl $
$Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

* Casos de uso: uc-04.05.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO                                                            );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php"               );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                   );

class RRelatorioEventoComplementarPorContrato extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRFolhaPagamentoPeriodoContratoServidor;

/**
     * @access Public
     * @param Object $valor
*/
function setRFolhaPagamentoPeriodoContratoServidor($valor) { $this->obRFolhaPagamentoPeriodoContratoServidor = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getRFolhaPagamentoPeriodoContratoServidor() { return $this->obRFolhaPagamentoPeriodoContratoServidor;           }

/**
    * Método Construtor
    * @access Private
*/
function RRelatorioEventoComplementarPorContrato()
{
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRelatorioRegistroEventoComplementar)
{
    $arFiltro = Sessao::read("filtroRelatorio");

    $inRegistro           = $arFiltro['inContrato'];
    $inCodMes             = $arFiltro['inCodMes'];
    $inAno                = $arFiltro['inAno'];
    $inCodComplementar    = $arFiltro['inCodComplementar'];
    $arEventos            = array();
    $arEventos["filtro"]  = array();
    $arEventos["base"]    = array();
    $arEventos["eventos"] = array();
    $inCountBase          = 0;
    $inCountEventos       = 0;

    $stMeses = array(1 =>"Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
    if ( substr($inCodMes, 0, 1) == '0' ) {
        $stMes = $stMeses[substr($inCodMes, 1, 1)];
    } else {
        $stMes = $stMeses[$inCodMes];
    }

    //Consulta o CGM a partir do registro
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setRegistro( $inRegistro );
    $obRPessoalServidor->roUltimoContratoServidor->listarContratosServidorResumido( $rsContratoServidor , $boTransacao );

    //Define a string numcgm - nome_do_servidor
    $stCGM = $rsContratoServidor->getCampo("numcgm") ." - ". $rsContratoServidor->getCampo("servidor");

    //Define o código do contrato do servidor
    $inContrato = $rsContratoServidor->getCampo("cod_contrato");

    //Define os objetos e faz a consulta
    $obPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obPeriodoMovimentacao->setDtFinal( $inAno."-".$inCodMes );
    $obPeriodoMovimentacao->listarPeriodoMovimentacao( $rsPeriodoMovimentacao );
    $obPeriodoMovimentacao->setCodPeriodoMovimentacao( $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao") );

    $obPeriodoMovimentacao->addRFolhaPagamentoPeriodoContratoServidor();
    $obPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->setCodContrato( $inContrato );

    $obFolhaComplementar = new RFolhaPagamentoFolhaComplementar( $obPeriodoMovimentacao );
    $obFolhaComplementar->setCodComplementar($inCodComplementar);

    $obRegistroEventoComplementar = new RFolhaPagamentoRegistroEventoComplementar( $obFolhaComplementar );
    $obRegistroEventoComplementar->listarRegistroEventoComplementar( $rsRegistroEventoComplementar );

    $arEventos["filtro"][0]["campo"] = "CGM:";
    $arEventos["filtro"][0]["valor"] = $stCGM;
    $arEventos["filtro"][1]["campo"] = "Matrícula:";
    $arEventos["filtro"][1]["valor"] = $inRegistro;
    $arEventos["filtro"][2]["campo"] = "Competência:";
    $arEventos["filtro"][2]["valor"] = $stMes."/".$inAno;
    $arEventos["filtro"][3]["campo"] = "Complementar:";
    $arEventos["filtro"][3]["valor"] = $inCodComplementar;

    //Algoritmo para separar o record set em 3 para as listas.
    $rsRegistroEventoComplementar->setPrimeiroElemento();
    while ( !$rsRegistroEventoComplementar->eof() ) {
        if ( $rsRegistroEventoComplementar->getCampo("natureza") == "B" ) {
            $arEventos["base"][$inCountBase]["codigo"]     = $rsRegistroEventoComplementar->getCampo("codigo");
            $arEventos["base"][$inCountBase]["descricao"]  = $rsRegistroEventoComplementar->getCampo("descricao");
            $arEventos["base"][$inCountBase]["valor"]      = $rsRegistroEventoComplementar->getCampo("valor");
            $arEventos["base"][$inCountBase]["quantidade"] = $rsRegistroEventoComplementar->getCampo("quantidade");
    //         if ( $rsRegistroEvento->getCampo("automatico") == "t" )
    //             $arBaseCalculo[$inCountBase]["automatico"] = "Sim";
    //         else
    //             $arBaseCalculo[$inCountBase]["automatico"] = "Não";

            $inCountBase = $inCountBase + 1;
        } else {
            $arEventos["eventos"][$inCountEventos]["codigo"]     = $rsRegistroEventoComplementar->getCampo("codigo");
            $arEventos["eventos"][$inCountEventos]["descricao"]  = $rsRegistroEventoComplementar->getCampo("descricao");
            $arEventos["eventos"][$inCountEventos]["valor"]      = $rsRegistroEventoComplementar->getCampo("valor");
            $arEventos["eventos"][$inCountEventos]["quantidade"] = $rsRegistroEventoComplementar->getCampo("quantidade");
/*            if ( $rsRegistroEventoComplementar->getCampo("automatico") == "t" )
                $arEventos[][$inCountEventos]["automatico"] = "Sim";
            else
                $arEventos[][$inCountEventos]["automatico"] = "Não";*/
            $inCountEventos = $inCountEventos + 1;
        }
        $rsRegistroEventoComplementar->proximo();
    }

    $rsRelatorioRegistroEventoComplementar = new RecordSet;
    $rsRelatorioRegistroEventoComplementar->preenche($arEventos);

    return $obErro;
}

}
