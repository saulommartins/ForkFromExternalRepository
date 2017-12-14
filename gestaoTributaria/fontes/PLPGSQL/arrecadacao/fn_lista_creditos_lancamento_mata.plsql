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
* Retorna as creditos/valores do lançamento em uma STRING concatenando com caracter '§'
* Utilização na consulta para emissão de carnês para gráfica
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_lista_creditos_lancamento_mata.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.11
*              uc-05.03.19
*/

/*
$Log$
Revision 1.3  2007/07/03 15:57:50  dibueno
Melhorias na gerção de carnê pra gráfica

Revision 1.2  2007/01/23 11:02:41  fabio
correção da tag de caso de uso


*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_lista_creditos_lancamento_mata( integer, integer, integer ) returns VARCHAR as '
declare
    stSql           varchar := '''';
    stRetorno       varchar := '''';
    stAux           varchar := '''';
    inCont          integer := 0;
    nuSoma          numeric;
    reRecord        RECORD;

    inCodLancamento	ALIAS FOR $1;
    inCodGrupo      ALIAS FOR $2;
    inExercicio     ALIAS FOR $3;

BEGIN

    stSql := ''
        SELECT
            ac.cod_credito,
            mc.descricao_credito as descricao,
            alc.valor
        FROM
            arrecadacao.lancamento_calculo as alc

            INNER JOIN arrecadacao.calculo_grupo_credito as acgc
            ON acgc.cod_calculo = alc.cod_calculo

            INNER JOIN arrecadacao.calculo as ac
            ON acgc.cod_calculo = ac.cod_calculo
            AND ac.exercicio = acgc.ano_exercicio

            INNER JOIN arrecadacao.credito_grupo as acg
            ON acg.cod_grupo = acgc.cod_grupo
            AND acg.ano_exercicio = acgc.ano_exercicio
            AND acg.cod_natureza = ac.cod_natureza
            AND acg.cod_especie = ac.cod_especie
            AND acg.cod_genero = ac.cod_genero
            AND acg.cod_credito = ac.cod_credito

            INNER JOIN monetario.credito as mc
            ON ac.cod_credito   = mc.cod_credito
            AND ac.cod_especie  = mc.cod_especie
            AND ac.cod_genero   = mc.cod_genero
            AND ac.cod_natureza = mc.cod_natureza
        WHERE
            alc.cod_lancamento      = ''|| inCodLancamento ||''
            and acg.cod_grupo       = ''|| inCodGrupo ||''
            and acg.ano_exercicio   = ''|| quote_literal(inExercicio) ||''
            and mc.cod_credito in (2,3)

        ORDER BY acg.ordem
    '';

    

    nuSoma := 0.00;
    FOR reRecord IN EXECUTE stSql LOOP
        stRetorno := stRetorno||''§''||reRecord.cod_credito||''§''||reRecord.descricao||''§''||reRecord.valor;
        nuSoma := nuSoma + reRecord.valor;
        inCont := inCont + 1;
    END LOOP;

    return nuSoma||stRetorno;
end;
'language 'plpgsql';
