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
 * Classe de regra de cheque
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 *
 * $Id: $
 *
 */
include CAM_GF_TES_MAPEAMENTO    . 'TTesourariaCheque.class.php';
include CAM_GF_TES_MAPEAMENTO    . 'TTesourariaChequeEmissao.class.php';
include CAM_GF_TES_MAPEAMENTO    . 'TTesourariaChequeEmissaoTransferencia.class.php';
include CAM_GF_TES_MAPEAMENTO    . 'TTesourariaChequeEmissaoOrdemPagamento.class.php';
include CAM_GF_TES_MAPEAMENTO    . 'TTesourariaChequeEmissaoReciboExtra.class.php';
include CAM_GF_TES_MAPEAMENTO    . 'TTesourariaChequeEmissaoAnulada.class.php';
include CAM_GF_TES_MAPEAMENTO    . 'TTesourariaChequeEmissaoBaixa.class.php';
include CAM_GF_TES_MAPEAMENTO    . 'TTesourariaChequeEmissaoBaixaAnulada.class.php';
include CAM_GF_TES_NEGOCIO       . 'RTesourariaImpressoraCheque.class.php';
include_once CAM_GF_TES_NEGOCIO  . 'RTesourariaTransferencia.class.php';
include_once CAM_GF_TES_NEGOCIO  . 'RTesourariaBoletim.class.php';
include CAM_GT_MON_NEGOCIO       . 'RMONContaCorrente.class.php';
include_once CAM_GF_CONT_NEGOCIO . 'RContabilidadePlanoBanco.class.php';
include_once CAM_GF_EMP_NEGOCIO  . 'REmpenhoOrdemPagamento.class.php';
include_once CAM_GA_ADM_NEGOCIO  . 'RAdministracaoConfiguracao.class.php';

class RTesourariaCheque
{
    public $obTTesourariaCheque,
        $obTTesourariaChequeEmissao,
        $obTTesourariaChequeEmissaoTransferencia,
        $obTTesourariaChequeEmissaoDespesaExtra,
        $obTTesourariaChequeEmissaoOrdemPagamento,
        $obTTesourariaChequeEmissaoAnulada,
        $obTTesourariaChequeEmissaoBaixa,
        $obTTesourariaChequeEmissaoBaixaAnulada,
        $obRTesourariaTransferencia,
        $obRMONContaCorrente,
        $obREmpenhoOrdemPagamento,
        $obRTesourariaImpressoraCheque,
        $obRAdministracaoConfiguracao,
        $obRContabilidadePlanoBanco,
        $obTransacao,
        $stNumCheque,
        $stNumChequeInicial,
        $stNumChequeFinal,
        $stDtEmissao,
        $flValor,
        $stDescricao,
        $stTimestampEmissao,
        $stTimestampBaixa,
        $stAcao;
        

