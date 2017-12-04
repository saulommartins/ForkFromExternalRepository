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
    * Extensão da Classe de mapeamento
    * Data de Criação: 20/04/2007

    * @author Analista: Tonismar Bernardo
    * @author Desenvolvedor: André Machado

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 32939 $
    $Name$
    $Author: domluc $
    $Date: 2008-09-03 18:14:50 -0300 (Qua, 03 Set 2008) $

    * Casos de uso: uc-06.03.00
    $Id: TCEPBEstornoPagamento.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCEPBEstornoPagamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEPBEstornoPagamento()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
    $this->setDado('entidade', Sessao::read('entidade'));
    $this->setDado('dtInicial', Sessao::read('stInicial'));
    $this->setDado('dtFinal', Sessao::read('stFinal'));
}

function montaRecuperaTodos()
{
$stSql ="
CREATE TEMPORARY TABLE tmp_estornado AS (
            SELECT
                p.cod_entidade as cod_entidade,
                p.cod_nota as cod_nota,
                p.exercicio_liquidacao as exercicio_liquidacao,
                p.timestamp as timestamp,
                pa.cod_plano as cod_plano,
                pc.nom_conta as nom_conta
            FROM
                contabilidade.pagamento p,
                contabilidade.lancamento_empenho le,
                contabilidade.conta_debito cd,
                contabilidade.plano_analitica pa,
                contabilidade.plano_conta pc
            WHERE
                --Ligação PAGAMENTO : LANCAMENTO EMPENHO
                    p.cod_entidade      IN (".$this->getDado('entidade').")
                AND p.exercicio     = '".$this->getDado('exercicio')."'
                AND p.cod_lote = le.cod_lote
                AND p.tipo = le.tipo
                AND p.sequencia = le.sequencia
                AND p.exercicio = le.exercicio
                AND p.cod_entidade = le.cod_entidade
                AND le.estorno = true

                --Ligação LANCAMENTO EMPENHO : CONTA_CREDITO
                AND le.cod_lote = cd.cod_lote
                AND le.tipo = cd.tipo
                AND le.exercicio = cd.exercicio
                AND le.cod_entidade = cd.cod_entidade
                AND le.sequencia = cd.sequencia

                --Ligação CONTA_CREDITO : PLANO ANALITICA
                AND cd.cod_plano = pa.cod_plano
                AND cd.exercicio = pa.exercicio

               --Ligação PLANO ANALITICA : PLANO CONTA
                AND pa.cod_conta = pc.cod_conta
                AND pa.exercicio = pc.exercicio
        );


 SELECT entidade,
    descricao_categoria,
    nom_tipo,
    empenho,
    exercicio,
    cgm,
    razao_social,
    cod_nota,
    stData AS data_estorno,
    ordem,
    conta,
    coalesce(nome_conta,'NÃO INFORMADO'),
    replace(valor::text,'.',',') as valor,
    valor_anulado,
    descricao,
    recurso,
    despesa,
    num_unidade,
    num_orgao,
    cod_unidade,
    tipo_lancamento

            FROM (
                    SELECT e.cod_entidade as entidade,
               categoria_empenho.descricao as descricao_categoria,
               tipo_empenho.nom_tipo,
               e.cod_empenho as empenho,
               e.exercicio as exercicio,
               pe.cgm_beneficiario as cgm,
               cgm.nom_cgm as razao_social,
               cast( pe.descricao as varchar ) as descricao,
                   to_char(nlpa.timestamp_anulada,'dd/mm/yyyy') as stData,
               nlpa.cod_nota as cod_nota,
               sum(nlpa.vl_anulado) as valor,
               cast(0.00 as numeric) as valor_anulado,
               pl.cod_ordem as ordem,
               tmp.cod_plano as conta,
               tmp.nom_conta as nome_conta,
               ped_d_cd.nom_recurso as recurso,
               ped_d_cd.cod_estrutural as despesa,
               ped_d_cd.num_unidade as num_unidade,
               ped_d_cd.num_orgao as num_orgao,
               lpad(ped_d_cd.num_orgao::varchar, 2, '0')||lpad(ped_d_cd.cod_unidade::varchar, 2, '0') as cod_unidade,
               1 AS tipo_lancamento
                    FROM
                        empenho.empenho     as e
                JOIN empenho.categoria_empenho
                  ON ( categoria_empenho.cod_categoria = e.cod_categoria)
                 , empenho.historico   as h
                         , empenho.nota_liquidacao nl
                         , empenho.nota_liquidacao_paga nlp

                    INNER JOIN ( SELECT exercicio_liquidacao
                                        , cod_entidade
                                        , cod_nota
                                        , timestamp
                                        , cod_plano
                                        , nom_conta
                                     FROM tmp_estornado
                                 GROUP BY exercicio_liquidacao
                                        , cod_entidade
                                        , cod_nota
                                        , timestamp
                                        , cod_plano
                                        , nom_conta
                      ) AS tmp
                      ON tmp.exercicio_liquidacao = nlp.exercicio
                     AND tmp.cod_entidade = nlp.cod_entidade
                     AND tmp.cod_nota = nlp.cod_nota
                     AND tmp.timestamp = nlp.timestamp

                    , empenho.pagamento_liquidacao_nota_liquidacao_paga plnlp
                    , empenho.nota_liquidacao_paga_anulada nlpa
                    , empenho.pagamento_liquidacao pl
                , sw_cgm              as cgm
                , empenho.pre_empenho as pe
                JOIN empenho.tipo_empenho
                ON ( tipo_empenho.cod_tipo = pe.cod_tipo)
                LEFT OUTER JOIN (
                    SELECT
                        ped.exercicio,
                        ped.cod_pre_empenho,
                        d.num_pao,
                        d.num_orgao,
                        d.num_unidade,
                        ou.num_unidade AS cod_unidade,
                        d.cod_recurso,
                        d.cod_despesa,
                        rec.nom_recurso,
                        d.cod_conta,
                        cd.cod_estrutural,
                        rec.masc_recurso_red,
                        rec.cod_detalhamento
                    FROM
                        empenho.pre_empenho_despesa as ped,
                        orcamento.despesa           as d
                        JOIN orcamento.recurso('".$this->getDado('exercicio')."') as rec
                        ON ( rec.cod_recurso = d.cod_recurso
                            AND rec.exercicio = d.exercicio )
                        JOIN orcamento.unidade as OU
                          ON OU.num_orgao = d.num_orgao
                         AND OU.num_unidade = d.num_unidade
                         AND OU.exercicio = d.exercicio

                        ,orcamento.conta_despesa     as cd
                    WHERE
                        ped.exercicio      = '".$this->getDado('exercicio')."'   AND
                        ped.cod_despesa    = d.cod_despesa and
                        ped.exercicio      = d.exercicio   and
                        ped.cod_conta      = cd.cod_conta  and
                        ped.exercicio      = cd.exercicio
                ) as ped_d_cd ON pe.exercicio = ped_d_cd.exercicio AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho

            WHERE
                    e.exercicio         = '".$this->getDado('exercicio')."'
                AND e.exercicio         = pe.exercicio
                AND e.cod_pre_empenho   = pe.cod_pre_empenho
                AND e.cod_entidade      IN (".$this->getDado('entidade').")
                AND pe.cgm_beneficiario = cgm.numcgm
                AND h.cod_historico     = pe.cod_historico
                AND h.exercicio         = pe.exercicio
                        --Ligação EMPENHO : NOTA LIQUIDAÇÃO
                        AND e.cod_empenho = nl.cod_empenho
                        AND e.exercicio = nl.exercicio_empenho
                        AND e.cod_entidade = nl.cod_entidade

                        --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
                        AND nl.exercicio = nlp.exercicio
                        AND nl.cod_nota = nlp.cod_nota
                        AND nl.cod_entidade = nlp.cod_entidade


                        --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA
                        AND nlp.cod_entidade = plnlp.cod_entidade
                        AND nlp.cod_nota = plnlp.cod_nota
                        AND nlp.exercicio = plnlp.exercicio_liquidacao
                        AND nlp.timestamp = plnlp.timestamp

                        --Ligação PAGAMENTO LIQUIDACAO : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA
                        AND pl.cod_ordem = plnlp.cod_ordem
                        AND pl.exercicio = plnlp.exercicio
                        AND pl.cod_entidade = plnlp.cod_entidade
                        AND pl.exercicio_liquidacao = plnlp.exercicio_liquidacao
                        AND pl.cod_nota = plnlp.cod_nota

                        --Ligação NOTA LIQUIDAÇÃO ITEM : NOTA LIQUIDAÇÃO ITEM ANULADO
                        AND nlp.exercicio = nlpa.exercicio
                        AND nlp.cod_nota = nlpa.cod_nota
                        AND nlp.cod_entidade = nlpa.cod_entidade
                        AND nlp.timestamp = nlpa.timestamp
                        AND to_date(to_char(nlpa.timestamp_anulada,'dd/mm/yyyy'),'dd/mm/yyyy')
                 BETWEEN to_date('".$this->getDado('dtInicial')."','dd/mm/yyyy')
                     AND to_date('".$this->getDado('dtFinal')."','dd/mm/yyyy')


            GROUP BY to_char(nlpa.timestamp_anulada,'dd/mm/yyyy'),
             nlpa.cod_nota,
             pl.cod_ordem,
             tmp.cod_plano,
             tmp.nom_conta,
             e.cod_entidade,
             e.cod_empenho,
             e.exercicio,
             pe.cgm_beneficiario,
             cgm.nom_cgm,
             pe.descricao,
             ped_d_cd.cod_estrutural,
             ped_d_cd.nom_recurso,
             categoria_empenho.descricao,
             tipo_empenho.nom_tipo,
             ped_d_cd.num_unidade,
             ped_d_cd.num_orgao,
             ped_d_cd.cod_unidade

          ORDER BY to_date(to_char(nlpa.timestamp_anulada,'dd/mm/yyyy'),'dd/mm/yyyy'),
               e.cod_entidade,
               e.cod_empenho,
               e.exercicio,
               nlpa.cod_nota,
               pl.cod_ordem,
               tmp.cod_plano,
               tmp.nom_conta,
               pe.cgm_beneficiario,
               cgm.nom_cgm ) as tbl

            WHERE valor <> '0.00'
             ORDER BY to_date(stData,'dd/mm/yyyy'), entidade, empenho, exercicio, cgm, razao_social, cod_nota, ordem, conta, nome_conta
";

return $stSql;
    }
}
