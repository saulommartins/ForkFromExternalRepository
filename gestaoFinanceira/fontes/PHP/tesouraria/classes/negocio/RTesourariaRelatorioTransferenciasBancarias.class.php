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
    * Classe de Regra do Relatório de Situação de Empenho
    * Data de Criação   : 30/11/2005

    * @author Analista: Lucas Leusin Oiagem
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Regra

    $Revision: 30835 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

	$Id: RTesourariaRelatorioTransferenciasBancarias.class.php 64153 2015-12-09 19:16:02Z evandro $

    * Casos de uso: uc-02.04.16
*/

/*
$Log$
Revision 1.6  2006/07/05 20:38:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO   );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"             );
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );

/**
    * Classe de Regra de Negócios Transferencias Bancarias
    * @author Desenvolvedor: Jose Eduardo Porto
*/
class RTesourariaRelatorioTransferenciasBancarias extends PersistenteRelatorio
{
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var String
    * @access Private
*/
var $stEntidade;
/**
    * @var String
    * @access Private
*/
var $stDataInicial;
/**
    * @var String
    * @access Private
*/
var $stDataFinal;
/**
    * @var String
    * @access Private
*/
var $stFiltro;
/**
    * @var Integer
    * @access Private
*/
var $inContaBancoInicial;
/**
    * @var Integer
    * @access Private
*/
var $inContaBancoFinal;
/**
    * @var Integer
    * @access Private
*/
var $inCodTipoTransferencia;

/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio        = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setEntidade($valor) { $this->stEntidade           = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataInicial($valor) { $this->stDataInicial      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDataFinal($valor) { $this->stDataFinal      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setFiltro($valor) { $this->stFiltro      = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setContaBancoInicial($valor) { $this->inContaBancoInicial= $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setContaBancoFinal($valor) { $this->inContaBancoFinal= $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodTipoTransferencia($valor) { $this->inCodTipoTransferencia = $valor; }

/*
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;                      }
/*
    * @access Public
    * @return String
*/
function getEntidade() { return $this->stEntidade;                      }
/*
    * @access Public
    * @return String
*/
function getDataInicial() { return $this->stDataInicial;                      }
/*
    * @access Public
    * @return String
*/
function getDataFinal() { return $this->stDataFinal;                      }
/*
    * @access Public
    * @return String
*/
function getFiltro() { return $this->stFiltro;                      }
/*
    * @access Public
    * @return Integer
*/
function getContaBancoInicial() { return $this->inContaBancoInicial;   }
/*
    * @access Public
    * @return Integer
*/
function getContaBancoFinal() { return $this->inContaBancoFinal;                      }
/*
    * @access Public
    * @return Integer
*/
function getCodTipoTransferencia() { return $this->inCodTipoTransferencia;                 }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaRelatorioTransferenciasBancarias()
{
    $this->obRTesourariaBoletim            = new RTesourariaBoletim;
    $this->obRRelatorio                    = new RRelatorio;
    $this->obRTesourariaBoletim->addArrecadacao();
    $this->obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );

}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet, $stOrder = '')
{
    include_once CAM_GF_TES_MAPEAMENTO.'FTesourariaTransferenciasBancarias.class.php';
    $obFTesourariaTransferenciasBancarias = new FTesourariaTransferenciasBancarias;
    
    $obFTesourariaTransferenciasBancarias->setDado('stExercicio'           , $this->getExercicio());
    $obFTesourariaTransferenciasBancarias->setDado('stEntidade'            , $this->getEntidade());
    $obFTesourariaTransferenciasBancarias->setDado('stDataInicial'         , $this->getDataInicial());
    $obFTesourariaTransferenciasBancarias->setDado('stDataFinal'           , $this->getDataFinal());
    $obFTesourariaTransferenciasBancarias->setDado('inContaBancoInicial'   , $this->getContaBancoInicial());
    $obFTesourariaTransferenciasBancarias->setDado('inContaBancoFinal'     , $this->getContaBancoFinal());
    $obFTesourariaTransferenciasBancarias->setDado('inCodTipoTransferencia', $this->getCodTipoTransferencia());
    $obFTesourariaTransferenciasBancarias->setDado('boUtilizaEstruturalTCE', 'false');

    if (Sessao::getExercicio() > '2012') {
        $obFTesourariaTransferenciasBancarias->setDado("boUtilizaEstruturalTCE", 'true' );
    }

    $obErro = $obFTesourariaTransferenciasBancarias->recuperaTodos($rsTransferencias, $stFiltro, $stOrder);

    $arTransferencias       = array();
    $arVlTipoTransferencias = array();

    if ($rsTransferencias->getNumLinhas() > -1) {

        $inCount  = 0;
        $total    = 0;
        $subTotal = 0;
        $inCodigoUFSistema = SistemaLegado::pegaConfiguracao('cod_uf');

        $data = $rsTransferencias->getCampo('data');

        while (!$rsTransferencias->eof()) {
            $arVlTipoTransferencias[$rsTransferencias->getCampo('tipo')] = $arVlTipoTransferencias[$rsTransferencias->getCampo('tipo')] + $rsTransferencias->getCampo('valor');
            if ($data == $rsTransferencias->getCampo('data')) {

                $arTransferencias[$inCount]['data']  = $rsTransferencias->getCampo('data');
                $arTransferencias[$inCount]['lote']  = $rsTransferencias->getCampo('lote');
                $arTransferencias[$inCount]['valor'] = number_format($rsTransferencias->getCampo('valor'), 2, ',', '.');

                $inCountTipo    = $inCount;
                $inCountDebito  = $inCount;
                $inCountCredito = $inCount;
                
                //Se Municipio é TO, Tipo Transferência é obrigatório 
                if($inCodigoUFSistema==27){
                    $stTipo = str_replace(chr(10), '', $rsTransferencias->getCampo('tipo_transferencia_to'));
                    $stTipo = wordwrap(utf8_decode($stTipo) , 18, chr(13));
                    $arTipo = explode(chr(13), utf8_encode($stTipo));
                    foreach ($arTipo as $stTipo) {
                        $arTransferencias[$inCountTipo]['tipo_transferencia'] = $stTipo;
                        $inCountTipo++;
                    }
                }

                $stDebito = str_replace(chr(10), '', $rsTransferencias->getCampo('debito'));
                $stDebito = wordwrap(($stDebito) , 34, chr(13));
                $arDebito = explode(chr(13), ($stDebito));
                foreach ($arDebito as $stDebito) {
                    $arTransferencias[$inCountDebito]['debito'] = $stDebito;
                    $inCountDebito++;
                }
                
                if($inCodigoUFSistema==27){
                    if ($inCountTipo > $inCountDebito) {
                        $inCountDebito = $inCountTipo;
                    } elseif ($inCountTipo < $inCountDebito) {
                        $inCountTipo = $inCountDebito;
                    }
                }

                $stCredito = str_replace(chr(10) , '', $rsTransferencias->getCampo('credito'));
                $stCredito = wordwrap(($stCredito) , 34, chr(13));
                $arCredito = explode(chr(13), ($stCredito));
                foreach ($arCredito as $stCredito) {
                    $arTransferencias[$inCountCredito]['credito'] = $stCredito;
                    $inCountCredito++;
                }

                if ($inCountCredito == $inCountDebito) {
                    $inCount = $inCountCredito;
                } elseif ($inCountCredito > $inCountDebito) {
                    $inCount = $inCountCredito;
                } elseif ($inCountCredito < $inCountDebito) {
                    $inCount = $inCountDebito;
                }

                $subTotal += $rsTransferencias->getCampo('valor');
                $total    += $rsTransferencias->getCampo('valor');
            } else {
                $arTransferencias[$inCount]['data']    = '';
                $arTransferencias[$inCount]['lote']    = '';
                $arTransferencias[$inCount]['debito']  = '';
                $arTransferencias[$inCount]['credito'] = 'Total de Transferências nesta Data';
                $arTransferencias[$inCount]['valor']   = number_format($subTotal, 2, ',', '.');
                if($inCodigoUFSistema==27)
                    $arTransferencias[$inCount]['tipo_transferencia']   = '';

                $arTransferencias[$inCount+1]['data']    = '';
                $arTransferencias[$inCount+1]['lote']    = '';
                $arTransferencias[$inCount+1]['debito']  = '';
                $arTransferencias[$inCount+1]['credito'] = '';
                $arTransferencias[$inCount+1]['valor']   = '';
                if($inCodigoUFSistema==27)
                    $arTransferencias[$inCount+1]['tipo_transferencia']   = '';

                $arTransferencias[$inCount+2]['data']  = $rsTransferencias->getCampo('data');
                $arTransferencias[$inCount+2]['lote']  = $rsTransferencias->getCampo('lote');
                $arTransferencias[$inCount+2]['valor'] = number_format($rsTransferencias->getCampo('valor'),2,',','.');

                $inCountTipo    = $inCount;
                $inCountDebito  = $inCount;
                $inCountCredito = $inCount;
                
                //Se Municipio é TO, Tipo Transferência é obrigatório 
                if($inCodigoUFSistema==27){
                    $stTipo = str_replace(chr(10), '', $rsTransferencias->getCampo('tipo_transferencia_to'));
                    $stTipo = wordwrap(utf8_decode($stTipo) , 18, chr(13));
                    $arTipo = explode(chr(13), utf8_encode($stTipo));
                    foreach ($arTipo as $stTipo) {
                        $arTransferencias[$inCountTipo+2]['tipo_transferencia'] = $stTipo;
                        $inCountTipo++;
                    }
                }

                $stDebito = str_replace(chr(10), '', $rsTransferencias->getCampo('debito'));
                $stDebito = wordwrap(($stDebito) , 34, chr(13));
                $arDebito = explode(chr(13), ($stDebito));
                foreach ($arDebito as $stDebito) {
                    $arTransferencias[$inCountDebito+2]['debito'] = $stDebito;
                    $inCountDebito++;
                }
                
                if($inCodigoUFSistema==27){
                    if ($inCountTipo > $inCountDebito) {
                        $inCountDebito = $inCountTipo;
                    } elseif ($inCountTipo < $inCountDebito) {
                        $inCountTipo = $inCountDebito;
                    }
                }

                $stCredito = str_replace(chr(10) , '', $rsTransferencias->getCampo('credito'));
                $stCredito = wordwrap(($stCredito) , 34, chr(13));
                $arCredito = explode(chr(13), ($stCredito));
                foreach ($arCredito as $stCredito) {
                    $arTransferencias[$inCountCredito+2]['credito'] = $stCredito;
                    $inCountCredito++;
                }

                if ($inCountCredito == $inCountDebito) {
                    $inCount = $inCountCredito;
                } elseif ($inCountCredito > $inCountDebito) {
                    $inCount = $inCountCredito;
                } elseif ($inCountCredito < $inCountDebito) {
                    $inCount = $inCountDebito;
                }

                $subTotal  = $rsTransferencias->getCampo('valor');
                $total    += $rsTransferencias->getCampo('valor');
                $inCount  += 2;
            }

            $data = $rsTransferencias->getCampo('data');

            $rsTransferencias->proximo();
        }

        $arTransferencias[$inCount]['data']    = '';
        $arTransferencias[$inCount]['lote']    = '';
        $arTransferencias[$inCount]['debito']  = '';
        $arTransferencias[$inCount]['credito'] = 'Total de Transferências nesta Data';
        $arTransferencias[$inCount]['valor']   = number_format($subTotal, 2, ',', '.');
        if($inCodigoUFSistema==27)
            $arTransferencias[$inCount]['tipo_transferencia']   = '';

        $arTransferencias[$inCount+1]['data']    = '';
        $arTransferencias[$inCount+1]['lote']    = '';
        $arTransferencias[$inCount+1]['debito']  = '';
        $arTransferencias[$inCount+1]['credito'] = '';
        $arTransferencias[$inCount+1]['valor']   = '';
        if($inCodigoUFSistema==27)
            $arTransferencias[$inCount+1]['tipo_transferencia']   = '';

        if ($this->getCodTipoTransferencia() == 0) {
            $arTransferencias[$inCount+2]['data']    = '';
            $arTransferencias[$inCount+2]['lote']    = '';
            $arTransferencias[$inCount+2]['debito']  = '';
            $arTransferencias[$inCount+2]['credito'] = 'Total Geral de Aplicações';
            $arTransferencias[$inCount+2]['valor']   = number_format($arVlTipoTransferencias[3], 2, ',', '.');
            if($inCodigoUFSistema==27)
                $arTransferencias[$inCount+2]['tipo_transferencia']   = '';

            $arTransferencias[$inCount+3]['data']    = '';
            $arTransferencias[$inCount+3]['lote']    = '';
            $arTransferencias[$inCount+3]['debito']  = '';
            $arTransferencias[$inCount+3]['credito'] = 'Total Geral de Resgates';
            $arTransferencias[$inCount+3]['valor']   = number_format($arVlTipoTransferencias[4], 2, ',', '.');
            if($inCodigoUFSistema==27)
                $arTransferencias[$inCount+3]['tipo_transferencia']   = '';

            $arTransferencias[$inCount+4]['data']    = '';
            $arTransferencias[$inCount+4]['lote']    = '';
            $arTransferencias[$inCount+4]['debito']  = '';
            $arTransferencias[$inCount+4]['credito'] = 'Total Geral de Depósitos/Retiradas';
            $arTransferencias[$inCount+4]['valor']   = number_format($arVlTipoTransferencias[5], 2, ',', '.');
            if($inCodigoUFSistema==27)
                $arTransferencias[$inCount+4]['tipo_transferencia']   = '';

            $arTransferencias[$inCount+5]['data']    = '';
            $arTransferencias[$inCount+5]['lote']    = '';
            $arTransferencias[$inCount+5]['debito']  = '';
            $arTransferencias[$inCount+5]['credito'] = 'Total Geral de Transferências';
            $arTransferencias[$inCount+5]['valor']   = number_format($total, 2, ',', '.');
            if($inCodigoUFSistema==27)
                $arTransferencias[$inCount+5]['tipo_transferencia']   = '';
        } else {
            $arTransferencias[$inCount+2]['data']    = '';
            $arTransferencias[$inCount+2]['lote']    = '';
            $arTransferencias[$inCount+2]['debito']  = '';
            $arTransferencias[$inCount+2]['credito'] = 'Total Geral de Transferências';
            $arTransferencias[$inCount+2]['valor']   = number_format($total, 2, ',', '.');
            if($inCodigoUFSistema==27)
                $arTransferencias[$inCount+2]['tipo_transferencia']   = '';
        }
    }

    $rsTransferencias  = new RecordSet;
    $rsTransferencias->preenche($arTransferencias);
    $rsRecordSet = $rsTransferencias;

    return $obErro;
}

}