    /**
     * Método contrutor, instancia as classes necessarias.
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        $this->obTransacao                              = new Transacao                             ();
        $this->obTTesourariaCheque                      = new TTesourariaCheque                     ();
        $this->obTTesourariaChequeEmissaoOrdemPagamento = new TTesourariaChequeEmissaoOrdemPagamento();
        $this->obTTesourariaChequeEmissao               = new TTesourariaChequeEmissao              ();
        $this->obTTesourariaChequeEmissaoReciboExtra    = new TTesourariaChequeEmissaoReciboExtra   ();
        $this->obTTesourariaChequeEmissaoTransferencia  = new TTesourariaChequeEmissaoTransferencia ();
        $this->obTTesourariaChequeEmissaoAnulada        = new TTesourariaChequeEmissaoAnulada       ();
        $this->obTTesourariaChequeEmissaoBaixa          = new TTesourariaChequeEmissaoBaixa         ();
        $this->obTTesourariaChequeEmissaoBaixaAnulada   = new TTesourariaChequeEmissaoBaixaAnulada  ();
        $this->obRTesourariaImpressoraCheque            = new RTesourariaImpressoraCheque           ();
        $this->obRTesourariaTransferencia               = new RTesourariaTransferencia              (new RTesourariaBoletim());
        $this->obRMONContaCorrente                      = new RMONContaCorrente                     ();
        $this->obREmpenhoOrdemPagamento                 = new REmpenhoOrdemPagamento                ();
        $this->obRAdministracaoConfiguracao             = new RAdministracaoConfiguracao            ();
        $this->obRContabilidadePlanoBanco               = new RContabilidadePlanoBanco              ();
    }

    /**
    * Método que inclui os cheques na base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boFlagTransacao
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function addCheque($boFlagTransacao = true, $boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {

            if (!$obErro->ocorreu()) {
                $this->obRMONContaCorrente->listarContaCorrente($rsContaCorrente,$boTransacao);

                $this->obTTesourariaCheque->setDado ('cod_banco'         , $rsContaCorrente->getCampo('cod_banco')         );
                $this->obTTesourariaCheque->setDado ('cod_agencia'       , $rsContaCorrente->getCampo('cod_agencia')       );
                $this->obTTesourariaCheque->setDado ('cod_conta_corrente', $rsContaCorrente->getCampo('cod_conta_corrente'));
                $this->obTTesourariaCheque->setDado ('num_cheque'        , $this->stNumCheque                              );
                $obErro = $this->obTTesourariaCheque->inclusao($boTransacao                                                );
            }
        }
        if ($obErro->ocorreu()) {
            $obErro->setDescricao('Este cheque já está cadastrado para esta conta');
        }

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTTesourariaCheque);

        return $obErro;
    }

    /**
     * Método que exclui um cheque da base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boFlagTransacao
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function deleteCheque($boTransacao = '')
    {
        $this->obTTesourariaCheque->setDado('cod_banco'         ,$this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco);
        $this->obTTesourariaCheque->setDado('cod_agencia'       ,$this->obRMONContaCorrente->obRMONAgencia->inCodAgencia           );
        $this->obTTesourariaCheque->setDado('cod_conta_corrente',$this->obRMONContaCorrente->inCodigoConta                         );
        $this->obTTesourariaCheque->setDado('num_cheque'        ,$this->stNumCheque                                                );

        $obErro = $this->obTTesourariaCheque->exclusao($boTransacao);

        return $obErro;
    }

    /**
     * Método que inclui emissao de cheque por op
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function emitirPorOP($boTransacao)
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
         
        //Insere na table tesouraria.emissao_cheque
        $this->obTTesourariaChequeEmissao->setDado ('num_cheque'        , $this->stNumCheque                                                );
        $this->obTTesourariaChequeEmissao->setDado ('cod_banco'         , $this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco);
        $this->obTTesourariaChequeEmissao->setDado ('cod_agencia'       , $this->obRMONContaCorrente->obRMONAgencia->inCodAgencia           );
        $this->obTTesourariaChequeEmissao->setDado ('cod_conta_corrente', $this->obRMONContaCorrente->inCodigoConta                         );
        $this->obTTesourariaChequeEmissao->setDado ('valor'             , $this->flValor                                                    );
        $this->obTTesourariaChequeEmissao->setDado ('data_emissao'      , $this->stDtEmissao                                                );
        $this->obTTesourariaChequeEmissao->setDado ('descricao'         , $this->stDescricao                                                );
        $this->obTTesourariaChequeEmissao->setDado ('timestamp_emissao' , $this->stTimestampEmissao                                         );
        $obErro = $this->obTTesourariaChequeEmissao->inclusao($boTransacao); 

        if (!$obErro->ocorreu()) {
            //Insere na table tesouraria.emissao_cheque_ordem_pagamento
            $this->obTTesourariaChequeEmissaoOrdemPagamento->setDado ('num_cheque'        ,$this->stNumCheque                                                     );
            $this->obTTesourariaChequeEmissaoOrdemPagamento->setDado ('cod_banco'         ,$this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco     );
            $this->obTTesourariaChequeEmissaoOrdemPagamento->setDado ('cod_agencia'       ,$this->obRMONContaCorrente->obRMONAgencia->inCodAgencia                );
            $this->obTTesourariaChequeEmissaoOrdemPagamento->setDado ('cod_conta_corrente',$this->obRMONContaCorrente->inCodigoConta                              );
            $this->obTTesourariaChequeEmissaoOrdemPagamento->setDado ('cod_ordem'         ,$this->obREmpenhoOrdemPagamento->inCodigoOrdem                         );
            $this->obTTesourariaChequeEmissaoOrdemPagamento->setDado ('exercicio'         ,$this->obREmpenhoOrdemPagamento->stExercicio                           );
            $this->obTTesourariaChequeEmissaoOrdemPagamento->setDado ('cod_entidade'      ,$this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade);
            $this->obTTesourariaChequeEmissaoOrdemPagamento->setDado ('timestamp_emissao' ,$this->stTimestampEmissao                                         );

            $obErro = $this->obTTesourariaChequeEmissaoOrdemPagamento->inclusao($boTransacao);
        }
        
        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro,  $this->obTTesourariaChequeEmissao);
        return $obErro;
    }

    /**
     * Método que inclui emissao de cheque por transferencia
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function emitirPorTransferencia($boTransacao)
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        
        //Insere na table tesouraria.emissao_cheque
        $this->obTTesourariaChequeEmissao->setDado ('num_cheque'        , $this->stNumCheque                                                );
        $this->obTTesourariaChequeEmissao->setDado ('cod_banco'         , $this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco);
        $this->obTTesourariaChequeEmissao->setDado ('cod_agencia'       , $this->obRMONContaCorrente->obRMONAgencia->inCodAgencia           );
        $this->obTTesourariaChequeEmissao->setDado ('cod_conta_corrente', $this->obRMONContaCorrente->inCodigoConta                         );
        $this->obTTesourariaChequeEmissao->setDado ('valor'             , $this->flValor                                                    );
        $this->obTTesourariaChequeEmissao->setDado ('data_emissao'      , $this->stDtEmissao                                                );
        $this->obTTesourariaChequeEmissao->setDado ('descricao'         , $this->stDescricao                                                );
        $this->obTTesourariaChequeEmissao->setDado ('timestamp_emissao' , $this->stTimestampEmissao                                         );
        $obErro = $this->obTTesourariaChequeEmissao->inclusao($boTransacao);

        if (!$obErro->ocorreu()) {
            //Insere na table tesouraria.emissao_cheque_ordem_pagamento
            $this->obTTesourariaChequeEmissaoTransferencia->setDado ('num_cheque'        ,$this->stNumCheque                                                     );
            $this->obTTesourariaChequeEmissaoTransferencia->setDado ('cod_banco'         ,$this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco     );
            $this->obTTesourariaChequeEmissaoTransferencia->setDado ('cod_agencia'       ,$this->obRMONContaCorrente->obRMONAgencia->inCodAgencia                );
            $this->obTTesourariaChequeEmissaoTransferencia->setDado ('cod_conta_corrente',$this->obRMONContaCorrente->inCodigoConta                              );
            $this->obTTesourariaChequeEmissaoTransferencia->setDado ('cod_lote'          ,$this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->inCodLote);
            $this->obTTesourariaChequeEmissaoTransferencia->setDado ('exercicio'         ,$this->obRTesourariaTransferencia->roRTesourariaBoletim->stExercicio);
            $this->obTTesourariaChequeEmissaoTransferencia->setDado ('cod_entidade'      ,$this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade);
            $this->obTTesourariaChequeEmissaoTransferencia->setDado ('tipo'              ,$this->obRTesourariaTransferencia->obRTesourariaAutenticacao->stTipo);
            $this->obTTesourariaChequeEmissaoTransferencia->setDado ('timestamp_emissao' ,$this->stTimestampEmissao                                         );
            $obErro = $this->obTTesourariaChequeEmissaoTransferencia->inclusao($boTransacao);
        }
        
        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro,  $this->obTTesourariaChequeEmissao);

        return $obErro;
    }

    /**
     * Método que inclui emissao de cheque por recibo extra
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function emitirPorReciboExtra($arParam, $boTransacao)
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        
        //Insere na table tesouraria.emissao_cheque
        $this->obTTesourariaChequeEmissao->setDado ('num_cheque'        , $this->stNumCheque                                                );
        $this->obTTesourariaChequeEmissao->setDado ('cod_banco'         , $this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco);
        $this->obTTesourariaChequeEmissao->setDado ('cod_agencia'       , $this->obRMONContaCorrente->obRMONAgencia->inCodAgencia           );
        $this->obTTesourariaChequeEmissao->setDado ('cod_conta_corrente', $this->obRMONContaCorrente->inCodigoConta                         );
        $this->obTTesourariaChequeEmissao->setDado ('valor'             , $this->flValor                                                    );
        $this->obTTesourariaChequeEmissao->setDado ('data_emissao'      , $this->stDtEmissao                                                );
        $this->obTTesourariaChequeEmissao->setDado ('descricao'         , $this->stDescricao                                                );
        $this->obTTesourariaChequeEmissao->setDado ('timestamp_emissao' , $this->stTimestampEmissao                                         );
        $obErro = $this->obTTesourariaChequeEmissao->inclusao($boTransacao);

        if (!$obErro->ocorreu()) {
            //Insere na table tesouraria.emissao_cheque_ordem_pagamento
            $this->obTTesourariaChequeEmissaoReciboExtra->setDado ('num_cheque'        ,$this->stNumCheque                                                     );
            $this->obTTesourariaChequeEmissaoReciboExtra->setDado ('cod_banco'         ,$this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco     );
            $this->obTTesourariaChequeEmissaoReciboExtra->setDado ('cod_agencia'       ,$this->obRMONContaCorrente->obRMONAgencia->inCodAgencia                );
            $this->obTTesourariaChequeEmissaoReciboExtra->setDado ('cod_conta_corrente',$this->obRMONContaCorrente->inCodigoConta                              );
            $this->obTTesourariaChequeEmissaoReciboExtra->setDado ('cod_recibo_extra'  ,$arParam['inCodReciboExtra']);
            $this->obTTesourariaChequeEmissaoReciboExtra->setDado ('exercicio'         ,$arParam['stExercicio']     );
            $this->obTTesourariaChequeEmissaoReciboExtra->setDado ('cod_entidade'      ,$arParam['inCodEntidade']   );
            $this->obTTesourariaChequeEmissaoReciboExtra->setDado ('tipo_recibo'       ,'D'                         );
            $this->obTTesourariaChequeEmissaoReciboExtra->setDado ('timestamp_emissao' , $this->stTimestampEmissao                                         );
            $obErro = $this->obTTesourariaChequeEmissaoReciboExtra->inclusao($boTransacao);
        }

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro,  $this->obTTesourariaChequeEmissao);

        return $obErro;
    }

    /**
     * Método que anula a emissao de cheque
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function anularChequeEmissao($boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $this->findChequeEmissao();

        //Insere na table tesouraria.emissao_cheque_anulada
        $this->obTTesourariaChequeEmissaoAnulada->setDado ('num_cheque'        , $this->stNumCheque                                                );
        $this->obTTesourariaChequeEmissaoAnulada->setDado ('cod_banco'         , $this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco);
        $this->obTTesourariaChequeEmissaoAnulada->setDado ('cod_agencia'       , $this->obRMONContaCorrente->obRMONAgencia->inCodAgencia           );
        $this->obTTesourariaChequeEmissaoAnulada->setDado ('cod_conta_corrente', $this->obRMONContaCorrente->inCodigoConta                         );
        $this->obTTesourariaChequeEmissaoAnulada->setDado ('timestamp_emissao' , $this->stTimestampEmissao);
        $obErro = $this->obTTesourariaChequeEmissaoAnulada->inclusao($boTransacao);

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTTesourariaChequeEmissaoAnulada);

        return $obErro;
    }

    /**
     * Método que inclui a baixa da emissao de cheque
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function baixarChequeEmissao(&$boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $this->findChequeEmissao();

        //Insere na table tesouraria.emissao_cheque_baixa
        $this->obTTesourariaChequeEmissaoBaixa->setDado ('num_cheque'        , $this->stNumCheque                                                );
        $this->obTTesourariaChequeEmissaoBaixa->setDado ('cod_banco'         , $this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco);
        $this->obTTesourariaChequeEmissaoBaixa->setDado ('cod_agencia'       , $this->obRMONContaCorrente->obRMONAgencia->inCodAgencia           );
        $this->obTTesourariaChequeEmissaoBaixa->setDado ('cod_conta_corrente', $this->obRMONContaCorrente->inCodigoConta                         );
        $this->obTTesourariaChequeEmissaoBaixa->setDado ('timestamp_emissao' , $this->stTimestampEmissao);
        $obErro = $this->obTTesourariaChequeEmissaoBaixa->inclusao($boTransacao);

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTTesourariaChequeEmissaoBaixa);

        return $obErro;
    }

    /**
     * Método que inclui a anulacao da baixa da emissao de cheque
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function anularBaixaChequeEmissao($boTransacao = '')
    {
        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $this->findChequeEmissao();

        //Insere na table tesouraria.emissao_cheque_baixa
        $this->obTTesourariaChequeEmissaoBaixaAnulada->setDado ('num_cheque'        , $this->stNumCheque                                                );
        $this->obTTesourariaChequeEmissaoBaixaAnulada->setDado ('cod_banco'         , $this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco);
        $this->obTTesourariaChequeEmissaoBaixaAnulada->setDado ('cod_agencia'       , $this->obRMONContaCorrente->obRMONAgencia->inCodAgencia           );
        $this->obTTesourariaChequeEmissaoBaixaAnulada->setDado ('cod_conta_corrente', $this->obRMONContaCorrente->inCodigoConta                         );
        $this->obTTesourariaChequeEmissaoBaixaAnulada->setDado ('timestamp_emissao' , $this->stTimestampEmissao);
        $this->obTTesourariaChequeEmissaoBaixaAnulada->setDado ('timestamp_baixa'   , $this->stTimestampBaixa);
        $obErro = $this->obTTesourariaChequeEmissaoBaixaAnulada->inclusao($boTransacao);

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTTesourariaChequeEmissaoBaixa);

        return $obErro;
    }

    /**
     * Método para buscar um cheque na base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function findCheque($boTransacao = '')
    {
        $stFiltro  = " AND banco.cod_banco = '" . $this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco . "' ";
        $stFiltro .= " AND agencia.cod_agencia = '" . $this->obRMONContaCorrente->obRMONAgencia->inCodAgencia . "' ";
        $stFiltro .= " AND conta_corrente.cod_conta_corrente = '" . $this->obRMONContaCorrente->inCodigoConta . "' ";
        $stFiltro .= " AND cheque.num_cheque = '" . $this->stNumCheque . "' ";
        $stFiltro = ' WHERE ' . substr($stFiltro,4);

        $obErro = $this->obTTesourariaCheque->getCheque($rsCheque, $stFiltro, '', $boTransacao);
        if (!$obErro->ocorreu()) {
            $this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->stNumBanco = $rsCheque->getCampo('num_banco'         );
            $this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->stNomBanco = $rsCheque->getCampo('nom_banco'         );
            $this->obRMONContaCorrente->obRMONAgencia->stNumAgencia = $rsCheque->getCampo           ('num_agencia'       );
            $this->obRMONContaCorrente->obRMONAgencia->stNomAgencia = $rsCheque->getCampo           ('nom_agencia'       );
            $this->obRMONContaCorrente->stNumeroConta = $rsCheque->getCampo                         ('num_conta_corrente');
        }

        return $obErro;
    }

    /**
     * Método para buscar uma emissao de cheque na base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function findChequeEmissao($boTransacao = '')
    {
        $stFiltro  = " AND banco.cod_banco = '" . $this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco . "' ";
        $stFiltro .= " AND agencia.cod_agencia = '" . $this->obRMONContaCorrente->obRMONAgencia->inCodAgencia . "' ";
        $stFiltro .= " AND conta_corrente.cod_conta_corrente = '" . $this->obRMONContaCorrente->inCodigoConta . "' ";
        $stFiltro .= " AND cheque.num_cheque = '" . $this->stNumCheque . "' ";
        $stFiltro = ' WHERE ' . substr($stFiltro,4);

        $obErro = $this->obTTesourariaChequeEmissao->getChequeEmissao($rsCheque, $stFiltro, '', $boTransacao);
        if (!$obErro->ocorreu()) {
            $this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->stNumBanco = $rsCheque->getCampo('num_banco'         );
            $this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->stNomBanco = $rsCheque->getCampo('nom_banco'         );
            $this->obRMONContaCorrente->obRMONAgencia->stNumAgencia            = $rsCheque->getCampo('num_agencia'       );
            $this->obRMONContaCorrente->obRMONAgencia->stNomAgencia            = $rsCheque->getCampo('nom_agencia'       );
            $this->obRMONContaCorrente->stNumeroConta                          = $rsCheque->getCampo('num_conta_corrente');
            $this->stDtEmissao                                                 = $rsCheque->getCampo('data_emissao'	   );
            $this->flValor                                                     = $rsCheque->getCampo('valor'			   );
            $this->stDescricao                                                 = $rsCheque->getCampo('descricao'		   );
            $this->stTimestampEmissao                                          = $rsCheque->getCampo('timestamp_emissao' );
            $this->stTimestampBaixa                                            = $rsCheque->getCampo('timestamp_baixa'   );
            if ($rsCheque->getCampo('tipo_emissao') == 'transferencia') {
                $this->obRTesourariaTransferencia->obRCGM->stNomCGM            = $rsCheque->getCampo('nom_credor'        );
            } else {
                $this->obREmpenhoOrdemPagamento->obREmpenhoEmpenho->obRCGM->stNomCGM = $rsCheque->getCampo('nom_credor'  );
            }
        }

        return $obErro;
    }

    /**
     * Método para buscar uma emissao de cheque sintetico
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function findChequeAnalitico(&$rsCheque, $boTransacao = '')
    {
        $stFiltro  = " AND banco.cod_banco = '" . $this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco . "' ";
        $stFiltro .= " AND agencia.cod_agencia = '" . $this->obRMONContaCorrente->obRMONAgencia->inCodAgencia . "' ";
        $stFiltro .= " AND conta_corrente.cod_conta_corrente = '" . $this->obRMONContaCorrente->inCodigoConta . "' ";
        $stFiltro .= " AND cheque.num_cheque = '" . $this->stNumCheque . "' ";
        $stFiltro = ' WHERE ' . substr($stFiltro,4);

        $obErro = $this->obTTesourariaCheque->getChequeAnalitico($rsCheque, $stFiltro, '', $boTransacao);

        return $obErro;
    }

    /**
     * Método para buscar um conjunto de cheques
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object  $rsCheque
     * @param array   $arParam
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listCheque(&$rsCheque, $arParam = array(),$boTransacao = '')
    {
        if ($this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->stNumBanco != '') {
            $stFiltro  = " AND banco.num_banco = '" . $this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->stNumBanco . "' ";
        }
        if ($this->obRMONContaCorrente->obRMONAgencia->stNumAgencia != '') {
            $stFiltro .= " AND agencia.num_agencia = '" . $this->obRMONContaCorrente->obRMONAgencia->stNumAgencia . "' ";
        }
        if ($this->obRMONContaCorrente->stNumeroConta != '') {
            $stFiltro .= " AND conta_corrente.num_conta_corrente = '" . $this->obRMONContaCorrente->stNumeroConta . "' ";
        }
        if ($this->stNumCheque != '') {
            $stFiltro .= " AND cheque.num_cheque = '" . $this->stNumCheque . "' ";
        }
        if ($this->stNumChequeInicial != '') {
            $stFiltro .= " AND cheque.num_cheque >= '" . $this->stNumChequeInicial . "' ";
        }
        if ($this->stNumChequeFinal != '') {
            $stFiltro .= " AND cheque.num_cheque <= '" . $this->stNumChequeFinal . "' ";
        }
        if ($arParam['stTipoBusca'] == 'naoEmitidos') {
            $stFiltro .= " AND cheque_emissao.num_cheque IS NULL";
        } elseif ($arParam['stTipoBusca'] == 'emitidos') {
            $stFiltro .= " AND cheque_emissao.num_cheque IS NOT NULL ";
            //Restringe somente ao tipo de pagamento selecionado

            switch ($arParam['stTipoPagamento']) {
            case 'ordem_pagamento':
                $stFiltro .= " AND cheque_emissao_ordem_pagamento.num_cheque IS NOT NULL ";

                if ($this->obREmpenhoOrdemPagamento->inCodigoOrdemInicial) {
                    $stFiltro .= " AND cheque_emissao_ordem_pagamento.cod_ordem >= ".$this->obREmpenhoOrdemPagamento->inCodigoOrdemInicial." ";
                }
                if ($this->obREmpenhoOrdemPagamento->inCodigoOrdemFinal) {
                    $stFiltro .= " AND cheque_emissao_ordem_pagamento.cod_ordem <= ".$this->obREmpenhoOrdemPagamento->inCodigoOrdemFinal." ";
                }
                if ($this->obREmpenhoOrdemPagamento->stExercicio) {
                    $stFiltro .= " AND cheque_emissao_ordem_pagamento.exercicio = '" . $this->obREmpenhoOrdemPagamento->stExercicio . "' ";
                }
                if ($this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade) {
                    $stFiltro .= " AND cheque_emissao_ordem_pagamento.cod_entidade = " . $this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade . " ";
                }
                break;
            case 'transferencia':
                $stFiltro .= " AND cheque_emissao_transferencia.num_cheque IS NOT NULL ";
                $stFiltro .= " AND cheque_emissao_transferencia.cod_tipo = 5 ";
                if ($this->obModel->obRTesourariaTransferencia->roRTesourariaBoletim->stExercicio) {
                    $stFiltro .= " AND cheque_emissao_transferencia.exercicio = '" . $this->obModel->obRTesourariaTransferencia->roRTesourariaBoletim->stExercicio . "' ";
                }
                if ($this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade) {
                    $stFiltro .= ' AND cheque_emissao_transferencia.cod_entidade = ' .$this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade. ' ';
                }
                if ($this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaCredito) {
                    $stFiltro .= " AND cheque_emissao_transferencia.cod_plano_credito = " . $this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaCredito ." ";
                }
                if ($this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaDebito) {
                    $stFiltro .= " AND cheque_emissao_transferencia.cod_plano_debito = " . $this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaDebito ." ";
                }
                break;
            case 'despesa_extra':
                if ($arParam['inCodEntidade'] != '') {
                    $stFiltro .= ' AND cheque_emissao_recibo_extra.cod_entidade = ' . $arParam['inCodEntidade'] . ' ';
                }
                if ($arParam['stExercicio'] != '') {
                    $stFiltro .= " AND cheque_emissao_recibo_extra.exercicio = '" . $arParam['stExercicio'] . "' ";
                }
                if ($arParam['stDataEmissao'] != '') {
                    $stFiltro .= " AND TO_DATE(cheque_emissao_recibo_extra.timestamp,'yyyy-mm-dd') = TO_DATE('" . $arParam['stDataEmissao'] . "','dd/mm/yyyy') ";
                }
                if ($arParam['stNumRecibo'] != '') {
                    $stFiltro .= ' AND cheque_emissao_recibo_extra.cod_recibo_extra = ' . $arParam['stNumRecibo'] . ' ';
                }
                if ($arParam['inCodContaDespesa'] != '') {
                    $stFiltro .= ' AND cheque_emissao_recibo_extra.cod_plano = ' . $arParam['inCodContaDespesa'] . ' ';
                }

                break;
            }

            //Aplica os filtros de cada tipo possivel
        }
        if ($arParam['stTipoBusca'] == 'anulado') {
            $stFiltro .= ' AND cheque_emissao_anulada.num_cheque IS NOT NULL ';
        }
        if ($arParam['stAcao'] == 'anular') {
            $stFiltro .= ' AND cheque_emissao_anulada.num_cheque IS NULL ';
            $stFiltro .= ' AND cheque_emissao_baixa.num_cheque IS NULL';
        }
        if ($arParam['stBaixado'] == 'sim') {
            $stFiltro .= ' AND cheque_emissao_baixa.num_cheque IS NOT NULL ';
        }
        if ($arParam['stBaixado'] == 'nao') {
            $stFiltro .= ' AND cheque_emissao_baixa.num_cheque IS NULL ';
        }
        if ($stFiltro != '') {
            $stFiltro = ' WHERE ' . substr($stFiltro,4);
        }
        $stOrder  = ' ORDER BY TO_NUMBER(cheque.num_cheque,\'999999999999999\'), num_conta_corrente';

        if ($arParam['stAcao'] == 'anular') {
            $obErro = $this->obTTesourariaChequeEmissao->getChequeAnulacao($rsCheque, $stFiltro, $stOrder, $boTransacao);
        } else {
            $obErro = $this->obTTesourariaCheque->getCheque($rsCheque, $stFiltro, $stOrder, $boTransacao);
        }

        return $obErro;
    }

    /**
     * Método para buscar OP's que possam ser utilizadas para emitir cheque
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listChequeOPSaldo(&$rsCheque, $boTransacao = '')
    {
        if ($this->obREmpenhoOrdemPagamento->stExercicio) {
            $stFiltro .= " AND EE.exercicio  = CAST(".$this->obREmpenhoOrdemPagamento->stExercicio." as varchar) ";
            $stFiltroOrdem .= " AND EPL.exercicio_empenho = CAST(".$this->obREmpenhoOrdemPagamento->stExercicio." as varchar) ";
        }
        if ($this->obREmpenhoOrdemPagamento->inCodigoOrdemInicial) {
            $stFiltroOrdem .= " AND  EOP.cod_ordem >= ".$this->obREmpenhoOrdemPagamento->inCodigoOrdemInicial." ";
        }
        if ($this->obREmpenhoOrdemPagamento->inCodigoOrdemFinal) {
            $stFiltroOrdem .= " AND EOP.cod_ordem <= ".$this->obREmpenhoOrdemPagamento->inCodigoOrdemFinal." ";
        }
        if ($this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade) {
            $stFiltro .= " AND EE.cod_entidade in (".$this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade.") ";
            $stFiltroOrdem .= " AND EPL.cod_entidade IN(".$this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade.") ";
        }

        $stFiltroFinal = ' WHERE ((ordem_pagamento.vl_ordem) - COALESCE(cheque_emissao_ordem_pagamento.valor,0) - COALESCE(ordem_pagamento_retencao.vl_retencao,0)) > 0';

        $this->obTTesourariaChequeEmissaoOrdemPagamento->setDado('stFiltro',$stFiltro);
        $this->obTTesourariaChequeEmissaoOrdemPagamento->setDado('stFiltroOrdem',$stFiltroOrdem);

        $obErro = $this->obTTesourariaChequeEmissaoOrdemPagamento->recuperaChequesOPSaldo($rsCheque,$stFiltroFinal, "", $boTransacao);

        return $obErro;
    }

    /**
     * Método para buscar os cheques ja utilizados numa OP
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listChequesOP(&$rsCheque, $boTransacao = '')
    {
        if ($this->obREmpenhoOrdemPagamento->inCodigoOrdem) {
            $stFiltro .= " cheque_emissao_ordem_pagamento.cod_ordem = " . $this->obREmpenhoOrdemPagamento->inCodigoOrdem . " AND ";
        }
        if ($this->obREmpenhoOrdemPagamento->stExercicio) {
            $stFiltro .= " cheque_emissao_ordem_pagamento.exercicio = '" . $this->obREmpenhoOrdemPagamento->stExercicio . "' AND ";
        }
        if ($this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade) {
            $stFiltro .= " cheque_emissao_ordem_pagamento.cod_entidade = " . $this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade . " AND ";
        }
        $obErro = $this->obTTesourariaChequeEmissaoOrdemPagamento->listChequesEmissaoOP($rsCheque, $stFiltro);

        return $obErro;
    }

    /**
     * Método para buscar os cheques a serem baixados numa op
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listChequesOPBaixa(&$rsCheque, $boTransacao = '')
    {
        if ($this->obREmpenhoOrdemPagamento->inCodigoOrdem) {
            $stFiltro .= " cheque_emissao_ordem_pagamento.cod_ordem = " . $this->obREmpenhoOrdemPagamento->inCodigoOrdem . " AND ";
        }
        if ($this->obREmpenhoOrdemPagamento->stExercicio) {
            $stFiltro .= " cheque_emissao_ordem_pagamento.exercicio = '" . $this->obREmpenhoOrdemPagamento->stExercicio . "' AND ";
        }
        if ($this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade) {
            $stFiltro .= " cheque_emissao_ordem_pagamento.cod_entidade = " . $this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade . " AND ";
        }
        $stFiltro .= " NOT EXISTS ( SELECT 1
                                     FROM tesouraria.cheque_emissao_baixa
                                    WHERE cheque_emissao.cod_banco          = cheque_emissao_baixa.cod_banco
                                      AND cheque_emissao.cod_agencia        = cheque_emissao_baixa.cod_agencia
                                      AND cheque_emissao.cod_conta_corrente = cheque_emissao_baixa.cod_conta_corrente
                                      AND cheque_emissao.num_cheque         = cheque_emissao_baixa.num_cheque
                                      AND cheque_emissao.timestamp_emissao  = cheque_emissao_baixa.timestamp_emissao
                                      AND NOT EXISTS ( SELECT 1
                                                         FROM tesouraria.cheque_emissao_baixa_anulada
                                                        WHERE cheque_emissao_baixa.cod_banco          = cheque_emissao_baixa_anulada.cod_banco
                                                          AND cheque_emissao_baixa.cod_agencia        = cheque_emissao_baixa_anulada.cod_agencia
                                                          AND cheque_emissao_baixa.cod_conta_corrente = cheque_emissao_baixa_anulada.cod_conta_corrente
                                                          AND cheque_emissao_baixa.num_cheque         = cheque_emissao_baixa_anulada.num_cheque
                                                          AND cheque_emissao_baixa.timestamp_emissao  = cheque_emissao_baixa_anulada.timestamp_emissao
                                                          AND cheque_emissao_baixa.timestamp_baixa    = cheque_emissao_baixa_anulada.timestamp_baixa
                                                      )
                                  ) AND ";
        $stFiltro .= " plano_conta.cod_estrutural NOT LIKE '1.1.1.1.3%' AND ";
        $obErro = $this->obTTesourariaChequeEmissaoOrdemPagamento->listChequesEmissaoOP($rsCheque, $stFiltro);

        return $obErro;
    }

    /**
     * Método para buscar os cheques a serem anuladas na baixa de uma op
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listChequesOPAnularBaixa(&$rsCheque, $boTransacao = '')
    {
        $stFiltro = "";
        if ($this->obREmpenhoOrdemPagamento->inCodigoOrdem) {
            $stFiltro .= " cheque_emissao_ordem_pagamento.cod_ordem = " . $this->obREmpenhoOrdemPagamento->inCodigoOrdem . " AND ";
        }
        if ($this->obREmpenhoOrdemPagamento->stExercicio) {
            $stFiltro .= " cheque_emissao_ordem_pagamento.exercicio = '" . $this->obREmpenhoOrdemPagamento->stExercicio . "' AND ";
        }
        if ($this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade) {
            $stFiltro .= " cheque_emissao_ordem_pagamento.cod_entidade = " . $this->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade . " AND ";
        }
        $stFiltro .= " EXISTS ( SELECT 1
                                     FROM tesouraria.cheque_emissao_baixa
                                    WHERE cheque_emissao.cod_banco          = cheque_emissao_baixa.cod_banco
                                      AND cheque_emissao.cod_agencia        = cheque_emissao_baixa.cod_agencia
                                      AND cheque_emissao.cod_conta_corrente = cheque_emissao_baixa.cod_conta_corrente
                                      AND cheque_emissao.num_cheque         = cheque_emissao_baixa.num_cheque
                                      AND cheque_emissao.timestamp_emissao  = cheque_emissao_baixa.timestamp_emissao
                                      AND NOT EXISTS ( SELECT 1
                                                         FROM tesouraria.cheque_emissao_baixa_anulada
                                                        WHERE cheque_emissao_baixa.cod_banco          = cheque_emissao_baixa_anulada.cod_banco
                                                          AND cheque_emissao_baixa.cod_agencia        = cheque_emissao_baixa_anulada.cod_agencia
                                                          AND cheque_emissao_baixa.cod_conta_corrente = cheque_emissao_baixa_anulada.cod_conta_corrente
                                                          AND cheque_emissao_baixa.num_cheque         = cheque_emissao_baixa_anulada.num_cheque
                                                          AND cheque_emissao_baixa.timestamp_emissao  = cheque_emissao_baixa_anulada.timestamp_emissao
                                                          AND cheque_emissao_baixa.timestamp_baixa    = cheque_emissao_baixa_anulada.timestamp_baixa
                                                      )
                                  ) AND ";
        $stFiltro .= " plano_conta.cod_estrutural NOT LIKE '1.1.1.1.3%' AND ";
        $obErro = $this->obTTesourariaChequeEmissaoOrdemPagamento->listChequesEmissaoOP($rsCheque, $stFiltro);

        return $obErro;
    }

    /**
     * Método para buscar Transferencias que possam ser utilizadas para emitir cheque
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listChequeTransferenciaSaldo(&$rsCheque, $boTransacao = '')
    {
        $stFiltro = ' WHERE t.cod_tipo = ' . $this->obRTesourariaTransferencia->inCodTipoTransferencia . ' ';
        $stFiltro.= '   AND (coalesce(t.valor,0.00) - coalesce(te.valor,0.00) - COALESCE(cheque_emissao_transferencia.valor,0.00)) > 0 ';

        if ($this->obRTesourariaTransferencia->roRTesourariaBoletim->stExercicio) {
            $stFiltro .= " AND t.exercicio = '" . $this->obRTesourariaTransferencia->roRTesourariaBoletim->stExercicio . "' ";
        }
        if ($this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade) {
            $stFiltro .= " AND t.cod_entidade IN (" . $this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade . ") ";
        }
        if ($this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaCredito) {
               $stFiltro .= " AND t.cod_plano_credito = " . $this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaCredito ." ";
        }
        if ($this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaDebito) {
               $stFiltro .= " AND t.cod_plano_debito = " . $this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaDebito ." ";
        }

        $obErro = $this->obTTesourariaChequeEmissaoTransferencia->recuperaChequeTransferenciaSaldo($rsCheque, $stFiltro, $stOrder, $boTransacao);

        return $obErro;
    }

    /**
     * Método para buscar os cheques ja utilizados numa transferencia
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listChequesTransferencia(&$rsCheque, $boTransacao = '')
    {
        if ($this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->inCodLote) {
            $stFiltro .= ' cheque_emissao_transferencia.cod_lote = ' . $this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->inCodLote . ' AND';
        }
        if ($this->obRTesourariaTransferencia->roRTesourariaBoletim->stExercicio) {
            $stFiltro .= " cheque_emissao_transferencia.exercicio = '" . $this->obRTesourariaTransferencia->roRTesourariaBoletim->stExercicio . "' AND ";
        }
        if ($this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade) {
            $stFiltro .= ' cheque_emissao_transferencia.cod_entidade = ' . $this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade . ' AND';
        }
        if ($this->obRTesourariaTransferencia->obRTesourariaAutenticacao->stTipo) {
            $stFiltro .= " cheque_emissao_transferencia.tipo = '" . $this->obRTesourariaTransferencia->obRTesourariaAutenticacao->stTipo . "' AND ";
        }

        if ($stFiltro != '') {
            $stFiltro = " WHERE " . substr($stFiltro,0,-4);
        }
        $obErro = $this->obTTesourariaChequeEmissaoTransferencia->listChequesEmissaoTransferencia($rsCheque, $stFiltro);

        return $obErro;
    }

    /**
     * Método para buscar os cheques vinculados a transferencia para serem baixados
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listChequesTransferenciaBaixa($arParam, &$rsCheque, $boTransacao = '')
    {
        if ($this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->stNumBanco != '') {
            $this->obTTesourariaChequeEmissaoTransferencia->setDado('num_banco', $this->obRMONContaCorrente->obRMONAgencia->obRMONBanco->stNumBanco);
        }
        if ($this->obRMONContaCorrente->obRMONAgencia->stNumAgencia != '') {
            $this->obTTesourariaChequeEmissaoTransferencia->setDado('num_agencia', $this->obRMONContaCorrente->obRMONAgencia->stNumAgencia);
        }
        if ($this->obRMONContaCorrente->stNumeroConta != '') {
            $this->obTTesourariaChequeEmissaoTransferencia->setDado('num_conta_corrente', $this->obRMONContaCorrente->stNumeroConta);
        }
        if ($this->stNumCheque != '') {
            $this->obTTesourariaChequeEmissaoTransferencia->setDado('num_cheque', $this->stNumCheque);
        }
        if ($this->obRTesourariaTransferencia->roRTesourariaBoletim->stExercicio) {
            $stFiltro .= " AND t.exercicio = '" . $this->obRTesourariaTransferencia->roRTesourariaBoletim->stExercicio . "' ";
        }
        if ($this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade) {
            $stFiltro .= " AND t.cod_entidade IN (" . $this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade . ") ";
        }
        if ($this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaCredito) {
               $stFiltro .= " AND t.cod_plano_credito = " . $this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaCredito ." ";
        }
        if ($this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaDebito) {
               $stFiltro .= " AND t.cod_plano_debito = " . $this->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaDebito ." ";
        }
        $stFiltro .= " AND (COALESCE(t.valor,0.00) - COALESCE(te.valor,0.00)) = (COALESCE(cheque_emissao_transferencia.valor,0.00)) ";
        $stFiltro .= " AND cheque_emissao_transferencia.tipo = 'T' ";

        if ($arParam['stAcao'] == 'baixar') {
            $this->obTTesourariaChequeEmissaoTransferencia->setDado('baixado','nao');
        } else {
            $this->obTTesourariaChequeEmissaoTransferencia->setDado('baixado','sim');
        }

        if ($stFiltro != '') {
            $stFiltro = " WHERE " . substr($stFiltro,4);
        }
        $obErro = $this->obTTesourariaChequeEmissaoTransferencia->listChequesEmissaoTransferenciaBaixa($rsCheque, $stFiltro);

        return $obErro;
    }

    /**
     * Método para buscar recibos extra que possam ser utilizadas para emitir cheque
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listChequeReciboExtraSaldo(&$rsCheque, $arParam, $boTransacao = '')
    {
        if ($arParam['inCodEntidade'] != '') {
            $stFiltro .= ' AND recibo_extra.cod_entidade = ' . $arParam['inCodEntidade'] . ' ';
        }
        if ($arParam['stExercicio'] != '') {
            $stFiltro .= " AND recibo_extra.exercicio = '" . $arParam['stExercicio'] . "' ";
        }
        if ($arParam['stDataEmissao'] != '') {
            $stFiltro .= " AND TO_DATE(recibo_extra.timestamp,'yyyy-mm-dd') = TO_DATE('" . $arParam['stDataEmissao'] . "','dd/mm/yyyy') ";
        }
        if ($arParam['stNumRecibo'] != '') {
            $stFiltro .= ' AND recibo_extra.cod_recibo_extra = ' . $arParam['stNumRecibo'] . ' ';
        }
        if ($arParam['inCodContaDespesa'] != '') {
            $stFiltro .= ' AND recibo_extra.cod_plano = ' . $arParam['inCodContaDespesa'] . ' ';
        }
        $stFiltro .= " AND recibo_extra.tipo_recibo = 'D' ";

        $obErro = $this->obTTesourariaChequeEmissaoReciboExtra->recuperaChequeReciboExtraSaldo($rsCheque, $stFiltro, $stOrder, $boTransacao);

        return $obErro;
    }

    /**
     * Método para buscar os cheques ja utilizados num recibo extra
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function listChequesReciboExtra(&$rsCheque, $arParam, $boTransacao = '')
    {
        if ($arParam['inCodEntidade'] != '') {
            $stFiltro .= ' AND cheque_emissao_recibo_extra.cod_entidade = ' . $arParam['inCodEntidade'] . ' ';
        }
        if ($arParam['stExercicio'] != '') {
            $stFiltro .= " AND cheque_emissao_recibo_extra.exercicio = '" . $arParam['stExercicio'] . "' ";
        }
        if ($arParam['inCodReciboExtra'] != '') {
            $stFiltro .= ' AND cheque_emissao_recibo_extra.cod_recibo_extra = ' . $arParam['inCodReciboExtra'] . ' ';
        }
        $stFiltro .= " AND cheque_emissao_recibo_extra.tipo_recibo = 'D' ";

        $stFiltro = ' WHERE ' . substr($stFiltro, 4);
        $obErro = $this->obTTesourariaChequeEmissaoReciboExtra->listChequesEmissaoReciboExtra($rsCheque, $stFiltro);

        return $obErro;
    }

    /**
     * Método para buscar os dados de uma conta de banco
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param boolean $boTransacao
     *
     * @return object $obErro
     */
    public function searchContaBanco()
    {
        $obErro = $this->obRContabilidadePlanoBanco->consultar();

        return $obErro;
    }
}
