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
* Casos de uso: uc-06.01.23
*/

/*
$Log$
Revision 1.2  2007/10/15 14:11:43  tonismar
Relatório Anexo4

Revision 1.1  2006/09/26 10:15:54  cleisson
Inclusão

Revision 1.4  2006/08/14 18:54:48  cako
Ajustes para trazer somente valores diferentes de 0.00

Revision 1.3  2006/08/14 18:37:45  cako
Ajustes.

Revision 1.2  2006/08/09 19:47:39  cako
Melhoramentos.
Adição do campo ''linha'' para auxiliar no desenvolvimento do layout no agata

Revision 1.1  2006/08/09 17:15:17  cako
Versão inicial


*/


CREATE OR REPLACE FUNCTION stn.fn_rgf_anexo4(varchar,integer,varchar) RETURNS SETOF RECORD AS '
DECLARE

    stExercicio         ALIAS FOR $1;
    inQuadrimestre      ALIAS FOR $2;
    stCodEntidades      ALIAS FOR $3;

    stDtInicial         VARCHAR := '''';
    stDtFinal           VARCHAR := '''';
    
    nuRCL               NUMERIC;
    nu_I                NUMERIC;
    nu_II               NUMERIC;
    nuPercent           NUMERIC;
    nuPercent2          NUMERIC;
    nuPercent3          NUMERIC;
    nuPercent4          NUMERIC;
    flValorRCL          NUMERIC(14,2) := 0.00;
    crCursor            REFCURSOR;

    arEntidades         VARCHAR[];
    stEntidadesCamara   VARCHAR := '''';
    inEntidadeCamara    INTEGER;
    inCount             INTEGER;


    stSql               VARCHAR   := '''';
    stSqlAux            VARCHAR   := '''';
    reRegistro          RECORD;

BEGIN

    stDtInicial := ''01/01/''||stExercicio||'''';

    IF inQuadrimestre = 1 THEN
        stDtFinal := ''30/04/''||stExercicio||'''';
    END IF;
    
    IF inQuadrimestre = 2 THEN
        stDtFinal := ''31/08/''||stExercicio||'''';
    END IF;

    IF inQuadrimestre = 3 THEN
        stDtFinal := ''31/12/''||stExercicio||'''';
    END IF; 

    IF inQuadrimestre = 4 THEN
        stDtFinal := ''30/06/''||stExercicio||'''';
    END IF;
    
    IF inQuadrimestre = 5 THEN
        stDtFinal := ''31/12/''||stExercicio||'''';
    END IF;


stSql := ''
-- ## Plano Conta <=> Plano Analítica

    CREATE TEMPORARY table tmp_plano_conta as
    SELECT  pc.cod_estrutural
           ,pc.cod_conta
           ,pc.exercicio
           ,pc.nom_conta
           ,pa.cod_plano
    FROM contabilidade.plano_conta as pc
         join contabilidade.plano_analitica as pa
              ON (
                       pc.cod_conta = pa.cod_conta
                   and pc.exercicio = pa.exercicio
                 )
    WHERE
         pc.exercicio = ''''''|| stExercicio ||''''''  and
         (pc.cod_estrutural like ''''4.2.1%'''' OR pc.cod_estrutural like ''''2.1.2.2.2.02.07%'''');

    CREATE INDEX unq_tmp_plano_conta   on tmp_plano_conta ( cod_conta, exercicio );
    CREATE INDEX unq_tmp_plano_conta_1 on tmp_plano_conta ( cod_plano, exercicio ); 
    '';

EXECUTE stSql;

stSql := ''
-- ## Lote

    CREATE TEMPORARY table tmp_lote as
    SELECT
         cod_lote
        ,exercicio
        ,tipo
        ,cod_entidade
    FROM 
         contabilidade.lote
    WHERE 
         dt_lote BETWEEN TO_DATE( ''''''|| stDtInicial ||'''''',''''dd/mm/yyyy'''' ) AND TO_DATE( ''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''') AND 
         exercicio = ''''''|| stExercicio ||'''''';

    CREATE INDEX unq_tmp_lote on tmp_lote ( cod_lote, exercicio, tipo, cod_entidade );
    '';

EXECUTE stSql;

