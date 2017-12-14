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
    * Script de função PLPGSQL - Relatório STN - RREO - Anexo 14.
    * Data de Criação: 09/06/2008


    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-06.01.14

    $Id: $

*/

CREATE OR REPLACE FUNCTION stn.fn_anexo14_somatorio_balancete_receita(VARCHAR, VARCHAR, VARCHAR, VARCHAR)  RETURNS numeric(14,2) AS '
DECLARE
    stCodEstrutural             ALIAS FOR $1;
    dtInicial                   ALIAS FOR $2;
    dtFinal                     ALIAS FOR $3;
    stCodRecursos               ALIAS FOR $4;
    stSql                       VARCHAR   := '''';
    nuSoma                      NUMERIC   := 0;
    crCursor                    REFCURSOR;

BEGIN
     stSql := ''
        SELECT   sum(valor)
        FROM
                 tmp_valor
        WHERE
                cod_estrutural like '''''' || stCodEstrutural || ''%'''' AND
                recurso IN ( '' || stCodRecursos || '' ) AND
                data BETWEEN to_date(''''''||dtInicial||'''''',''''dd/mm/yyyy'''') AND 
                             to_date(''''''||dtFinal||'''''',''''dd/mm/yyyy'''')'';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuSoma;
    CLOSE crCursor;

    RETURN nuSoma;
END;
' LANGUAGE 'plpgsql';