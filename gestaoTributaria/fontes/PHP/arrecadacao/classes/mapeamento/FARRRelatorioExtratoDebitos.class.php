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
    * Classe de mapeamento para relatorio de Valores Lançados
    * Data de Criação: 13/07/2007

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: FARRRelatorioExtratoDebitos.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.13
*/

/*
$Log$
Revision 1.12  2007/10/09 18:46:40  cercato
 Ticket#9281#

Revision 1.11  2007/10/02 14:20:48  vitor
Ticket#10301#

Revision 1.10  2007/08/17 15:47:44  dibueno
Bug#9927#

Revision 1.9  2007/08/16 19:08:31  dibueno
Bug#9927#

Revision 1.8  2007/08/16 18:45:12  dibueno
Bug#9927#

Revision 1.7  2007/08/15 18:42:26  dibueno
Bug#9927#

Revision 1.5  2007/08/01 21:05:42  dibueno
Bug#9781#

Revision 1.4  2007/08/01 13:57:01  dibueno
Bug#9793#

Revision 1.3  2007/07/16 21:00:40  dibueno
Bug #9659#

Revision 1.2  2007/07/16 18:23:09  dibueno
Bug #9659#

Revision 1.1  2007/07/16 16:04:22  dibueno
Bug #9659#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

set_time_limit(0);

class FARRRelatorioExtratoDebitos extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function FARRRelatorioExtratoDebitos()
    {
        parent::Persistente();
        $this->setTabela('imobiliario.fn_rl_cadastro_imobiliario');

        $this->setCampoCod('');
        $this->setComplementoChave('');

        $this->AddCampo( 'numcgm'           ,'integer'  , true, '',false, false );
        $this->AddCampo( 'nom_cgm'          ,'varchar'  , true, '',false, false );
        $this->AddCampo( 'inscricao'        ,'integer'  , true, '',false, false );
        $this->AddCampo( 'exercicio'        ,'integer'  , true, '',false, false );
        $this->AddCampo( 'cod_grupo'        ,'integer'  , true, '',false, false );
        $this->AddCampo( 'descricao'        ,'varchar'  , true, '',false, false );
        $this->AddCampo( 'numeracao'        ,'varchar'  , true, '',false, false );
        $this->AddCampo( 'info_parcela'     ,'varchar'  , true, '',false, false );
        $this->AddCampo( 'valor'            ,'numeric'  , true, '',false, false );
        $this->AddCampo( 'data_pagamento'   ,'date'     , true, '',false, false );
        $this->AddCampo( 'data_vencimento'  ,'date'     , true, '',false, false );
        $this->AddCampo( 'juros'            ,'numeric'  , true, '',false, false );
        $this->AddCampo( 'multa'            ,'numeric'  , true, '',false, false );
    }

    public function recuperaRelatorioOrigem(&$rsRecordSet, $stINNER = "", $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql  = $this->montaRecuperaRelatorioOrigem( $stINNER, $stFiltro );
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelatorioOrigem($stINNER, $stFiltro)
    {
        $stSql = "  SELECT                                                                                  \n";
        $stSql .="      origem                                                                              \n";
        $stSql .="      , cod_lancamento                                                                    \n";
        $stSql .="      , exercicio                                                                         \n";
        $stSql .="      , count(cod_parcela) as qtde                                                        \n";
        $stSql .="      , ( sum(valor) + sum(juros) + sum(multa) + sum(correcao) ) as valor                 \n";
        $stSql .="  FROM                                                                                    \n";
        $stSql .="      (                                                                                   \n";
        $stSql .="          SELECT                                                                          \n";
        $stSql .="              *                                                                           \n";
        $stSql .="              , coalesce ( aplica_juro ( numeracao, exercicio, cod_parcela, now()::date)  \n";
        $stSql .="                          , 0.0                                                           \n";
        $stSql .="              ) as juros                                                                  \n";
        $stSql .="              , coalesce ( aplica_multa ( numeracao, exercicio, cod_parcela, now()::date) \n";
        $stSql .="                          , 0.0                                                           \n";
        $stSql .="              ) as multa                                                                  \n";
        $stSql .="              , coalesce ( aplica_correcao( numeracao, exercicio, cod_parcela,now()::date)\n";
        $stSql .="                          , 0.0                                                           \n";
        $stSql .="              ) as correcao                                                               \n";
        $stSql .="          FROM                                                                            \n";
        $stSql .="              (                                                                           \n";
        $stSql .="              SELECT                                                                      \n";
        $stSql .="                  busca_parcelas.cod_lancamento                                           \n";
        $stSql .="                  , max(carne.numeracao) as numeracao                                     \n";
        $stSql .="                  , busca_parcelas.cod_parcela                                            \n";
        $stSql .="                  , busca_parcelas.nr_parcela                                             \n";
        $stSql .="                  , busca_parcelas.exercicio                                              \n";
        $stSql .="                  , busca_parcelas.situacao_lancamento                                    \n";
        $stSql .="                  , busca_parcelas.origem                                                 \n";
        $stSql .="                  , busca_parcelas.valor                                                  \n";
        $stSql .="                  , busca_parcelas.vencimento                                             \n";
        $stSql .="                  , CASE WHEN (parcela_reemitida IS NOT NULL) THEN
                                                CASE WHEN (
                                                            SELECT COUNT(carne.numeracao) as qtd
                                                              FROM arrecadacao.pagamento as pag
                                                              JOIN arrecadacao.carne as carne
                                                                ON carne.numeracao = pag.numeracao
                                                               AND carne.cod_convenio = pag.cod_convenio
                                                             WHERE carne.cod_parcela = parcela_reemitida
                                                        ) > 0 THEN
                                                                    'false'
                                                              ELSE
                                                                    'true'
                                                    END
                                            ELSE
                                                'true'
                                    END as situacao_reemissao\n";
        $stSql .="              FROM                                                                        \n";
        $stSql .="                  (                                                                       \n";
        $stSql .="                      SELECT                                                              \n";
        $stSql .="                          ( CASE WHEN dpc.num_parcelamento IS NULL THEN                   \n";
        $stSql .="                              al.cod_lancamento::varchar                                  \n";
        $stSql .="                            ELSE                                                          \n";
        $stSql .="                              dp.numero_parcelamento||'/'||dp.exercicio                   \n";
        $stSql .="                            END                                                           \n";
        $stSql .="                          )::varchar as cod_lancamento                                    \n";
        $stSql .="                          , ( arrecadacao.fn_busca_lancamento_situacao (al.cod_lancamento)\n";
        $stSql .="                          ) as situacao_lancamento                                        \n";
        $stSql .="                          , ( CASE WHEN dpc.cod_calculo IS NOT NULL THEN                  \n";
        $stSql .="                                  divida.fn_lista_origem_cobranca (                       \n";
        $stSql .="                                      dpc.num_parcelamento, dp.exercicio::int )           \n";
        $stSql .="                            ELSE                                                          \n";
        $stSql .="                              arrecadacao.fn_busca_origem_lancamento(                     \n";
        $stSql .="                                  al.cod_lancamento,ac.exercicio::int, 1, 1 )             \n";
        $stSql .="                            END                                                           \n";
        $stSql .="                          ) as origem                                                     \n";
        $stSql .="                          , ac.exercicio::integer                                         \n";
        $stSql .="                          , ap.cod_parcela                                                \n";
        $stSql .="                          , ap.nr_parcela                                                 \n";
        $stSql .="                          , ap.vencimento                                                 \n";
        $stSql .="                          , apr.cod_parcela as parcela_reemitida                          \n";
        $stSql .="                          , ap.valor                                                      \n";
        $stSql .="                      FROM                                                                \n";
        $stSql .="                          arrecadacao.lancamento as al                                    \n";
        $stSql .="                          INNER JOIN (                                                    \n";
        $stSql .="                              SELECT                                                      \n";
        $stSql .="                                  cod_lancamento                                          \n";
        $stSql .="                                  , max(cod_calculo) as cod_calculo                       \n";
        $stSql .="                              FROM                                                        \n";
        $stSql .="                                  arrecadacao.lancamento_calculo                          \n";
        $stSql .="                              GROUP BY                                                    \n";
        $stSql .="                                  cod_lancamento                                          \n";
        $stSql .="                          ) as alc                                                        \n";
        $stSql .="                          ON alc.cod_lancamento = al.cod_lancamento                       \n";
        $stSql .="                          INNER JOIN arrecadacao.calculo as ac                            \n";
        $stSql .="                          ON ac.cod_calculo = alc.cod_calculo                             \n";
        $stSql .="                          INNER JOIN monetario.credito as mc                              \n";
        $stSql .="                          ON mc.cod_credito = ac.cod_credito                              \n";
        $stSql .="                          AND mc.cod_especie = ac.cod_especie                             \n";
        $stSql .="                          AND mc.cod_genero = ac.cod_genero                               \n";
        $stSql .="                          AND mc.cod_natureza = ac.cod_natureza                           \n";
        $stSql .="                          INNER JOIN arrecadacao.calculo_cgm as accgm                     \n";
        $stSql .="                          ON accgm.cod_calculo = ac.cod_calculo                           \n";

        $stSql .="                          ". $stINNER ."                                                  \n";

        $stSql .="                          LEFT JOIN divida.parcela_calculo as dpc                         \n";
        $stSql .="                          ON dpc.cod_calculo = ac.cod_calculo                             \n";
        $stSql .="                          LEFT JOIN divida.parcela as dpar                                \n";
        $stSql .="                          ON dpar.num_parcelamento = dpc.num_parcelamento                 \n";
        $stSql .="                          AND dpar.num_parcela = dpc.num_parcela                          \n";
        $stSql .="                          LEFT JOIN divida.parcelamento as dp                             \n";
        $stSql .="                          ON dp.num_parcelamento = dpc.num_parcelamento                   \n";

        $stSql .="                          INNER JOIN arrecadacao.parcela as ap                            \n";
        $stSql .="                          ON ap.cod_lancamento = al.cod_lancamento                        \n";

        $stSql .="                          LEFT JOIN (
                                                    SELECT
                                                        parcela_origem.cod_parcela

                                                    FROM
                                                        divida.parcela_origem

                                                    INNER JOIN
                                                        divida.divida_parcelamento
                                                    ON
                                                        divida_parcelamento.num_parcelamento = parcela_origem.num_parcelamento

                                                    INNER JOIN
                                                        divida.divida_cancelada
                                                    ON
                                                        divida.divida_cancelada.cod_inscricao = divida_parcelamento.cod_inscricao
                                                        AND divida.divida_cancelada.exercicio = divida_parcelamento.exercicio
                                            )AS dpcanc
                                            ON dpcanc.cod_parcela = ap.cod_parcela                          \n";

        $stSql .="                          LEFT JOIN arrecadacao.parcela_reemissao AS apr                  \n";
        $stSql .="                          ON apr.cod_parcela = ap.cod_parcela                             \n";
        $stSql .="                          LEFT JOIN (                                                     \n";
        $stSql .="                               SELECT                                                     \n";
        $stSql .="                                  cod_parcela,                                            \n";
        $stSql .="                                  TRUE as possui_cobranca                                 \n";
        $stSql .="                               FROM                                                       \n";
        $stSql .="                                    divida.parcela_origem AS dpo                          \n";
        $stSql .="                               INNER JOIN divida.parcela AS dp                            \n";
        $stSql .="                                  ON dp.num_parcelamento =  dpo.num_parcelamento          \n";
        $stSql .="                               WHERE dp.paga = 'f' AND cancelada = 'f'                    \n";
        $stSql .="                                     AND num_parcela <> 0                                 \n";
        $stSql .="                              GROUP BY 1                                                  \n";
        $stSql .="                            ) as dpo                                                      \n";
        $stSql .="                         ON dpo.cod_parcela = ap.cod_parcela                              \n";
        $stSql .="                      WHERE                                                               \n";
        $stSql .="                          ". $stFiltro ."                                                 \n";
        $stSql .="                          AND    dpo.possui_cobranca is null
                                            AND (
                                                CASE WHEN
                                                    (   SELECT
                                                            count(*)
                                                        FROM
                                                            divida.parcela
                                                        inner join
                                                            divida.parcela_origem
                                                        on
                                                            parcela_origem.num_parcelamento = parcela.num_parcelamento
                                                            AND parcela_origem.cod_parcela = ap.cod_parcela

                                                        WHERE num_parcela = 0
                                                        AND paga = true
                                                        AND cancelada = false
                                                    ) > 0
                                                    THEN
                                                        FALSE
                                                    ELSE
                                                        TRUE
                                                    END
                                            )                                                               \n";
        $stSql .="                          AND dpcanc.cod_parcela IS NULL                                  \n";
        $stSql .="                          AND (
                                                CASE WHEN
                                                    (
                                                        SELECT count(*)
                                                        FROM arrecadacao.parcela
                                                        WHERE nr_parcela != 0
                                                        AND cod_lancamento = al.cod_lancamento
                                                        AND now()::date > vencimento
                                                    ) > 0
                                                THEN
                                                    CASE WHEN
                                                        (
                                                            SELECT
                                                                count(*)
                                                            FROM arrecadacao.parcela

                                                            INNER JOIN arrecadacao.carne
                                                            ON carne.cod_parcela = parcela.cod_parcela

                                                            INNER JOIN arrecadacao.pagamento
                                                            ON pagamento.numeracao = carne.numeracao
                                                            WHERE nr_parcela = 0
                                                            AND parcela.cod_lancamento = al.cod_lancamento
                                                        ) > 0
                                                    THEN
                                                        FALSE
                                                    ELSE
                                                        TRUE
                                                    END
                                                ELSE
                                                    TRUE
                                                END                                                         \n";
        $stSql .="                          )                                                               \n";
        $stSql .="                          AND (                                                           \n";
        $stSql .="                              CASE WHEN dpc.cod_calculo IS NOT NULL THEN                  \n";
        $stSql .="                                 CASE WHEN dpar.cancelada = false OR dpar.paga = true THEN\n";
        $stSql .="                                      TRUE                                                \n";
        $stSql .="                                  ELSE                                                    \n";
        $stSql .="                                      FALSE                                               \n";
        $stSql .="                                  END                                                     \n";
        $stSql .="                              ELSE                                                        \n";
        $stSql .="                                  TRUE                                                    \n";
        $stSql .="                              END                                                         \n";
        $stSql .="                          )                                                               \n";
        $stSql .="                      GROUP BY                                                            \n";
        $stSql .="                          al.cod_lancamento , ac.exercicio                                \n";
        $stSql .="                          , ap.cod_parcela, ap.nr_parcela, ap.valor, ap.vencimento        \n";
        $stSql .="                          , apr.cod_parcela, ap.valor                                     \n";
        $stSql .="                          , dpc.cod_calculo, dpc.num_parcelamento                         \n";
        $stSql .="                          , dp.numero_parcelamento                                        \n";
        $stSql .="                          , dp.exercicio                                                  \n";
        $stSql .="                  ) as busca_parcelas                                                     \n";
        $stSql .="                  INNER JOIN arrecadacao.carne                                            \n";
        $stSql .="                  ON carne.cod_parcela = busca_parcelas.cod_parcela                       \n";

        $stSql .="                  LEFT JOIN arrecadacao.pagamento as apag                                 \n";
        $stSql .="                  ON apag.numeracao = carne.numeracao                                     \n";
        $stSql .="                  AND apag.cod_convenio = carne.cod_convenio                              \n";

        $stSql .="                  LEFT JOIN arrecadacao.tipo_pagamento as atp                             \n";
        $stSql .="                  ON atp.cod_tipo = apag.cod_tipo                                         \n";

        $stSql .="                  LEFT JOIN arrecadacao.carne_devolucao as acd                            \n";
        $stSql .="                  ON acd.numeracao = carne.numeracao                                      \n";
        $stSql .="                  AND acd.cod_convenio = carne.cod_convenio                               \n";

        $stSql .="              WHERE                                                                       \n";

        $stSql .="                  apag.numeracao IS NULL                                                  \n";
        $stSql .="                  AND (                                                                   \n";
        $stSql .="                      CASE WHEN acd.numeracao IS NOT NULL THEN                            \n";
        $stSql .="                          CASE WHEN acd.cod_motivo = 11  THEN                             \n";
        $stSql .="                              TRUE                                                        \n";
        $stSql .="                          ELSE                                                            \n";
        $stSql .="                              FALSE                                                       \n";
        $stSql .="                          END                                                             \n";
        $stSql .="                      ELSE                                                                \n";
        $stSql .="                          TRUE                                                            \n";
        $stSql .="                      END                                                                 \n";
        $stSql .="                  )                                                                       \n";

        $stSql .="              GROUP BY                                                                    \n";
        $stSql .="                  busca_parcelas.cod_lancamento, situacao_lancamento                      \n";
        $stSql .="                  , busca_parcelas.origem, busca_parcelas.cod_parcela                     \n";
        $stSql .="                  , busca_parcelas.nr_parcela, busca_parcelas.valor                       \n";
        $stSql .="                  , busca_parcelas.exercicio, busca_parcelas.parcela_reemitida            \n";
        $stSql .="                  , busca_parcelas.vencimento                                             \n";

        $stSql .="          ) as busca_carnes                                                               \n";

        $stSql .="  ) as busca_valores                                                                      \n";

        $stSql .="  GROUP BY                                                                                \n";
        $stSql .="      exercicio, cod_lancamento,origem                                                    \n";
        $stSql .="  ORDER BY                                                                                \n";
        $stSql .="      exercicio, cod_lancamento                                                           \n";

        return $stSql;

    }

    public function recuperaRelatorioOrigemFiltro(&$rsRecordSet, $stINNER = "", $stFiltro = "", $stFiltro2 = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql  = $this->montaRecuperaRelatorioOrigemFiltro( $stINNER, $stFiltro, $stFiltro2 );
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelatorioOrigemFiltro($stINNER, $stFiltro, $stFiltro2)
    {
        $stSql = "  SELECT                                                                                  \n";
        $stSql .="      origem                                                                              \n";
        $stSql .="      , cod_lancamento                                                                    \n";
        $stSql .="      , exercicio                                                                         \n";
        $stSql .="      , count(cod_parcela) as qtde                                                        \n";
        $stSql .="      , ( sum(valor) + sum(juros) + sum(multa) + sum(correcao) ) as valor                 \n";
        $stSql .="  FROM                                                                                    \n";
        $stSql .="      (                                                                                   \n";
        $stSql .="          SELECT                                                                          \n";
        $stSql .="              *                                                                           \n";
        $stSql .="              , coalesce ( aplica_juro ( numeracao, exercicio, cod_parcela, now()::date)  \n";
        $stSql .="                          , 0.0                                                           \n";
        $stSql .="              ) as juros                                                                  \n";
        $stSql .="              , coalesce ( aplica_multa ( numeracao, exercicio, cod_parcela, now()::date) \n";
        $stSql .="                          , 0.0                                                           \n";
        $stSql .="              ) as multa                                                                  \n";
        $stSql .="              , coalesce ( aplica_correcao( numeracao, exercicio, cod_parcela,now()::date)\n";
        $stSql .="                          , 0.0                                                           \n";
        $stSql .="              ) as correcao                                                               \n";
        $stSql .="          FROM                                                                            \n";
        $stSql .="              (                                                                           \n";
        $stSql .="              SELECT                                                                      \n";
        $stSql .="                  busca_parcelas.cod_lancamento                                           \n";
        $stSql .="                  , max(carne.numeracao) as numeracao                                     \n";
        $stSql .="                  , busca_parcelas.cod_parcela                                            \n";
        $stSql .="                  , busca_parcelas.nr_parcela                                             \n";
        $stSql .="                  , busca_parcelas.exercicio                                              \n";
        $stSql .="                  , busca_parcelas.situacao_lancamento                                    \n";
        $stSql .="                  , busca_parcelas.origem                                                 \n";
        $stSql .="                  , busca_parcelas.valor                                                  \n";
        $stSql .="                  , busca_parcelas.vencimento                                             \n";
        $stSql .="                  , ( CASE WHEN ( parcela_reemitida IS NOT NULL ) THEN                    \n";
        $stSql .="                          CASE WHEN (                                                     \n";
        $stSql .="                              SELECT count(carne.numeracao) as qtde_reemitidas_pagas      \n";
        $stSql .="                              FROM                                                        \n";
        $stSql .="                                  arrecadacao.pagamento as apag2                          \n";
        $stSql .="                                  INNER JOIN arrecadacao.carne as carne2                  \n";
        $stSql .="                                  ON carne2.numeracao = apag2.numeracao                   \n";
        $stSql .="                                  AND carne2.cod_convenio = apag2.cod_convenio            \n";
        $stSql .="                              WHERE                                                       \n";
        $stSql .="                                  carne2.cod_parcela = parcela_reemitida                  \n";
        $stSql .="                          ) > 0                                                           \n";
        $stSql .="                          THEN                                                            \n";
        $stSql .="                              'false'                                                     \n";
        $stSql .="                          ELSE                                                            \n";
        $stSql .="                              'true'                                                      \n";
        $stSql .="                          END                                                             \n";
        $stSql .="                      ELSE                                                                \n";
        $stSql .="                          'true'                                                          \n";
        $stSql .="                      END                                                                 \n";
        $stSql .="                  ) as situacao_reemissao                                                 \n";

        $stSql .="              FROM                                                                        \n";
        $stSql .="                  (                                                                       \n";
        $stSql .="                      SELECT                                                              \n";
        $stSql .="                          ( CASE WHEN dpc.num_parcelamento IS NULL THEN                   \n";
        $stSql .="                              al.cod_lancamento::varchar                                  \n";
        $stSql .="                            ELSE                                                          \n";
        $stSql .="                              dp.numero_parcelamento||'/'||dp.exercicio                   \n";
        $stSql .="                            END                                                           \n";
        $stSql .="                          )::varchar as cod_lancamento                                    \n";
        $stSql .="                          , ( arrecadacao.fn_busca_lancamento_situacao (al.cod_lancamento)\n";
        $stSql .="                          ) as situacao_lancamento                                        \n";
        $stSql .="                          , ( CASE WHEN dpc.cod_calculo IS NOT NULL THEN                  \n";
        $stSql .="                                  divida.fn_lista_origem_cobranca (                       \n";
        $stSql .="                                      dpc.num_parcelamento, dp.exercicio::int )           \n";
        $stSql .="                            ELSE                                                          \n";
        $stSql .="                              arrecadacao.fn_busca_origem_lancamento(                     \n";
        $stSql .="                                  al.cod_lancamento,ac.exercicio::int, 1, 1 )             \n";
        $stSql .="                            END                                                           \n";
        $stSql .="                          ) as origem                                                     \n";
        $stSql .="                          , ac.exercicio::integer                                         \n";
        $stSql .="                          , ap.cod_parcela                                                \n";
        $stSql .="                          , ap.nr_parcela                                                 \n";
        $stSql .="                          , ap.vencimento                                                 \n";
        $stSql .="                          , apr.cod_parcela as parcela_reemitida                          \n";
        $stSql .="                          , ap.valor                                                      \n";
        $stSql .="                      FROM                                                                \n";
        $stSql .="                          arrecadacao.lancamento as al                                    \n";
        $stSql .="                          INNER JOIN (                                                    \n";
        $stSql .="                              SELECT                                                      \n";
        $stSql .="                                  cod_lancamento                                          \n";
        $stSql .="                                  , max(cod_calculo) as cod_calculo                       \n";
        $stSql .="                              FROM                                                        \n";
        $stSql .="                                  arrecadacao.lancamento_calculo                          \n";
        $stSql .="                              GROUP BY                                                    \n";
        $stSql .="                                  cod_lancamento                                          \n";
        $stSql .="                          ) as alc                                                        \n";
        $stSql .="                          ON alc.cod_lancamento = al.cod_lancamento                       \n";
        $stSql .="                          INNER JOIN arrecadacao.calculo as ac                            \n";
        $stSql .="                          ON ac.cod_calculo = alc.cod_calculo                             \n";
        $stSql .="                          INNER JOIN monetario.credito as mc                              \n";
        $stSql .="                          ON mc.cod_credito = ac.cod_credito                              \n";
        $stSql .="                          AND mc.cod_especie = ac.cod_especie                             \n";
        $stSql .="                          AND mc.cod_genero = ac.cod_genero                               \n";
        $stSql .="                          AND mc.cod_natureza = ac.cod_natureza                           \n";
        $stSql .="                          INNER JOIN arrecadacao.calculo_cgm as accgm                     \n";
        $stSql .="                          ON accgm.cod_calculo = ac.cod_calculo                           \n";

        $stSql .="                          ". $stINNER ."                                                  \n";

        $stSql .="                          LEFT JOIN divida.parcela_calculo as dpc                         \n";
        $stSql .="                          ON dpc.cod_calculo = ac.cod_calculo                             \n";
        $stSql .="                          LEFT JOIN divida.parcela as dpar                                \n";
        $stSql .="                          ON dpar.num_parcelamento = dpc.num_parcelamento                 \n";
        $stSql .="                          AND dpar.num_parcela = dpc.num_parcela                          \n";
        $stSql .="                          LEFT JOIN divida.parcelamento as dp                             \n";
        $stSql .="                          ON dp.num_parcelamento = dpc.num_parcelamento                   \n";

        $stSql .="                          INNER JOIN arrecadacao.parcela as ap                            \n";
        $stSql .="                          ON ap.cod_lancamento = al.cod_lancamento                        \n";

        $stSql .="          LEFT JOIN (
                                    SELECT
                                        parcela_origem.cod_parcela

                                    FROM
                                        divida.parcela_origem

                                    INNER JOIN
                                        divida.divida_parcelamento
                                    ON
                                        divida_parcelamento.num_parcelamento = parcela_origem.num_parcelamento

                                    INNER JOIN
                                        divida.divida_cancelada
                                    ON
                                        divida.divida_cancelada.cod_inscricao = divida_parcelamento.cod_inscricao
                                        AND divida.divida_cancelada.exercicio = divida_parcelamento.exercicio
                            )AS dpcanc
                            ON dpcanc.cod_parcela = ap.cod_parcela                                          \n";

        $stSql .="                          LEFT JOIN arrecadacao.parcela_reemissao AS apr                  \n";
        $stSql .="                          ON apr.cod_parcela = ap.cod_parcela                             \n";
        $stSql .="                          LEFT JOIN (                                                     \n";
        $stSql .="                               SELECT                                                     \n";
        $stSql .="                                  cod_parcela,                                            \n";
        $stSql .="                                  TRUE as possui_cobranca                                 \n";
        $stSql .="                               FROM                                                       \n";
        $stSql .="                                    divida.parcela_origem AS dpo                          \n";
        $stSql .="                               INNER JOIN divida.parcela AS dp                            \n";
        $stSql .="                                  ON dp.num_parcelamento =  dpo.num_parcelamento          \n";
        $stSql .="                               WHERE dp.paga = 'f' AND cancelada = 'f'                    \n";
        $stSql .="                                     AND num_parcela <> 0                                 \n";
        $stSql .="                              GROUP BY 1                                                  \n";
        $stSql .="                            ) as dpo                                                      \n";
        $stSql .="                         ON dpo.cod_parcela = ap.cod_parcela                              \n";
        $stSql .="                      WHERE                                                               \n";
        $stSql .="                          ". $stFiltro ."                                                 \n";
        $stSql .="                          AND    dpo.possui_cobranca is null
                                            AND (
                                                CASE WHEN
                                                    (   SELECT
                                                            count(*)
                                                        FROM
                                                            divida.parcela
                                                        inner join
                                                            divida.parcela_origem
                                                        on
                                                            parcela_origem.num_parcelamento = parcela.num_parcelamento
                                                            AND parcela_origem.cod_parcela = ap.cod_parcela

                                                        WHERE num_parcela = 0
                                                        AND paga = true
                                                        AND cancelada = false
                                                    ) > 0
                                                    THEN
                                                        FALSE
                                                    ELSE
                                                        TRUE
                                                    END
                                            )                                                               \n";
        $stSql .="                          AND dpcanc.cod_parcela IS NULL                                  \n";
        $stSql .="                          AND (                                                           \n";
        $stSql .="                              CASE WHEN ap.nr_parcela = 0 THEN                            \n";
        $stSql .="                                  CASE WHEN now()::date > ap.vencimento THEN              \n";
        $stSql .="                                      FALSE                                               \n";
        $stSql .="                                  ELSE                                                    \n";
        $stSql .="                                      TRUE                                                \n";
        $stSql .="                                  END                                                     \n";
        $stSql .="                              ELSE                                                        \n";
        $stSql .="                                  CASE WHEN                                               \n";
        $stSql .="                                      (   SELECT count(*)                                 \n";
        $stSql .="                                          FROM arrecadacao.parcela                        \n";
        $stSql .="                                          WHERE nr_parcela = 0                            \n";
        $stSql .="                                          AND cod_lancamento = al.cod_lancamento          \n";
        $stSql .="                                          AND now()::date <= vencimento                   \n";
        $stSql .="                                      ) > 0                                               \n";
        $stSql .="                                  THEN                                                    \n";
        $stSql .="                                      FALSE                                               \n";
        $stSql .="                                  ELSE                                                    \n";
        $stSql .="                                      TRUE                                                \n";
        $stSql .="                                  END                                                     \n";
        $stSql .="                              END                                                         \n";
        $stSql .="                          )                                                               \n";
        $stSql .="                          AND (                                                           \n";
        $stSql .="                              CASE WHEN dpc.cod_calculo IS NOT NULL THEN                  \n";
        $stSql .="                                 CASE WHEN dpar.cancelada = false OR dpar.paga = true THEN\n";
        $stSql .="                                      TRUE                                                \n";
        $stSql .="                                  ELSE                                                    \n";
        $stSql .="                                      FALSE                                               \n";
        $stSql .="                                  END                                                     \n";
        $stSql .="                              ELSE                                                        \n";
        $stSql .="                                  TRUE                                                    \n";
        $stSql .="                              END                                                         \n";
        $stSql .="                          )                                                               \n";
        $stSql .="                      GROUP BY                                                            \n";
        $stSql .="                          al.cod_lancamento , ac.exercicio                                \n";
        $stSql .="                          , ap.cod_parcela, ap.nr_parcela, ap.valor, ap.vencimento        \n";
        $stSql .="                          , apr.cod_parcela, ap.valor                                     \n";
        $stSql .="                          , dpc.cod_calculo, dpc.num_parcelamento                         \n";
        $stSql .="                          , dp.numero_parcelamento                                        \n";
        $stSql .="                          , dp.exercicio                                                  \n";
        $stSql .="                  ) as busca_parcelas                                                     \n";
        $stSql .="                  INNER JOIN arrecadacao.carne                                            \n";
        $stSql .="                  ON carne.cod_parcela = busca_parcelas.cod_parcela                       \n";

        $stSql .="                  LEFT JOIN arrecadacao.pagamento as apag                                 \n";
        $stSql .="                  ON apag.numeracao = carne.numeracao                                     \n";
        $stSql .="                  AND apag.cod_convenio = carne.cod_convenio                              \n";

        $stSql .="                  LEFT JOIN arrecadacao.tipo_pagamento as atp                             \n";
        $stSql .="                  ON atp.cod_tipo = apag.cod_tipo                                         \n";

        $stSql .="                  LEFT JOIN arrecadacao.carne_devolucao as acd                            \n";
        $stSql .="                  ON acd.numeracao = carne.numeracao                                      \n";
        $stSql .="                  AND acd.cod_convenio = carne.cod_convenio                               \n";

        $stSql .="              WHERE                                                                       \n";

        $stSql .="                  ".$stFiltro2." \n";

        $stSql .="                  apag.numeracao IS NULL                                                  \n";
        $stSql .="                  AND (                                                                   \n";
        $stSql .="                      CASE WHEN acd.numeracao IS NOT NULL THEN                            \n";
        $stSql .="                          CASE WHEN acd.cod_motivo = 11  THEN                             \n";
        $stSql .="                              TRUE                                                        \n";
        $stSql .="                          ELSE                                                            \n";
        $stSql .="                              FALSE                                                       \n";
        $stSql .="                          END                                                             \n";
        $stSql .="                      ELSE                                                                \n";
        $stSql .="                          TRUE                                                            \n";
        $stSql .="                      END                                                                 \n";
        $stSql .="                  )                                                                       \n";

        $stSql .="              GROUP BY                                                                    \n";
        $stSql .="                  busca_parcelas.cod_lancamento, situacao_lancamento                      \n";
        $stSql .="                  , busca_parcelas.origem, busca_parcelas.cod_parcela                     \n";
        $stSql .="                  , busca_parcelas.nr_parcela, busca_parcelas.valor                       \n";
        $stSql .="                  , busca_parcelas.exercicio, busca_parcelas.parcela_reemitida            \n";
        $stSql .="                  , busca_parcelas.vencimento                                             \n";

        $stSql .="          ) as busca_carnes                                                               \n";

        $stSql .="  ) as busca_valores                                                                      \n";

        $stSql .="  GROUP BY                                                                                \n";
        $stSql .="      exercicio, cod_lancamento,origem                                                    \n";
        $stSql .="  ORDER BY                                                                                \n";
        $stSql .="      exercicio, cod_lancamento                                                           \n";

        return $stSql;

    }

    public function recuperaRelatorio(&$rsRecordSet, $stINNER = "", $stFiltro = "", $stFiltroRel = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql  = $this->montaRecuperaRelatorio( $stINNER, $stFiltro, $stFiltroRel );
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelatorio($stINNER, $stFiltro, $stFiltroRel)
    {
        $stSql = "  SELECT                                                                                  \n";
        $stSql .="      *                                                                                   \n";
        $stSql .="      , ( valor + juros + multa + correcao ) as total                                     \n";
        $stSql .="  FROM                                                                                    \n";
        $stSql .="      (                                                                                   \n";
        $stSql .="          SELECT                                                                          \n";
        $stSql .="              *                                                                           \n";
        $stSql .="              , coalesce ( aplica_juro ( numeracao, exercicio, cod_parcela, now()::date)  \n";
        $stSql .="                          , 0.0                                                           \n";
        $stSql .="              ) as juros                                                                  \n";
        $stSql .="              , coalesce ( aplica_multa ( numeracao, exercicio, cod_parcela, now()::date) \n";
        $stSql .="                          , 0.0                                                           \n";
        $stSql .="              ) as multa                                                                  \n";
        $stSql .="              , coalesce (aplica_correcao( numeracao, exercicio, cod_parcela, now()::date)\n";
        $stSql .="                          , 0.0                                                           \n";
        $stSql .="              ) as correcao                                                               \n";
        $stSql .="              , arrecadacao.fn_total_parcelas ( cod_lancamento ) as total_parcelas        \n";
        $stSql .="          FROM                                                                            \n";
        $stSql .="              (                                                                           \n";
        $stSql .="              SELECT                                                                      \n";
        $stSql .="                  busca_parcelas.cod_lancamento                                           \n";
        $stSql .="                  , max(carne.numeracao) as numeracao                                     \n";
        $stSql .="                  , busca_parcelas.cod_parcela                                            \n";
        $stSql .="                  , busca_parcelas.nr_parcela                                             \n";
        $stSql .="                  , busca_parcelas.exercicio                                              \n";
        $stSql .="                  , busca_parcelas.situacao_lancamento                                    \n";
        $stSql .="                  , busca_parcelas.origem                                                 \n";
        $stSql .="                  , busca_parcelas.valor                                                  \n";
        $stSql .="                  , busca_parcelas.vencimento                                             \n";
        $stSql .="                  , to_char ( busca_parcelas.vencimento, 'dd/mm/yyyy' ) as vencimento_br  \n";
        $stSql .="                  , ( CASE WHEN ( parcela_reemitida IS NOT NULL ) THEN                    \n";
        $stSql .="                          CASE WHEN (                                                     \n";
        $stSql .="                              SELECT count(carne.numeracao) as qtde_reemitidas_pagas      \n";
        $stSql .="                              FROM                                                        \n";
        $stSql .="                                  arrecadacao.pagamento as apag2                          \n";
        $stSql .="                                  INNER JOIN arrecadacao.carne as carne2                  \n";
        $stSql .="                                  ON carne2.numeracao = apag2.numeracao                   \n";
        $stSql .="                                  AND carne2.cod_convenio = apag2.cod_convenio            \n";
        $stSql .="                              WHERE                                                       \n";
        $stSql .="                                  carne2.cod_parcela = parcela_reemitida                  \n";
        $stSql .="                          ) > 0                                                           \n";
        $stSql .="                          THEN                                                            \n";
        $stSql .="                              'false'                                                     \n";
        $stSql .="                          ELSE                                                            \n";
        $stSql .="                              'true'                                                      \n";
        $stSql .="                          END                                                             \n";
        $stSql .="                      ELSE                                                                \n";
        $stSql .="                          'true'                                                          \n";
        $stSql .="                      END                                                                 \n";
        $stSql .="                  ) as situacao_reemissao                                                 \n";
        $stSql .="                  , ( CASE WHEN ( vencimento < now()::date ) THEN                         \n";
        $stSql .="                          'vencida'                                                       \n";
        $stSql .="                      ELSE                                                                \n";
        $stSql .="                          'em aberto'                                                     \n";
        $stSql .="                      END                                                                 \n";
        $stSql .="                  ) as situacao_parcela                                                   \n";
        $stSql .="              FROM                                                                        \n";
        $stSql .="                  (                                                                       \n";
        $stSql .="                      SELECT                                                              \n";
        $stSql .="                          ( CASE WHEN dpc.cod_calculo IS NULL THEN                        \n";
        $stSql .="                              al.cod_lancamento::varchar                                  \n";
        $stSql .="                            ELSE                                                          \n";
        $stSql .="                              dp.numero_parcelamento||'/'||dp.exercicio                   \n";
        $stSql .="                            END                                                           \n";
        $stSql .="                          )::varchar as lancamento_nominal                                \n";
        $stSql .="                          , al.cod_lancamento                                             \n";
        $stSql .="                          , ( arrecadacao.fn_busca_lancamento_situacao (al.cod_lancamento)\n";
        $stSql .="                          ) as situacao_lancamento                                        \n";
        $stSql .="                        , ( CASE WHEN dpc.cod_calculo IS NOT NULL THEN                    \n";
        $stSql .="                                'Cobrança em D.A.'                                        \n";
        $stSql .="                            ELSE                                                          \n";
        $stSql .="                              arrecadacao.fn_busca_origem_lancamento(                     \n";
        $stSql .="                                  al.cod_lancamento,ac.exercicio::int, 1, 1 )             \n";
        $stSql .="                            END                                                           \n";
        $stSql .="                          ) as origem                                                     \n";
        $stSql .="                          , ac.exercicio::integer                                         \n";
        $stSql .="                          , ap.cod_parcela                                                \n";
        $stSql .="                          , ap.nr_parcela                                                 \n";
        $stSql .="                          , ap.vencimento                                                 \n";
        $stSql .="                          , apr.cod_parcela as parcela_reemitida                          \n";
        $stSql .="                          , ap.valor                                                      \n";
        $stSql .="                      FROM                                                                \n";
        $stSql .="                          arrecadacao.lancamento as al                                    \n";
        $stSql .="                          INNER JOIN (                                                    \n";
        $stSql .="                              SELECT                                                      \n";
        $stSql .="                                  cod_lancamento                                          \n";
        $stSql .="                                  , max(cod_calculo) as cod_calculo                       \n";
        $stSql .="                              FROM                                                        \n";
        $stSql .="                                  arrecadacao.lancamento_calculo                          \n";
        $stSql .="                              GROUP BY                                                    \n";
        $stSql .="                                  cod_lancamento                                          \n";
        $stSql .="                          ) as alc                                                        \n";
        $stSql .="                          ON alc.cod_lancamento = al.cod_lancamento                       \n";

        $stSql .="                          INNER JOIN arrecadacao.calculo as ac                            \n";
        $stSql .="                          ON ac.cod_calculo = alc.cod_calculo                             \n";

        $stSql .="                          INNER JOIN arrecadacao.calculo_cgm as accgm                     \n";
        $stSql .="                          ON accgm.cod_calculo = ac.cod_calculo                           \n";

        $stSql .="                          ". $stINNER ."                                                  \n";

        $stSql .="                          LEFT JOIN divida.parcela_calculo as dpc                         \n";
        $stSql .="                          ON dpc.cod_calculo = ac.cod_calculo                             \n";
        $stSql .="                          LEFT JOIN divida.parcela as dpar                                \n";
        $stSql .="                          ON dpar.num_parcelamento = dpc.num_parcelamento                 \n";
        $stSql .="                          AND dpar.num_parcela = dpc.num_parcela                          \n";
        $stSql .="                          LEFT JOIN divida.parcelamento as dp                             \n";
        $stSql .="                          ON dp.num_parcelamento = dpc.num_parcelamento                   \n";

        $stSql .="                          INNER JOIN arrecadacao.parcela as ap                            \n";
        $stSql .="                          ON ap.cod_lancamento = al.cod_lancamento                        \n";

        $stSql .="                          LEFT JOIN arrecadacao.parcela_reemissao AS apr                  \n";
        $stSql .="                          ON apr.cod_parcela = ap.cod_parcela                             \n";

        $stSql .="                      WHERE                                                               \n";

        $stSql .="                          ". $stFiltro ."                                                 \n";

        $stSql .="                          AND (                                                           \n";
        $stSql .="                              CASE WHEN ap.nr_parcela = 0 THEN                            \n";
        $stSql .="                                  CASE WHEN now()::date > ap.vencimento THEN              \n";
        $stSql .="                                      FALSE                                               \n";
        $stSql .="                                  ELSE                                                    \n";
        $stSql .="                                      TRUE                                                \n";
        $stSql .="                                  END                                                     \n";
        $stSql .="                              ELSE                                                        \n";
        $stSql .="                                  CASE WHEN                                               \n";
        $stSql .="                                      (   SELECT count(*)                                 \n";
        $stSql .="                                          FROM arrecadacao.parcela                        \n";
        $stSql .="                                          WHERE nr_parcela = 0                            \n";
        $stSql .="                                          AND cod_lancamento = al.cod_lancamento          \n";
        $stSql .="                                          AND now()::date <= vencimento                   \n";
        $stSql .="                                      ) > 0                                               \n";
        $stSql .="                                  THEN                                                    \n";
        $stSql .="                                      FALSE                                               \n";
        $stSql .="                                  ELSE                                                    \n";
        $stSql .="                                      TRUE                                                \n";
        $stSql .="                                  END                                                     \n";
        $stSql .="                              END                                                         \n";
        $stSql .="                          )                                                               \n";
        $stSql .="                      GROUP BY                                                            \n";
        $stSql .="                          al.cod_lancamento , ac.exercicio                                \n";
        $stSql .="                          , ap.cod_parcela, ap.nr_parcela, ap.valor, ap.vencimento        \n";
        $stSql .="                          , apr.cod_parcela, ap.valor, dpc.cod_calculo                    \n";
        $stSql .="                          , dp.numero_parcelamento, dp.exercicio                          \n";

        $stSql .="                  ) as busca_parcelas                                                     \n";

        $stSql .="                  INNER JOIN arrecadacao.carne                                            \n";
        $stSql .="                  ON carne.cod_parcela = busca_parcelas.cod_parcela                       \n";
        $stSql .="                  AND carne.exercicio = busca_parcelas.exercicio::varchar                 \n";

        $stSql .="                  LEFT JOIN arrecadacao.pagamento as apag                                 \n";
        $stSql .="                  ON apag.numeracao = carne.numeracao                                     \n";
        $stSql .="                  AND apag.cod_convenio = carne.cod_convenio                              \n";

        $stSql .="                  LEFT JOIN arrecadacao.tipo_pagamento as atp                             \n";
        $stSql .="                  ON atp.cod_tipo = apag.cod_tipo                                         \n";

        $stSql .="                  LEFT JOIN arrecadacao.carne_devolucao as acd                            \n";
        $stSql .="                  ON acd.numeracao = carne.numeracao                                      \n";
        $stSql .="                  AND acd.cod_convenio = carne.cod_convenio                               \n";

        $stSql .="              WHERE                                                                       \n";
        $stSql .="                  apag.numeracao IS NULL                                                  \n";
        $stSql .="                  AND (                                                                   \n";
        $stSql .="                      CASE WHEN acd.cod_motivo != 11 THEN                            \n";
        $stSql .="                          FALSE                                                           \n";
        $stSql .="                      ELSE                                                                \n";
        $stSql .="                          TRUE                                                            \n";
        $stSql .="                      END                                                                 \n";
        $stSql .="                  )                                                                       \n";
        $stSql .="                  ".$stFiltroRel."                                                        \n";

        $stSql .="              GROUP BY                                                                    \n";

        $stSql .="                  busca_parcelas.cod_lancamento, situacao_lancamento                      \n";
        $stSql .="                  , busca_parcelas.origem, busca_parcelas.cod_parcela                     \n";
        $stSql .="                  , busca_parcelas.nr_parcela, busca_parcelas.valor                       \n";
        $stSql .="                  , busca_parcelas.exercicio, busca_parcelas.parcela_reemitida            \n";
        $stSql .="                  , busca_parcelas.vencimento, acd.cod_motivo                             \n";

        $stSql .="          ) as busca_carnes                                                               \n";
        $stSql .="      WHERE                                                                               \n";
        $stSql .="          ( situacao_reemissao != 'false' )                                               \n";
        $stSql .="          AND (   ( situacao_lancamento = 'Ativo' )                                       \n";
        $stSql .="                  OR                                                                      \n";
        $stSql .="                  ( situacao_lancamento = 'Cancelada por DA')
                                    OR
                                    ( situacao_lancamento = 'Inscrito em D.A.') )                           \n";

        $stSql .="      ORDER BY                                                                            \n";
        $stSql .="          exercicio, cod_lancamento, nr_parcela                                           \n";

        $stSql .="  ) as busca_valores                                                                      \n";

        return $stSql;
    }

    public function recuperaListaDeParcelasLancamento(&$rsRecordSet, $stCodLancamento, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql  = $this->montaRecuperaListaDeParcelasLancamento( $stCodLancamento );
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDeParcelasLancamento($stCodLancamento)
    {
        $stSql = " SELECT DISTINCT
                        parcela.cod_parcela
                        ,parcela.vencimento

                    FROM
                        arrecadacao.lancamento

                    INNER JOIN
                        arrecadacao.parcela
                    ON
                        lancamento.cod_lancamento = parcela.cod_lancamento

                    INNER JOIN
                        arrecadacao.carne
                    ON
                        carne.cod_parcela = parcela.cod_parcela

                    LEFT JOIN
                        arrecadacao.pagamento
                    ON
                        pagamento.numeracao = carne.numeracao

                    WHERE
                        parcela.nr_parcela > 0 AND
                        pagamento IS NULL AND
                        lancamento.cod_lancamento = ".$stCodLancamento;

        return $stSql;
    }

    public function recuperaListaDeParcelasDivida(&$rsRecordSet, $stNumeracaoParcelamento, $stExercicio, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql  = $this->montaRecuperaListaDeParcelasDivida( $stNumeracaoParcelamento, $stExercicio );
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDeParcelasDivida($stNumeracaoParcelamento, $stExercicio)
    {
        $stSql = " SELECT DISTINCT
                        arrecadacao.parcela.cod_parcela
                        ,arrecadacao.parcela.vencimento

                    FROM
                        divida.parcelamento

                    INNER JOIN
                        divida.parcela
                    ON
                        divida.parcela.num_parcelamento = parcelamento.num_parcelamento

                    INNER JOIN
                        divida.parcela_calculo
                    ON
                        parcela_calculo.num_parcelamento = divida.parcela.num_parcelamento
                        AND parcela_calculo.num_parcela = divida.parcela.num_parcela

                    INNER JOIN
                        arrecadacao.lancamento_calculo
                    ON
                        lancamento_calculo.cod_calculo = parcela_calculo.cod_calculo

                    INNER JOIN
                        arrecadacao.parcela
                    ON
                        arrecadacao.parcela.cod_lancamento = lancamento_calculo.cod_lancamento

                    INNER JOIN
                        arrecadacao.carne
                    ON
                        carne.cod_parcela = arrecadacao.parcela.cod_parcela

                    LEFT JOIN
                        arrecadacao.pagamento
                    ON
                        pagamento.numeracao = carne.numeracao

                    WHERE
                        arrecadacao.parcela.nr_parcela > 0 AND
                        pagamento IS NULL AND
                        parcelamento.numero_parcelamento = ".$stNumeracaoParcelamento." AND
                        parcelamento.exercicio = ".$stExercicio."::VARCHAR";

        return $stSql;
    }

}
