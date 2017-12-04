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
* Retorna as parcelas normais do lançamento em uma STRING concatenando com caracter '§'
* Utilização na consulta para emissão de carnês para gráfica
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_lista_parcelas_normais.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.11
*              uc-05.03.19
*/

/*
$Log$
Revision 1.4  2007/07/12 14:18:53  dibueno
Melhorias na gerção de carnê pra gráfica

Revision 1.3  2007/07/03 15:57:31  dibueno
Melhorias na gerção de carnê pra gráfica

Revision 1.2  2007/01/23 11:02:41  fabio
correção da tag de caso de uso


*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_lista_parcelas_normais( integer ) returns VARCHAR as '
declare
    stSql           varchar := '''';
    stRetorno       varchar := '''';
    stAux           varchar := '''';
    inCont          integer := 0;
    reRecord        RECORD;

    inCodLancamento	ALIAS FOR $1;

BEGIN

    stSql := ''
        SELECT
            *
        from
        (
            select
                ap.cod_parcela
                , ap.cod_lancamento
                , ap.nr_parcela
                , ap.vencimento
                --, to_char(arrecadacao.fn_atualiza_data_vencimento (ap.vencimento),''''dd/mm/yyyy'''') as vencimento_br
                , to_char(ap.vencimento,''''dd/mm/yyyy'''') as vencimento_br
                , ap.valor
                , ( select
                        numeracao
                    from
                        arrecadacao.carne
                    where
                        cod_parcela = ap.cod_parcela
                        AND NOT EXISTS (
                            select apag.numeracao
                            from arrecadacao.pagamento as apag
                            INNER JOIN arrecadacao.carne as ac
                            ON ac.numeracao = apag.numeracao
                            where ac.cod_parcela = carne.cod_parcela
                        )
                        /*
                        AND NOT EXISTS (
                            select numeracao
                            from arrecadacao.pagamento as apag
                            where apag.numeracao = carne.numeracao
                        )*/
                    order by
                        timestamp desc
                    limit 1
                ) as numeracao
            from
                arrecadacao.parcela as ap
            WHERE
                ap.cod_lancamento = ''|| inCodLancamento ||''
                and ap.nr_parcela > 0
            order by
                nr_parcela
        ) as parcelas
        WHERE numeracao is not null
    '';

    FOR reRecord IN EXECUTE stSql LOOP
        stRetorno := stRetorno||''§''||reRecord.cod_parcela||''§''||reRecord.valor||''§''||reRecord.vencimento_br;
        stRetorno := stRetorno||''§''||reRecord.numeracao||''§''||reRecord.nr_parcela||''§''||reRecord.vencimento;
        inCont := inCont + 1;
    END LOOP;

    return inCont||stRetorno;

end;
'language 'plpgsql';
