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
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.05.03,uc-02.05.04,uc-02.05.05,uc-02.05.06,uc-02.05.07,uc-02.05.08,uc-02.05.10,uc-02.05.11
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:50  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcers.fn_somatorio_contabil(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR)  RETURNS numeric(14,2) AS '
DECLARE
    stCodEstrutural             ALIAS FOR $1;
    stTipo                      ALIAS FOR $2;
    stTipoValorDespesa          ALIAS FOR $3;
    stDtInicial               ALIAS FOR $4;
    stDtFinal                 ALIAS FOR $5;

    stSql                       VARCHAR   := '''';
    nuSoma                      NUMERIC   := 0;

    crCursor                    REFCURSOR;

BEGIN
   IF (stTipo=1) THEN
            stSql := ''
               SELECT   (
                   SELECT   coalesce(sum(valor),0.00)
                   FROM
                        tmp_credito
                   WHERE
                       cod_estrutural like '''''' || stCodEstrutural || ''%''''
                ) + (
                   SELECT   coalesce(sum(valor),0.00)
                   FROM
                        tmp_debito
                   WHERE
                       cod_estrutural like '''''' || stCodEstrutural || ''%''''
                )'';
    ELSE
        IF (stTipo=2) THEN
            stSql := ''
               SELECT   (
                   SELECT   coalesce(sum(valor),0.00)
                   FROM
                        tmp_credito
                   WHERE
                       cod_estrutural like '''''' || stCodEstrutural || ''%'''' AND tipo=''''A''''
                ) + (
                   SELECT   coalesce(sum(valor),0.00)
                   FROM
                        tmp_debito
                   WHERE
                       cod_estrutural like '''''' || stCodEstrutural || ''%'''' AND tipo=''''A''''
                )'';
        ELSE
            if (stTipoValorDespesa=''E'') then
                stSql := ''
                    SELECT ((
                        SELECT
                            coalesce(sum(ipe.vl_total),0.00) as valor
                        FROM
                            tmp_empenhado               as tmp,
                            empenho.item_pre_empenho    as ipe
                        WHERE
                            tmp.dt_empenho BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') AND to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''') AND
                            tmp.cod_estrutural ilike substr('''''' || stCodEstrutural || ''%'''',3,length('''''' || stCodEstrutural || ''%''''))   AND

                            tmp.exercicio       = ipe.exercicio         AND
                            tmp.cod_pre_empenho = ipe.cod_pre_empenho
                        ) - (
                        SELECT
                            coalesce(sum(eai.vl_anulado),0.00) as valor
                        FROM
                            tmp_empenhado                   as tmp,
                            empenho.empenho_anulado         as ea,
                            empenho.empenho_anulado_item    as eai
                        WHERE
                            to_date(to_char(ea.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) BETWEEN to_date(''''''|| stDtInicial ||'''''',''''dd/mm/yyyy'''') AND to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''') AND
                            tmp.cod_estrutural ilike substr('''''' || stCodEstrutural || ''%'''',3,length('''''' || stCodEstrutural || ''%''''))   AND

                            tmp.exercicio       = ea.exercicio      AND
                            tmp.cod_entidade    = ea.cod_entidade   AND
                            tmp.cod_empenho     = ea.cod_empenho    AND

                            ea.exercicio        = eai.exercicio     AND
                            ea.timestamp        = eai.timestamp     AND
                            ea.cod_entidade     = eai.cod_entidade  AND
                            ea.cod_empenho      = eai.cod_empenho
                    ))
                '';
            else
                if (stTipoValorDespesa=''L'') then
                  stSql := ''
                      SELECT ((
                          SELECT
                              coalesce(sum(nli.vl_total),0.00) as valor
                          FROM
                              tmp_empenhado                   as tmp,
                              empenho.nota_liquidacao         as nl,
                              empenho.nota_liquidacao_item    as nli
                          WHERE
                              tmp.cod_estrutural ilike substr('''''' || stCodEstrutural || ''%'''',3,length('''''' || stCodEstrutural || ''%''''))   AND
                              tmp.exercicio     = nl.exercicio_empenho    AND
                              tmp.cod_entidade  = nl.cod_entidade         AND
                              tmp.cod_empenho   = nl.cod_empenho          AND

                              nl.dt_liquidacao BETWEEN to_date(''''''|| stDtInicial ||'''''',''''dd/mm/yyyy'''') AND to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''') AND
                              nl.exercicio    = nli.exercicio             AND
                              nl.cod_nota     = nli.cod_nota              AND
                              nl.cod_entidade = nli.cod_entidade
                          ) - (
                          SELECT
                              coalesce(sum(nlia.vl_anulado),0.00) as valor
                          FROM
                              tmp_empenhado                           as tmp,
                              empenho.nota_liquidacao                 as nl,
                              empenho.nota_liquidacao_item            as nli,
                              empenho.nota_liquidacao_item_anulado    as nlia
                          WHERE
                              tmp.cod_estrutural ilike substr('''''' || stCodEstrutural || ''%'''',3,length('''''' || stCodEstrutural || ''%''''))   AND
                              tmp.exercicio       = nl.exercicio_empenho  AND
                              tmp.cod_entidade    = nl.cod_entidade       AND
                              tmp.cod_empenho     = nl.cod_empenho        AND

                              to_date(to_char(nlia.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') BETWEEN to_date(''''''|| stDtInicial ||'''''',''''dd/mm/yyyy'''') AND to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''') AND

                              nl.exercicio        = nli.exercicio         AND
                              nl.cod_nota         = nli.cod_nota          AND
                              nl.cod_entidade     = nli.cod_entidade      AND

                              nli.exercicio       = nlia.exercicio        AND
                              nli.cod_nota        = nlia.cod_nota         AND
                              nli.cod_entidade    = nlia.cod_entidade     AND
                              nli.num_item        = nlia.num_item         AND
                              nli.cod_pre_empenho = nlia.cod_pre_empenho  AND
                              nli.exercicio_item  = nlia.exercicio_item
                      ))
                  '';
                else
                    if (stTipoValorDespesa=''P'') then
                      stSql := ''
                          SELECT ((
                              SELECT
                                  coalesce(sum(nlp.vl_pago),0.00) as valor
                              FROM
                                  tmp_empenhado                   as tmp,
                                  empenho.nota_liquidacao         as nl,
                                  empenho.nota_liquidacao_paga    as nlp
                              WHERE
                                  tmp.cod_estrutural ilike substr('''''' || stCodEstrutural || ''%'''',3,length('''''' || stCodEstrutural || ''%''''))   AND
                                  tmp.exercicio       = nl.exercicio_empenho  AND
                                  tmp.cod_entidade    = nl.cod_entidade       AND
                                  tmp.cod_empenho     = nl.cod_empenho        AND

                                  nl.exercicio        = nlp.exercicio         AND
                                  nl.cod_nota         = nlp.cod_nota          AND
                                  nl.cod_entidade     = nlp.cod_entidade      AND
                                  to_date(to_char(nlp.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') BETWEEN to_date(''''''|| stDtInicial ||'''''',''''dd/mm/yyyy'''') AND to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''')
                              ) - (
                              SELECT
                                  coalesce(sum(nlpa.vl_anulado),0.00) as valor
                              FROM
                                  tmp_empenhado                           as tmp,
                                  empenho.nota_liquidacao                 as nl,
                                  empenho.nota_liquidacao_paga            as nlp,
                                  empenho.nota_liquidacao_paga_anulada    as nlpa
                              WHERE
                                  tmp.cod_estrutural ilike substr('''''' || stCodEstrutural || ''%'''',3,length('''''' || stCodEstrutural || ''%''''))   AND
                                  tmp.exercicio       = nl.exercicio_empenho  AND
                                  tmp.cod_entidade    = nl.cod_entidade       AND
                                  tmp.cod_empenho     = nl.cod_empenho        AND

                                  nl.exercicio        = nlp.exercicio         AND
                                  nl.cod_nota         = nlp.cod_nota          AND
                                  nl.cod_entidade     = nlp.cod_entidade      AND

                                  nlp.exercicio       = nlpa.exercicio        AND
                                  nlp.cod_nota        = nlpa.cod_nota         AND
                                  nlp.cod_entidade    = nlpa.cod_entidade     AND
                                  nlp.timestamp       = nlpa.timestamp        AND
                                  to_date(to_char(nlpa.timestamp_anulada,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') BETWEEN to_date(''''''|| stDtInicial ||'''''',''''dd/mm/yyyy'''') AND to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''')
                          ))
                      '';
                    end if;
                end if;
            end if;
        END IF;
    END IF;


    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuSoma;
    CLOSE crCursor;


    RETURN nuSoma;
END;
' LANGUAGE 'plpgsql';
