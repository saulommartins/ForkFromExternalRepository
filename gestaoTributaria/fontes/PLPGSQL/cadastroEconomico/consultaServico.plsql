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
* $Id: consultaServico.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.02.03
*/

/*
$Log$
Revision 1.4  2006/09/15 10:19:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

--
-- Gris -- Solicitação e Criação Tonismar R. Bernardo.
--
CREATE OR REPLACE FUNCTION economico.fn_consulta_servico(INTEGER,INTEGER)
RETURNS VARCHAR AS '

    DECLARE
        reRecord          RECORD;

        stValor          VARCHAR := '''';
        stSql             VARCHAR;

    inCodVigencia ALIAS FOR $1;
        inCodServico ALIAS FOR $2;

    BEGIN
        stSql := ''
                  SELECT
                      LN.cod_nivel,
                      LN.cod_vigencia,
                      LN.cod_servico,
                      LPAD( LN.valor , LENGTH(N.mascara),''''0'''') AS valor
                  FROM
                      ( SELECT
                           N.*
                       FROM
                           ECONOMICO.VIGENCIA_SERVICO AS V,
                           ECONOMICO.NIVEL_SERVICO AS N
                       WHERE
                           V.cod_vigencia = N.cod_vigencia ) AS N,
                       ECONOMICO.NIVEL_SERVICO_VALOR AS LN
                  WHERE
                       N.cod_nivel     = LN.cod_nivel          AND
                       N.cod_vigencia  = LN.cod_vigencia       AND
                       LN.cod_vigencia = ''||inCodVigencia||'' AND
                       LN.cod_servico = ''||inCodServico||''
                  ORDER BY
                       LN.cod_nivel
                  '';
        FOR reRecord IN EXECUTE stSql LOOP
            stValor := stValor||''.''|| reRecord.valor;
        END LOOP;

        stValor := SUBSTR( stValor, 2, LENGTH(stValor) );

        RETURN stValor;
    END;

'language 'plpgsql';
