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
/*
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 29177 $
* $Name$
* $Author: eduardoschitz $
* $Date: 2008-04-14 16:24:58 -0300 (Seg, 14 Abr 2008) $
*
* Casos de uso: uc-02.02.27
*/

/*
$Log$
Revision 1.6  2007/06/19 20:14:19  bruce
Correção da identificação de Bug

Revision 1.5  2007/06/12 20:16:56  vitor
Bug#9374#

Revision 1.4  2007/04/11 21:43:36  luciano
Bug#8824#

Revision 1.3  2006/12/08 18:50:28  cako
Bug#7778#

Revision 1.2  2006/10/19 15:53:26  gris
-- Função relatorioRazao.plsql excluida, foi substituida pela relatorioRazao.sql

Revision 1.1  2006/10/19 11:44:01  gris
-- Função relatorioRazao.plsql excluida, foi substituida pela relatorioRazao.sql

Revision 1.28  2006/10/09 16:02:05  cako
Bug#7085#

Revision 1.27  2006/10/09 15:11:53  gris
-- Ordenação correta do rela?ório razão por código estrutural e data.

Revision 1.26  2006/09/14 14:57:36  jose.eduardo
Bug#6832#

Revision 1.25  2006/08/04 17:58:49  cleisson
Bug#6620#

Revision 1.24  2006/07/27 17:17:04  cako
Bug#4343#

Revision 1.23  2006/07/25 14:36:16  jose.eduardo
Bug#4343#

Revision 1.22  2006/07/21 18:23:08  jose.eduardo
Bug#6620#

Revision 1.21  2006/07/21 14:33:48  jose.eduardo
Bug#6620#

Revision 1.20  2006/07/21 13:44:47  jose.eduardo
Bug#6620#

Revision 1.19  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION contabilidade.fn_relatorio_razao(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio            ALIAS FOR $1;
    stFiltro               ALIAS FOR $2;
    stCodEstruturalInicial ALIAS FOR $3;
    stCodEstruturalFinal   ALIAS FOR $4;
    stDtInicial            ALIAS FOR $5;
    stDtFinal              ALIAS FOR $6;
    stCodEntidades         ALIAS FOR $7;
    dtInicialAnterior      ALIAS FOR $8;
    dtFinalAnterior        ALIAS FOR $9;
    boMovimentacaoConta    ALIAS FOR $10;
    stSql                  VARCHAR   := '';
    reRegistro             RECORD;
    stAux                  VARCHAR   := '';

BEGIN

If dtInicialAnterior != dtFinalAnterior Then
   stAux := ' AND     lo.dt_lote BETWEEN to_date('''||dtInicialAnterior||''',''dd/mm/yyyy'') AND (to_date('''||dtFinalAnterior||''',''dd/mm/yyyy'')) ';
Else
   stAux := ' AND     lo.dt_lote = to_date('''||dtInicialAnterior||''',''dd/mm/yyyy'')  and lo.tipo = ''I'' ';
End If;

stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
    SELECT
        pc.cod_estrutural as cod_estrutural,
        pa.cod_plano as cod_plano,
        vl.vl_lancamento as vl_lancamento,
        vl.oid as oid_tmp
    FROM
         contabilidade.plano_conta      as pc
        ,contabilidade.plano_analitica  as pa
        ,contabilidade.conta_debito     as cd
        ,contabilidade.valor_lancamento as vl
        ,contabilidade.lote             as lo
    WHERE
            pc.cod_conta = pa.cod_conta
    AND     pc.exercicio = pa.exercicio
    AND     pc.cod_estrutural BETWEEN ''' || stCodEstruturalInicial || ''' AND ''' || stCodEstruturalFinal || '''

    AND     pa.cod_plano = cd.cod_plano
    AND     pa.exercicio = cd.exercicio

    AND     cd.exercicio = ''' || stExercicio || '''
    AND     cd.cod_entidade   IN (' || stCodEntidades || ')
    AND     cd.tipo_valor       = ''D''

    AND     cd.cod_lote  = vl.cod_lote
    AND     cd.tipo      = vl.tipo
    AND     cd.sequencia = vl.sequencia
    AND     cd.exercicio = vl.exercicio
    AND     cd.tipo_valor= vl.tipo_valor
    AND     cd.cod_entidade= vl.cod_entidade

    AND     vl.cod_lote  = lo.cod_lote
    AND     vl.tipo      = lo.tipo
    AND     vl.exercicio = lo.exercicio
    AND     vl.cod_entidade= lo.cod_entidade ' || stAux  || stFiltro || '

    UNION ALL

    SELECT
        pc.cod_estrutural as cod_estrutural,
        pa.cod_plano as cod_plano,
        vl.vl_lancamento as vl_lancamento,
        vl.oid as oid_tmp
    FROM
         contabilidade.plano_conta      as pc
        ,contabilidade.plano_analitica  as pa
        ,contabilidade.conta_credito    as cc
        ,contabilidade.valor_lancamento as vl
        ,contabilidade.lote             as lo
    WHERE
            pc.cod_conta = pa.cod_conta
    AND     pc.exercicio = pa.exercicio
    AND     pc.cod_estrutural BETWEEN ''' || stCodEstruturalInicial || ''' AND ''' || stCodEstruturalFinal || '''

    AND     pa.cod_plano = cc.cod_plano
    AND     pa.exercicio = cc.exercicio

    AND     cc.exercicio = ''' || stExercicio || '''
    AND     cc.cod_entidade   IN (' || stCodEntidades || ')
    AND     cc.tipo_valor       = ''C''

    AND     cc.cod_lote  = vl.cod_lote
    AND     cc.tipo      = vl.tipo
    AND     cc.sequencia = vl.sequencia
    AND     cc.exercicio = vl.exercicio
    AND     cc.tipo_valor= vl.tipo_valor
    AND     cc.cod_entidade= vl.cod_entidade

    AND     vl.cod_lote  = lo.cod_lote
    AND     vl.tipo      = lo.tipo
    AND     vl.exercicio = lo.exercicio
    AND     vl.cod_entidade= lo.cod_entidade ' || stAux || stFiltro || '

)';

--RAISE NOTICE '%',stSql;
EXECUTE stSql;

CREATE UNIQUE INDEX uq_valor ON tmp_valor (cod_estrutural varchar_pattern_ops, oid_tmp);

stSql := 'CREATE TEMPORARY TABLE tmp_somatorio AS (
    SELECT
        pc.cod_estrutural,
        contabilidade.fn_somatorio_razao(pc.cod_estrutural) as saldoAnterior
    FROM
        contabilidade.plano_conta      as pc,
        contabilidade.plano_analitica  as pa
    WHERE pc.exercicio = pa.exercicio
      AND pc.cod_conta = pa.cod_conta
      AND pc.exercicio = '''||stExercicio||'''
      ' || stFiltro || '
)';

EXECUTE stSql;
RAISE NOTICE '%',stSql;

CREATE UNIQUE INDEX uq_somatorio ON tmp_somatorio (cod_estrutural varchar_pattern_ops);

If stDtInicial != stDtFinal Then
   stAux := ' AND lo.dt_lote BETWEEN to_date('''||stDtInicial||''',''dd/mm/yyyy'') AND to_date('''||stDtFinal||''',''dd/mm/yyyy'') ';
Else
   stAux := ' AND lo.dt_lote =  to_date('''||stDtInicial||''',''dd/mm/yyyy'') ';
End If;

stSql := 'CREATE TEMPORARY TABLE tmp_razao AS (
   SELECT
        l.oid as oid_tmp,
        l.cod_lote,
        l.sequencia,
        l.cod_historico,
        l.complemento,
        l.exercicio,
        l.cod_entidade,
        l.tipo,
        hc.nom_historico,

        CASE WHEN tt.cod_lote IS NOT NULL AND tret.cod_recibo_extra is not null
                THEN '' recibo '' || tret.cod_recibo_extra || '' - '' || tt.observacao
             WHEN tt.cod_lote IS NOT NULL AND tret.cod_recibo_extra is null
                THEN tt.observacao
             WHEN tte.cod_lote IS NOT NULL AND tret2.cod_recibo_extra is not null
                THEN '' recibo '' || tret2.cod_recibo_extra || '' - '' || tte.observacao
             WHEN tte.cod_lote IS NOT NULL AND tret2.cod_recibo_extra is null
                THEN tte.observacao
        END AS observacao,

        vl.vl_lancamento,
        vl.tipo_valor,
        lo.dt_lote,
        cd.cod_plano AS cod_plano
    FROM
          contabilidade.lancamento         AS  l,
          contabilidade.lote               AS lo
          LEFT JOIN tesouraria.transferencia AS tt ON (
                tt.cod_lote     = lo.cod_lote
            AND tt.exercicio    = lo.exercicio
            AND tt.tipo         = lo.tipo
            AND tt.cod_entidade = lo.cod_entidade
          )
          
          LEFT JOIN tesouraria.recibo_extra_transferencia AS tret ON (
                tt.cod_lote     = tret.cod_lote
            AND tt.exercicio    = tret.exercicio
            AND tt.tipo         = tret.tipo
            AND tt.cod_entidade = tret.cod_entidade
          )
          
          LEFT JOIN tesouraria.transferencia_estornada AS tte ON (
                tte.cod_lote_estorno  = lo.cod_lote
            AND tte.exercicio         = lo.exercicio
            AND tte.tipo              = lo.tipo
            AND tte.cod_entidade      = lo.cod_entidade
          )
          
          LEFT JOIN tesouraria.recibo_extra_transferencia AS tret2 ON (
                tte.cod_lote     = tret2.cod_lote
            AND tte.exercicio    = tret2.exercicio
            AND tte.tipo         = tret2.tipo
            AND tte.cod_entidade = tret2.cod_entidade
          )
          ,
          contabilidade.historico_contabil AS hc,
          contabilidade.valor_lancamento   AS vl,
          contabilidade.conta_debito AS cd,
          contabilidade.plano_analitica  AS pa
    WHERE
            cd.exercicio    = vl.exercicio
        AND cd.cod_lote     = vl.cod_lote
        AND cd.tipo         = vl.tipo
        AND cd.sequencia    = vl.sequencia
        AND cd.tipo_valor   = vl.tipo_valor
        AND cd.cod_entidade = vl.cod_entidade

        AND vl.cod_lote      = l.cod_lote
        AND vl.tipo          = l.tipo
        AND vl.sequencia     = l.sequencia
        AND vl.exercicio     = l.exercicio
        AND vl.cod_entidade  = l.cod_entidade

        AND l.exercicio = ''' || stExercicio || '''
        AND l.cod_entidade   IN (' || stCodEntidades || ')

        AND lo.cod_lote      = l.cod_lote
        AND lo.exercicio     = l.exercicio
        AND lo.tipo          = l.tipo
        AND lo.cod_entidade  = l.cod_entidade ' || stAux ||'

        AND hc.cod_historico = l.cod_historico
        AND hc.exercicio     = l.exercicio

        AND cd.cod_plano = pa.cod_plano
        AND cd.exercicio = pa.exercicio

        ' || stFiltro || '
UNION ALL

    SELECT
        l.oid as oid_tmp,
        l.cod_lote,
        l.sequencia,
        l.cod_historico,
        l.complemento,
        l.exercicio,
        l.cod_entidade,
        l.tipo,
        hc.nom_historico,
        
        CASE WHEN tt.cod_lote IS NOT NULL AND tret.cod_recibo_extra is not null
                THEN '' recibo '' || tret.cod_recibo_extra || '' - '' || tt.observacao
             WHEN tt.cod_lote IS NOT NULL AND tret.cod_recibo_extra is null
                THEN tt.observacao
             WHEN tte.cod_lote IS NOT NULL AND tret2.cod_recibo_extra is not null
                THEN '' recibo '' || tret2.cod_recibo_extra || '' - '' || tte.observacao
             WHEN tte.cod_lote IS NOT NULL AND tret2.cod_recibo_extra is null
                THEN tte.observacao
        END AS observacao,
        
        vl.vl_lancamento,
        vl.tipo_valor,
        lo.dt_lote,
        cc.cod_plano AS cod_plano
    FROM
          contabilidade.lancamento         AS  l,
          contabilidade.lote               AS lo
          LEFT JOIN tesouraria.transferencia AS tt ON (
                tt.cod_lote     = lo.cod_lote
            AND tt.exercicio    = lo.exercicio
            AND tt.tipo         = lo.tipo
            AND tt.cod_entidade = lo.cod_entidade
          )
          LEFT JOIN tesouraria.recibo_extra_transferencia AS tret ON (
                tt.cod_lote     = tret.cod_lote
            AND tt.exercicio    = tret.exercicio
            AND tt.tipo         = tret.tipo
            AND tt.cod_entidade = tret.cod_entidade
          )
          
          LEFT JOIN tesouraria.transferencia_estornada AS tte ON (
                tte.cod_lote_estorno  = lo.cod_lote
            AND tte.exercicio         = lo.exercicio
            AND tte.tipo              = lo.tipo
            AND tte.cod_entidade      = lo.cod_entidade
          )

          LEFT JOIN tesouraria.recibo_extra_transferencia AS tret2 ON (
                tte.cod_lote     = tret2.cod_lote
            AND tte.exercicio    = tret2.exercicio
            AND tte.tipo         = tret2.tipo
            AND tte.cod_entidade = tret2.cod_entidade
          )
          ,
          contabilidade.historico_contabil AS hc,
          contabilidade.valor_lancamento   AS vl,
          contabilidade.conta_credito    AS cc,
          contabilidade.plano_analitica  AS pa
    WHERE
            cc.exercicio    = vl.exercicio
        AND cc.cod_lote     = vl.cod_lote
        AND cc.tipo         = vl.tipo
        AND cc.sequencia    = vl.sequencia
        AND cc.tipo_valor   = vl.tipo_valor
        AND cc.cod_entidade = vl.cod_entidade

        AND vl.cod_lote      = l.cod_lote
        AND vl.tipo          = l.tipo
        AND vl.sequencia     = l.sequencia
        AND vl.exercicio     = l.exercicio
        AND vl.cod_entidade  = l.cod_entidade

        AND l.exercicio = ''' || stExercicio || '''
        AND l.cod_entidade   IN (' || stCodEntidades || ')

        AND lo.cod_lote      = l.cod_lote
        AND lo.exercicio     = l.exercicio
        AND lo.tipo          = l.tipo
        AND lo.cod_entidade  = l.cod_entidade ' || stAux || '

        AND hc.cod_historico = l.cod_historico
        AND hc.exercicio     = l.exercicio

        AND cc.cod_plano = pa.cod_plano
        AND cc.exercicio = pa.exercicio

        ' || stFiltro || '
)';

RAISE NOTICE '%',stSql;
EXECUTE stSql;

stSql := 'CREATE TEMPORARY TABLE tmp_relatorio AS
    SELECT
        tabela.cod_lote,
        tabela.sequencia,
        tabela.cod_historico,
        tabela.nom_historico,
        tabela.observacao,
        tabela.complemento,
        tabela.exercicio,
        tabela.cod_entidade,
        tabela.tipo,
        tabela.vl_lancamento,
        tabela.tipo_valor,
        cast( to_char( tabela.dt_lote, '|| quote_literal('dd/mm/yyyy') || ' ) as varchar )AS dt_lote,
        pa.cod_plano,
        pc.cod_estrutural,
        pc.nom_conta,
        contabilidade.fn_recupera_contra_partida(
              tabela.exercicio
             ,tabela.cod_lote
             ,tabela.tipo
             ,tabela.sequencia
             ,tabela.tipo_valor
             ,tabela.cod_entidade
        ) AS contra_partida,
        sum(tmp_somatorio.saldoAnterior) as saldoAnterior,
        tabela.dt_lote  as data
    FROM
        contabilidade.plano_conta     AS pc
            LEFT JOIN tmp_somatorio AS tmp_somatorio
                ON ( pc.cod_estrutural = tmp_somatorio.cod_estrutural),
        contabilidade.plano_analitica AS pa
        LEFT JOIN tmp_razao AS tabela
        ON( tabela.cod_plano = pa.cod_plano
        AND tabela.exercicio = pa.exercicio )
    WHERE
            pa.cod_conta     = pc.cod_conta
        AND pa.exercicio     = pc.exercicio
        AND pa.cod_plano is not null
        AND pa.exercicio = ''' || stExercicio || '''
--        AND tabela.tipo<>''I''
        AND cast( pa.cod_plano as varchar ) != ''''
        AND pc.cod_estrutural BETWEEN ''' || stCodEstruturalInicial || ''' AND ''' || stCodEstruturalFinal || '''
        ' || stFiltro || '
    GROUP BY
        tabela.cod_lote,
        tabela.sequencia,
        tabela.cod_historico,
        tabela.nom_historico,
        tabela.observacao,
        tabela.complemento,
        tabela.exercicio,
        tabela.cod_entidade,
        tabela.tipo,
        tabela.vl_lancamento,
        tabela.tipo_valor,
        tabela.dt_lote,
        pa.cod_plano,
        pc.cod_estrutural,
        pc.nom_conta
    ORDER BY
        pc.cod_estrutural,
        tabela.dt_lote';
--RAISE NOTICE '%',stSql;
EXECUTE stSql;

stSql := 'CREATE TEMPORARY TABLE tmp_aux_rel AS
    SELECT
         cod_estrutural
        ,count(cod_estrutural) as num_lancamentos
    FROM
        tmp_relatorio
    GROUP BY
         cod_estrutural';

EXECUTE stSql;

stSql := '
    SELECT
        rel.cod_lote,
        rel.sequencia,
        rel.cod_historico,
        rel.nom_historico,
        rel.complemento,
        rel.observacao,
        rel.exercicio,
        rel.cod_entidade,
        rel.tipo,
        rel.vl_lancamento,
        rel.tipo_valor,
        rel.dt_lote,
        rel.cod_plano,
        rel.cod_estrutural,
        rel.nom_conta,
        rel.contra_partida,
        rel.saldoAnterior
          ,cast(aux.num_lancamentos as integer) as num_lancamentos
    FROM
         tmp_relatorio as rel
        ,tmp_aux_rel   as aux
    WHERE
        rel.cod_estrutural = aux.cod_estrutural ';

    IF boMovimentacaoConta = 'N' THEN
        stSql := stSql || ' AND ( ( aux.num_lancamentos >= 1 AND rel.exercicio IS NOT NULL )
                                  OR
                                   ( aux.num_lancamentos >= 1 AND rel.exercicio IS NULL AND rel.saldoAnterior > 0.00 )
                                 )
        ';
    END IF;

    stSql := stSql || ' ORDER BY rel.cod_estrutural, rel.data';

FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN NEXT reRegistro;
END LOOP;

DROP INDEX uq_valor;
DROP INDEX uq_somatorio;

DROP TABLE tmp_razao;
DROP TABLE tmp_valor;
DROP TABLE tmp_somatorio;
DROP TABLE tmp_relatorio;
DROP TABLE tmp_aux_rel;

RETURN;

END;
$$ LANGUAGE 'plpgsql';
