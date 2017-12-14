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
    * Classe de mapeamento da tabela ARRECADACAO.NOTA_AVULSA
    * Data de Criação: 20/06/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: $

* Casos de uso: uc-05.03.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRNotaAvulsa extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TARRNotaAvulsa()
    {
        parent::Persistente();
        $this->setTabela( 'arrecadacao.nota_avulsa' );

        $this->setCampoCod( 'cod_nota' );
        $this->setComplementoChave( '' );

        $this->AddCampo( 'numcgm_tomador', 'integer', true, '', false, true );
        $this->AddCampo( 'numcgm_usuario', 'integer', true, '', false, true );
        $this->AddCampo( 'cod_nota', 'integer', true, '', true, true );
        $this->AddCampo( 'nro_serie', 'varchar', true, '10', false, false );
        $this->AddCampo( 'nro_nota', 'integer', true, '', false, false );
        $this->AddCampo( 'exercicio', 'varchar', true, '4', false, false );
        $this->AddCampo( 'observacao', 'varchar', false, '300', false, false );
    }

    public function recuperaProximoCodNotaSerie(&$inCodNota, &$inCodSerie, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql  = $this->montaRecuperaProximoCodNotaSerie();
        $this->setDebug($stSql);
        #$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        $inCodNota = 1;
        $inCodSerie = 'a';

        if ( !$obErro->ocorreu() ) {
            if ( !$rsRecordSet->Eof() ) {
                $inCodNota = $rsRecordSet->getCampo("nro_nota");
                $inCodSerie = $rsRecordSet->getCampo("nro_serie");
                if ($inCodNota+1 > 999999) {
                    $inCodNota = 1;
                    $inCodSerie++;
                } else {
                    $inCodNota++;
                }
            }
        }

        return $obErro;
    }

    public function montaRecuperaProximoCodNotaSerie()
    {
        $stSql = "
            SELECT
                MAX(nro_nota) AS nro_nota,
                nro_serie
            FROM
                arrecadacao.nota_avulsa
            WHERE
                exercicio = '".Sessao::getExercicio()."'
                AND nro_serie =  (
                    SELECT
                        MAX(nro_serie)
                    FROM
                        arrecadacao.nota_avulsa
                    WHERE
                        exercicio = '".Sessao::getExercicio()."'
                )
            GROUP BY
                nro_serie
        ";

        return $stSql;
    }

    public function recuperaListaNovaAvulsa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaListaNovaAvulsa().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql,  $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaNovaAvulsa()
    {
        $stSql = "
            SELECT DISTINCT
                cadastro_economico.inscricao_economica,
                nota_avulsa.cod_nota,
                nota_avulsa.nro_nota,
                nota_avulsa.nro_serie,
                atividade.nom_atividade,
                atividade.cod_atividade,
                (
                    SELECT
                        eml.nom_modalidade
                    FROM
                        economico.modalidade_lancamento AS eml
                    WHERE
                        eml.cod_modalidade = faturamento_servico.cod_modalidade
                )AS nom_modalidade,
                faturamento_servico.cod_modalidade,
                COALESCE( cadastro_economico_empresa_direito.numcgm, cadastro_economico_empresa_fato.numcgm, cadastro_economico_autonomo.numcgm ) AS numcgm,
                (
                    SELECT
                        cgm.nom_cgm
                    FROM
                        sw_cgm AS cgm
                    WHERE
                        cgm.numcgm = COALESCE( cadastro_economico_empresa_direito.numcgm, cadastro_economico_empresa_fato.numcgm, cadastro_economico_autonomo.numcgm )
                ) AS nom_cgm,
                carne.numeracao,
                carne.cod_convenio

            FROM
                economico.cadastro_economico

            LEFT JOIN
                economico.cadastro_economico_empresa_direito
            ON
                cadastro_economico_empresa_direito.inscricao_economica = cadastro_economico.inscricao_economica

            LEFT JOIN
                economico.cadastro_economico_empresa_fato
            ON
                cadastro_economico_empresa_fato.inscricao_economica = cadastro_economico.inscricao_economica

            LEFT JOIN
                economico.cadastro_economico_autonomo
            ON
                cadastro_economico_autonomo.inscricao_economica = cadastro_economico.inscricao_economica

            INNER JOIN
                arrecadacao.faturamento_servico
            ON
                faturamento_servico.inscricao_economica = cadastro_economico.inscricao_economica

            INNER JOIN
                arrecadacao.cadastro_economico_calculo
            ON
                cadastro_economico_calculo.inscricao_economica = cadastro_economico.inscricao_economica
                AND cadastro_economico_calculo.timestamp = faturamento_servico.timestamp

            INNER JOIN
                arrecadacao.calculo
            ON
                NOT ( calculo.cod_credito = 99 AND calculo.cod_especie = 1 AND calculo.cod_genero = 2 AND calculo.cod_natureza = 1 )
                AND calculo.cod_calculo = cadastro_economico_calculo.cod_calculo

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
                arrecadacao.carne_devolucao
            ON
                carne_devolucao.numeracao = carne.numeracao

            LEFT JOIN
                arrecadacao.pagamento
            ON
                pagamento.numeracao = carne.numeracao

            INNER JOIN
                economico.atividade
            ON
                atividade.cod_atividade = faturamento_servico.cod_atividade

            INNER JOIN
                arrecadacao.nota_servico
            ON
                nota_servico.cod_atividade = faturamento_servico.cod_atividade
                AND nota_servico.inscricao_economica = faturamento_servico.inscricao_economica
                AND nota_servico.cod_servico = faturamento_servico.cod_servico
                AND nota_servico.timestamp = faturamento_servico.timestamp
                AND nota_servico.ocorrencia = faturamento_servico.ocorrencia

            INNER JOIN
                arrecadacao.nota_avulsa
            ON
                nota_avulsa.cod_nota = nota_servico.cod_nota

            LEFT JOIN
                arrecadacao.nota_avulsa_cancelada
            ON
                nota_avulsa_cancelada.cod_nota = nota_avulsa.cod_nota

            WHERE
                nota_avulsa_cancelada.cod_nota IS NULL
                AND pagamento.numeracao IS NULL
                AND carne_devolucao.numeracao IS NULL
        ";

        return $stSql;
    }

    public function recuperaConsultaNotaAvulsa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaConsultaNotaAvulsa().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql,  $boTransacao );

        return $obErro;
    }

    public function montaRecuperaConsultaNotaAvulsa()
    {
        $stSql = "
            select distinct
                carne.numeracao,
                carne.exercicio,
                cadastro_economico_calculo.inscricao_economica,
                faturamento_servico.cod_modalidade,
                (
                    select
                        nom_cgm
                    from
                        sw_cgm
                    where
                        sw_cgm.numcgm = coalesce ( cadastro_economico_empresa_fato.numcgm, cadastro_economico_autonomo.numcgm, cadastro_economico_empresa_direito.numcgm )
                )AS nomcgm_prestador,
                coalesce ( cadastro_economico_empresa_fato.numcgm, cadastro_economico_autonomo.numcgm, cadastro_economico_empresa_direito.numcgm ) AS numcgm_prestador,
                (
                    select
                        modalidade_lancamento.nom_modalidade
                    from
                        economico.modalidade_lancamento
                    where
                        modalidade_lancamento.cod_modalidade = faturamento_servico.cod_modalidade
                )AS descricao_modalidade,
                arrecadacao.fn_consulta_endereco_empresa( cadastro_economico_calculo.inscricao_economica ) AS endereco_empresa,
                cadastro_economico_faturamento.competencia,
                nota_avulsa.nro_nota,
                nota_avulsa.nro_serie,
                case when nota_avulsa_cancelada.cod_nota is not null then
                    'cancelada'
                else
                    'ativa'
                end as situacao_nota,
                to_char ( faturamento_servico.dt_emissao, 'dd/mm/YYYY' ) as dt_emissao,
                nota_avulsa.numcgm_tomador,
                (
                    select
                        nom_cgm
                    from
                        sw_cgm
                    where
                        sw_cgm.numcgm = nota_avulsa.numcgm_tomador
                )as nomcgm_tomador,
                servico_sem_retencao.cod_servico,
                (
                    select
                        servico.nom_servico
                    from
                        economico.servico
                    where
                        servico.cod_servico = servico_sem_retencao.cod_servico
                )AS descricao_servico,
                servico_sem_retencao.valor_declarado,
                servico_sem_retencao.valor_deducao,
                servico_sem_retencao.valor_lancado,
                servico_sem_retencao.aliquota,
                ap.valor as valor_a_pagar,
                aplica_correcao( carne.numeracao::varchar, carne.exercicio::integer, carne.cod_parcela, now()::date )::numeric(14,2) AS valor_correcao_a_pagar,
                aplica_multa( carne.numeracao::varchar, carne.exercicio::integer, carne.cod_parcela, now()::date )::numeric(14,2) AS valor_multa_a_pagar,
                aplica_juro( carne.numeracao::varchar, carne.exercicio::integer, carne.cod_parcela, now()::date )::numeric(14,2) AS valor_juros_a_pagar,
                (ap.valor + aplica_correcao( carne.numeracao::varchar, carne.exercicio::integer, carne.cod_parcela, now()::date ) + aplica_multa( carne.numeracao::varchar, carne.exercicio::integer, carne.cod_parcela, now()::date ) +  aplica_juro( carne.numeracao::varchar, carne.exercicio::integer, carne.cod_parcela, now()::date ) )::numeric(14,2) AS valor_total_a_pagar,
                to_char ( ap.vencimento, 'dd/mm/YYYY' ) as dt_vencimento,
                case when pagamento.numeracao is not null then
                    'pago'
                else
                    'aberto'
                end as situacao_parcela,
                pagamento_lote.cod_lote,
                to_char ( pagamento.data_pagamento, 'dd/mm/YYYY' ) as dt_pagamento,
                pagamento.observacao as observacao_pagamento,
                to_char ( pagamento.data_baixa, 'dd/mm/YYYY' ) as dt_baixa,
                processo_pagamento.cod_processo,
                processo_pagamento.ano_exercicio,
                banco.num_banco,
                banco.nom_banco,
                agencia.num_agencia,
                agencia.nom_agencia,
                coalesce( pagamento.valor, 0.00 ) as valor_pago_total,
                coalesce (
                    (
                        select
                            sum(valor)
                        from
                            arrecadacao.pagamento_acrescimo
                        where
                            pagamento_acrescimo.numeracao = pagamento.numeracao
                            and pagamento_acrescimo.cod_acrescimo = 1
                            and pagamento_acrescimo.cod_tipo = 2
                            and pagamento_acrescimo.ocorrencia_pagamento = pagamento.ocorrencia_pagamento
                    ),
                    0.00
                )as valor_pago_juros,
                coalesce (
                    (
                        select
                            sum(valor)
                        from
                            arrecadacao.pagamento_acrescimo
                        where
                            pagamento_acrescimo.numeracao = pagamento.numeracao
                            and pagamento_acrescimo.cod_acrescimo = 2
                            and pagamento_acrescimo.cod_tipo = 3
                            and pagamento_acrescimo.ocorrencia_pagamento = pagamento.ocorrencia_pagamento
                    ),
                    0.00
                )as valor_pago_multa,
                coalesce(
                    (
                        select
                            sum(valor)
                        from
                            arrecadacao.pagamento_acrescimo
                        where
                            pagamento_acrescimo.numeracao = pagamento.numeracao
                            and pagamento_acrescimo.cod_acrescimo = 3
                            and pagamento_acrescimo.cod_tipo = 1
                            and pagamento_acrescimo.ocorrencia_pagamento = pagamento.ocorrencia_pagamento
                    ),
                    0.00
                )as valor_pago_correcao,
                coalesce (
                    (
                        select
                            sum(valor)
                        from
                            arrecadacao.pagamento_calculo
                        where
                            pagamento_calculo.numeracao = pagamento.numeracao
                            and pagamento_calculo.ocorrencia_pagamento = pagamento.ocorrencia_pagamento
                    ),
                    0.00
                )as valor_pago,
                coalesce( parcela_desconto.valor, 0.00 ) as valor_desconto

            from
                arrecadacao.carne

            left join
                arrecadacao.pagamento
            on
                carne.numeracao = pagamento.numeracao

            left join
                arrecadacao.processo_pagamento
            on
                processo_pagamento.numeracao = pagamento.numeracao
                and processo_pagamento.ocorrencia_pagamento = pagamento.ocorrencia_pagamento

            left join
                arrecadacao.pagamento_lote
            on
                pagamento_lote.numeracao = pagamento.numeracao
                and pagamento_lote.ocorrencia_pagamento = pagamento.ocorrencia_pagamento

            left join
                arrecadacao.lote
            on
                lote.cod_lote = pagamento_lote.cod_lote

            left join
                monetario.banco
            on
                banco.cod_banco = lote.cod_banco

            left join
                monetario.agencia
            on
                agencia.cod_banco = lote.cod_banco
                and agencia.cod_agencia = lote.cod_agencia

            inner join
                arrecadacao.parcela AS ap
            on
                ap.cod_parcela = carne.cod_parcela

            left join
                arrecadacao.parcela_desconto
            on
                parcela_desconto.cod_parcela = ap.cod_parcela

            inner join
                arrecadacao.lancamento_calculo
            on
                lancamento_calculo.cod_lancamento = ap.cod_lancamento

            inner join
                arrecadacao.calculo
            on
                calculo.cod_calculo = lancamento_calculo.cod_calculo
                and not ( calculo.cod_credito = 99 and calculo.cod_genero = 2 and calculo.cod_especie = 1 and calculo.cod_natureza = 1 )

            inner join
                arrecadacao.cadastro_economico_calculo
            on
                cadastro_economico_calculo.cod_calculo = lancamento_calculo.cod_calculo

            left join
                economico.cadastro_economico_empresa_fato
            on
                cadastro_economico_empresa_fato.inscricao_economica = cadastro_economico_calculo.inscricao_economica

            left join
                economico.cadastro_economico_autonomo
            on
                cadastro_economico_autonomo.inscricao_economica = cadastro_economico_calculo.inscricao_economica

            left join
                economico.cadastro_economico_empresa_direito
            on
                cadastro_economico_empresa_direito.inscricao_economica = cadastro_economico_calculo.inscricao_economica

            inner join
                arrecadacao.cadastro_economico_faturamento
            on
                cadastro_economico_faturamento.inscricao_economica = cadastro_economico_calculo.inscricao_economica
                AND cadastro_economico_faturamento.timestamp = cadastro_economico_calculo.timestamp

            inner join
                arrecadacao.faturamento_servico
            on
                faturamento_servico.inscricao_economica = cadastro_economico_faturamento.inscricao_economica
                AND faturamento_servico.timestamp = cadastro_economico_faturamento.timestamp

            inner join
                arrecadacao.servico_sem_retencao
            on
                servico_sem_retencao.inscricao_economica = faturamento_servico.inscricao_economica
                AND servico_sem_retencao.timestamp = faturamento_servico.timestamp
                AND servico_sem_retencao.cod_servico = faturamento_servico.cod_servico
                AND servico_sem_retencao.ocorrencia = faturamento_servico.ocorrencia
                AND servico_sem_retencao.cod_atividade = faturamento_servico.cod_atividade

            inner join
                arrecadacao.nota_servico
            on
                nota_servico.inscricao_economica = faturamento_servico.inscricao_economica
                AND nota_servico.timestamp = faturamento_servico.timestamp
                AND nota_servico.cod_servico = faturamento_servico.cod_servico
                AND nota_servico.ocorrencia = faturamento_servico.ocorrencia
                AND nota_servico.cod_atividade = faturamento_servico.cod_atividade

            inner join
                arrecadacao.nota
            on
                nota.cod_nota = nota_servico.cod_nota

            inner join
                arrecadacao.nota_avulsa
            on
                nota_avulsa.cod_nota = nota.cod_nota

            left join
                arrecadacao.nota_avulsa_cancelada
            on
                nota_avulsa_cancelada.cod_nota = nota_avulsa.cod_nota
        ";

        return $stSql;
    }
}
?>