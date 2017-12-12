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
* $Revision: 59612 $
* $Name$
* $Author: gelson $
* $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*
* Casos de uso: uc-02.00.00
* Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.8  2007/09/25 14:10:43  cako
Ticket#10193#

Revision 1.7  2007/07/30 18:28:33  tonismar
alteração de uc-00.00.00 para uc-02.00.00

Revision 1.6  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcers.fn_exportacao_Empenho_Restos_Pagar(varchar,varchar) RETURNS SETOF record AS $$
DECLARE
    stExercicio     ALIAS FOR $1    ;
    stCodEntidade   ALIAS FOR $2    ;
    stSql           VARCHAR := '' ;
    raRegistro      RECORD          ;
    arDados         VARCHAR[] := array[0];
BEGIN

    stSql := 'CREATE TEMPORARY TABLE tmp_empenho AS
                SELECT  emp.exercicio, 
                        emp.cod_entidade, 
                        emp.cod_empenho, 
                        pre.cod_pre_empenho,
                        emp.dt_empenho,
                        pre.cgm_beneficiario,
                        pre.descricao,
                        pre.oid     as pre_oid
                FROM     empenho.empenho        as emp
                        ,empenho.pre_empenho    as pre
                WHERE   emp.exercicio        <   ''' || stExercicio || '''
                    AND emp.cod_entidade     IN ('||stCodEntidade||')
                    -- Liga a pre empenho
                    AND pre.exercicio       =   emp.exercicio
                    AND pre.cod_pre_empenho =   emp.cod_pre_empenho
                ';
    EXECUTE stSql;
    CREATE UNIQUE INDEX unq_tmp_empenho     ON tmp_empenho  (exercicio, cod_entidade, cod_empenho);
    CREATE UNIQUE INDEX unq_tmp_pre_empenho ON tmp_empenho  (exercicio, cod_pre_empenho);

    stSql := 'CREATE TEMPORARY TABLE tmp_item_empenho AS
                SELECT  temp.exercicio, 
                        temp.cod_pre_empenho, 
                        sum(ipre.vl_total) as vl_total,
                        max(ipre.nom_unidade) as nom_unidade,
                        max(ipre.nom_item) as nom_item,
                        max(ipre.quantidade) as quantidade,
                        max(ipre.complemento) as complemento
                        
                FROM     tmp_empenho   as temp
                        ,empenho.item_pre_empenho  as ipre
                WHERE   temp.exercicio       = ipre.exercicio
                    AND temp.cod_pre_empenho = ipre.cod_pre_empenho
                GROUP BY temp.exercicio, temp.cod_pre_empenho 
                ';
    EXECUTE stSql;
    CREATE UNIQUE INDEX unq_tmp_item_empenho   ON tmp_item_empenho  (exercicio, cod_pre_empenho);


    stSql := 'CREATE TEMPORARY TABLE tmp_item_empenho_anulado AS
                SELECT  temp.exercicio, 
                        temp.cod_pre_empenho, 
                        coalesce(Sum(eai.vl_anulado),0.00) as vl_anulado
                FROM     tmp_empenho   as temp
                        ,empenho.empenho_anulado_item    as eai
                WHERE   temp.exercicio       = eai.exercicio
                    AND temp.cod_pre_empenho = eai.cod_pre_empenho
                    AND eai.timestamp <= to_date(''31/12/''||(to_number('||stExercicio||'::varchar,''9999'')-1)::varchar,''dd/mm/yyyy'')
                GROUP BY temp.exercicio, temp.cod_pre_empenho 

                ';
    EXECUTE stSql;
    CREATE UNIQUE INDEX unq_tmp_item_empenho_anulado   ON tmp_item_empenho_anulado  (exercicio, cod_pre_empenho);

    stSql := 'CREATE TEMPORARY TABLE tmp_nota_paga AS
                SELECT  temp.exercicio, 
                        temp.cod_entidade,
                        temp.cod_empenho,
                        coalesce(Sum(enlp.vl_pago),0.00) as vl_pago
                FROM     tmp_empenho   as temp
                        ,empenho.nota_liquidacao         as enl
                        ,empenho.nota_liquidacao_paga    as enlp
                  -- Nota Liquidacao
                WHERE   enl.exercicio_empenho   =   temp.exercicio
                    AND enl.cod_empenho         =   temp.cod_empenho
                    AND enl.cod_entidade        =   temp.cod_entidade
                  -- Nota Liquidacao Paga
                    AND enlp.cod_entidade       =   enl.cod_entidade
                    AND enlp.cod_nota           =   enl.cod_nota
                    AND enlp.exercicio          =   enl.exercicio
                    AND enlp.timestamp <= to_date(''31/12/''||(to_number('||stExercicio||'::varchar,''9999'')-1)::varchar,''dd/mm/yyyy'')
                    GROUP BY temp.exercicio, temp.cod_entidade, temp.cod_empenho
                ';
    EXECUTE stSql;
    CREATE UNIQUE INDEX unq_tmp_nota_paga   ON tmp_nota_paga  (exercicio, cod_entidade, cod_empenho);

    stSql := 'CREATE TEMPORARY TABLE tmp_nota_paga_anulada AS
                SELECT  temp.exercicio, 
                        temp.cod_entidade,
                        temp.cod_empenho,
                        coalesce(Sum(enlpa.vl_anulado),0.00) as vl_anulado
                FROM     tmp_empenho   as temp
                        ,empenho.nota_liquidacao         as enl
                        ,empenho.nota_liquidacao_paga    as enlp
                        ,empenho.nota_liquidacao_paga_anulada    as enlpa
                  -- Nota Liquidacao
                WHERE   enl.exercicio_empenho   =   temp.exercicio
                    AND enl.cod_empenho         =   temp.cod_empenho
                    AND enl.cod_entidade        =   temp.cod_entidade
                  -- Nota Liquidacao Paga
                    AND enlp.cod_entidade       =   enl.cod_entidade
                    AND enlp.cod_nota           =   enl.cod_nota
                    AND enlp.exercicio          =   enl.exercicio
                    AND enlp.timestamp::date <= to_date(''31/12/''||(to_number('||stExercicio||'::varchar,''9999'')-1)::varchar,''dd/mm/yyyy'')
                 -- Nota Liquidacao Paga Anulada
                    AND enlpa.exercicio         =   enlp.exercicio
                    AND enlpa.cod_nota          =   enlp.cod_nota
                    AND enlpa.cod_entidade      =   enlp.cod_entidade
                    AND enlpa.timestamp       =   enlp.timestamp
                    AND enlpa.timestamp_anulada <= to_date(''31/12/''||(to_number('||stExercicio||'::varchar,''9999'')-1)::varchar, ''dd/mm/yyyy'')
                    GROUP BY temp.exercicio, temp.cod_entidade, temp.cod_empenho
                ';
    EXECUTE stSql;
    CREATE UNIQUE INDEX unq_tmp_nota_paga_anulada   ON tmp_nota_paga_anulada  (exercicio, cod_entidade, cod_empenho);



