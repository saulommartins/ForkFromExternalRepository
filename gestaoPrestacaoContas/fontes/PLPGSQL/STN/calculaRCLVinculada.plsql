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
    * Script de função PLPGSQL - Calcula o valor da RCL vinculada via sistema
    * Data de Criação: 24/11/2008


    * @author Henrique Boaventura

    $Id: $

*/

CREATE OR REPLACE FUNCTION stn.fn_calcula_rcl_vinculada(varchar, varchar, varchar) RETURNS NUMERIC AS $$
DECLARE
    stExercicio     ALIAS FOR $1;
    stDataFinal     ALIAS FOR $2;
    stEntidades     ALIAS FOR $3;
    stSql           VARCHAR := '';
    reRegistro      RECORD;
    flValorRCL      NUMERIC := 0;
    arData          VARCHAR[];

BEGIN
    -- converte a data para uma array
    arData = string_to_array(stDataFinal,'/');

    --------------------------------------------------------------------------------------------
    -- recupera os valores da tabela de acordo com a data passada, trazendo os 12 ultimos meses
    --------------------------------------------------------------------------------------------
    stSql := '
    SELECT SUM(valor) AS valor
      FROM stn.receita_corrente_liquida
     WHERE exercicio = ''' || stExercicio || '''
       --AND cod_entidade IN (' || stEntidades || ')
       AND (    TO_DATE(''01/'' || mes || ''/'' || ano, ''dd/mm/yyyy'') <= TO_DATE(''01/'||(TO_NUMBER(arData[2],'99')+1)||'/'||arData[3]||''',''dd/mm/yyyy'') - 1
            AND TO_DATE(''01/'' || mes || ''/'' || ano, ''dd/mm/yyyy'') >= TO_DATE(''01/'||arData[2]||'/'||arData[3]||''',''dd/mm/yyyy'') - INTERVAL ''11 MONTHS'') 
    ';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        IF(reRegistro.valor IS NOT NULL) THEN
            flValorRCL := reRegistro.valor;
        END IF;
    END LOOP;

    RETURN flValorRCL;   
 
END;
$$ LANGUAGE 'plpgsql';

