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
* $Id: consultaLogradouro.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.01.04
*/

/*
$Log$
Revision 1.3  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_consulta_logradouro( INTEGER ) RETURNS VARCHAR[] AS '

    DECLARE
        reRecord   RECORD;
        arRetorno  VARCHAR[] := array[0];
        stSql      VARCHAR;
        reRegistro RECORD;

        stInscricaoMunicipal ALIAS FOR $1;

    BEGIN
        stSql := ''
            SELECT
                LO.cod_logradouro,
                TLO.nom_tipo||'''' ''''||NLO.nom_logradouro as logradouro
            FROM
                imobiliario.imovel_confrontacao      AS ICO,
                imobiliario.confrontacao_trecho      AS CT,
                sw_logradouro                        AS LO,
                sw_nome_logradouro                   AS NLO,
                sw_tipo_logradouro                   AS TLO
            WHERE
                ICO.inscricao_municipal = ''||stInscricaoMunicipal||'' AND

                CT.cod_confrontacao    = ICO.cod_confrontacao          AND
                CT.cod_lote            = ICO.cod_lote                  AND
                CT.cod_logradouro      = LO.cod_logradouro             AND
                CT.principal           = true                          AND

                NLO.cod_logradouro     = LO.cod_logradouro             AND
                NLO.cod_tipo           = TLO.cod_tipo
            '';


        FOR reRegistro IN EXECUTE stSql LOOP
            arRetorno[1] := reRegistro.cod_logradouro;
            arRetorno[2] := reRegistro.logradouro;
        END LOOP;

        RETURN arRetorno;
    END;

'language 'plpgsql';