stSql = '
    -- Select para soma do itens  --> data vem de empenho.empenho.dt_empenho
SELECT
        -- Nome Tmp para campos que vem da funcao Dados Empenho
        0           as num_orgao        ,
        0           as num_unidade      ,
        0           as cod_funcao       ,
        0           as cod_subfuncao    ,
        0           as cod_programa     ,
        0           as num_pao          ,
        0           as cod_recurso      ,
        cast('''' as varchar)  as cod_estrutural   ,
        temp.cod_empenho,
        temp.dt_empenho ,
        
        (
            ( coalesce(titem.vl_total,0.00)
            - coalesce(Sum(tpago.vl_pago),0.00)
            )
        -
            ( coalesce(( SELECT vl_anulado FROM tmp_item_empenho_anulado tiea WHERE temp.exercicio=tiea.exercicio AND temp.cod_pre_empenho=tiea.cod_pre_empenho),0.00)
            - coalesce((SELECT vl_anulado FROM tmp_nota_paga_anulada as tanu WHERE temp.exercicio=tanu.exercicio AND temp.cod_entidade=tanu.cod_entidade AND temp.cod_empenho=tanu.cod_empenho),0.00)
            )
        )::numeric(14,2) as vl_empenhado,

        cast(''+'' as varchar(1)) as sinal,
        temp.cgm_beneficiario,
        CAST( CASE WHEN trim(temp.descricao) = '''' THEN
            publico.concatenar_hifen(titem.quantidade::varchar) || '' '' || publico.concatenar_hifen(titem.nom_unidade) || '' '' || publico.concatenar_hifen(titem.nom_item) || '' '' || publico.concatenar_hifen(titem.complemento)
        ELSE temp.descricao END as varchar ) as historico,
        temp.cod_pre_empenho,
        temp.exercicio,
        temp.cod_entidade,
        cast(''3'' as integer) as ordem ,
        temp.pre_oid

     FROM   tmp_item_empenho    as titem
           ,tmp_empenho         as temp
        LEFT JOIN
            tmp_nota_paga       as tpago
        ON (    temp.exercicio        = tpago.exercicio
            AND temp.cod_entidade     = tpago.cod_entidade
            AND temp.cod_empenho      = tpago.cod_empenho
        )
    WHERE   temp.exercicio        = titem.exercicio
        AND temp.cod_pre_empenho  = titem.cod_pre_empenho
        GROUP BY
            num_orgao      ,
            num_unidade    ,
            cod_funcao     ,
            cod_subfuncao  ,
            cod_programa   ,
            num_pao        ,
            cod_recurso    ,
            cod_estrutural   ,
            temp.cod_empenho,
            temp.dt_empenho,
            sinal,
            temp.cgm_beneficiario,
            temp.cod_pre_empenho,
            temp.exercicio,
            titem.vl_total,
            temp.cod_entidade,
            ordem,
            temp.descricao,
            temp.pre_oid
