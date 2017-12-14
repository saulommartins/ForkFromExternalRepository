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
    * Classe de Regra de Negócio para Arquivo de Baixa com Layout da Febraban
    * Data de Criação   : 28/03/2006

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Regra

    * $Id: RARRLayoutFebraban.class.php 59612 2014-09-02 12:00:51Z gelson $

   * Casos de uso: uc-05.03.10
*/

/*
$Log$
Revision 1.21  2007/07/10 14:51:35  cercato
correcao no controle do valor final do arquivo.

Revision 1.20  2007/02/09 15:34:43  cercato
correcao da transferencia da baixa automatica.

Revision 1.19  2007/02/09 10:13:28  cercato
adicao de mensagens de erro.

Revision 1.18  2007/02/08 17:16:48  cercato
correcao do cancelamento da parcela na baixa automatica

Revision 1.17  2006/12/01 17:27:25  cercato
Bug #7615#

Revision 1.16  2006/11/09 14:57:19  cercato
bug #7338#

Revision 1.15  2006/09/15 10:48:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

set_time_limit(0);

/**
    * Classe de Regra de Negócio para Arquivo de Baixa com Layout da Febraban
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino
*/

class RARRLayoutFebraban extends RARRPagamento
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
function RARRLayoutFebraban()
{
    parent::RARRPagamento();
    $this->obTransacao = new Transacao;
}

/**
    * Verifica o layout do arquivo de baixa
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function efetuarBaixa($arDadosArquivo, $boTransacao = "")
{
    ;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $obErro->ocorreu() ) {
        return $obErro;
    }

    /**
        * Chama função que retorna lista de imoveis que pagaram ITBI.
    */
    $this->buscaPorITBI( $arDadosArquivo, $boTransacao );
    $nuTotalPago = 0;
    foreach ($arDadosArquivo AS $key => $stValorLinha) {
        $rsNumeracao = $rsConvenio =  new RecordSet;
        array_shift( $arDadosArquivo );
        $stPrimeiroElemento = substr( $stValorLinha, 0, 1 );

        //HEADER
        if ( strtoupper( $stPrimeiroElemento ) == "A" ) {
            //seta os valores do Lote contidos no cabeçalho do arquivo
            $dtDataLoteAno = substr( $stValorLinha, 65, 4 );
            $dtDataLoteMes = substr( $stValorLinha, 69, 2 );
            $dtDataLoteDia = substr( $stValorLinha, 71, 2 );
            $dtDataLote    = $dtDataLoteDia."/".$dtDataLoteMes."/".$dtDataLoteAno;
            $this->dtDataLote  = $dtDataLote;
            $this->stExercicio = $dtDataLoteAno;

            $nuBanco = substr( $stValorLinha, 42, 3 );

            $this->obRMONBanco->setNumBanco( trim($nuBanco) );
            $this->obRMONBanco->listarBanco( $rsBanco, $boTransacao );
            $this->obRMONBanco->setCodBanco( $rsBanco->getCampo('cod_banco') );

            if ($nuBanco == '037') {
                $inTamanhoAgencia = 4;
            }else
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
            } else
            if ($nuBanco == '104') {
                $inTamanhoAgencia = 4;
            } else {
                $inTamanhoAgencia = 2;
            }
            

            $nuAgencia = substr( $arDadosArquivo[0], 1, $inTamanhoAgencia );

            //$this->obRMONAgencia->setNumAgencia( trim($nuAgencia) );
            $this->obRMONAgencia->listarAgencia( $rsAgencia, $boTransacao );
            while ( !$rsAgencia->Eof() ) {
                $stResultado = str_replace( "-", "", $rsAgencia->getCampo( "num_agencia" ) );
                if ( strtoupper($stResultado) === trim( $nuAgencia ) ) {
                    $this->obRMONAgencia->setCodAgencia( $rsAgencia->getCampo( "cod_agencia" ) );
                    break;
                }

                $rsAgencia->proximo();
            }

            if ( !$this->obRMONAgencia->getCodAgencia() OR !$this->obRMONBanco->getCodBanco() ) {
                $obErro->setDescricao("Banco/Agência não encontrados.");

                return $obErro;
            }

            $obErro = $this->efetuarPagamentoAutomatico( $boTransacao );
            if ( $obErro->ocorreu() ) {
                return $obErro;
            }
        }

        //CONTEUDO
        if ( strtoupper( $stPrimeiroElemento ) == "G" ) {
            //busca data de pagamento na string
            $stDataPagamento    = substr( $stValorLinha, 21, 8 );
            $dtDataPagamentoAno = substr( $stDataPagamento, 0, 4 );
            $dtDataPagamentoMes = substr( $stDataPagamento, 4, 2 );
            $dtDataPagamentoDia = substr( $stDataPagamento, 6, 2 );
            $dtDataPagamento    = $dtDataPagamentoDia."/".$dtDataPagamentoMes."/".$dtDataPagamentoAno;

            //busca valor pago na string
            $nuValorPago = substr( $stValorLinha, 81, 12 );
            $nuValorPago = substr($nuValorPago,0,10).".".substr($nuValorPago,10,2);
            $nuTotalPago += $nuValorPago;

            //busca numeracao na string
            $stNumeracao = substr( $stValorLinha, 64, 17 );
            $stNumeracao = ltrim( $stNumeracao, 0 );

            //busca data do lancamento
            //$stDataLancamento    = substr( $stValorLinha, 56, 8 );
            //$dtDataLancamentoAno = substr( $stDataLancamento, 0, 4 );

            $this->setDataPagamento                  ( $dtDataPagamento );
            $this->setObservacao                     ( ''               );
            $this->setValorPagamento                 ( $nuValorPago     );
            $this->obRARRCarne->setNumeracao         ( $stNumeracao     );
            //$this->obRARRCarne->setExercicio         ( $dtDataLancamentoAno );
            $this->obRARRTipoPagamento->setCodigoTipo( '3' );
            $this->obRARRTipoPagamento->setPagamento( 't' );

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
            $boInconsistente = false;
            for ( $inX=0; $inX<count($this->arITBI); $inX++ ) {
                if ($this->arITBI[$inX]["numeracao"] == $stNumeracao) {
                    if ( !$this->arITBI[$inX]["fazer"] )
                        $boInconsistente = true;

                    break;
                }
            }

            if ($boInconsistente) {
                $obErro = $this->salvarInconsistencia( $boTransacao );
                if ( $obErro->ocorreu() ) {
                    return $obErro;
                }
            }else //grava numeracao como inconsistente
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

        //TRAILLER
        if ( strtoupper( $stPrimeiroElemento ) == "Z" ) {
//echo "irmaos finito<br>";exit;
            //busca valor total do Arquivo
            $nuTotalPagoArquivo = ltrim( substr( $stValorLinha, 7, 17 ), 0 );
            $nuTotalPago = number_format( $nuTotalPago, 2, '', '' );

            //verifica se o valor total baixado é igual ao valor total do arquivo
            if ( trim($nuTotalPago) != trim($nuTotalPagoArquivo) ) {
                $obErro->setDescricao("O valor total baixado difere do valor total do arquivo.");

                return $obErro;
            }
        }
    }

    /* Após baixa, executa ações recorrentes*/
    $obErro = $this->posBaixa($boTransacao);

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRPagamento );

    return $obErro;
}

} // fecha classe

?>