stSql :=''
-- ## Lançamento <=> Lote
    CREATE TEMPORARY table tmp_lancamento as
    SELECT
          l.sequencia
         ,l.cod_lote
         ,l.tipo
         ,l.exercicio
         ,l.cod_entidade
    FROM
         contabilidade.lancamento as l
         join tmp_lote as tlo
              on (     tlo.cod_lote     = l.cod_lote
                   and tlo.exercicio    = l.exercicio
                   and tlo.cod_entidade = l.cod_entidade
                   and tlo.tipo         = l.tipo
                 )
    WHERE
    '';
    IF (length(stCodEntidades) = 1) THEN
        stSql := stSql || '' l.cod_entidade = '' || stCodEntidades ||'' AND '';
    ELSE
        stSql := stSql || '' l.cod_entidade IN (''|| stCodEntidades ||'') AND '';
    END IF;

stSql := stSql ||''
        l.exercicio = ''''''|| stExercicio ||'''''';

    CREATE INDEX unq_tmp_lancamento   ON tmp_lancamento (sequencia, cod_lote, tipo, exercicio, cod_entidade);
    '';
EXECUTE stSql;

stSql :=''
-- ## Conta Crédito
       CREATE TEMPORARY TABLE tmp_credito AS
       SELECT
           pc.cod_estrutural      as cod_estrutural,
           pc.nom_conta           as nom_conta,
           l.exercicio            as exercicio,
           vl.vl_lancamento       as valor
       FROM
           tmp_lancamento as l,
           contabilidade.valor_lancamento as vl,
           contabilidade.conta_credito as cc,
           tmp_plano_conta as pc
       WHERE
               l.exercicio    = vl.exercicio
           AND l.cod_lote     = vl.cod_lote
           AND l.tipo         = vl.tipo
           AND l.sequencia    = vl.sequencia
           AND l.cod_entidade = vl.cod_entidade

           AND vl.exercicio    = cc.exercicio
           AND vl.cod_lote     = cc.cod_lote
           AND vl.tipo         = cc.tipo
           AND vl.tipo_valor   = cc.tipo_valor
           AND vl.sequencia    = cc.sequencia
           AND vl.cod_entidade = cc.cod_entidade

           AND cc.cod_plano = pc.cod_plano
           AND cc.exercicio = pc.exercicio

       ORDER BY pc.cod_estrutural;

    CREATE INDEX unq_tmp_credito   ON tmp_credito (exercicio, cod_estrutural, nom_conta);
    '';

EXECUTE stSql;

stSql :=''
-- ## Conta Débito
       CREATE TEMPORARY TABLE tmp_debito AS
       SELECT
           pc.cod_estrutural      as cod_estrutural,
           pc.nom_conta           as nom_conta,
           l.exercicio            as exercicio,
           vl.vl_lancamento       as valor
       FROM
           tmp_lancamento as l,
           contabilidade.valor_lancamento as vl,
           contabilidade.conta_debito as cd,
           tmp_plano_conta as pc

       WHERE
               l.exercicio    = vl.exercicio
           AND l.cod_lote     = vl.cod_lote
           AND l.tipo         = vl.tipo
           AND l.sequencia    = vl.sequencia
           AND l.cod_entidade = vl.cod_entidade

           AND vl.exercicio    = cd.exercicio
           AND vl.cod_lote     = cd.cod_lote
           AND vl.tipo         = cd.tipo
           AND vl.tipo_valor   = cd.tipo_valor
           AND vl.sequencia    = cd.sequencia
           AND vl.cod_entidade = cd.cod_entidade

           AND cd.cod_plano = pc.cod_plano
           AND cd.exercicio = pc.exercicio

      ORDER BY pc.cod_estrutural;
    
      CREATE INDEX unq_tmp_debito   ON tmp_debito  (exercicio, cod_estrutural, nom_conta);
    '';

EXECUTE stSql;

stSql :=''
-- # Valores Principais
    CREATE TEMPORARY TABLE tmp_valores AS
     SELECT cod_estrutural,nom_conta, (sum(valor)*-1) as valor
        from (
        SELECT exercicio,cod_estrutural,nom_conta,valor from tmp_credito
        UNION
        SELECT exercicio,cod_estrutural,nom_conta,valor from tmp_debito
        ) as tabela
     WHERE valor <> 0.00
    GROUP BY cod_estrutural, nom_conta
    ORDER BY cod_estrutural;

    CREATE INDEX unq_tmp_valores ON tmp_valores (cod_estrutural, nom_conta, valor);
    '';

