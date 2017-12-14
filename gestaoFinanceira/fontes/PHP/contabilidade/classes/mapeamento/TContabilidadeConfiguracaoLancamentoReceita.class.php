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

    $Id: TContabilidadeConfiguracaoLancamentoReceita.class.php 66481 2016-09-01 20:15:15Z michel $

    * Casos de uso: uc-02.03.03
                    uc-02.02.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TContabilidadeConfiguracaoLancamentoReceita extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('contabilidade.configuracao_lancamento_receita');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_conta_receita,estorno');

        $this->AddCampo('cod_conta','integer',true,'',false,true);
        $this->AddCampo('exercicio','char',true,'04',true,true);
        $this->AddCampo('cod_conta_receita','integer',true,'',true,true);
        $this->AddCampo('estorno','boolean',true,'',true,false);
    }

    /**
        * Método Retorna valores das configurações de crédito
        * @access Public
    */
    public function recuperaContasConfiguracaoReceita(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaContasConfiguracaoReceita().$stFiltro.$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        $this->setDebug($stSql);

        return $obErro;
    }

    /**
        * Método Retorna Record Set
        * @access Public
    */
    public function montaRecuperaContasConfiguracaoReceita()
    {
        $stSql = " SELECT configuracao_lancamento_receita.cod_conta
                     , CASE WHEN plano_conta.cod_estrutural LIKE '2.1.2.%' THEN
                            'operacoesCredito'
                            WHEN plano_conta.cod_estrutural LIKE '1.2.3.%' THEN
                            'alienacaoBens'
                            WHEN plano_conta.cod_estrutural LIKE '1.1.2.3.%'
                              OR plano_conta.cod_estrutural LIKE '1.1.2.4.%'
                              OR plano_conta.cod_estrutural LIKE '1.1.2.5.1.%' THEN
                            'dividaAtiva'
                       ELSE
                            'arrecadacaoDireta'
                       END AS tipo_arrecadacao
                     , configuracao_lancamento_receita.cod_conta_receita
                     , CASE WHEN conta_receita.vl_arrecadacao > 0
                            THEN TRUE
                            ELSE FALSE
                       END AS bo_arrecadacao
                  FROM contabilidade.configuracao_lancamento_receita
            INNER JOIN contabilidade.plano_conta
                    ON configuracao_lancamento_receita.cod_conta = plano_conta.cod_conta
                   AND configuracao_lancamento_receita.exercicio = plano_conta.exercicio

             LEFT JOIN ( SELECT conta_receita.exercicio
                              , conta_receita.cod_conta
                              , SUM(arrecadacao_receita.vl_arrecadacao) AS vl_arrecadacao
                           FROM orcamento.conta_receita
                     INNER JOIN orcamento.receita
                             ON receita.exercicio            = conta_receita.exercicio
                            AND receita.cod_conta            = conta_receita.cod_conta
                     INNER JOIN tesouraria.arrecadacao_receita
                             ON arrecadacao_receita.exercicio            = receita.exercicio
                            AND arrecadacao_receita.cod_receita          = receita.cod_receita
                          WHERE conta_receita.exercicio = '".Sessao::getExercicio()."'
                       GROUP BY conta_receita.exercicio
                              , conta_receita.cod_conta
                       ) AS conta_receita
                    ON conta_receita.exercicio = configuracao_lancamento_receita.exercicio
                   AND conta_receita.cod_conta = configuracao_lancamento_receita.cod_conta_receita
                   ";

        return $stSql;
    }

    /**
        * Método Retorna valores das configurações de crédito
        * @access Public
    */
    public function recuperaContasReceita(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaContasReceita().$stFiltro.$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        $this->setDebug($stSql);

        return $obErro;
    }

    /**
        * Método Retorna Record Set
        * @access Public
    */
    public function montaRecuperaContasReceita()
    {
        $stSql = "  SELECT configuracao_lancamento_receita.cod_conta
                         , plano_conta.cod_estrutural
                         , configuracao_lancamento_receita.cod_conta_receita
                         , receita.cod_receita
                         , plano_analitica.cod_plano
                      FROM contabilidade.configuracao_lancamento_receita
                INNER JOIN contabilidade.plano_conta
                        ON configuracao_lancamento_receita.cod_conta = plano_conta.cod_conta
                       AND configuracao_lancamento_receita.exercicio = plano_conta.exercicio
                INNER JOIN contabilidade.plano_analitica
                        ON plano_conta.cod_conta = plano_analitica.cod_conta
                       AND plano_conta.exercicio = plano_analitica.exercicio
                INNER JOIN orcamento.receita
                        ON configuracao_lancamento_receita.cod_conta_receita = receita.cod_conta
                       AND configuracao_lancamento_receita.exercicio = receita.exercicio
                   ";

        return $stSql;
    }

    public function salvar()
    {
        $obErro = new Erro;
        $inCodConta = $this->getDado('cod_conta');
        $obErro = $this->consultar();

        $this->setDado('cod_conta', $inCodConta);

        if ($obErro->ocorreu()) { // se ocorreu não possui registros
            if (!empty($inCodConta)) {
                $obErro = $this->inclusao();
            }
        } else {
            if (empty($inCodConta)) {
                $obErro = $this->exclusao();
            } else {
                $obErro = $this->alteracao();
            }
        }

        return $obErro;
    }
}
?>
