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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_consulta_parcela_tmp.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.5  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_consulta_parcela_tmp (integer,date) returns SETOF RECORD AS '
DECLARE
   inCodLancamento      ALIAS FOR $1;
   dtDataBase           ALIAS FOR $2;
   stSql                VARCHAR;
   stSql2               VARCHAR;
   reRecord1            RECORD;
   reRecord2            RECORD;
BEGIN
    stSql = ''
        SELECT
            al.cod_lancamento::integer,
            ap.cod_parcela::integer,
            ap.nr_parcela::integer,
            ap.valor::numeric,
            (to_char(ap.vencimento,''''dd/mm/YYYY''''))::varchar as vencimento,
            (to_char(ap.vencimento,''''dd/mm/YYYY''''))::varchar as vencimento_original,
            ap.vencimento as vencimento_us,
            case
                when ap.nr_parcela = 0 then ''''Única''''::VARCHAR
                else (ap.nr_parcela::varchar||''''/''''|| arrecadacao.fn_total_parcelas(al.cod_lancamento))::varchar
            end as info_parcela,
            now()::date as database,
            (to_char(now()::date, ''''dd/mm/YYYY''''))::varchar as database_br,
            ''''''''::varchar as numeracao,
            0::integer as exercicio,
            ''''''''::varchar as situacao,
            ''''''''::varchar as situacao_resumida,
            ''''''''::varchar as numeracao_migracao,
            ''''''''::varchar as prefixo,
            now()::date  as pagamento,
            0::integer as ocorrencia_pagamento
        FROM
            arrecadacao.lancamento al 
            INNER JOIN arrecadacao.parcela ap ON al.cod_lancamento   = ap.cod_lancamento
        WHERE
            al.cod_lancamento=''||inCodLancamento||''
        ORDER BY 
            ap.cod_parcela
        '';

    FOR reRecord1 IN EXECUTE stSql LOOP
        stSql2 := ''
                    SELECT
                        *
                    FROM
                        arrecadacao.fn_consulta_numeracao_parcela_tmp(''||reRecord1.cod_parcela||'',''''''||dtDataBase||'''''')
                    as ( numeracao varchar, exercicio int, situacao varchar, situacao_resumida varchar, numeracao_migracao varchar, prefixo varchar, vencimento_original varchar, pagamento date, ocorrencia_pagamento int )
                    '';

      FOR reRecord2 IN EXECUTE stSql2 LOOP
           reRecord1.numeracao          := reRecord2.numeracao;
           reRecord1.exercicio              := reRecord2.exercicio;
           reRecord1.situacao              := reRecord2.situacao ;
           reRecord1.situacao_resumida     := reRecord2.situacao_resumida;
           reRecord1.numeracao_migracao := reRecord2.numeracao_migracao ;
           reRecord1.prefixo                 := reRecord2.prefixo ;
           reRecord1.pagamento               := reRecord2.pagamento;
           reRecord1.vencimento_original     := reRecord2.vencimento_original;
           reRecord1.ocorrencia_pagamento     := reRecord2.ocorrencia_pagamento;
      END LOOP;
      return next reRecord1;
    END LOOP;

    return;
END;
' LANGUAGE 'plpgsql';