EXECUTE stSql;

stSql := ''
-- # Tabela para os dados Definitivos do Relatório
    CREATE TEMPORARY TABLE tmp_relatorio (
        nivel INTEGER,
        item  VARCHAR,
        valor NUMERIC(14,2),
        linha CHAR
    )
    '';

EXECUTE stSql;

stSql :=''
-- # Operações de Crédito (I)
    SELECT coalesce(sum(valor),0.00) as valor
    FROM tmp_valores 
    WHERE cod_estrutural like ''''4.2.1%'''';
    '';
    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nu_I;
    CLOSE crCursor;

stSql := ''
    INSERT INTO tmp_relatorio values(1,''''OPERAÇÕES DE CRÉDITO (I)'''',''||nu_I||'',''''N'''');
-- #    Externas
    INSERT INTO tmp_relatorio values(2,''''Externas'''',(SELECT coalesce(sum(valor),0.00) 
                                                         FROM tmp_valores 
                                                         WHERE cod_estrutural like ''''4.2.1.2%''''),''''N'''');    

-- #       Identificação das Operações de Crédito Externas
    INSERT INTO tmp_relatorio (nivel,item,valor,linha)
        SELECT 
                3         as nivel,
                nom_conta as item,
                coalesce(valor,0.00),
                ''''N'''' as linha
        FROM tmp_valores 
        WHERE cod_estrutural like ''''4.2.1.2%''''; 

-- #    Internas
    INSERT INTO tmp_relatorio values(2,''''Internas'''',(SELECT coalesce(sum(valor),0.00) 
                                                         FROM tmp_valores 
                                                         WHERE cod_estrutural like ''''4.2.1.1%''''),''''N'''');

-- #       Identificação das Operações de Crédito Internas
    INSERT INTO tmp_relatorio (nivel,item,valor,linha)
        SELECT
            3         as nivel,
            nom_conta as item,
            coalesce(valor,0.00),
            ''''N'''' as linha
        FROM tmp_valores
        WHERE cod_estrutural like ''''4.2.1.1%'''';

    '';
EXECUTE stSql;

stSql :=''
-- # Por Antecipação da Receita (II)
    SELECT coalesce(sum(valor),0.00) as valor
    FROM tmp_valores 
    WHERE cod_estrutural like ''''2.1.2.2.2.02.07%'''';
    '';

    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nu_II;
    CLOSE crCursor;


stSql := ''
    INSERT INTO tmp_relatorio values(1,''''POR ANTECIPAÇÃO DA RECEITA (II)'''',''||nu_II||'',''''N'''');

-- # Total das Operações de Crédito (I+II)
    INSERT INTO tmp_relatorio values(1,''''TOTAL DAS OPERAÇÕES DE CRÉDITO (I+II)'''',''||nu_I+nu_II||'',''''S'''');
    '';
EXECUTE stSql;

stSql :=''
-- ## RCL do Bruce | Anexo III - RREO
    SELECT
        sum(cast( ( total_mes_1 + total_mes_2 + total_mes_3 + total_mes_4  + total_mes_5  + total_mes_6 
                   + total_mes_7 + total_mes_8 + total_mes_9 + total_mes_10 + total_mes_11 + total_mes_12 ) as numeric(14,2))) as valor
    FROM stn.pl_total_subcontas (''''''||stDtFinal||'''''') as retorno (
                                                                  ordem      integer
                                                                 ,cod_conta      varchar
                                                                 ,nom_conta      varchar
                                                                 ,cod_estrutural varchar
                                                                 ,mes_1      numeric
                                                                 ,mes_2      numeric
                                                                 ,mes_3      numeric
                                                                 ,mes_4      numeric
                                                                 ,mes_5      numeric
                                                                 ,mes_6      numeric
                                                                 ,mes_7      numeric
                                                                 ,mes_8      numeric
                                                                 ,mes_9      numeric
                                                                 ,mes_10     numeric
                                                                 ,mes_11     numeric
                                                                 ,mes_12     numeric
                                                                 ,total_mes_1  numeric
                                                                 ,total_mes_2  numeric
                                                                 ,total_mes_3  numeric
                                                                 ,total_mes_4  numeric
                                                                 ,total_mes_5  numeric
                                                                 ,total_mes_6  numeric
                                                                 ,total_mes_7  numeric
                                                                 ,total_mes_8  numeric
                                                                 ,total_mes_9  numeric
                                                                 ,total_mes_10 numeric
                                                                 ,total_mes_11 numeric
                                                                 ,total_mes_12 numeric)
    WHERE ordem = 1;
    '';

    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuRCL;
    CLOSE crCursor;

    --------------------------------------------------------------------------
    -- Verifica se a entidade camara veio junto com outras entidades e separa --
    --------------------------------------------------------------------------
    SELECT STRING_TO_ARRAY(stCodEntidades,'','')
      INTO arEntidades;

    SELECT valor
      INTO inEntidadeCamara
      FROM administracao.configuracao
     WHERE cod_modulo = 8
       AND parametro = ''cod_entidade_camara''
       AND exercicio = stExercicio;

    IF(ARRAY_UPPER(arEntidades,1) > 1) THEN
        FOR inCount IN 1..(ARRAY_UPPER(arEntidades,1))
        LOOP
            IF(arEntidades[inCount] <> inEntidadeCamara::varchar) THEN
                stEntidadesCamara := stEntidadesCamara || arEntidades[inCount] || '','';
            END IF;
        END LOOP;
        stEntidadesCamara := SUBSTR(stEntidadesCamara,1,LENGTH(stEntidadesCamara) -1);
    ELSE
        stEntidadesCamara := arEntidades[1];
    END IF;

    --
    -- Acrescenta o valor da rcl vinculada ao periodo
    --
    SELECT stn.fn_calcula_rcl_vinculada(stExercicio,stDtFinal,stEntidadesCamara)
      INTO flValorRCL;

    nuRCL := nuRCL + flValorRCL;

    stSql := ''INSERT INTO tmp_relatorio values(1,''''RECEITA CORRENTE LÍQUIDA - RCL'''',''||nuRCL||'',''''S'''')'';
    EXECUTE stSql;

    nuPercent := round((nu_I*100)/nuRCL,2);
    nuPercent2:= round((nu_II*100)/nuRCL,2);

    nuPercent3:= round((nuRCL*16)/100,2);
    nuPercent4:= round((nuRCL*7)/100,2);


stSql :=''
-- ## % das Operações de Crédito Internas e Externas sobre a RCL

    INSERT INTO tmp_relatorio values(1,''''% das OPERAÇÕES DE CRÉDITO INTERNAS E EXTERNAS sobre a RCL'''',''||nuPercent||'',''''S'''');

    INSERT INTO tmp_relatorio values(1,''''% das OPERAÇÕES DE CRÉDITO POR ANTECIPAÇÃO DA RECEITA sobre a RCL'''',''||nuPercent2||'',''''S'''');
    
    INSERT INTO tmp_relatorio values(1,''''LIMITE DEFINIDO POR RESOLUÇÃO DO SENADO FEDERAL PARA AS OPERAÇÕES DE CRÉDITO INTERNAS E EXTERNAS 16%'''',''||nuPercent3||'',''''S'''');
 
    INSERT INTO tmp_relatorio values(1,''''LIMITE DEFINIDO POR RESOLUÇÃO DO SENADO FEDERAL PARA AS OPERAÇÕES DE CRÉDITO POR ANTECIPAÇÃO DA RECEITA 7%'''',''||nuPercent4||'',''''S'''');
   '';     

EXECUTE stSql; 

stSql := ''SELECT nivel, item, valor,linha from tmp_relatorio;'';

FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN next reRegistro;
END LOOP; 

drop index unq_tmp_credito;
drop index unq_tmp_debito; 
drop index unq_tmp_plano_conta;
drop index unq_tmp_plano_conta_1;
drop index unq_tmp_lote;
drop index unq_tmp_lancamento;
drop index unq_tmp_valores;

drop table tmp_credito;
drop table tmp_debito;
drop table tmp_plano_conta;
drop table tmp_lote;
drop table tmp_lancamento;
drop table tmp_valores;
drop table tmp_relatorio;

RETURN;
END;

'language 'plpgsql';

