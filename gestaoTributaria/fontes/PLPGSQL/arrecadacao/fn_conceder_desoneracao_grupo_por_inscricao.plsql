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
* $Id: fn_conceder_desoneracao_grupo.plsql 29203 2008-04-15 14:45:04Z fabio $
*
* Caso de uso: uc-05.03.04
*/

CREATE OR REPLACE FUNCTION fn_conceder_desoneracao_grupo_por_inscricao(integer, varchar, varchar, integer) returns integer as $$
DECLARE
     desoneracao     ALIAS FOR $1;
     regraConcessao  ALIAS FOR $2;
     tipoDesoneracao ALIAS FOR $3;
     inInscricao     ALIAS FOR $4;
     stSql           VARCHAR;
     reRecord        RECORD;
     iCount          INTEGER := 0;
 
BEGIN

    IF tipoDesoneracao = 'II' THEN
        stSql := ' 
                        SELECT i.inscricao_municipal
                             , ip.numcgm
                          FROM imobiliario.imovel AS i
                     LEFT JOIN (
                                 SELECT bal.*
                                   FROM imobiliario.baixa_imovel AS bal
                                      , (
                                            SELECT MAX (TIMESTAMP) AS TIMESTAMP
                                                 , inscricao_municipal
                                              FROM imobiliario.baixa_imovel
                                          GROUP BY inscricao_municipal
                                        ) AS bt
                                  WHERE bal.inscricao_municipal = bt.inscricao_municipal
                                    AND bal.timestamp           = bt.timestamp
                               ) AS bi
                            ON i.inscricao_municipal = bi.inscricao_municipal
                     LEFT JOIN imobiliario.proprietario AS ip
                            ON ip.inscricao_municipal = i.inscricao_municipal
                         WHERE (
                                     (bi.dt_inicio IS NULL)
                                  OR (bi.dt_inicio IS NOT NULL AND bi.dt_termino IS NOT NULL)
                                 AND bi.inscricao_municipal = i.inscricao_municipal
                               )
                           AND '|| regraConcessao ||'(i.inscricao_municipal) = TRUE
                           AND i.inscricao_municipal = '|| inInscricao ||'
                             ;
                 '; 

        FOR reRecord IN EXECUTE stSql LOOP
            PERFORM 1 FROM arrecadacao.desonerado  WHERE numcgm  = reRecord.numcgm AND cod_desoneracao = desoneracao;

            IF NOT FOUND THEN
                INSERT INTO arrecadacao.desonerado VALUES ( desoneracao, reRecord.numcgm, now()::date, null, null, 1  );
                INSERT INTO arrecadacao.desonerado_imovel VALUES ( reRecord.numcgm, desoneracao, reRecord.inscricao_municipal, 1 );
            ELSE
                INSERT INTO arrecadacao.desonerado VALUES ( desoneracao, reRecord.numcgm, now()::date, null, null, (SELECT max(ocorrencia)+1 FROM arrecadacao.desonerado  WHERE numcgm  = reRecord.numcgm AND cod_desoneracao = desoneracao) );
                INSERT INTO arrecadacao.desonerado_imovel VALUES ( reRecord.numcgm, desoneracao, reRecord.inscricao_municipal, (SELECT max(ocorrencia) FROM arrecadacao.desonerado  WHERE numcgm  = reRecord.numcgm AND cod_desoneracao = desoneracao) );
            END IF;

            iCount := iCount + 1 ;
        END LOOP;
    ELSIF tipoDesoneracao  = 'IE' THEN
        stSql := ' 
                       SELECT DISTINCT COALESCE (ef.numcgm, ed.numcgm, au.numcgm) AS numcgm
                            , ce.inscricao_economica
                            , ce.timestamp
                            , TO_CHAR(ce.dt_abertura,''dd/mm/yyyy'') as dt_abertura
                            , cgm.nom_cgm
                            , CASE
                                   WHEN CAST( ef.numcgm AS VARCHAR) IS NOT NULL THEN
                                       ''1''
                                   WHEN CAST( au.numcgm AS VARCHAR) IS NOT NULL THEN
                                       ''3''
                                   WHEN cast( ed.numcgm as varchar) is not null THEN
                                       ''2''
                              END AS enquadramento
                            , economico.fn_busca_sociedade(ce.inscricao_economica) AS sociedade
                         FROM economico.cadastro_economico                      AS ce
                    LEFT JOIN economico.cadastro_economico_empresa_fato         AS ef
                           ON ce.inscricao_economica = ef.inscricao_economica
                    LEFT JOIN economico.cadastro_economico_autonomo             AS au
                           ON ce.inscricao_economica = au.inscricao_economica
                    LEFT JOIN economico.cadastro_economico_empresa_direito      AS ed
                           ON ce.inscricao_economica = ed.inscricao_economica
                    LEFT JOIN economico.baixa_cadastro_economico                AS ba
                           ON ce.inscricao_economica = ba.inscricao_economica
                            , sw_cgm AS cgm
                        WHERE COALESCE (ef.numcgm, ed.numcgm, au.numcgm)   = cgm.numcgm
                          AND ba.inscricao_economica                       IS NULL
                          AND '|| regraConcessao ||'(ce.inscricao_economica) = TRUE
                          AND ce.inscricao_economica                       = '|| inInscricao ||'
                     ORDER BY ce.inscricao_economica
                            ;
        ';

        FOR reRecord IN EXECUTE stSql LOOP
            PERFORM 1 FROM arrecadacao.desonerado  WHERE numcgm  = reRecord.numcgm AND cod_desoneracao = desoneracao;

            IF NOT FOUND THEN
                INSERT INTO arrecadacao.desonerado VALUES ( desoneracao, reRecord.numcgm, now()::date, null, null, 1  );
                INSERT INTO arrecadacao.desonerado_cad_economico VALUES ( reRecord.numcgm, desoneracao, reRecord.inscricao_economica, 1 );
            ELSE
                INSERT INTO arrecadacao.desonerado VALUES ( desoneracao, reRecord.numcgm, now()::date, null, null, (SELECT MAX(ocorrencia)+1 FROM arrecadacao.desonerado  WHERE numcgm  = reRecord.numcgm AND cod_desoneracao = desoneracao) );
                INSERT INTO arrecadacao.desonerado_cad_economico VALUES ( reRecord.numcgm, desoneracao, reRecord.inscricao_economica, (SELECT MAX(ocorrencia) FROM arrecadacao.desonerado  WHERE numcgm  = reRecord.numcgm AND cod_desoneracao = desoneracao) );
            END IF;

            iCount := iCount + 1 ;
        END LOOP;
    ELSE
        PERFORM 1 FROM arrecadacao.desonerado  WHERE numcgm  = inInscricao;

        IF NOT FOUND THEN
            INSERT INTO arrecadacao.desonerado VALUES ( desoneracao, inInscricao, now()::date, null, null, 1  );
        ELSE
            INSERT INTO arrecadacao.desonerado VALUES ( desoneracao, inInscricao, now()::date, null, null, (SELECT MAX(ocorrencia)+1 FROM arrecadacao.desonerado  WHERE numcgm  = inInscricao AND cod_desoneracao = desoneracao) );
        END IF;

        iCount := iCount + 1 ;
    END IF;

    RETURN iCount; 
END;  
$$ LANGUAGE 'plpgsql';


