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
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.03.02
*               uc-02.03.03
*               uc-02.01.08
*/

/*
$Log$
Revision 1.5  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.autorizacao_reserva() RETURNS BOOLEAN AS '

DECLARE
    stSql               VARCHAR   := '''';
    stSql2              VARCHAR   := '''';
    reRegistro          RECORD;
    inCount             INTEGER   := 0;
BEGIN

    stSql := ''
        SELECT
            ae.cod_autorizacao,
            to_char(ae.dt_autorizacao,''''dd/mm/yyyy'''') as dt_autorizacao,
            ar.cod_reserva,
            ar.exercicio,
            to_char(rs.dt_inclusao,''''dd/mm/yyyy'''') as dt_inclusao,
            to_char(rs.dt_validade_inicial,''''dd/mm/yyyy'''') as dt_validade_inicial
        FROM
            empenho.autorizacao_empenho ae,
            empenho.autorizacao_reserva ar,
            orcamento.reserva_saldos rs
        WHERE
            ae.cod_autorizacao  = ar.cod_autorizacao    AND
            ae.exercicio        = ar.exercicio          AND
            ae.cod_entidade     = ar.cod_entidade       AND

            ar.cod_reserva      = rs.cod_reserva        AND
            ar.exercicio        = rs.exercicio          AND

            rs.tipo             = ''''A''''             AND
            rs.dt_inclusao      <> ae.dt_autorizacao
    '';

    FOR reRegistro IN EXECUTE stSql
    LOOP

        inCount := inCount + 1;

        stSql2 := ''
            UPDATE
                orcamento.reserva_saldos
            SET
                dt_inclusao         = to_date('''''' || reRegistro.dt_autorizacao || '''''',''''dd/mm/yyyy''''),
                dt_validade_inicial = to_date('''''' || reRegistro.dt_autorizacao || '''''',''''dd/mm/yyyy'''')
            WHERE
                cod_reserva         = '' || reRegistro.cod_reserva || ''    AND
                exercicio           = '' || reRegistro.exercicio ;

        EXECUTE stSql2;

    END LOOP;


inCount := 0;

    stSql := ''
        SELECT
            ae.cod_autorizacao,
            aa.cod_entidade,
            aa.exercicio,
            to_char(ae.dt_autorizacao,''''dd/mm/yyyy'''') as dt_autorizacao,
            to_char(aa.dt_anulacao,''''dd/mm/yyyy'''') as dt_anulacao
        FROM
            empenho.autorizacao_empenho ae,
            empenho.autorizacao_anulada aa
        WHERE
            ae.cod_autorizacao  = aa.cod_autorizacao    AND
            ae.exercicio        = aa.exercicio          AND
            ae.cod_entidade     = aa.cod_entidade       AND

            aa.dt_anulacao      < ae.dt_autorizacao
    '';

    FOR reRegistro IN EXECUTE stSql
    LOOP

        inCount := inCount + 1;

        stSql2 := ''
            UPDATE
                empenho.autorizacao_anulada
            SET
                dt_anulacao         = to_date('''''' || reRegistro.dt_autorizacao || '''''',''''dd/mm/yyyy'''')
            WHERE
                cod_autorizacao     = '' || reRegistro.cod_autorizacao || ''    AND
                cod_entidade        = '' || reRegistro.cod_entidade || ''       AND
                exercicio           = '' || reRegistro.exercicio ;

        EXECUTE stSql2;

    END LOOP;


inCount := 0;

    stSql := ''
        SELECT
            rs.cod_reserva,
            rs.exercicio,
            to_char(rs.dt_inclusao,''''dd/mm/yyyy'''') as dt_inclusao,
            to_char(rsa.dt_anulacao,''''dd/mm/yyyy'''') as dt_anulacao
        FROM
            orcamento.reserva_saldos            as rs,
            orcamento.reserva_saldos_anulada    as rsa
        WHERE
            rs.cod_reserva  = rsa.cod_reserva   AND
            rs.exercicio    = rsa.exercicio     AND

            rs.dt_inclusao > rsa.dt_anulacao
    '';

    FOR reRegistro IN EXECUTE stSql
    LOOP

        inCount := inCount + 1;

        stSql2 := ''
            UPDATE
                orcamento.reserva_saldos_anulada
            SET
                dt_anulacao     = to_date('''''' || reRegistro.dt_inclusao || '''''',''''dd/mm/yyyy'''')
            WHERE
                cod_reserva         = '' || reRegistro.cod_reserva || ''    AND
                exercicio           = '' || reRegistro.exercicio ;

        EXECUTE stSql2;

    END LOOP;



RETURN true;

END;
'language 'plpgsql';