';

        -- Encerra conteudo do sql

    FOR raRegistro IN EXECUTE stSql
    LOOP
        IF (raRegistro.vl_empenhado > 0 ) THEN
            arDados := tcers.fn_exportacao_dados_empenho(raRegistro.cod_empenho,raRegistro.exercicio,raRegistro.cod_entidade);
            raRegistro.num_orgao        := to_number(arDados[1], '9999999999');
            raRegistro.num_unidade      := to_number(arDados[2], '9999999999');
            raRegistro.cod_funcao       := to_number(arDados[3], '9999999999');
            raRegistro.cod_subfuncao    := to_number(arDados[4], '9999999999');
            raRegistro.cod_programa     := to_number(arDados[5], '9999999999');
            raRegistro.num_pao          := to_number(arDados[6], '9999999999');
            raRegistro.cod_estrutural   := arDados[7];
            raRegistro.cod_recurso      := to_number(arDados[8], '9999999999');
            RETURN NEXT raRegistro;
        END IF;
    END LOOP;

    DROP INDEX unq_tmp_empenho;
    DROP INDEX unq_tmp_pre_empenho;
    DROP INDEX unq_tmp_item_empenho;
    DROP INDEX unq_tmp_item_empenho_anulado;
    DROP INDEX unq_tmp_nota_paga;
    DROP INDEX unq_tmp_nota_paga_anulada;

    DROP TABLE tmp_empenho;
    DROP TABLE tmp_item_empenho;
    DROP TABLE tmp_item_empenho_anulado;
    DROP TABLE tmp_nota_paga;
    DROP TABLE tmp_nota_paga_anulada;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';
