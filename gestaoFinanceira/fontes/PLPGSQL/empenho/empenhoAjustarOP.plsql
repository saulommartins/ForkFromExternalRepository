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
* $Revision: 12710 $
* $Name$
* $Author: andre.almeida $
* $Date: 2006-07-14 14:58:46 -0300 (Sex, 14 Jul 2006) $
*
* Casos de uso: uc-02.03.05
*/

/*
$Log$
Revision 1.6  2006/07/14 17:58:35  andre.almeida
Bug #6556#

Alterado scripts de NOT IN para NOT EXISTS.

Revision 1.5  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.ajusta_op() RETURNS BOOLEAN AS '

DECLARE
    stSql               VARCHAR   := '''';
    stSql2              VARCHAR   := '''';
    inOrdem             INTEGER   := 0;
    inCount             INTEGER   := 0;
    reRegistro          RECORD;
BEGIN

    stSql := ''
        SELECT
            e.cod_empenho,
            e.exercicio,
            e.cod_entidade,
            nlp.exercicio as exercicio_liquidacao,
            nlp.cod_nota,
            nlp.vl_pago,
            nlp.timestamp,
            to_char(nlp.timestamp,''''dd/mm/yyyy'''') as dt_emissao,
            nl.observacao
        FROM
             empenho.empenho                                e,
             empenho.nota_liquidacao                        nl,
             empenho.nota_liquidacao_paga                   nlp
        WHERE
             e.cod_empenho       = nl.cod_empenho           AND
             e.cod_entidade      = nl.cod_entidade          AND
             e.exercicio         = nl.exercicio_empenho     AND
             e.exercicio         < 2005                     AND

             nl.exercicio        = nlp.exercicio            AND
             nl.cod_nota         = nlp.cod_nota             AND
             nl.cod_entidade     = nlp.cod_entidade         AND
             nl.exercicio        < 2005

        AND NOT EXISTS ( SELECT 1
                           FROM empenho.pagamento_liquidacao_nota_liquidacao_paga plnlp
                          WHERE plnlp.cod_entidade         = nlp.cod_entidade
                            AND plnlp.cod_nota             = nlp.cod_nota
                            AND plnlp.exercicio_liquidacao = nlp.exercicio
                            AND plnlp.timestamp            = nlp.timestamp
                            AND plnlp.exercicio_liquidacao < 2005
                       )
        ORDER BY
            e.exercicio,
            e.cod_entidade,
            e.cod_empenho,
            nlp.cod_nota
    '';

    FOR reRegistro IN EXECUTE stSql
    LOOP

        inCount := inCount + 1;

        SELECT
            (coalesce(max(cod_ordem),0)+1)
        INTO
            inOrdem
        FROM
            empenho.ordem_pagamento
        WHERE
            exercicio       = reRegistro.exercicio AND
            cod_entidade    = reRegistro.cod_entidade;

        stSql2 := ''
            INSERT INTO empenho.ordem_pagamento VALUES
                ('' || inOrdem || '', '''''' || reRegistro.exercicio || '''''', '' || reRegistro.cod_entidade || '', to_date('''''' || reRegistro.dt_emissao || '''''',''''dd/mm/yyyy''''), to_date(''''31/12/'' || reRegistro.exercicio || '''''',''''dd/mm/yyyy''''), '''''' || reRegistro.observacao || '''''')
        '';
        EXECUTE stSql2;

        stSql2 := ''
            INSERT INTO empenho.pagamento_liquidacao VALUES
                ('' || inOrdem || '', '''''' || reRegistro.exercicio || '''''', '' || reRegistro.cod_entidade || '', '''''' || reRegistro.exercicio_liquidacao || '''''', '' || reRegistro.cod_nota || '', '''''' || reRegistro.vl_pago || '''''')
        '';
        EXECUTE stSql2;

        stSql2 := ''
            INSERT INTO empenho.pagamento_liquidacao_nota_liquidacao_paga VALUES
                ('' || reRegistro.cod_nota || '', '''''' || reRegistro.exercicio_liquidacao || '''''', '' || reRegistro.cod_entidade || '', '''''' || reRegistro.exercicio || '''''', '' || inOrdem || '', '''''' || reRegistro.timestamp || '''''')
        '';
        EXECUTE stSql2;

    END LOOP;


RETURN true;

END;
'language 'plpgsql';

