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
    * Classe de mapeamento da tabela CONTABILIDADE.CONFIGURACAO_LANCAMENTO_DEBITO
    * Data de Criação: 24/10/2011

    * @author Analista: Tonismar Bernardo
    * @author Desenvolvedor: Davi Aroldi

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-02.03.03
                    uc-02.02.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TContabilidadeConfiguracaoLancamentoDebito extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TContabilidadeConfiguracaoLancamentoDebito()
    {
        parent::Persistente();
        $this->setTabela('contabilidade.configuracao_lancamento_debito');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,escrituracao,cod_conta_despesa,estorno,tipo');

        $this->AddCampo('cod_conta','integer',true,'',true,true);
        $this->AddCampo('exercicio','char',true,'04',true,true);
        $this->AddCampo('cod_conta_despesa','integer',true,'',true,true);
        $this->AddCampo('tipo','char',true,'20',false,false);
        $this->AddCampo('estorno','boolean',true,'',true,false);
        $this->AddCampo('rpps','boolean',true,'',false,false);

    }

    /**
        * Método Retorna Record Set
        * @access Public
    */
    public function consultarDespesa($boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaConsultarDespesa();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( !$rsRecordSet->eof() ) {
                foreach ($this->arEstrutura AS $obCampo) {
                    $this->setDado( $obCampo->getNomeCampo(), $rsRecordSet->getCampo( $obCampo->getNomeCampo() ) );
                }
            } else {
                $obErro->setDescricao( 'Nenhum registro encontrado!' );
            }
        }
        $this->setDebug($stSql);

        return $obErro;
    }

    /**
        * Método Retorna Sql Montado
        * @access Public
    */
    public function montaConsultarDespesa()
    {
        $stSql = " SELECT cod_conta
                        , exercicio
                        , cod_conta_despesa
                        , estorno
                        , tipo
                        , rpps
                     FROM ".$this->getTabela()."
                    WHERE 1 = 1";
        if ($this->getDado('exercicio')) {
            $stSql .= " AND exercicio = '".$this->getDado('exercicio')."' ";
        }

        if ($this->getDado('cod_conta_despesa')) {
            $stSql .= " AND cod_conta_despesa = ".$this->getDado('cod_conta_despesa')." ";
        }

        if ($this->getDado('estorno')) {
            $stSql .= " AND estorno = '".$this->getDado('estorno')."' ";
        }

        if ($this->getDado('tipo')) {
            $stSql .= " AND tipo = '".$this->getDado('tipo')."' ";
        }

        return $stSql;
    }

    public function salvar()
    {
        $inCodConta = $this->getDado('cod_conta');
        $stEntidadeRPPS = $this->getDado('rpps');
        $stEstorno = $this->getDado('estorno');
        $obErro = $this->consultarDespesa();

        $this->setDado('cod_conta', $inCodConta);
        $this->setDado('rpps', $stEntidadeRPPS);
        $this->setDado('estorno', $stEstorno);

        if ($obErro->ocorreu()) { //se $obErro->ocorreu() não possui registro referente a estas chaves
            if (!empty($inCodConta)) {
                $this->inclusao();
            }
        } else {
            if (empty($inCodConta)) {
                $this->exclusao();
            } else {
                $this->alteracao();
            }
        }
    }

    public function recuperaContasDebitoCredito(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stGroup = " GROUP BY configuracao_lancamento_debito.cod_conta
                 , configuracao_lancamento_credito.cod_conta
                 , configuracao_lancamento_debito.cod_conta_despesa
                 , conta_despesa.cod_estrutural ";
        $stSql = $this->montaContasDebitoCredito().$stFiltro.$stGroup.$stOrder;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaContasDebitoCredito()
    {
        $stSql = "
            SELECT configuracao_lancamento_debito.cod_conta as conta_debito
                 , configuracao_lancamento_credito.cod_conta as conta_credito
                 , configuracao_lancamento_debito.cod_conta_despesa as conta_despesa
                 , conta_despesa.cod_estrutural
              FROM contabilidade.configuracao_lancamento_debito
        INNER JOIN contabilidade.configuracao_lancamento_credito
                ON configuracao_lancamento_debito.cod_conta_despesa = configuracao_lancamento_credito.cod_conta_despesa
               AND configuracao_lancamento_debito.exercicio = configuracao_lancamento_credito.exercicio
               AND configuracao_lancamento_debito.estorno = configuracao_lancamento_credito.estorno
               AND configuracao_lancamento_debito.tipo = configuracao_lancamento_credito.tipo
        INNER JOIN orcamento.conta_despesa
                ON contabilidade.configuracao_lancamento_credito.cod_conta_despesa = conta_despesa.cod_conta
               AND contabilidade.configuracao_lancamento_credito.exercicio = conta_despesa.exercicio
        ";

        return $stSql;
    }
}
?>
