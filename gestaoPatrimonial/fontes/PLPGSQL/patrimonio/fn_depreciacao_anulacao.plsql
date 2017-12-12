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

CREATE OR REPLACE FUNCTION patrimonio.fn_depreciacao_anulacao(VARCHAR,VARCHAR) RETURNS SETOF VOID AS $$
DECLARE
    stCompetencia     ALIAS FOR $1;
    stMotivo          ALIAS FOR $2;
    
    stQuery           VARCHAR;
    inQuantRegistro   INTEGER;
    
    rcDepreciacao     RECORD;
BEGIN

    stQuery = ' SELECT cod_depreciacao
                     , cod_bem
                     , TO_CHAR(timestamp,''yyyy-mm-dd hh24:mi:ss.us'') AS timestamp
                     , vl_depreciado
                     , TO_CHAR(dt_depreciacao,''dd/mm/yyyy'') AS dt_depreciacao
                     , competencia
                     , motivo
                     , acelerada
                     , quota_utilizada 
                  FROM patrimonio.depreciacao
                  
                  WHERE competencia = '|| quote_literal(stCompetencia) ||'
               ORDER BY cod_depreciacao DESC ';
 
    FOR rcDepreciacao IN EXECUTE stQuery LOOP
        
        SELECT count(*)
          INTO inQuantRegistro
          FROM patrimonio.depreciacao_anulada
         WHERE cod_depreciacao = rcDepreciacao.cod_depreciacao
           AND cod_bem         = rcDepreciacao.cod_bem
           AND timestamp       = TO_TIMESTAMP(rcDepreciacao.timestamp, 'yyyy-mm-dd hh24:mi:ss.us');
           
        IF inQuantRegistro <= 0 THEN
            
            INSERT INTO patrimonio.depreciacao_anulada (
                cod_depreciacao , cod_bem, timestamp, timestamp_anulacao, motivo
            )VALUES(
                rcDepreciacao.cod_depreciacao, rcDepreciacao.cod_bem, TO_TIMESTAMP(rcDepreciacao.timestamp ,'yyyy-mm-dd hh24:mi:ss.US'), NOW(), stMotivo
            );
            
        END IF;
           
    END LOOP;
    
    RETURN;
    
END;
$$ LANGUAGE 'plpgsql';