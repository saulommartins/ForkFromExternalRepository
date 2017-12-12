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
    * Classe de mapeamento da tabela NOVA_CONTABILIDADE.CONFIGURACAO_LANCAMENTO_CREDITO
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

class TContabilidadeConfiguracaoLancamentoCredito extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TContabilidadeConfiguracaoLancamentoCredito()
    {
        parent::Persistente();
        $this->setTabela('contabilidade.configuracao_lancamento_credito');

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

    /**
        * Método Retorna valores das configurações de débito e crédito
        * @access Public
    */
    public function recuperaCodigoPlano(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaCodigoPlano().$stFiltro.$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        $this->setDebug($stSql);

        return $obErro;
    }

    public function montaRecuperaCodigoPlano()
    {
        $stSql = '
        SELECT pa.cod_plano
          from contabilidade.configuracao_lancamento_credito as clc
         inner join contabilidade.plano_conta as pc
            on pc.exercicio = clc.exercicio
           and pc.cod_conta = clc.cod_conta
         inner join contabilidade.plano_analitica as pa
            on pa.exercicio = clc.exercicio
           and pa.cod_conta = clc.cod_conta
        ';

        return $stSql;
    }

    /**
        * Método Retorna valores das configurações de débito e crédito
        * @access Public
    */
    public function recuperaContasConfiguracaoDespesa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaContasConfiguracaoDespesa().$stFiltro.$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        $this->setDebug($stSql);

        return $obErro;
    }

    /**
        * Método Retorna Record Set
        * @access Public
    */
    public function montaRecuperaContasConfiguracaoDespesa()
    {
        $stSql = "
             SELECT configuracao_lancamento_credito.cod_conta AS conta_credito
                     , configuracao_lancamento_debito.cod_conta AS conta_debito
                     , CASE WHEN   plano_conta_debito.cod_estrutural like '3.1.1%'
                                OR plano_conta_debito.cod_estrutural like '3.1.2%'
                                OR plano_conta_debito.cod_estrutural like '3.1.3%'
                                OR plano_conta_debito.cod_estrutural like '3.1.8%'
                                OR plano_conta_debito.cod_estrutural like '3.1.9%'
                                OR plano_conta_debito.cod_estrutural like '3.2.0%'
                                OR plano_conta_debito.cod_estrutural like '3.2.1%'
                                OR plano_conta_debito.cod_estrutural like '3.2.2%'
                                OR plano_conta_debito.cod_estrutural like '3.2.4%'
                                OR plano_conta_debito.cod_estrutural like '3.2.5%'
                                OR plano_conta_debito.cod_estrutural like '3.2.9%'
                            THEN  'despesaPessoal'
                            WHEN   plano_conta_debito.cod_estrutural like '3.3.2%'
                                OR plano_conta_debito.cod_estrutural like '3.4.1%'
                                OR plano_conta_debito.cod_estrutural like '3.4.2%'
                                OR plano_conta_debito.cod_estrutural like '3.4.3%'
                                OR plano_conta_debito.cod_estrutural like '3.4.4%'
                                OR plano_conta_debito.cod_estrutural like '3.4.9%'
                                OR plano_conta_debito.cod_estrutural like '3.5.5%'
                            THEN 'demaisDespesas'
                            WHEN plano_conta_debito.cod_estrutural like '1.1.5.6%'
                            THEN 'materialConsumo'
                            WHEN plano_conta_debito.cod_estrutural like '1.2.3%'
                            THEN 'materialPermanente'
                            WHEN plano_conta_debito.cod_estrutural like '3.3.1.1%'
                            THEN 'almoxarifado'
                       END AS tipo_despesa
                     , configuracao_lancamento_debito.cod_conta_despesa
                  FROM contabilidade.configuracao_lancamento_credito
            INNER JOIN contabilidade.configuracao_lancamento_debito
                    ON configuracao_lancamento_credito.exercicio = configuracao_lancamento_debito.exercicio
                   AND configuracao_lancamento_credito.cod_conta_despesa = configuracao_lancamento_debito.cod_conta_despesa
                   AND configuracao_lancamento_credito.tipo = configuracao_lancamento_debito.tipo
                   AND configuracao_lancamento_credito.estorno = configuracao_lancamento_debito.estorno
            INNER JOIN contabilidade.plano_conta plano_conta_credito
                    ON plano_conta_credito.cod_conta = configuracao_lancamento_credito.cod_conta
                   AND plano_conta_credito.exercicio = configuracao_lancamento_credito.exercicio
            INNER JOIN contabilidade.plano_conta plano_conta_debito
                    ON plano_conta_debito.cod_conta = configuracao_lancamento_debito.cod_conta
                   AND plano_conta_debito.exercicio = configuracao_lancamento_debito.exercicio  \n";

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
}
?>
