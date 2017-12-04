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

CREATE OR REPLACE FUNCTION TCEMG.buscar_responsavel_lqd( intCodEntidade VARCHAR, intCodNota VARCHAR, stDtLiquidacao VARCHAR ) RETURNS VARCHAR AS $$
DECLARE
    recRegistro         RECORD;
    numCgm              VARCHAR;
    stSql               VARCHAR := '';
    
BEGIN
    stSql := '
                SELECT numcgm::varchar AS numcgm
                
                  FROM administracao.auditoria_detalhe
                  
                 WHERE cod_acao = 812
                   AND valores::varchar like ''%"cod_nota"=>"' || intCodNota || '"%''
                   AND valores::varchar like ''%"cod_entidade"=>"' || intCodEntidade || '"%''
                   AND valores::varchar like ''%"dt_liquidacao"=>"' || stDtLiquidacao || '"%''
                   
              GROUP BY numcgm
    ';
RAISE NOTICE 'stSql: %', stSql;
    FOR recRegistro IN EXECUTE stSql
    LOOP
        numCgm := recRegistro.numcgm;
    END LOOP;

    RETURN numCgm;

   END;

$$ LANGUAGE 'plpgsql';
