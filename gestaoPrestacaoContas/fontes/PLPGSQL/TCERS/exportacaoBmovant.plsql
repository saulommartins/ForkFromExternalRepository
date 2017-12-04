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
* Casos de uso: uc-02.08.07
*/

CREATE OR REPLACE FUNCTION tcers.fn_exportacao_bmovant(VARCHAR, VARCHAR) RETURNS SETOF RECORD AS '

DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidades      ALIAS FOR $2;

    stSql                   VARCHAR := '''';
    reRegistro              RECORD;
    stMascPlanoConta        VARCHAR := '''';
    stEstruturalReduzido    VARCHAR := '''';
    stReduzidoTemp          VARCHAR := '''';
    stCodEstrutural         VARCHAR := '''';
    inNivel                 INTEGER;
    inCount                 INTEGER;
    nuMovDeb                NUMERIC := 0.00;

BEGIN

stSql := ''
CREATE TEMPORARY TABLE tmp_debito_arquivo AS(
  SELECT
      cod_estrutural,
      tipo,
      coalesce(sum(bimestre_1),0.00) as bimestre_1,
      coalesce(sum(bimestre_2),0.00) as bimestre_2,
      coalesce(sum(bimestre_3),0.00) as bimestre_3,
      coalesce(sum(bimestre_4),0.00) as bimestre_4,
      coalesce(sum(bimestre_5),0.00) as bimestre_5,
      coalesce(sum(bimestre_6),0.00) as bimestre_6 
  FROM(        
    SELECT
        cod_estrutural,
        tipo,
        CASE WHEN EXTRACT(MONTH FROM dt_lote) BETWEEN 1 AND 2
             THEN coalesce(sum(valor),0.00)
        END as bimestre_1,

        CASE WHEN EXTRACT(MONTH FROM dt_lote) BETWEEN 3 AND 4
             THEN coalesce(sum(valor),0.00)
        END as bimestre_2,

        CASE WHEN EXTRACT(MONTH FROM dt_lote) BETWEEN 5 AND 6
             THEN coalesce(sum(valor),0.00)
        END as bimestre_3,

        CASE WHEN EXTRACT(MONTH FROM dt_lote) BETWEEN 7 AND 8
             THEN coalesce(sum(valor),0.00)
        END as bimestre_4,

        CASE WHEN EXTRACT(MONTH FROM dt_lote) BETWEEN 9 AND 10
             THEN coalesce(sum(valor),0.00)
        END as bimestre_5,

        CASE WHEN EXTRACT(MONTH FROM dt_lote) BETWEEN 11 AND 12
             THEN coalesce(sum(valor),0.00)
        END as bimestre_6
    FROM(
        SELECT
            pc.cod_estrutural
            ,coalesce(sum(vl.vl_lancamento),0.00) as valor
            ,pc.exercicio 
            ,lo.dt_lote                
            ,vl.tipo
        FROM
            contabilidade.plano_conta       as pc,
            contabilidade.plano_analitica   as pa,
            contabilidade.conta_debito      as cb,
            contabilidade.valor_lancamento  as vl,
            contabilidade.lancamento        as la,
            contabilidade.lote              as lo
        WHERE
            pc.exercicio    = pa.exercicio    AND
            pc.cod_conta    = pa.cod_conta    AND

            pa.exercicio    = cb.exercicio    AND
            pa.cod_plano    = cb.cod_plano    AND

            cb.exercicio    = vl.exercicio    AND
            cb.cod_lote     = vl.cod_lote     AND
            cb.tipo         = vl.tipo         AND
            cb.sequencia    = vl.sequencia    AND
            cb.tipo_valor   = vl.tipo_valor   AND
            cb.cod_entidade = vl.cod_entidade AND

            vl.exercicio    = la.exercicio    AND
            vl.cod_lote     = la.cod_lote     AND
            vl.tipo         = la.tipo         AND
            vl.sequencia    = la.sequencia    AND
            vl.cod_entidade = la.cod_entidade AND
            
            vl.tipo_valor   = ''''D''''       AND
            vl.tipo         <>''''I''''       AND
            
            lo.exercicio    = la.exercicio    AND
            lo.cod_lote     = la.cod_lote     AND
            lo.tipo         = la.tipo         AND
            lo.cod_entidade = la.cod_entidade AND
            lo.dt_lote::VARCHAR NOT ILIKE ''''%bc%'''' AND
            lo.cod_entidade IN ('' || stCodEntidades || '') AND
            lo.exercicio=''|| quote_literal(stExercicio) ||''
        GROUP BY
            pc.cod_estrutural, pc.exercicio, lo.dt_lote, vl.tipo
        ) AS tbl
    GROUP BY cod_estrutural, dt_lote, tipo
    ) as tbl
  GROUP BY cod_estrutural, tipo
  ORDER BY cod_estrutural
)
'';
EXECUTE stSql;

stSql := ''
CREATE TEMPORARY TABLE tmp_credito_arquivo AS(
  SELECT
      cod_estrutural,
      tipo,
      abs(coalesce(sum(bimestre_1),0.00)) as bimestre_1,
      abs(coalesce(sum(bimestre_2),0.00)) as bimestre_2,
      abs(coalesce(sum(bimestre_3),0.00)) as bimestre_3,
      abs(coalesce(sum(bimestre_4),0.00)) as bimestre_4,
      abs(coalesce(sum(bimestre_5),0.00)) as bimestre_5,
      abs(coalesce(sum(bimestre_6),0.00)) as bimestre_6 
  FROM(        
    SELECT
        cod_estrutural,
        tipo,
        CASE WHEN EXTRACT(MONTH FROM dt_lote) BETWEEN 1 AND 2
             THEN coalesce(sum(valor),0.00)
        END as bimestre_1,

        CASE WHEN EXTRACT(MONTH FROM dt_lote) BETWEEN 3 AND 4
             THEN coalesce(sum(valor),0.00)
        END as bimestre_2,

        CASE WHEN EXTRACT(MONTH FROM dt_lote) BETWEEN 5 AND 6
             THEN coalesce(sum(valor),0.00)
        END as bimestre_3,

        CASE WHEN EXTRACT(MONTH FROM dt_lote) BETWEEN 7 AND 8
             THEN coalesce(sum(valor),0.00)
        END as bimestre_4,

        CASE WHEN EXTRACT(MONTH FROM dt_lote) BETWEEN 9 AND 10
             THEN coalesce(sum(valor),0.00)
        END as bimestre_5,

        CASE WHEN EXTRACT(MONTH FROM dt_lote) BETWEEN 11 AND 12
             THEN coalesce(sum(valor),0.00)
        END as bimestre_6

    FROM(
        SELECT
            pc.cod_estrutural,
            coalesce(sum(vl.vl_lancamento),0.00) as valor
            ,pc.exercicio 
            ,lo.dt_lote
            ,vl.tipo
        FROM
            contabilidade.plano_conta       as pc,
            contabilidade.plano_analitica   as pa,
            contabilidade.conta_credito     as cc,
            contabilidade.valor_lancamento  as vl,
            contabilidade.lancamento        as la,
            contabilidade.lote              as lo
        WHERE
            pc.exercicio    = pa.exercicio    AND
            pc.cod_conta    = pa.cod_conta    AND
            
            pa.exercicio    = cc.exercicio    AND
            pa.cod_plano    = cc.cod_plano    AND
            
            cc.exercicio    = vl.exercicio    AND
            cc.cod_lote     = vl.cod_lote     AND
            cc.tipo         = vl.tipo         AND
            cc.sequencia    = vl.sequencia    AND
            cc.tipo_valor   = vl.tipo_valor   AND
            cc.cod_entidade = vl.cod_entidade AND
            
            vl.exercicio    = la.exercicio    AND
            vl.cod_lote     = la.cod_lote     AND
            vl.tipo         = la.tipo         AND
            vl.sequencia    = la.sequencia    AND
            vl.cod_entidade = la.cod_entidade AND
            
            vl.tipo_valor   = ''''C''''       AND
            vl.tipo         <>''''I''''       AND
           
            lo.exercicio    = la.exercicio    AND
            lo.cod_lote     = la.cod_lote     AND
            lo.tipo         = la.tipo         AND
            lo.cod_entidade = la.cod_entidade AND
            lo.dt_lote::VARCHAR NOT ILIKE ''''%bc%'''' AND
            lo.cod_entidade IN (''|| stCodEntidades ||'') AND
            lo.exercicio=''|| quote_literal(stExercicio) ||''
        GROUP BY
            pc.cod_estrutural, pc.exercicio,lo.dt_lote, vl.tipo
        ) AS tbl
    GROUP BY cod_estrutural, dt_lote, tipo
  ) AS tbl
  GROUP BY cod_estrutural, tipo
  ORDER BY cod_estrutural
)
'';
EXECUTE stSql;

CREATE INDEX unq_debito_arquivo   ON tmp_debito_arquivo (cod_estrutural varchar_pattern_ops);
CREATE INDEX unq_credito_arquivo  ON tmp_credito_arquivo(cod_estrutural varchar_pattern_ops);

stSql := ''
	SELECT
		administracao.configuracao.valor
	FROM administracao.configuracao
	WHERE administracao.configuracao.cod_modulo = 9
  	AND administracao.configuracao.parametro = ''''masc_plano_contas''''
  	AND administracao.configuracao.exercicio = ''|| quote_literal(stExercicio) ||'';'';

FOR reRegistro IN EXECUTE stSql
LOOP
    stMascPlanoConta := publico.fn_mascarareduzida(reRegistro.valor);
END LOOP;    

CREATE TEMPORARY TABLE tmp_bmovant (
    cod_estrutural  varchar,
    tipo            varchar,
    mov_deb_1       numeric(14,2),
    mov_deb_2       numeric(14,2),
    mov_deb_3       numeric(14,2),
    mov_deb_4       numeric(14,2),
    mov_deb_5       numeric(14,2),
    mov_deb_6       numeric(14,2),
    mov_cre_1       numeric(14,2),
    mov_cre_2       numeric(14,2),
    mov_cre_3       numeric(14,2),
    mov_cre_4       numeric(14,2),
    mov_cre_5       numeric(14,2),
    mov_cre_6       numeric(14,2)
);
CREATE INDEX unq_bmovant  ON tmp_bmovant(cod_estrutural varchar_pattern_ops);

stSql := '' CREATE TEMPORARY TABLE tmp_saldo_inicial AS
SELECT *
  FROM contabilidade.fn_exportacao_balancete_verificacao(''|| quote_literal(stExercicio) ||'','''' cod_entidade IN (''|| stCodEntidades ||'')'''',''''01/01/''||stExercicio||'''''',''''31/12/''||stExercicio||'''''') AS tabela
     ( cod_estrutural      VARCHAR
     , cod_entidade        INTEGER
     , nivel               INTEGER
     , nom_conta           VARCHAR
     , vl_saldo_anterior   NUMERIC
     , vl_saldo_debitos    NUMERIC
     , vl_saldo_creditos   NUMERIC
     , vl_saldo_atual      NUMERIC
     , tipo_conta          VARCHAR
     , nom_sistema         VARCHAR
     , escrituracao        CHAR(9)
     , indicador_superavit CHAR(12))
 WHERE (vl_saldo_debitos = 0.00 AND vl_saldo_creditos = 0.00 AND vl_saldo_anterior <> 0.00 )
'';

EXECUTE stSql;


stSql := ''
SELECT 
    cod_estrutural,
    tipo,
    max(mov_cre_1) as mov_cre_1,
    max(mov_cre_2) as mov_cre_2,  
    max(mov_cre_3) as mov_cre_3,
    max(mov_cre_4) as mov_cre_4,
    max(mov_cre_5) as mov_cre_5,
    max(mov_cre_6) as mov_cre_6,
    max(mov_deb_1) as mov_deb_1,
    max(mov_deb_2) as mov_deb_2,
    max(mov_deb_3) as mov_deb_3,
    max(mov_deb_4) as mov_deb_4,
    max(mov_deb_5) as mov_deb_5,
    max(mov_deb_6) as mov_deb_6
FROM (
    SELECT 
        cod_estrutural,
        tipo,
        bimestre_1 as mov_deb_1, 
        bimestre_2 as mov_deb_2,
        bimestre_3 as mov_deb_3,
        bimestre_4 as mov_deb_4,
        bimestre_5 as mov_deb_5,
        bimestre_6 as mov_deb_6,
        0.00 as mov_cre_1,
        0.00 as mov_cre_2,
        0.00 as mov_cre_3,
        0.00 as mov_cre_4,
        0.00 as mov_cre_5,
        0.00 as mov_cre_6
    FROM 
        tmp_debito_arquivo
    UNION
    SELECT
        cod_estrutural,
        tipo,
        0.00 as mov_deb_1,
        0.00 as mov_deb_2,
        0.00 as mov_deb_3,
        0.00 as mov_deb_4,
        0.00 as mov_deb_5,
        0.00 as mov_deb_6,
        bimestre_1 as mov_cre_1,
        bimestre_2 as mov_cre_2,
        bimestre_3 as mov_cre_3,
        bimestre_4 as mov_cre_4,
        bimestre_5 as mov_cre_5,
        bimestre_6 as mov_cre_6
   FROM tmp_credito_arquivo
   UNION
   SELECT cod_estrutural
        , ''''I'''' AS tipo
        , 0.00 as mov_deb_1
        , 0.00 as mov_deb_2
        , 0.00 as mov_deb_3
        , 0.00 as mov_deb_4
        , 0.00 as mov_deb_5
        , 0.00 as mov_deb_6
        , 0.00 as mov_cred_1
        , 0.00 as mov_cred_2
        , 0.00 as mov_cred_3
        , 0.00 as mov_cred_4
        , 0.00 as mov_cred_5
        , 0.00 as mov_cred_6
   FROM tmp_saldo_inicial
) AS tbl     
GROUP BY cod_estrutural, tipo
ORDER BY cod_estrutural'';


FOR reRegistro IN EXECUTE stSql
LOOP

    stEstruturalReduzido := publico.fn_mascarareduzida(reRegistro.cod_estrutural);
    inNivel := publico.fn_nivel(stEstruturalReduzido);
    inCount := inNivel;
    WHILE inCount > 0 LOOP

        stReduzidoTemp := publico.substring_estrutural(stEstruturalReduzido,''.'',(inCount));
        stReduzidoTemp := publico.fn_mascarareduzida(stReduzidoTemp);
        inCount := publico.fn_nivel(stReduzidoTemp);
        stReduzidoTemp := publico.fn_mascara_completa(stMascPlanoConta,stReduzidoTemp);
        inCount := inCount - 1;
    SELECT INTO stCodEstrutural cod_estrutural FROM tmp_bmovant where cod_estrutural = stReduzidoTemp;

    nuMovDeb = nuMovDeb + reRegistro.mov_deb_1;
    IF FOUND THEN
        stSql := ''
        UPDATE tmp_bmovant 
        SET 
            mov_deb_1 = mov_deb_1 + ''||reRegistro.mov_deb_1 ||'', 
            mov_deb_2 = mov_deb_2 + ''||reRegistro.mov_deb_2||'',
            mov_deb_3 = mov_deb_3 + ''||reRegistro.mov_deb_3||'',
            mov_deb_4 = mov_deb_4 + ''||reRegistro.mov_deb_4||'',
            mov_deb_5 = mov_deb_5 + ''||reRegistro.mov_deb_5||'',
            mov_deb_6 = mov_deb_6 + ''||reRegistro.mov_deb_6||'',
            mov_cre_1 = mov_cre_1 + ''||reRegistro.mov_cre_1||'',
            mov_cre_2 = mov_cre_2 + ''||reRegistro.mov_cre_2||'',
            mov_cre_3 = mov_cre_3 + ''||reRegistro.mov_cre_3||'',
            mov_cre_4 = mov_cre_4 + ''||reRegistro.mov_cre_4||'',
            mov_cre_5 = mov_cre_5 + ''||reRegistro.mov_cre_5||'',
            mov_cre_6 = mov_cre_6 + ''||reRegistro.mov_cre_6||''
        WHERE cod_estrutural =  ''''''||stReduzidoTemp||'''''';'';
    ELSE 
        stSql := ''
        INSERT INTO tmp_bmovant
        VALUES(
            ''''''||stReduzidoTemp||'''''',
            ''''''||reRegistro.tipo||'''''',
            ''''''||reRegistro.mov_deb_1||'''''',
            ''''''||reRegistro.mov_deb_2||'''''',
            ''''''||reRegistro.mov_deb_3||'''''',
            ''''''||reRegistro.mov_deb_4||'''''',
            ''''''||reRegistro.mov_deb_5||'''''',
            ''''''||reRegistro.mov_deb_6||'''''',
            ''''''||reRegistro.mov_cre_1||'''''',
            ''''''||reRegistro.mov_cre_2||'''''',
            ''''''||reRegistro.mov_cre_3||'''''',
            ''''''||reRegistro.mov_cre_4||'''''',
            ''''''||reRegistro.mov_cre_5||'''''',
            ''''''||reRegistro.mov_cre_6||''''''
        );'';
    END IF;
    
    EXECUTE stSql;
--    END IF;
    END LOOP;
END LOOP;

stSql := ''
SELECT 
    cast(replace(pco.cod_estrutural,''''.'''','''''''') as varchar)   as cod_conta,
    cast(pba.cod_entidade as varchar) as cod_entidade,
    coalesce(tmp.mov_deb_1,0.00) as mov_deb_1,
    coalesce(tmp.mov_cre_1,0.00) as mov_cre_1,
    coalesce(tmp.mov_deb_2,0.00) as mov_deb_2,
    coalesce(tmp.mov_cre_2,0.00) as mov_cre_2,
    coalesce(tmp.mov_deb_3,0.00) as mov_deb_3,
    coalesce(tmp.mov_cre_3,0.00) as mov_cre_3,
    coalesce(tmp.mov_deb_4,0.00) as mov_deb_4,
    coalesce(tmp.mov_cre_4,0.00) as mov_cre_4,
    coalesce(tmp.mov_deb_5,0.00) as mov_deb_5,
    coalesce(tmp.mov_cre_5,0.00) as mov_cre_5,
    coalesce(tmp.mov_deb_6,0.00) as mov_deb_6,
    coalesce(tmp.mov_cre_6,0.00) as mov_cre_6
FROM
    contabilidade.plano_conta       as pco
    LEFT JOIN ( 
        select 
            pb.cod_entidade,
            pa.cod_conta,
            pa.exercicio
        from 
            contabilidade.plano_banco as pb,
            contabilidade.plano_analitica as pa
        where
            pb.cod_plano    = pa.cod_plano AND
            pb.exercicio    = pa.exercicio 
    ) as pba ON (   pco.cod_conta   = pba.cod_conta AND
                    pco.exercicio   = pba.exercicio ),
    tmp_bmovant as tmp 
WHERE   pco.cod_estrutural = tmp.cod_estrutural 
    AND pco.exercicio      = ''|| quote_literal(stExercicio) ||''
AND (
    tmp.mov_deb_1 <> 0.00 OR
    tmp.mov_cre_1 <> 0.00 OR
    tmp.mov_deb_2 <> 0.00 OR
    tmp.mov_cre_2 <> 0.00 OR
    tmp.mov_deb_3 <> 0.00 OR
    tmp.mov_cre_3 <> 0.00 OR
    tmp.mov_deb_4 <> 0.00 OR
    tmp.mov_cre_4 <> 0.00 OR
    tmp.mov_deb_5 <> 0.00 OR
    tmp.mov_cre_5 <> 0.00 OR
    tmp.mov_deb_6 <> 0.00 OR
    tmp.mov_cre_6 <> 0.00 OR
    tmp.tipo = ''''I''''
  ) 
    
ORDER BY pco.cod_estrutural;
'';

FOR reRegistro IN EXECUTE stSql
LOOP
     RETURN NEXT reRegistro;
END LOOP;

DROP INDEX unq_credito_arquivo;
DROP INDEX unq_debito_arquivo;

DROP TABLE tmp_credito_arquivo;
DROP TABLE tmp_debito_arquivo;
DROP TABLE tmp_saldo_inicial;
DROP TABLE tmp_bmovant;

RETURN;

END;
' LANGUAGE 'plpgsql';

