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
/**
Arquivo de mapeamento para a função que busca os dados de variação patrimonial
    * Data de Criação   : 04/02/2009

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Lucas Andrades Mendes
    * @package URBEM
    * @subpackage

    $Id:$
*/

CREATE OR REPLACE FUNCTION tcemg.fn_ativo_perm(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    stDataInicial       ALIAS FOR $3;
    stDataFinal         ALIAS FOR $4;
    stSql               VARCHAR := '';
    reRegistro          RECORD;

BEGIN 

CREATE TEMPORARY TABLE tmp_arquivo (
          mes                  INTEGER
        , valorBensMov         NUMERIC(14,2)
        , valorBensImo         NUMERIC(14,2)
        , valorObrasInst       NUMERIC(14,2)
        , valorTitVal          NUMERIC(14,2)
        , valorDivAtiva        NUMERIC(14,2)
        , valorTransRecebidas  NUMERIC(14,2)
        , valorReversaoRPPS    NUMERIC(14,2)
        , codTipo              INTEGER

    );

stSql := '
INSERT INTO tmp_arquivo(mes,valorBensMov,valorBensImo,valorObrasInst,valorTitVal,valorDivAtiva)VALUES(12,

 (SELECT
            coalesce(sum(vl.vl_lancamento),0.00)
        FROM
             contabilidade.plano_conta      as pc
            ,contabilidade.plano_analitica  as pa
            ,contabilidade.conta_debito     as cd
            ,contabilidade.valor_lancamento as vl
            ,contabilidade.lancamento       as la
            ,contabilidade.lote             as lo
        WHERE   pc.cod_conta = pa.cod_conta
        AND     pc.exercicio = pa.exercicio
        AND     pc.indicador_superavit = ''permanente''
        AND     pa.cod_plano = cd.cod_plano
        AND     pa.exercicio = cd.exercicio
        AND     cd.cod_lote  = vl.cod_lote
        AND     cd.tipo      = vl.tipo
        AND     cd.sequencia = vl.sequencia
        AND     cd.exercicio = vl.exercicio
        AND     cd.tipo_valor= vl.tipo_valor
        AND     cd.cod_entidade= vl.cod_entidade
        AND     vl.cod_lote  = la.cod_lote
        AND     vl.tipo      = la.tipo
        AND     vl.sequencia = la.sequencia
        AND     vl.exercicio = la.exercicio
        AND     vl.tipo      = la.tipo
        AND     vl.cod_entidade= la.cod_entidade
        AND     la.cod_lote  = lo.cod_lote
        AND     la.exercicio = lo.exercicio
        AND     la.tipo      = lo.tipo
        AND     la.cod_entidade=lo.cod_entidade
        AND     lo.dt_lote BETWEEN TO_DATE(''' || stDataInicial || ''', ''dd/mm/yyyy'') AND TO_DATE(''' || stDataFinal || ''', ''dd/mm/yyyy'')
        AND     pc.exercicio  = '''||stExercicio||'''
        AND     cd.cod_entidade IN ( ' || stCodEntidade || ' )
        AND     cod_estrutural like  ''1.2.3.1%''),

 (SELECT
            coalesce(sum(vl.vl_lancamento),0.00)
        FROM
             contabilidade.plano_conta      as pc
            ,contabilidade.plano_analitica  as pa
            ,contabilidade.conta_debito     as cd
            ,contabilidade.valor_lancamento as vl
            ,contabilidade.lancamento       as la
            ,contabilidade.lote             as lo
        WHERE   pc.cod_conta = pa.cod_conta
        AND     pc.exercicio = pa.exercicio
        AND     pc.indicador_superavit = ''permanente''
        AND     pa.cod_plano = cd.cod_plano
        AND     pa.exercicio = cd.exercicio
        AND     cd.cod_lote  = vl.cod_lote
        AND     cd.tipo      = vl.tipo
        AND     cd.sequencia = vl.sequencia
        AND     cd.exercicio = vl.exercicio
        AND     cd.tipo_valor= vl.tipo_valor
        AND     cd.cod_entidade= vl.cod_entidade
        AND     vl.cod_lote  = la.cod_lote
        AND     vl.tipo      = la.tipo
        AND     vl.sequencia = la.sequencia
        AND     vl.exercicio = la.exercicio
        AND     vl.tipo      = la.tipo
        AND     vl.cod_entidade= la.cod_entidade
        AND     la.cod_lote  = lo.cod_lote
        AND     la.exercicio = lo.exercicio
        AND     la.tipo      = lo.tipo
        AND     la.cod_entidade=lo.cod_entidade
        AND     lo.dt_lote BETWEEN TO_DATE(''' || stDataInicial || ''', ''dd/mm/yyyy'') AND TO_DATE(''' || stDataFinal || ''', ''dd/mm/yyyy'')
        AND     pc.exercicio  = '''||stExercicio||'''
        AND     cd.cod_entidade IN ( ' || stCodEntidade || ' )
        AND     (cod_estrutural like  ''1.2.3.2%''  OR cod_estrutural like ''1.2.3.2.1.06.06%'')),


  (SELECT
            coalesce(sum(vl.vl_lancamento),0.00)
        FROM
             contabilidade.plano_conta      as pc
            ,contabilidade.plano_analitica  as pa
            ,contabilidade.conta_debito     as cd
            ,contabilidade.valor_lancamento as vl
            ,contabilidade.lancamento       as la
            ,contabilidade.lote             as lo
        WHERE   pc.cod_conta = pa.cod_conta
        AND     pc.exercicio = pa.exercicio
        AND     pc.indicador_superavit = ''permanente''
        AND     pa.cod_plano = cd.cod_plano
        AND     pa.exercicio = cd.exercicio
        AND     cd.cod_lote  = vl.cod_lote
        AND     cd.tipo      = vl.tipo
        AND     cd.sequencia = vl.sequencia
        AND     cd.exercicio = vl.exercicio
        AND     cd.tipo_valor= vl.tipo_valor
        AND     cd.cod_entidade= vl.cod_entidade
        AND     vl.cod_lote  = la.cod_lote
        AND     vl.tipo      = la.tipo
        AND     vl.sequencia = la.sequencia
        AND     vl.exercicio = la.exercicio
        AND     vl.tipo      = la.tipo
        AND     vl.cod_entidade= la.cod_entidade
        AND     la.cod_lote  = lo.cod_lote
        AND     la.exercicio = lo.exercicio
        AND     la.tipo      = lo.tipo
        AND     la.cod_entidade=lo.cod_entidade
        AND     lo.dt_lote BETWEEN TO_DATE(''' || stDataInicial || ''', ''dd/mm/yyyy'') AND TO_DATE(''' || stDataFinal || ''', ''dd/mm/yyyy'')
        AND     pc.exercicio  = '''||stExercicio||'''
        AND     cd.cod_entidade IN ( ' || stCodEntidade || ' )
        AND     cod_estrutural like ''1.2.3.2.1.06%'' 
        AND     cod_estrutural NOT like ''1.2.3.2.1.06.06%'' ),

(SELECT
            coalesce(sum(vl.vl_lancamento),0.00)
        FROM
             contabilidade.plano_conta      as pc
            ,contabilidade.plano_analitica  as pa
            ,contabilidade.conta_debito     as cd
            ,contabilidade.valor_lancamento as vl
            ,contabilidade.lancamento       as la
            ,contabilidade.lote             as lo
        WHERE   pc.cod_conta = pa.cod_conta
        AND     pc.exercicio = pa.exercicio
        AND     pc.indicador_superavit = ''permanente''
        AND     pa.cod_plano = cd.cod_plano
        AND     pa.exercicio = cd.exercicio
        AND     cd.cod_lote  = vl.cod_lote
        AND     cd.tipo      = vl.tipo
        AND     cd.sequencia = vl.sequencia
        AND     cd.exercicio = vl.exercicio
        AND     cd.tipo_valor= vl.tipo_valor
        AND     cd.cod_entidade= vl.cod_entidade
        AND     vl.cod_lote  = la.cod_lote
        AND     vl.tipo      = la.tipo
        AND     vl.sequencia = la.sequencia
        AND     vl.exercicio = la.exercicio
        AND     vl.tipo      = la.tipo
        AND     vl.cod_entidade= la.cod_entidade
        AND     la.cod_lote  = lo.cod_lote
        AND     la.exercicio = lo.exercicio
        AND     la.tipo      = lo.tipo
        AND     la.cod_entidade=lo.cod_entidade
        AND     lo.dt_lote BETWEEN TO_DATE(''' || stDataInicial || ''', ''dd/mm/yyyy'') AND TO_DATE(''' || stDataFinal || ''', ''dd/mm/yyyy'')
        AND     pc.exercicio  = '''||stExercicio||'''
        AND     cd.cod_entidade IN ( ' || stCodEntidade || ' )
        AND     cod_estrutural like  ''1.4.1%''),


(SELECT
            coalesce(sum(vl.vl_lancamento),0.00)
        FROM
             contabilidade.plano_conta      as pc
            ,contabilidade.plano_analitica  as pa
            ,contabilidade.conta_debito     as cd
            ,contabilidade.valor_lancamento as vl
            ,contabilidade.lancamento       as la
            ,contabilidade.lote             as lo
        WHERE   pc.cod_conta = pa.cod_conta
        AND     pc.exercicio = pa.exercicio
        AND     pc.indicador_superavit = ''permanente''
        AND     pa.cod_plano = cd.cod_plano
        AND     pa.exercicio = cd.exercicio
        AND     cd.cod_lote  = vl.cod_lote
        AND     cd.tipo      = vl.tipo
        AND     cd.sequencia = vl.sequencia
        AND     cd.exercicio = vl.exercicio
        AND     cd.tipo_valor= vl.tipo_valor
        AND     cd.cod_entidade= vl.cod_entidade
        AND     vl.cod_lote  = la.cod_lote
        AND     vl.tipo      = la.tipo
        AND     vl.sequencia = la.sequencia
        AND     vl.exercicio = la.exercicio
        AND     vl.tipo      = la.tipo
        AND     vl.cod_entidade= la.cod_entidade
        AND     la.cod_lote  = lo.cod_lote
        AND     la.exercicio = lo.exercicio
        AND     la.tipo      = lo.tipo
        AND     la.cod_entidade=lo.cod_entidade
        AND     lo.dt_lote BETWEEN TO_DATE(''' || stDataInicial || ''', ''dd/mm/yyyy'') AND TO_DATE(''' || stDataFinal || ''', ''dd/mm/yyyy'')
        AND     pc.exercicio  = '''||stExercicio||'''
        AND     cd.cod_entidade IN ( ' || stCodEntidade || ' )
        AND     (cod_estrutural like  ''1.1.2.3%''  OR cod_estrutural like ''1.1.3.2.4%'' ))

)';
EXECUTE stSql;

stSql := ' SELECT mes,COALESCE(valorBensMov, 0.00),COALESCE(valorBensImo, 0.00),COALESCE(valorObrasInst),COALESCE(valorTitVal),COALESCE(valorDivAtiva, 0.00),COALESCE(valorTransRecebidas, 0.00), COALESCE(valorReversaoRPPS, 0.00), 01 FROM tmp_arquivo; ';

FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_arquivo;

    RETURN;


END;
$$ LANGUAGE 'plpgsql';



