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
* $Id: recuperaProprietariosLote.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.01.09
* Casos de uso: uc-05.01.08
*/

/*
$Log$
Revision 1.8  2007/04/13 17:56:21  dibueno
Raise's comentados

Revision 1.7  2006/09/15 10:19:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_recupera_lote_proprietarios( INTEGER ) RETURNS SETOF RECORD AS '
DECLARE
    inCodLote       ALIAS FOR $1;
    stNovoSql       VARCHAR   := '''';
    stSql           VARCHAR   := '''';
    inCount         INTEGER   :=0;
    reRegistro      RECORD;
    reRegistroProp  RECORD;

BEGIN
    stSql := ''
            SELECT DISTINCT
                IL.cod_lote,
                IL.inscricao_municipal
            FROM
                imobiliario.imovel_lote IL
            LEFT JOIN
                imobiliario.baixa_imovel BI
            ON
                BI.inscricao_municipal = IL.inscricao_municipal
            WHERE
                BI.inscricao_municipal IS NULL AND
                IL.cod_lote = ''||inCodLote||''
             '';


    FOR reRegistro IN EXECUTE stSql LOOP
       stNovoSql := ''
                SELECT
                    numcgm
                FROM
                    imobiliario.proprietario
                WHERE
                    inscricao_municipal = ''||reRegistro.inscricao_municipal||'' AND
                    promitente          = false
                '';
        FOR reRegistroProp IN EXECUTE stNovoSql LOOP
            RETURN next reRegistroProp;
        END LOOP;

--        RETURN next reRegistro;
    END LOOP;

    RETURN;

END;
'language 'plpgsql';

