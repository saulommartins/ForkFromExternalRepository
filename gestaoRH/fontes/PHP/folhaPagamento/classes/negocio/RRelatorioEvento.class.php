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
    * Classe de regra de relatório para Evento
    * Data de Criação: 11/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Regra de Relatório

    $Revision: 32866 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO                                                                );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                    );
//include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

class RRelatorioEvento extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRFolhaPagamentoEvento;

/**
     * @access Public
     * @param Object $valor
*/
function setRFolhaPagamentoEvento($valor) { $this->obRFolhaPagamentoEvento = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getRFolhaPagamentoEvento() { return $this->obRFolhaPagamentoEvento;           }

/**
    * Método Construtor
    * @access Private
*/
function RRelatorioEvento()
{
    $this->setRFolhaPagamentoEvento( new RFolhaPagamentoEvento() );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordset)
{
    $arFiltro = Sessao::read("filtroRelatorio");

    if ($arFiltro['inCodEventoInicial'] != "") {
        $this->obRFolhaPagamentoEvento->setCodigos( $arFiltro['inCodEventoInicial'] );
    }
    if ($arFiltro['inCodEventoFinal'] != "") {
        $this->obRFolhaPagamentoEvento->setCodigos( $arFiltro['inCodEventoFinal'] );
    }
    if ($arFiltro['boProvento'] != "") {
        $this->obRFolhaPagamentoEvento->setNaturezas( $arFiltro['boProvento'] );
    }
    if ($arFiltro['boDesconto'] != "") {
        $this->obRFolhaPagamentoEvento->setNaturezas( $arFiltro['boDesconto'] );
    }
    if ($arFiltro['boInformativo'] != "") {
        $this->obRFolhaPagamentoEvento->setNaturezas( $arFiltro['boInformativo'] );
    }
    if ($arFiltro['boBase'] != "") {
        $this->obRFolhaPagamentoEvento->setNaturezas( $arFiltro['boBase'] );
    }
    if ($arFiltro['boFixo'] != "") {
        $this->obRFolhaPagamentoEvento->setTipos( $arFiltro['boFixo'] );
    }
    if ($arFiltro['boVariavel'] != "") {
        $this->obRFolhaPagamentoEvento->setTipos( $arFiltro['boVariavel'] );
    }
    if ($arFiltro['boValor'] != "") {
        $this->obRFolhaPagamentoEvento->setFixados( $arFiltro['boValor'] );
    }
    if ($arFiltro['boQuantidade'] != "") {
        $this->obRFolhaPagamentoEvento->setFixados( $arFiltro['boQuantidade'] );
    }
    if ($arFiltro['inCodSequencia'] != "") {
        $this->obRFolhaPagamentoEvento->obRFolhaPagamentoSequencia->setCodSequencia( $arFiltro['inCodSequencia'] );
    }
    if ($arFiltro['inAno'] != "" and $arFiltro['inCodMes'] != "") {
        $stTimestamp = SistemaLegado::PegaDado('last_day', "last_day('".$arFiltro['inAno']."-".$arFiltro['inCodMes']."-01')", '')." 00:00:00.000";
        $this->obRFolhaPagamentoEvento->setTimestamp( $stTimestamp );
    }

    //Ordenação
    if ($arFiltro['stOrdenacao'] != "") {
        $this->obRFolhaPagamentoEvento->setOrdenacao( $arFiltro['stOrdenacao'] );
    }
    $this->obRFolhaPagamentoEvento->listarEvento($rsEvento,$boTransacao);
    $rsEvento->addFormatacao("valor_quantidade","NUMERIC_BR");
    $rsEvento->addFormatacao("unidade_quantitativa","NUMERIC_BR");

    while ( !$rsEvento->eof() ) {
        switch ( $rsEvento->getCampo('natureza') ) {
            case 'P':
                $stNatureza = "Proventos";
            break;
            case 'D':
                $stNatureza = "Descontos";
            break;
            case 'B':
                $stNatureza = "Base";
            break;
            case 'I':
                $stNatureza = "Informativo";
            break;
        }
        switch ($rsEvento->getCampo('tipo')) {
            case 'F':
                $stTipo = "Fixo";
            break;
            case 'V':
                $stTipo = "Variável";
            break;
        }
        switch ($rsEvento->getCampo('fixado')) {
            case 'V':
                $stFixado = "Valor";
            break;
            case 'Q':
                $stFixado = "Quantidade";
            break;
        }
        $arEvento['evento']     = $rsEvento->getCampo('codigo');
        $arEvento['descricao']  = $rsEvento->getCampo('descricao');
        $arEvento['natureza']   = $stNatureza;
        $arEvento['tipo']       = $stTipo;
        $arEvento['fixado']     = $stFixado;
        $arEvento['quant_valor']= $rsEvento->getCampo('valor_quantidade');
        $arEvento['und_quant']  = $rsEvento->getCampo('unidade_quantitativa');
        $arEvento['sequencia']  = $rsEvento->getCampo('cod_sequencia');

        $this->obRFolhaPagamentoEvento->setCodEvento($rsEvento->getCampo('cod_evento'));
        $this->obRFolhaPagamentoEvento->setTimestamp($rsEvento->getCampo('timestamp'));
        $this->obRFolhaPagamentoEvento->addConfiguracaoEvento();
        $this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao($arFiltro['inCodConfiguracao']);
        $this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->listarConfiguracaoEvento($rsConfiguracao,$boTransacao);

        while ( !$rsConfiguracao->eof() ) {
            $this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao($rsConfiguracao->getCampo('cod_configuracao'));
            $this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->listarRubrica($rsRubrica,$boTransacao);

            $stVariavel1 = "ar".trim($rsConfiguracao->getCampo('descricao'))."1";
            $stVariavel2 = "ar".trim($rsConfiguracao->getCampo('descricao'))."2";

            $$stVariavel1 = array();
            $$stVariavel2 = array();

            while ( !$rsRubrica->eof() ) {

                $arTemp = array();
                $arTemp['rubrica']  = $rsRubrica->getCampo('cod_estrutural');
                $arTemp['descricao']= $rsRubrica->getCampo('descricao');
                $arTemp['formula']  = $rsRubrica->getCampo('nom_funcao');
                $arTeste = $$stVariavel1;
                $arTeste[] = $arTemp;
                $$stVariavel1 = $arTeste;

                if ($arFiltro['boApresentar']) {

                    $this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->addCasoEvento();
                    $this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->setCodCaso( $rsRubrica->getCampo('cod_caso') );
                    $this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao( $rsRubrica->getCampo('cod_configuracao') );
                    $this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->consultarCasoEvento();
                    for ($inIndex=0;$inIndex<count($this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->arRPessoalSubDivisao);$inIndex++) {
                        $obRPessoalSubDivisao = $this->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->arRPessoalSubDivisao[$inIndex];
                        for ($inIndex2=0;$inIndex2<count($obRPessoalSubDivisao->arRPessoalCargo);$inIndex2++) {
                            $obRPessoalCargo = $obRPessoalSubDivisao->arRPessoalCargo[$inIndex2];
                            for ($inIndex3=0;$inIndex3<count($obRPessoalCargo->arRPessoalEspecialidade);$inIndex3++) {
                                $obRPessoalEspecialidade = $obRPessoalCargo->arRPessoalEspecialidade[$inIndex3];
                                $arTemp = array();
                                if ( $arFiltro['inCodEspecialidade'] == "" or $arFiltro['inCodEspecialidade'] == $obRPessoalEspecialidade->getCodEspecialidade() ) {
                                    $arTemp['funcao_especialidade'] = $obRPessoalCargo->getDescricao()."/".$obRPessoalEspecialidade->getDescricaoEspecialidade();
                                    $arTeste = $$stVariavel2;
                                    $arTeste[] = $arTemp;
                                    $$stVariavel2 = $arTeste;
                                }
                            }
                        }
                    }
                }
                $rsRubrica->proximo();
            }
            $rsConfiguracao->proximo();
        }
        $arTemp2['evento'][]             = $arEvento;
        $arTemp2['rubrica']              = $arTemp['rubrica'];
        $arTemp2['descricao']            = $arTemp['descricao'];
        $arTemp2['forumla']              = $arTemp['formula'];
        $arTemp2['funcao_especialidade'] = $arTemp['funcao_especialidade'];

        $rsConfiguracao->setPrimeiroElemento();
        while ( !$rsConfiguracao->eof() ) {
            $stVariavel1 = "ar".trim($rsConfiguracao->getCampo('descricao'))."1";
            $stVariavel2 = "ar".trim($rsConfiguracao->getCampo('descricao'))."2";
            $arTemp2[trim($rsConfiguracao->getCampo('descricao')).'1'] = $$stVariavel1;
            $arTemp2[trim($rsConfiguracao->getCampo('descricao')).'2'] = $$stVariavel2;
            $rsConfiguracao->proximo();
        }
        $arTemp2['bases']    = array();
        $arRecordSet = array();
        $arRecordSet[] = $arTemp2;
        $rsEvento->proximo();
    }

    $rsRecordset = new RecordSet;
    if (is_array($arRecordSet)) {
        $rsRecordset->preenche( $arRecordSet );
    }

    return $obErro;
}

}
