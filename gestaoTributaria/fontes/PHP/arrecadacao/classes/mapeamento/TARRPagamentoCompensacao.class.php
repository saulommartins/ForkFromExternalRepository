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
    * Classe de mapeamento da tabela ARRECADACAO.PAGAMENTO_COMPENSACAO
    * Data de Criação: 10/12/2007

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRPagamentoCompensacao.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.10
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRPagamentoCompensacao extends Persistente
{
    public function TARRPagamentoCompensacao()
    {
        parent::Persistente();
        $this->setTabela('arrecadacao.pagamento_compensacao');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_compensacao,numeracao,ocorrencia_pagamento,cod_convenio');

        $this->AddCampo( 'cod_compensacao', 'integer', true, '', true, true);
        $this->AddCampo( 'numeracao', 'varchar', true, '17', true, true);
        $this->AddCampo( 'ocorrencia_pagamento', 'integer', true, '', true, true);
        $this->AddCampo( 'cod_convenio', 'integer', true, '', true, true);
    }

    public function ListaSaldoDisponivel(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stFiltro .= " GROUP BY 1
                       ) as compensacao
                           INNER JOIN
                           arrecadacao.compensacao_resto
                           ON
                           compensacao_resto.cod_compensacao = compensacao.cod_compensacao      \n";

        $stSql = $this->montaListaSaldoDisponivel().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaSaldoDisponivel()
    {
        $stSql = "
                   SELECT
                      COALESCE( sum( compensacao_resto.valor ), 0.00 ) AS saldo_disponivel
                   FROM (

                    SELECT
                    compensacao_resto.cod_compensacao

                    FROM
                        arrecadacao.calculo

                    LEFT JOIN
                        arrecadacao.imovel_calculo
                    ON
                        imovel_calculo.cod_calculo = calculo.cod_calculo

                    LEFT JOIN
                        arrecadacao.cadastro_economico_calculo
                    ON
                        cadastro_economico_calculo.cod_calculo = calculo.cod_calculo

                    LEFT JOIN
                        arrecadacao.calculo_cgm
                    ON
                        calculo_cgm.cod_calculo = calculo.cod_calculo

                    INNER JOIN
                        arrecadacao.lancamento_calculo
                    ON
                        lancamento_calculo.cod_calculo = calculo.cod_calculo

                    INNER JOIN
                        arrecadacao.parcela
                    ON
                        parcela.cod_lancamento = lancamento_calculo.cod_lancamento

                    INNER JOIN
                        arrecadacao.carne
                    ON
                        carne.cod_parcela = parcela.cod_parcela

                    INNER JOIN
                        arrecadacao.pagamento
                    ON
                        pagamento.numeracao = carne.numeracao
                        AND pagamento.cod_convenio = carne.cod_convenio

                    INNER JOIN
                        arrecadacao.pagamento_compensacao
                    ON
                        pagamento_compensacao.numeracao = pagamento.numeracao
                        AND pagamento_compensacao.ocorrencia_pagamento = pagamento.ocorrencia_pagamento
                        AND pagamento_compensacao.cod_convenio = pagamento.cod_convenio

                    INNER JOIN
                        arrecadacao.compensacao_resto
                    ON
                        compensacao_resto.cod_compensacao = pagamento_compensacao.cod_compensacao

                    WHERE ";

        return $stSql;
    }

    public function ListaParcelasPagas(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaListaParcelasPagas().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaParcelasPagas()
    {
        $stSql = " SELECT DISTINCT
                        calculo_cgm.numcgm,
                        (
                            SELECT
                                nom_cgm
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = calculo_cgm.numcgm
                        )AS nom_cgm,
                        CASE WHEN parcela.nr_parcela = 0 THEN
                            'única'
                        ELSE
                            parcela.nr_parcela::varchar
                        END AS nr_parcela,
                        parcela.cod_parcela,
                        CASE WHEN carne.cod_convenio = -1 THEN
                            split_part( arrecadacao.fn_busca_origem_lancamento_sem_exercicio( parcela.cod_lancamento, 1, 1 ), '§', 3)||'/'||split_part( arrecadacao.fn_busca_origem_lancamento_sem_exercicio( parcela.cod_lancamento, 1, 1 ), '§', 4)
                        ELSE
                            arrecadacao.fn_busca_origem_lancamento_sem_exercicio( parcela.cod_lancamento, 1, 1 )
                        END AS origem,
                        (
                            COALESCE(
                                (
                                    SELECT
                                        parcela_desconto.valor
                                    FROM
                                        arrecadacao.parcela_desconto
                                    WHERE
                                        parcela_desconto.cod_parcela = parcela.cod_parcela
                                ),
                                parcela.valor
                            )
                        ) AS valor_parcela,
                        pagamento.valor AS valor_pago,
                        to_char(parcela.vencimento, 'dd/mm/yyyy' ) AS vencimento,
                        carne.numeracao,
                        carne.exercicio,
                        pagamento.ocorrencia_pagamento,
                        pagamento.cod_convenio

                    FROM
                        arrecadacao.calculo

                    LEFT JOIN
                        arrecadacao.imovel_calculo
                    ON
                        imovel_calculo.cod_calculo = calculo.cod_calculo

                    LEFT JOIN
                        arrecadacao.cadastro_economico_calculo
                    ON
                        cadastro_economico_calculo.cod_calculo = calculo.cod_calculo

                    INNER JOIN
                        arrecadacao.calculo_cgm
                    ON
                        calculo_cgm.cod_calculo = calculo.cod_calculo

                    INNER JOIN
                        arrecadacao.lancamento_calculo
                    ON
                        lancamento_calculo.cod_calculo = calculo.cod_calculo

                    INNER JOIN
                        arrecadacao.parcela
                    ON
                        parcela.cod_lancamento = lancamento_calculo.cod_lancamento

                    INNER JOIN
                        arrecadacao.carne
                    ON
                        carne.cod_parcela = parcela.cod_parcela

                    INNER JOIN
                        arrecadacao.pagamento
                    ON
                        pagamento.numeracao = carne.numeracao
                        AND pagamento.cod_convenio = carne.cod_convenio

                    LEFT JOIN
                        arrecadacao.pagamento_compensacao
                    ON
                        pagamento_compensacao.numeracao = pagamento.numeracao
                        AND pagamento_compensacao.ocorrencia_pagamento = pagamento.ocorrencia_pagamento
                        AND pagamento_compensacao.cod_convenio = pagamento.cod_convenio

                    LEFT JOIN
                        arrecadacao.pagamento_diferenca_compensacao
                    ON
                        pagamento_diferenca_compensacao.numeracao = pagamento.numeracao
                        AND pagamento_diferenca_compensacao.ocorrencia_pagamento = pagamento.ocorrencia_pagamento
                        AND pagamento_diferenca_compensacao.cod_convenio = pagamento.cod_convenio

                    WHERE
                        pagamento_compensacao.numeracao IS NULL
                        AND pagamento_diferenca_compensacao.numeracao IS NULL
                        AND pagamento.ocorrencia_pagamento > 1
                        AND pagamento.cod_tipo != 12 ";

        return $stSql;
    }

    public function ListaParcelasVencer(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaListaParcelasVencer().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaParcelasVencer()
    {
        $stSql = " SELECT DISTINCT
                        calculo_cgm.numcgm,
                        (
                            SELECT
                                nom_cgm
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = calculo_cgm.numcgm
                        )AS nom_cgm,
                        CASE WHEN parcela.nr_parcela = 0 THEN
                            'única'
                        ELSE
                            parcela.nr_parcela::varchar
                        END AS nr_parcela,
                        parcela.cod_parcela,
                        arrecadacao.fn_busca_origem_lancamento_sem_exercicio( parcela.cod_lancamento, 1, 1 ) AS origem,
                        (
                            COALESCE(
                                (
                                    SELECT
                                        parcela_desconto.valor
                                    FROM
                                        arrecadacao.parcela_desconto
                                    WHERE
                                        parcela_desconto.cod_parcela = parcela.cod_parcela
                                ),
                                parcela.valor
                            )
                        ) AS valor_parcela,
                        (
                            COALESCE(
                                (
                                    SELECT
                                        parcela_desconto.valor
                                    FROM
                                        arrecadacao.parcela_desconto
                                    WHERE
                                        parcela_desconto.cod_parcela = parcela.cod_parcela
                                ),
                                parcela.valor
                            )
                                +
                            arrecadacao.aplica_acrescimo_parcela( carne.numeracao::varchar, carne.exercicio::integer, parcela.cod_parcela, now()::date, 3, 1)
                                +
                            arrecadacao.aplica_acrescimo_parcela( carne.numeracao::varchar, carne.exercicio::integer, parcela.cod_parcela, now()::date, 1, 2)
                                +
                            arrecadacao.aplica_acrescimo_parcela( carne.numeracao::varchar, carne.exercicio::integer, parcela.cod_parcela, now()::date, 2, 3)
                        ) AS valor_corrigido,
                        to_char(parcela.vencimento, 'dd/mm/yyyy' ) AS vencimento,
                        carne.numeracao,
                        carne.exercicio,
                        carne.cod_convenio

                    FROM
                        arrecadacao.calculo

                    LEFT JOIN
                        arrecadacao.imovel_calculo
                    ON
                        imovel_calculo.cod_calculo = calculo.cod_calculo

                    LEFT JOIN
                        arrecadacao.cadastro_economico_calculo
                    ON
                        cadastro_economico_calculo.cod_calculo = calculo.cod_calculo

                    INNER JOIN
                        arrecadacao.calculo_cgm
                    ON
                        calculo_cgm.cod_calculo = calculo.cod_calculo

                    INNER JOIN
                        arrecadacao.lancamento_calculo
                    ON
                        lancamento_calculo.cod_calculo = calculo.cod_calculo

                    INNER JOIN
                        arrecadacao.parcela
                    ON
                        parcela.cod_lancamento = lancamento_calculo.cod_lancamento

                    INNER JOIN
                        arrecadacao.carne
                    ON
                        carne.cod_parcela = parcela.cod_parcela

                    LEFT JOIN
                        arrecadacao.pagamento
                    ON
                        pagamento.numeracao = carne.numeracao
                        AND pagamento.cod_convenio = carne.cod_convenio

                    LEFT JOIN
                        arrecadacao.carne_devolucao
                    ON
                        carne_devolucao.numeracao = carne.numeracao

                    WHERE
                        carne_devolucao IS NULL
                        AND pagamento.numeracao IS NULL
                        AND carne.cod_convenio != -1 ";

        return $stSql;
    }

    public function ListaParcelasVencerDividaAtiva(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaListaParcelasVencerDividaAtiva($stFiltro);
        $this->stDebug = $stSql;
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaParcelasVencerDividaAtiva($stFiltro)
    {
        $stSql = "
               SELECT DISTINCT parcelamento.cod_modalidade
                    , to_char(parcela.vencimento, 'dd/mm/yyyy' ) AS vencimento
                    , parcela.vlr_parcela AS valor_parcela
                    , parcela.vlr_parcela
                          +
                      split_part( aplica_acrescimo_modalidade( 0, parcelamento.cod_inscricao, parcelamento.exercicio::integer, parcelamento.cod_modalidade, 2, parcelamento.num_parcelamento, parcela.vlr_parcela, parcela.vencimento, now()::date, 'true' ), ';', 1 )::numeric
                          +
                      split_part( aplica_acrescimo_modalidade( 0, parcelamento.cod_inscricao, parcelamento.exercicio::integer, parcelamento.cod_modalidade, 3, parcelamento.num_parcelamento, parcela.vlr_parcela, parcela.vencimento, now()::date, 'true' ), ';', 1 )::numeric
                          +
                      split_part( aplica_acrescimo_modalidade( 0, parcelamento.cod_inscricao, parcelamento.exercicio::integer, parcelamento.cod_modalidade, 1, parcelamento.num_parcelamento, parcela.vlr_parcela, parcela.vencimento, now()::date, 'true' ), ';', 1 )::numeric AS valor_corrigido
                    , split_part( arrecadacao.fn_busca_origem_lancamento_sem_exercicio( parcela.cod_lancamento, 1, 1 ), '§', 3)||'/'||split_part( arrecadacao.fn_busca_origem_lancamento_sem_exercicio( parcela.cod_lancamento, 1, 1 ), '§', 4) AS origem
                    , carne.numeracao
                    , carne.cod_convenio
                    , carne.exercicio
                    , CASE WHEN parcela.nr_parcela = 0 THEN
                            'única'
                      ELSE
                            parcela.nr_parcela::varchar
                      END AS nr_parcela
                 FROM ( SELECT max(parcelamento.num_parcelamento) AS num_parcelamento
                             , parcelamento.cod_modalidade
                             , divida_cgm.cod_inscricao
                             , divida_cgm.exercicio
                          FROM divida.parcelamento
                    INNER JOIN divida.divida_parcelamento
                            ON divida_parcelamento.num_parcelamento = parcelamento.num_parcelamento
                    INNER JOIN divida.divida_cgm
                            ON divida_cgm.cod_inscricao = divida_parcelamento.cod_inscricao
                           AND divida_cgm.exercicio = divida_parcelamento.exercicio
                     LEFT JOIN divida.divida_imovel
                            ON divida_imovel.cod_inscricao = divida_parcelamento.cod_inscricao
                           AND divida_imovel.exercicio = divida_parcelamento.exercicio
                     LEFT JOIN divida.divida_empresa
                            ON divida_empresa.cod_inscricao = divida_parcelamento.cod_inscricao
                           AND divida_empresa.exercicio = divida_parcelamento.exercicio
                         WHERE parcelamento.numero_parcelamento != -1
                         ".$stFiltro."
                      GROUP BY parcelamento.cod_modalidade
                             , divida_cgm.cod_inscricao
                             , divida_cgm.exercicio
                      )AS parcelamento
           INNER JOIN ( SELECT dp.vlr_parcela
                             , parcela.vencimento
                             , parcela.cod_lancamento
                             , parcela.cod_parcela
                             , parcela.nr_parcela
                             , parcela_calculo.num_parcelamento
                          FROM arrecadacao.parcela
                    INNER JOIN arrecadacao.lancamento_calculo
                            ON lancamento_calculo.cod_lancamento = parcela.cod_lancamento
                    INNER JOIN divida.parcela_calculo
                            ON parcela_calculo.num_parcela = parcela.nr_parcela
                           AND parcela_calculo.cod_calculo = lancamento_calculo.cod_calculo
                    INNER JOIN divida.parcela AS dp
                            ON dp.num_parcelamento = parcela_calculo.num_parcelamento
                           AND dp.num_parcela = parcela_calculo.num_parcela
                      GROUP BY dp.vlr_parcela
                             , parcela.vencimento
                             , parcela.cod_lancamento
                             , parcela.cod_parcela
                             , parcela.nr_parcela
                             , parcela_calculo.num_parcelamento
                      )AS parcela
                   ON parcela.num_parcelamento = parcelamento.num_parcelamento
           INNER JOIN arrecadacao.carne
                   ON carne.cod_parcela = parcela.cod_parcela
            LEFT JOIN arrecadacao.pagamento
                   ON pagamento.numeracao = carne.numeracao
            LEFT JOIN arrecadacao.carne_devolucao
                   ON carne_devolucao.numeracao = carne.numeracao
                WHERE carne_devolucao.numeracao IS NULL
                  AND pagamento.numeracao IS NULL
             ORDER BY carne.exercicio
                    , carne.numeracao ";

        return $stSql;
    }

    public function ListaCarnesPagamentoComResto(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stFiltro .= " GROUP BY 1,2,3,4,5    \n";

        $stSql = $this->montaListaCarnesPagamentoComResto().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaCarnesPagamentoComResto()
    {
        $stSql = "  SELECT
                        pagamento_compensacao.*,
                        compensacao_resto.valor

                    FROM
                        arrecadacao.calculo

                    LEFT JOIN
                        arrecadacao.imovel_calculo
                    ON
                        imovel_calculo.cod_calculo = calculo.cod_calculo

                    LEFT JOIN
                        arrecadacao.cadastro_economico_calculo
                    ON
                        cadastro_economico_calculo.cod_calculo = calculo.cod_calculo

                    LEFT JOIN
                        arrecadacao.calculo_cgm
                    ON
                        calculo_cgm.cod_calculo = calculo.cod_calculo

                    INNER JOIN
                        arrecadacao.lancamento_calculo
                    ON
                        lancamento_calculo.cod_calculo = calculo.cod_calculo

                    INNER JOIN
                        arrecadacao.parcela
                    ON
                        parcela.cod_lancamento = lancamento_calculo.cod_lancamento

                    INNER JOIN
                        arrecadacao.carne
                    ON
                        carne.cod_parcela = parcela.cod_parcela

                    INNER JOIN
                        arrecadacao.pagamento
                    ON
                        pagamento.numeracao = carne.numeracao
                        AND pagamento.cod_convenio = carne.cod_convenio

                    INNER JOIN
                        arrecadacao.pagamento_compensacao
                    ON
                        pagamento_compensacao.numeracao = pagamento.numeracao
                        AND pagamento_compensacao.ocorrencia_pagamento = pagamento.ocorrencia_pagamento
                        AND pagamento_compensacao.cod_convenio = pagamento.cod_convenio

                    INNER JOIN
                        arrecadacao.compensacao_resto
                    ON
                        compensacao_resto.cod_compensacao = pagamento_compensacao.cod_compensacao

                    WHERE ";

        return $stSql;
    }

    public function ListaCalculosParcela(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaListaCalculosParcela().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaCalculosParcela()
    {
        $stSql = " SELECT DISTINCT
                        lancamento_calculo.cod_calculo,
                        (lancamento_calculo.valor * arrecadacao.calculaProporcaoParcela ( parcela.cod_parcela ))::numeric(14,2) AS valor_calculo,
                        COALESCE (
                            (
                                SELECT
                                    valor
                                FROM
                                    arrecadacao.parcela_desconto
                                WHERE
                                    cod_parcela = parcela.cod_parcela
                            ),
                            parcela.valor
                        ) AS valor_parcela,

                        COALESCE( aplica_multa ( carne.numeracao, carne.exercicio::int, parcela.cod_parcela, now()::date ) * arrecadacao.calculaProporcaoParcela ( parcela.cod_parcela ), 0.00 )::numeric(14,2) AS multa,
                        COALESCE( aplica_juro ( carne.numeracao, carne.exercicio::int, parcela.cod_parcela, now()::date ) * arrecadacao.calculaProporcaoParcela ( parcela.cod_parcela ), 0.00 )::numeric(14,2) AS juro,
                        COALESCE( aplica_correcao ( carne.numeracao, carne.exercicio::int, parcela.cod_parcela, now()::date ) * arrecadacao.calculaProporcaoParcela ( parcela.cod_parcela ), 0.00 )::numeric(14,2) AS correcao

                    FROM
                        arrecadacao.carne

                    INNER JOIN
                        arrecadacao.parcela
                    ON
                        parcela.cod_parcela = carne.cod_parcela

                    INNER JOIN
                        arrecadacao.lancamento_calculo
                    ON
                        lancamento_calculo.cod_lancamento = parcela.cod_lancamento

                    WHERE ";

        return $stSql;
    }

    public function ListaCalculosParcelaDA(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaListaCalculosParcelaDA().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaCalculosParcelaDA()
    {
        $stSql = " SELECT DISTINCT
                        to_char(parcela.vencimento, 'dd/mm/yyyy' ) AS vencimento
                        , (dp.vlr_parcela * arrecadacao.calculaProporcaoParcela ( parcela.cod_parcela ))::numeric(14,2) AS valor_calculo
                        , (split_part( aplica_acrescimo_modalidade( 0, divida_cgm.cod_inscricao, divida_cgm.exercicio::integer, parcelamento.cod_modalidade, 2, parcelamento.num_parcelamento, dp.vlr_parcela, parcela.vencimento, now()::date, 'true' ), ';', 1 )::numeric * arrecadacao.calculaProporcaoParcela ( parcela.cod_parcela ))::numeric(14,2) AS juro
                        , (split_part( aplica_acrescimo_modalidade( 0, divida_cgm.cod_inscricao, divida_cgm.exercicio::integer, parcelamento.cod_modalidade, 3, parcelamento.num_parcelamento, dp.vlr_parcela, parcela.vencimento, now()::date, 'true' ), ';', 1 )::numeric * arrecadacao.calculaProporcaoParcela ( parcela.cod_parcela ))::numeric(14,2) AS multa
                        , (split_part( aplica_acrescimo_modalidade( 0, divida_cgm.cod_inscricao, divida_cgm.exercicio::integer, parcelamento.cod_modalidade, 1, parcelamento.num_parcelamento, dp.vlr_parcela, parcela.vencimento, now()::date, 'true' ), ';', 1 )::numeric * arrecadacao.calculaProporcaoParcela ( parcela.cod_parcela ))::numeric(14,2) AS correcao
                        , split_part( arrecadacao.fn_busca_origem_lancamento_sem_exercicio( parcela.cod_lancamento, 1, 1 ), '§', 3)||'/'||split_part( arrecadacao.fn_busca_origem_lancamento_sem_exercicio( parcela.cod_lancamento, 1, 1 ), '§', 4) AS origem
                        , carne.numeracao
                        , carne.exercicio
                        , parcela.nr_parcela
                    FROM
                        arrecadacao.carne

                    INNER JOIN
                        arrecadacao.parcela
                    ON
                        parcela.cod_parcela = carne.cod_parcela

                    INNER JOIN
                        arrecadacao.lancamento_calculo
                    ON
                        lancamento_calculo.cod_lancamento = parcela.cod_lancamento

                    INNER JOIN
                        divida.parcela_calculo
                    ON
                        parcela_calculo.num_parcela = parcela.nr_parcela
                        AND parcela_calculo.cod_calculo = lancamento_calculo.cod_calculo

                    INNER JOIN
                        divida.parcela AS dp
                    ON
                        dp.num_parcelamento = parcela_calculo.num_parcelamento
                        AND dp.num_parcela = parcela_calculo.num_parcela

                    INNER JOIN
                        divida.parcelamento
                    ON
                        parcelamento.num_parcelamento = parcela_calculo.num_parcelamento

                    INNER JOIN
                        divida.divida_parcelamento
                    ON
                        divida_parcelamento.num_parcelamento = parcelamento.num_parcelamento

                    INNER JOIN
                        divida.divida_cgm
                    ON
                        divida_cgm.cod_inscricao = divida_parcelamento.cod_inscricao
                        AND divida_cgm.exercicio = divida_parcelamento.exercicio

                    LEFT JOIN
                        divida.divida_imovel
                    ON
                        divida_imovel.cod_inscricao = divida_parcelamento.cod_inscricao
                        AND divida_imovel.exercicio = divida_parcelamento.exercicio

                    LEFT JOIN
                        divida.divida_empresa
                    ON
                        divida_empresa.cod_inscricao = divida_parcelamento.cod_inscricao
                        AND divida_empresa.exercicio = divida_parcelamento.exercicio

                    WHERE ";

        return $stSql;
    }

    public function ListaParcelasComDiferencaPagas(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaListaParcelasComDiferencaPagas().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaParcelasComDiferencaPagas()
    {
        $stSql = " SELECT DISTINCT
                    calculo_cgm.numcgm,
                    (
                        SELECT
                            nom_cgm
                        FROM
                            sw_cgm
                        WHERE
                            sw_cgm.numcgm = calculo_cgm.numcgm
                    )AS nom_cgm,
                    CASE WHEN parcela.nr_parcela = 0 THEN
                        'única'
                    ELSE
                        parcela.nr_parcela::varchar
                    END AS nr_parcela,
                    parcela.cod_parcela,
                    CASE WHEN carne.cod_convenio = -1 THEN
                        split_part( arrecadacao.fn_busca_origem_lancamento_sem_exercicio( parcela.cod_lancamento, 1, 1 ), '§', 3)||'/'||split_part( arrecadacao.fn_busca_origem_lancamento_sem_exercicio( parcela.cod_lancamento, 1, 1 ), '§', 4)
                    ELSE
                        arrecadacao.fn_busca_origem_lancamento_sem_exercicio( parcela.cod_lancamento, 1, 1 )
                    END AS origem,
                    (
                        COALESCE(
                            (
                                SELECT
                                    parcela_desconto.valor
                                FROM
                                    arrecadacao.parcela_desconto
                                WHERE
                                    parcela_desconto.cod_parcela = parcela.cod_parcela
                            ),
                            parcela.valor
                        )
                    ) AS valor_parcela,
                    to_char(parcela.vencimento, 'dd/mm/yyyy' ) AS vencimento,
                    carne.numeracao,
                    carne.exercicio,
                    pagamento.ocorrencia_pagamento,
                    pagamento.cod_convenio,
                    pagamento_diferenca.valor AS valor_pago,
                    pagamento_diferenca.cod_calculo

                FROM
                    arrecadacao.calculo

                LEFT JOIN
                    arrecadacao.imovel_calculo
                ON
                    imovel_calculo.cod_calculo = calculo.cod_calculo

                LEFT JOIN
                    arrecadacao.cadastro_economico_calculo
                ON
                    cadastro_economico_calculo.cod_calculo = calculo.cod_calculo

                INNER JOIN
                    arrecadacao.calculo_cgm
                ON
                    calculo_cgm.cod_calculo = calculo.cod_calculo

                INNER JOIN
                    arrecadacao.lancamento_calculo
                ON
                    lancamento_calculo.cod_calculo = calculo.cod_calculo

                INNER JOIN
                    arrecadacao.parcela
                ON
                    parcela.cod_lancamento = lancamento_calculo.cod_lancamento

                INNER JOIN
                    arrecadacao.carne
                ON
                    carne.cod_parcela = parcela.cod_parcela

                INNER JOIN
                    arrecadacao.pagamento
                ON
                    pagamento.numeracao = carne.numeracao
                    AND pagamento.cod_convenio = carne.cod_convenio

                LEFT JOIN
                    arrecadacao.pagamento_diferenca
                ON
                    pagamento_diferenca.numeracao = pagamento.numeracao
                    AND pagamento_diferenca.ocorrencia_pagamento = pagamento.ocorrencia_pagamento
                    AND pagamento_diferenca.cod_convenio = pagamento.cod_convenio

                LEFT JOIN
                    arrecadacao.pagamento_compensacao
                ON
                    pagamento_compensacao.numeracao = pagamento.numeracao
                    AND pagamento_compensacao.ocorrencia_pagamento = pagamento.ocorrencia_pagamento
                    AND pagamento_compensacao.cod_convenio = pagamento.cod_convenio

                LEFT JOIN
                    arrecadacao.pagamento_diferenca_compensacao
                ON
                    pagamento_diferenca_compensacao.numeracao = pagamento.numeracao
                    AND pagamento_diferenca_compensacao.ocorrencia_pagamento = pagamento.ocorrencia_pagamento
                    AND pagamento_diferenca_compensacao.cod_convenio = pagamento.cod_convenio

                WHERE
                    pagamento_compensacao.numeracao IS NULL
                    AND pagamento_diferenca_compensacao.numeracao IS NULL
                    AND pagamento.cod_tipo != 12
                    AND pagamento_diferenca IS NOT NULL ";

        return $stSql;
    }

}// end of class
?>
