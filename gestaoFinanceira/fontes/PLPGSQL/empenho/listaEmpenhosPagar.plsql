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
* $Revision: 16074 $
* $Name$
* $Author: eduardo $
* $Date: 2006-09-28 06:56:56 -0300 (Qui, 28 Set 2006) $
*
* Casos de uso: uc-02.04.05
*/

/*
$Log$
Revision 1.8  2006/09/28 09:56:56  eduardo
Bug #7060#

Revision 1.7  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_lista_empenhos_pagar(varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stFiltro            ALIAS FOR $1;
    stFiltroOrdem       ALIAS FOR $2;

    stSql               VARCHAR := '''';
    reRegistro          RECORD;
    dtDebug             timestamp;

BEGIN

    stSql := ''
    -- Cria temporaria da nota de liquidacao
    CREATE TEMPORARY TABLE tmp_liquidacao AS (
        SELECT ENL.cod_empenho||''''/''''||ENL.exercicio_empenho AS empenho
              ,ENL.cod_nota||''''/''''||ENL.exercicio            AS nota
              ,CAST( '''''''' as VARCHAR ) AS ordem
              ,ENL.cod_entidade
              ,CGME.nom_cgm AS entidade
              ,EPE.cgm_beneficiario
              ,CGMB.nom_cgm as beneficiario
              ,((empenho.fn_consultar_valor_liquidado_nota( ENL.exercicio
                                                          ,ENL.cod_empenho
                                                          ,ENL.cod_entidade
                                                          ,ENL.cod_nota
               )
              -empenho.fn_consultar_valor_liquidado_anulado_nota( ENL.exercicio
                                                                 ,ENL.cod_empenho
                                                                 ,ENL.cod_entidade
                                                                 ,ENL.cod_nota
              ))
              -(empenho.fn_consultar_valor_apagar_nota( ENL.exercicio
                                                       ,ENL.cod_nota
                                                       ,ENL.cod_entidade
              )
              -empenho.fn_consultar_valor_apagar_anulado_nota( ENL.exercicio
                                                              ,ENL.cod_nota
                                                              ,ENL.cod_entidade
              ))
              ) as vl_nota
              ,CAST( 0.00 as NUMERIC ) AS vl_pagamento
        FROM empenho.pre_empenho     AS EPE
            ,empenho.empenho         AS EE
            ,empenho.nota_liquidacao AS ENL
            ,orcamento.entidade      AS OE
            ,sw_cgm                  AS CGME
            ,sw_cgm                  AS CGMB
          -- Join pre_empenho / empenho
        WHERE EPE.exercicio       = EE.exercicio
          AND EPE.cod_pre_empenho = EE.cod_pre_empenho
          -- Join empenho / nota liquidacao
          AND EE.exercicio        = ENL.exercicio_empenho
          AND EE.cod_entidade     = ENL.cod_entidade
          AND EE.cod_empenho      = ENL.cod_empenho
          -- Join empenho / entidade
          AND EE.exercicio        = OE.exercicio

          AND EE.cod_entidade     = OE.cod_entidade
          -- Join entidade / cgm
          AND OE.numcgm           = CGME.numcgm
          -- Join pre_empenho / cgm
          AND EPE.cgm_beneficiario= CGMB.numcgm
          -- Filtros
          '' || stFiltro || ''
    )
    '';

    IF stFiltroOrdem ~ ''cod_ordem'' THEN 
        CREATE TEMPORARY TABLE tmp_liquidacao(
            empenho          varchar
           ,nota             varchar
           ,ordem            varchar
           ,cod_entidade     integer
           ,entidade         varchar
           ,cgm_beneficiario integer
           ,beneficiario     varchar
           ,vl_nota          numeric
           ,vl_pagamento     numeric
        );
    ELSE 
        EXECUTE stSql;
    END IF;


    -- Exclui liquidacoes anuladas
    DELETE FROM tmp_liquidacao WHERE vl_nota <= 0;


    stSql := ''
    -- Cria temporaria da ordem de pagamento
    CREATE TEMPORARY TABLE tmp_ordem AS(
        SELECT empenho.retorna_empenhos( EOP.exercicio, EOP.cod_ordem, EOP.cod_entidade ) AS empenho
              ,empenho.retorna_notas   ( EOP.exercicio, EOP.cod_ordem, EOP.cod_entidade ) AS nota
              ,EOP.cod_ordem||''''/''''||EOP.exercicio AS ordem
              ,EOP.cod_entidade
              ,CGME.nom_cgm AS entidade
              ,EPL.cgm_beneficiario
              ,EPL.beneficiario
              ,CAST( 0.00 as NUMERIC ) as vl_nota
              ,empenho.fn_consultar_valor_pagamento_ordem( eop.exercicio
                                                          ,eop.cod_ordem
                                                          ,eop.cod_entidade
               ) as vl_pagamento
        FROM empenho.ordem_pagamento AS EOP
        -- Join com ordem anulada
        LEFT JOIN (
                    select  opla.cod_ordem
                           ,opla.cod_entidade
                           ,opla.exercicio
                           ,coalesce(sum(opla.vl_anulado), 0.00) as vl_anulado
                           ,coalesce(sum(opa.vl_pagamento), 0.00) as vl_anulado
                    from empenho.ordem_pagamento_liquidacao_anulada as opla
                         empenho.pagamento_liquidacao as pl
                    where     opla.cod_ordem    = pl.cod_ordem
                          and opla.cod_entidade = pl.cod_entidade
                          and opla.exercicio    = pl.exercicio

                    group by  opla.cod_ordem
                             ,opla.cod_entidade
                             ,opla.exercicio

                  ) AS EOPA
        ON( EOP.exercicio    = EOPA.exercicio
        AND EOP.cod_entidade = EOPA.cod_entidade
        AND EOP.cod_ordem    = EOPA.cod_ordem     )
        -- Join com pagamento de liquidacao
        LEFT JOIN( SELECT EPL.exercicio
                         ,EPL.cod_entidade
                         ,EPL.cod_ordem
                   FROM empenho.pagamento_liquidacao AS EPL
                       ,empenho.pagamento_liquidacao_nota_liquidacao_paga AS EPLNLP
                       ,empenho.nota_liquidacao_paga AS ENLP
                      -- Join com nota_liquidacao_paga_anulada
                       LEFT JOIN empenho.nota_liquidacao_paga_anulada AS ENLPA
                       ON( ENLP.exercicio    = ENLPA.exercicio
                       AND ENLP.cod_entidade = ENLPA.cod_entidade
                       AND ENLP.cod_nota     = ENLPA.cod_nota
                       AND ENLP.timestamp    = ENLPA.timestamp    )
                      -- Join com pagamento_liquidacao_nota_liquidacao_paga
                   WHERE EPL.exercicio               = EPLNLP.exercicio
                     AND EPL.exercicio_liquidacao    = EPLNLP.exercicio_liquidacao
                     AND EPL.cod_entidade            = EPLNLP.cod_entidade
                     AND EPL.cod_ordem               = EPLNLP.cod_ordem
                     AND EPL.cod_nota                = EPLNLP.cod_nota
                     -- Join com nota_liquidacao_paga
                     AND EPLNLP.exercicio_liquidacao = ENLP.exercicio
                     AND EPLNLP.cod_entidade         = ENLP.cod_entidade
                     AND EPLNLP.cod_nota             = ENLP.cod_nota
                     AND EPLNLP.timestamp            = ENLP.timestamp
                     --Filtro
                     AND ENLPA.timestamp_anulada     IS NULL
                   ORDER BY EPL.exercicio
                           ,EPL.cod_entidade
                           ,EPL.cod_ordem
        ) AS ENLP ON( EOP.exercicio    = ENLP.exercicio
                  AND EOP.cod_entidade = ENLP.cod_entidade
                  AND EOP.cod_ordem    = ENLP.cod_ordem    )
        -- Join com empenho
        INNER JOIN( SELECT EPL.exercicio
                          ,EE.exercicio AS exercicio_empenho
                          ,EPL.cod_entidade
                          ,EPL.cod_ordem
                          ,EPE.cgm_beneficiario
                          ,CGM.nom_cgm   as beneficiario
                    FROM empenho.pagamento_liquidacao AS EPL
                        ,empenho.nota_liquidacao      AS ENL
                        ,empenho.empenho              AS EE
                        ,empenho.pre_empenho          AS EPE
                        ,sw_cgm                       AS CGM
                      -- Join com nota Liquidacao
                    WHERE EPL.exercicio_liquidacao = ENL.exercicio
                      AND EPL.cod_entidade         = ENL.cod_entidade
                      AND EPL.cod_nota             = ENL.cod_nota
                      -- Join com empenho
                      AND ENL.exercicio_empenho    = EE.exercicio
                      AND ENL.cod_entidade         = EE.cod_entidade
                      AND ENL.cod_empenho          = EE.cod_empenho
                      -- Join com pre_empenho
                      AND EE.exercicio             = EPE.exercicio
                      AND EE.cod_pre_empenho       = EPE.cod_pre_empenho
                      -- Join pre_empenho / cgm
                      AND EPE.cgm_beneficiario     = CGM.numcgm
                      -- Filtros
                      '' || stFiltro || ''
                    GROUP BY EPL.exercicio
                            ,EE.exercicio
                            ,EPL.cod_entidade
                            ,EPL.cod_ordem
                            ,EPE.cgm_beneficiario
                            ,CGM.nom_cgm
                    ORDER BY EPL.exercicio
                            ,EE.exercicio
                            ,EPL.cod_entidade
                            ,EPL.cod_ordem
                            ,EPE.cgm_beneficiario
                            ,CGM.nom_cgm
        ) AS EPL ON( EOP.exercicio    = EPL.exercicio
                 AND EOP.cod_entidade = EPL.cod_entidade
                 AND EOP.cod_ordem    = EPL.cod_ordem     )
            ,orcamento.entidade      AS OE
            ,sw_cgm                  AS CGME
        WHERE ENLP.cod_ordem IS NULL
          --AND EOPA.cod_ordem IS NULL
          AND EOPA.vl_anulado = EOPA.vl_pagamento
          -- Join com entidade
          AND EOP.exercicio    = OE.exercicio
          AND EOP.cod_entidade = OE.cod_entidade
          -- Join entidade / cgm
          AND OE.numcgm        = CGME.numcgm
          -- Filtros da ordem
          '' || stFiltroOrdem || ''
        ORDER BY EOP.exercicio
                ,EOP.cod_entidade
                ,EOP.cod_ordem
    )
    '';
    EXECUTE stSql;

    stSql := ''
                SELECT CAST( empenho AS VARCHAR ) AS empenho
                      ,CAST( nota    AS VARCHAR ) AS nota
                      ,CAST( ordem   AS VARCHAR ) AS ordem
                      ,cod_entidade
                      ,CAST( entidade  AS VARCHAR ) AS entidade
                      ,cgm_beneficiario
                      ,CAST( beneficiario AS VARCHAR ) as beneficiario
                      ,vl_nota
                      ,vl_pagamento
                FROM tmp_liquidacao AS TL

                UNION

                SELECT CAST( empenho AS VARCHAR ) AS empenho
                      ,CAST( nota    AS VARCHAR ) AS nota
                      ,CAST( ordem   AS VARCHAR ) AS ordem
                      ,cod_entidade
                      ,CAST( entidade  AS VARCHAR ) AS entidade
                      ,cgm_beneficiario
                      ,CAST( beneficiario AS VARCHAR ) as beneficiario
                      ,vl_nota
                      ,vl_pagamento
                FROM tmp_ordem AS TOR
    '';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;


    DROP TABLE tmp_liquidacao;
    DROP TABLE tmp_ordem;


RETURN;

END;

'language 'plpgsql';
