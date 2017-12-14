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
* $Id: fn_calculoImobiliario.plsql 29203 2008-04-15 14:45:04Z fabio $
*
* Caso de uso: uc-05.03.05
*/

CREATE OR REPLACE FUNCTION fn_desativa_calculo(varchar, varchar) returns boolean as '
DECLARE
     stGrupo                   ALIAS FOR $1;
     stExercicio               ALIAS FOR $2;
     stSqlGrupo                varchar;
     reRecordGrupo             record;
     boRetorno                 boolean;

BEGIN
    stSqlGrupo := ''
        SELECT acgr.cod_calculo
        FROM arrecadacao.calculo_grupo_credito    AS acgr

        JOIN arrecadacao.calculo                  AS ac
        ON ac.cod_calculo     = acgr.cod_calculo 
        AND ac.ativo           = TRUE

        LEFT JOIN arrecadacao.lancamento_calculo       AS alc
        ON alc.cod_calculo    = acgr.cod_calculo

        WHERE cod_grupo          = ''||stGrupo||''  
        AND acgr.ano_exercicio = ''''||stExercicio||''''
        AND alc.cod_calculo IS NULL;
    '';

    FOR reRecordGrupo IN EXECUTE stSqlGrupo LOOP
        UPDATE  arrecadacao.calculo SET ativo = FALSE WHERE cod_calculo = reRecordGrupo.cod_calculo;
    END LOOP;

    boRetorno = true;
    return boRetorno;
END;  
'language 'plpgsql';
