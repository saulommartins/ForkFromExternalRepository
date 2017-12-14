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

    * Classe de Regra de Negócio para Arquivo de Baixa com Layout da Febraban
    * Data de Criação   : 31/08/2007

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage Regra

    * $Id: RARRLayoutCNAB.class.php 64341 2016-01-15 20:11:16Z evandro $

   * Casos de uso: uc-05.03.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

set_time_limit(0);

/**
    * Classe de Regra de Negócio para Arquivo de Baixa com Layout da Febraban
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino
*/

class RARRLayoutCNAB extends RARRPagamento
{
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @access Private
    * @var Array
*/
var $arDadosArquivo;

// SETTERS
/**
    * @access Public
    * @param Array $valor
*/
function setDadosArquivo($valor) { $this->arDadosArquivo = $valor; }

// GETTERES
/**
    * @access Public
    * @param Array $valor
*/
function getDadosArquivo() { return $this->arDadosArquivo;  }

/**
     * Método construtor
     * @access Private
*/
function RARRLayoutCNAB()
{
    parent::RARRPagamento();
    $this->obTransacao = new Transacao;
}

function buscaPorITBI_CNAB($arDados , $boTransacao , $boAuto = true)
{
    if ($boAuto) {
        /* retirar as primeiras duas linhas e as duas ultimas, que são lixo*/
        array_shift($arDados);array_shift($arDados);array_pop($arDados); array_pop($arDados);
        foreach ($arDados AS $key => $stValorLinha) {
            $stPrimeiroElemento = substr( $stValorLinha, 13, 1 );
            if ( strtoupper($stPrimeiroElemento) == "T" ) {
                //busca numeracao na string
                $stNumeracao = substr( $stValorLinha, 37, 20 );
                $stNumeracao = trim( $stNumeracao );

                if ( $stNumeracao )
                     $stFiltro .= "'".$stNumeracao."',";
            }
        }
    } else {
        $stFiltro = $arDados;
    }

    $this->obRARRConfiguracao = new RARRConfiguracao;
    $this->obRARRConfiguracao->setAnoExercicio( Sessao::getExercicio() );
    $this->obRARRConfiguracao->consultar( $boTransacao );
    $inCodGrupoITBI = $this->obRARRConfiguracao->getCodigoGrupoCreditoITBI();
    $arTMPdados = explode( "/", $inCodGrupoITBI );
    $inCodGrupoITBI = $arTMPdados[0];

    $stFiltro  = substr ( $stFiltro , 0 , strlen( $stFiltro )  - 1 );
    $stFiltro  = " and carne.numeracao in (".$stFiltro.") ";
    $stFiltro .= " and calculo_grupo_credito.cod_grupo = ".$inCodGrupoITBI;

    if ($inCodGrupoITBI == "") {
        $obErro = new Erro;
        $obErro->setDescricao("Grupo ITBI não encontrado.");

        return $obErro;
    }

    $this->obTARRPagamento->recuperaListaITBIArquivo($rsImoveisArquivo,$stFiltro,'',$boTransacao);
    $arItbi  = array();
    while ( !$rsImoveisArquivo->eof() ) {
        $arItbi[] = array(  "imovel"            => $rsImoveisArquivo->getCampo('inscricao_municipal'),
                            "numeracao"         => $rsImoveisArquivo->getCampo('numeracao'),
                            "exercicio"         => $rsImoveisArquivo->getCampo('exercicio'),
                            "numcgm"            => $rsImoveisArquivo->getCampo('numcgm'),
                            "cod_transferencia" => $rsImoveisArquivo->getCampo('cod_transferencia'),
                            "cod_natureza"      => $rsImoveisArquivo->getCampo('cod_natureza'),
                            "fazer"             => !$rsImoveisArquivo->eof()
                         );
        $rsImoveisArquivo->proximo();
    }

    $this->arITBI = $arItbi;
    unset($arItbi,$rsImoveisArquivo,$arDados);

}

/**
    * Verifica o layout do arquivo de baixa
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function efetuarBaixa($arDadosArquivo, $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $obErro->ocorreu() ) {
        return $obErro;
    }

    /**
        * Chama função que retorna lista de imoveis que pagaram ITBI.
    */
    $this->buscaPorITBI_CNAB( $arDadosArquivo, $boTransacao );
    $nuTotalPago = 0;
    $inPosicao = 0;
    foreach ($arDadosArquivo AS $key => $stValorLinha) {
        $rsNumeracao = $rsConvenio =  new RecordSet;
        array_shift( $arDadosArquivo );
        $stPrimeiroElemento = substr( $stValorLinha, 13, 1 );

        //HEADER
        $inPosicao++;
        if ($inPosicao <= 1) { //header
            //seta os valores do Lote contidos no cabeçalho do arquivo
            $dtDataLoteAno = substr( $stValorLinha, 147, 4 );
            $dtDataLoteMes = substr( $stValorLinha, 145, 2 );
            $dtDataLoteDia = substr( $stValorLinha, 143, 2 );
            $dtDataLote    = $dtDataLoteDia."/".$dtDataLoteMes."/".$dtDataLoteAno;
            $this->dtDataLote  = $dtDataLote;
            $this->stExercicio = $dtDataLoteAno;

            $nuBanco = substr( $stValorLinha, 0, 3 );
            $this->obRMONBanco->setNumBanco( $nuBanco );
            $this->obRMONBanco->listarBanco( $rsBanco, $boTransacao );
            $this->obRMONBanco->setCodBanco( $rsBanco->getCampo('cod_banco') );

            if ($nuBanco == '001') {
                $inTamanhoAgencia = 5;
            }else
            if ($nuBanco == '237') {
                $inTamanhoAgencia = 4;
            }else
            if ($nuBanco == '399') {
                $inTamanhoAgencia = 4;
            }else
            if ($nuBanco == '341') {
                $inTamanhoAgencia = 4;
            }else
            if ($nuBanco == '409') {
                $inTamanhoAgencia = 4;
            }else
            if ($nuBanco == '033') {
                $inTamanhoAgencia = 4;
            }else
            if ($nuBanco == '353') {
                $inTamanhoAgencia = 4;
            }else
            if ($nuBanco == '008') {
                $inTamanhoAgencia = 4;
            }else
            if ($nuBanco == '275') {
                $inTamanhoAgencia = 4;
            } else {
                $inTamanhoAgencia = 2;
            }

            $nuAgencia = substr( $stValorLinha, 52+(6-$inTamanhoAgencia), $inTamanhoAgencia-1 );
            $nuAgencia .= "-".substr( $stValorLinha, 57, 1 );

            $this->obRMONAgencia->setNumAgencia( $nuAgencia );
            $this->obRMONAgencia->listarAgencia( $rsAgencia, $boTransacao );
            $this->obRMONAgencia->setCodAgencia( $rsAgencia->getCampo('cod_agencia') );

            if ( !$this->obRMONAgencia->getCodAgencia() OR !$this->obRMONBanco->getCodBanco() ) {
                $obErro->setDescricao("Banco/Agência não encontrados.");

                return $obErro;
            }

            $obErro = $this->efetuarPagamentoAutomatico( $boTransacao );
            if ( $obErro->ocorreu() ) {
                return $obErro;
            }
        }else
        if ($inPosicao <= 2) {
            continue; //pulando segunda linha do cabecalho
        }else
        if ( strtoupper( $stPrimeiroElemento ) == "T" ) { //CONTEUDO 1
            //busca numeracao na string
            $stNumeracao = substr( $stValorLinha, 37, 20 );
            $stNumeracao = trim($stNumeracao);
        }else
        if ( strtoupper( $stPrimeiroElemento ) == "U" ) { //CONTEUDO 2
            //busca data de pagamento na string
            $stDataPagamento    = substr( $stValorLinha, 137, 8 );
            $dtDataPagamentoAno = substr( $stDataPagamento, 4, 4 );
            $dtDataPagamentoMes = substr( $stDataPagamento, 2, 2 );
            $dtDataPagamentoDia = substr( $stDataPagamento, 0, 2 );
            $dtDataPagamento    = $dtDataPagamentoDia."/".$dtDataPagamentoMes."/".$dtDataPagamentoAno;

            //busca valor pago na string
            $nuValorPago = substr($stValorLinha, 79, 13);
            $nuValorPago = substr($nuValorPago, 0, strlen($nuValorPago)-2).".".substr($nuValorPago, strlen($nuValorPago)-2, 2);
            $nuTotalPago += $nuValorPago;

            //busca data do lancamento
            $this->setDataPagamento                  ( $dtDataPagamento );
            $this->setObservacao                     ( ''               );
            $this->setValorPagamento                 ( $nuValorPago     );
            $this->obRARRCarne->setNumeracao         ( $stNumeracao     );
            $this->obRARRTipoPagamento->setCodigoTipo( '3' );
            $this->obRARRTipoPagamento->setPagamento ( 't' );

            //recupera o convenio do carne
            //se nao encontrar busca o numero do carne migrado e encontra o convenio deste carne...
            $this->obRARRCarne->obRMONConvenio->setCodigoConvenio('');
            $obErro = $this->obRARRCarne->recuperaConvenio( $rsConvenio , $boTransacao );
            if ( $obErro->ocorreu() ) {
                return $obErro;
            }

            if ( $rsConvenio->eof() ) {
                $this->obRARRCarne->setNumeracaoMigrada( $stNumeracao );
                $obErro = $this->obRARRCarne->recuperaNumeracao( $rsNumeracao, $boTransacao );
                if ( $obErro->ocorreu() ) {
                    return $obErro;
                }

                $this->obRARRCarne->setNumeracao( $rsNumeracao->getCampo('numeracao') );
                $this->obRARRCarne->obRARRParcela->setCodParcela( $rsNumeracao->getCampo('cod_parcela')  );
                $this->obRARRCarne->obRMONConvenio->setCodigoConvenio($rsNumeracao->getCampo('cod_convenio'));
            } else {
                $this->obRARRCarne->obRARRParcela->setCodParcela( $rsConvenio->getCampo('cod_parcela')  );
                $this->obRARRCarne->obRMONConvenio->setCodigoConvenio( $rsConvenio->getCampo('cod_convenio') );
            }

            //verifica se o carne não é uma consolidação
            $this->obRARRCarne->setNumeracao( $stNumeracao );
            $obErro = $this->obRARRCarne->verificaConsolidacao( $rsVerificaConsolidacao, $boTransacao );
            if ( $obErro->ocorreu() ) {
                return $obErro;
            }

            //se a numeracao nao for encontrada no urbem ou nos dados da migraçao...
            //grava numeracao como inconsistente
            if ( $this->obRARRCarne->obRMONConvenio->getCodigoConvenio() == "" && $rsVerificaConsolidacao->eof() ) {
                $obErro = $this->salvarInconsistencia( $boTransacao );
                if ( $obErro->ocorreu() ) {
                    return $obErro;
                }
            } else {
                if ( $rsVerificaConsolidacao->eof() ) {
                    $this->obRARRCarne->setNumeracao( $stNumeracao );
                    $obErro = $this->efetuarPagamentoManual( $boTransacao, TRUE, '', $nuTotal, '' );
                    if ( $obErro->ocorreu() ) {
                        return $obErro;
                    }
                } else {
                    $this->setValorPagoConsolidacao( $this->getValorPagamento() );
                    while ( !$rsVerificaConsolidacao->eof() ) {
                        $this->obRARRCarne->setNumeracao( $rsVerificaConsolidacao->getCampo('numeracao') );
                        $obErro = $this->obRARRCarne->recuperaConvenio( $rsConvenio , $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            return $obErro;
                        }

                        $this->obRARRCarne->obRMONConvenio->setCodigoConvenio($rsConvenio->getCampo('cod_convenio'));
                        $obErro = $this->efetuarPagamentoManual( $boTransacao, TRUE, FALSE, $nuTotal, TRUE );
                        if ( $obErro->ocorreu() ) {
                            return $obErro;
                        }

                        $this->setValorPagoConsolidacao( $this->getValorPagoConsolidacao() - $nuTotal );
                        $rsVerificaConsolidacao->proximo();
                    }
                }
            }
        }

    }

    /* Após baixa, executa ações recorrentes*/
    $obErro = $this->posBaixa($boTransacao);

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->RARRLayoutCNAB );

    return $obErro;
}

} // fecha classe
