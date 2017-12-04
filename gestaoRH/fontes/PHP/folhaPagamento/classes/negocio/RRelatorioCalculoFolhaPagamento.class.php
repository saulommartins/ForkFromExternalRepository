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
* Classe de regra de relatório para calculo de folha de pagamento
* Data de Criação: 08/12/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra de Relatório

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

* Casos de uso: uc-04.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO                                                                );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalculoFolhaPagamento.class.php"                     );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                    );

class RRelatorioCalculoFolhaPagamento extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRFolhaPagamentoCalculoFolhaPagamento;
/**
    * @var Object
    * @access Private
*/
var $obRFolhaPagamentoEvento;

/**
     * @access Public
     * @param Object $valor
*/
function setRFolhaPagamentoCalculoFolhaPagamento($valor) { $this->obRFolhaPagamentoCalculoFolhaPagamento = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRFolhaPagamentoEvento($valor) { $this->obRFolhaPagamentoEvento                = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getRFolhaPagamentoCalculoFolhaPagamento() { return $this->obRFolhaPagamentoCalculoFolhaPagamento;           }
/**
     * @access Public
     * @param Object $valor
*/
function getRFolhaPagamentoEvento() { return $this->obRFolhaPagamentoEvento;                          }

/**
    * Método Construtor
    * @access Private
*/
function RRelatorioCalculoFolhaPagamento()
{
    $this->setRFolhaPagamentoCalculoFolhaPagamento( new RFolhaPagamentoCalculoFolhaPagamento() );
    $this->obRFolhaPagamentoCalculoFolhaPagamento->setRORFolhaPagamentoPeriodoMovimentacao( new RFolhaPagamentoPeriodoMovimentacao );
    $this->setRFolhaPagamentoEvento               ( new RFolhaPagamentoEvento()                );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordset)
{
    $this->obRFolhaPagamentoCalculoFolhaPagamento->listarLogErroCalculo($rsLogErro);
    $arLogErro  = array();
    $arRecordSet = array();
    while ( !$rsLogErro->eof() ) {
        $rsLogErro->proximo();
        $inCodRegistroProx = $rsLogErro->getCampo('cod_registro');
        $rsLogErro->anterior();
        $this->obRFolhaPagamentoEvento->setCodEvento($rsLogErro->getCampo('cod_evento'));
        $this->obRFolhaPagamentoEvento->listarEvento($rsEvento);
        $stErro = $rsLogErro->getCampo('erro');
        $stErro = str_replace( chr(10), "", $stErro );
        $stErro = wordwrap( $stErro, 60, chr(13) );
        $arErro = explode( chr(13), $stErro );
        $arTempLogErro['evento']    = $rsEvento->getCampo('codigo');
        $arTempLogErro['descricao'] = $rsEvento->getCampo('descricao');
        $arTempLogErro['erro']      = $arErro[0];
        $arLogErro[]                = $arTempLogErro;
        if ( count( $arErro ) > 1 ) {
            for ($inIndex=1;$inIndex<=count($arErro);$inIndex++) {
                $arTempLogErro['evento']    = "";
                $arTempLogErro['descricao'] = "";
                $arTempLogErro['erro']      = $arErro[$inIndex];
                $arLogErro[]                = $arTempLogErro;
            }
        }
        if ( $inCodRegistroProx != $rsLogErro->getCampo('cod_registro') ) {
            $arContrato              = array();
            $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoPeriodoContratoServidor();
            $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->setCodContrato($rsLogErro->getCampo('cod_registro'));
            $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->listarContratosServidorResumido($rsContratoServidor);
            $arContrato[0]['campo1'] = "Matrícula:";
            $arContrato[0]['campo2'] = $rsContratoServidor->getCampo('registro');
            $arContrato[1]['campo1'] = "CGM:";
            $arContrato[1]['campo2'] = $rsContratoServidor->getCampo('numcgm') ." - ".  $rsContratoServidor->getCampo('servidor');
            $rsContrato              = new recordset;
            $rsContrato->preenche( $arContrato );

            $rsErros                 = new recordset;
            $rsErros->preenche( $arLogErro );

            $arTemp                  = array();
            $arTemp['contrato']      = $rsContrato;
            $arTemp['erros']         = $rsErros;
            $arRecordSet[]           = $arTemp;
            $arLogErro               = array();
        }

        $rsLogErro->proximo();
    }
    $rsRecordset = new RecordSet;
    $rsRecordset->preenche( $arRecordSet );

    return $obErro;
}

}
