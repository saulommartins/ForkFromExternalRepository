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
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* Script de função PLPGSQL para buscar os empenhos vinculados no registro de preço
* Automação dos registros de preço para geracao do arquivo EMP.csv do tribunal de TCEMG - SICOM
*
* $Id: $
* $Revision: $
* $Author: $
* $Date: $
*
*/
CREATE OR REPLACE FUNCTION importa_empenho_registro_precos(VARCHAR,VARCHAR) RETURNS VOID AS $$
DECLARE
    stExercicio     ALIAS FOR $1;
    stCodEntidade   ALIAS FOR $2;
    inNumItem       INTEGER := 0;
    inCodLote       INTEGER := 0;
    stSQL           VARCHAR;
    reRecord        RECORD;
    stSQL2          VARCHAR;
    reRecord2       RECORD;
    stAux           VARCHAR;
BEGIN

    stSQL := '
                     SELECT catalogo_classificacao.cod_catalogo
                      , catalogo_classificacao.cod_classificacao
                      , catalogo_classificacao.descricao
                      , SPLIT_PART(SPLIT_PART(catalogo_classificacao.descricao, '' - '', 2), ''/'', 1)::INTEGER AS numero_rp
                      , substr(SPLIT_PART(catalogo_classificacao.descricao, ''/'', 2),1,4)                         AS exercicio_rp
                      , empenho.cod_entidade
                   FROM empenho.empenho
                   JOIN empenho.pre_empenho
                     ON pre_empenho.exercicio = empenho.exercicio
                    AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                   JOIN empenho.item_pre_empenho
                     ON item_pre_empenho.exercicio       = pre_empenho.exercicio
                    AND item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                   JOIN almoxarifado.catalogo_item
                     ON catalogo_item.cod_item = item_pre_empenho.cod_item
                   JOIN almoxarifado.catalogo_classificacao
                     ON catalogo_classificacao.cod_catalogo      = catalogo_item.cod_catalogo
                    AND catalogo_classificacao.cod_classificacao = catalogo_item.cod_classificacao
              LEFT JOIN tcemg.registro_precos
                     ON SPLIT_PART(SPLIT_PART(catalogo_classificacao.descricao, '' - '', 2), ''/'', 1)::INTEGER = registro_precos.numero_registro_precos
                    AND SPLIT_PART(catalogo_classificacao.descricao, ''/'', 2)                                  = registro_precos.exercicio
                    AND empenho.cod_entidade                                                                    = registro_precos.cod_entidade
                  WHERE catalogo_classificacao.cod_catalogo = 5
                    AND empenho.exercicio         = '''||stExercicio||'''
                    AND empenho.cod_entidade      IN ('||stCodEntidade||')
                    AND item_pre_empenho.cod_item IS NOT NULL
               GROUP BY catalogo_classificacao.cod_catalogo
                      , catalogo_classificacao.cod_classificacao
                      , catalogo_classificacao.descricao
                      , empenho.cod_entidade
               ORDER BY catalogo_classificacao.cod_catalogo
                      , catalogo_classificacao.cod_classificacao
                      , catalogo_classificacao.descricao
                      ;

             ';
    FOR reRecord IN EXECUTE stSQL LOOP

        stSQL2 := '
                      SELECT catalogo_classificacao.cod_catalogo
                           , catalogo_classificacao.cod_classificacao
                           , empenho.cod_empenho
                           , empenho.cod_entidade   AS cod_entidade_empenho
                           , empenho.exercicio      AS exercicio_empenho
                        FROM empenho.empenho
                        JOIN empenho.pre_empenho
                          ON pre_empenho.exercicio = empenho.exercicio
                         AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                        JOIN empenho.item_pre_empenho
                          ON item_pre_empenho.exercicio       = pre_empenho.exercicio
                         AND item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                        JOIN almoxarifado.catalogo_item
                          ON catalogo_item.cod_item = item_pre_empenho.cod_item
                        JOIN almoxarifado.catalogo_classificacao
                          ON catalogo_classificacao.cod_catalogo      = catalogo_item.cod_catalogo
                         AND catalogo_classificacao.cod_classificacao = catalogo_item.cod_classificacao
                       WHERE catalogo_classificacao.cod_catalogo      = '|| reRecord.cod_catalogo      ||'
                         AND catalogo_classificacao.cod_classificacao = '|| reRecord.cod_classificacao ||'
                         AND empenho.exercicio                        = '''||stExercicio||'''
                         AND empenho.cod_entidade                     IN ('||stCodEntidade||')
                         AND item_pre_empenho.cod_item IS NOT NULL
                    GROUP BY catalogo_classificacao.cod_catalogo
                           , catalogo_classificacao.cod_classificacao
                           , catalogo_classificacao.descricao
                           , empenho.cod_entidade
                           , empenho.cod_empenho
                           , empenho.exercicio
                    ORDER BY catalogo_classificacao.cod_catalogo
                           , catalogo_classificacao.cod_classificacao
                           , catalogo_classificacao.descricao
                           , empenho.cod_empenho
                           ;
                  ';
        FOR reRecord2 IN EXECUTE stSQL2 LOOP
            PERFORM 1
                FROM tcemg.empenho_registro_precos
            WHERE cod_entidade           = reRecord.cod_entidade
              AND numero_registro_precos = reRecord.numero_rp
              AND exercicio              = reRecord.exercicio_rp
              AND cod_empenho            = reRecord2.cod_empenho
              AND exercicio_empenho      = reRecord2.exercicio_empenho
              AND interno                = TRUE
              AND cod_entidade_empenho   = reRecord2.cod_entidade_empenho
              AND numcgm_gerenciador     = 6;

            IF NOT FOUND THEN
                stAux = '
                    INSERT
                      INTO tcemg.empenho_registro_precos
                         ( cod_entidade                     -- integer      | not null
                         , numero_registro_precos           -- integer      | not null
                         , exercicio                        -- character(4) | not null
                         , cod_empenho                      -- integer      | not null
                         , exercicio_empenho                -- character(4) | not null
                         , interno                          -- boolean      | not null
                         , cod_entidade_empenho             -- integer      | not null
                         , numcgm_gerenciador               -- integer      | not null
                         )
                      VALUES
                         (
                         '||reRecord.cod_entidade||'            -- cod_entidade
                         , '||reRecord.numero_rp||'               -- numero_registro_precos
                         , '''||reRecord.exercicio_rp||'''            -- exercicio
                         , '||reRecord2.cod_empenho||'            -- cod_empenho
                         , '''||reRecord2.exercicio_empenho||'''      -- exercicio_empenho
                         , TRUE                             -- interno
                         , '||reRecord2.cod_entidade_empenho||'   -- cod_entidade_empenho
                         , 6                                -- numcgm_gerenciador
                         );
                ';
                EXECUTE stAux;
            END IF;

        END LOOP;

    END LOOP;
END;
$$ LANGUAGE 'plpgsql';