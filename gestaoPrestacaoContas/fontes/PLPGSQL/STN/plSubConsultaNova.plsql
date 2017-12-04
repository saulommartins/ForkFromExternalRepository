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
    * Script de função PLPGSQL - Relatório STN - RGF - Anexo 1.
    * Data de Criação: 30/04/2013

    * @author Eduardo Paculski Schitz

    * Casos de uso:

    $Id: plSubConsultaNova.plsql 65047 2016-04-20 14:50:13Z michel $

*/

CREATE OR REPLACE FUNCTION stn.sub_consulta_rcl_novo (varchar, varchar, integer) RETURNS SETOF RECORD AS $$
DECLARE
    dtData          ALIAS FOR $1;
    cod_estrutural  ALIAS FOR $2;
    inNivel         ALIAS FOR $3;

    stDataIni   VARCHAR;
    stDataFim   VARCHAR;
    stMes       VARCHAR;
    inAno       INTEGER;
    inMes       INTEGER;
    inDia       INTEGER;
    inExercicio INTEGER;
    i           INTEGER;
    arDatas     VARCHAR[];
    reRegistro  RECORD;
    stSql       VARCHAR :='';
BEGIN
    inAno := SUBSTR(dtData, 7, 4);
    inMes := SUBSTR(dtData, 4, 2); 

    inExercicio := inAno;

    i := 1;
    WHILE i <= 12 LOOP
        IF ( inMes < 10 ) THEN
            stMes := '0' || inMes;
        ELSE
            stMes := inMes;
        END IF;

        arDatas[i] :=  '01/'||stMes||'/'||inAno;

        i := i +1;
        inMes := inMes -1;
        IF ( inMes = 0 ) THEN
            inAno := inAno -1;
            inMes := 12;
        END IF;
    END LOOP;

    stDataIni :=  '01'||SUBSTR(dtData,3,8);
    stDataFim := dtData;

    stSql := '
              SELECT CAST(conta_receita.cod_conta AS VARCHAR) AS cod_conta
                   , CAST(COALESCE(stn.tituloRCL(publico.fn_mascarareduzida(conta_receita.cod_estrutural)), conta_receita.descricao) AS VARCHAR) AS nom_conta
                   , CAST(conta_receita.cod_estrutural AS VARCHAR) AS cod_estrutural
    ';

    i := 12;

    WHILE i >= 1 LOOP
        stDataIni := arDatas[i];
        inDia := stn.calculaNrDiasAnoMes(SUBSTR(stDataIni,7,4)::INTEGER, SUBSTR(stDataIni,4,2)::INTEGER);
        stDataFim := inDia||SUBSTR(stDataIni,3,8);

        IF inNivel = 2 AND cod_estrutural = '4.1' THEN
            --SE contabilidade.lancamento_receita NÃO POSSUI VALORES, O VALOR RCL DO MÊS É O DA CONFIGURAÇÃO: Vincular Receita Corrente Líquida
            stSql = stSql || '
                   , CASE WHEN COALESCE(CAST(orcamento.fn_somatorio_balancete_receita(publico.fn_mascarareduzida(conta_receita.cod_estrutural), '''||stDataIni||''', '''||stDataFim||''') * -1  AS NUMERIC(14,2)), 0.00) = 0.00
                          THEN (SELECT COALESCE(SUM(valor), 0.00) FROM stn.receita_corrente_liquida WHERE mes ='||SUBSTR(stDataIni,4,2)::INTEGER||' AND ano = '''||SUBSTR(stDataIni,7,4)::INTEGER||''')
                          ELSE COALESCE(CAST(orcamento.fn_somatorio_balancete_receita(publico.fn_mascarareduzida(conta_receita.cod_estrutural), '''||stDataIni||''', '''||stDataFim||''') * -1  AS NUMERIC(14,2)), 0.00)
                      END AS mes_'||i;
        ELSE
            stSql = stSql || '
                   , COALESCE(CAST(orcamento.fn_somatorio_balancete_receita(publico.fn_mascarareduzida(conta_receita.cod_estrutural), '''||stDataIni||''', '''||stDataFim||''') * -1  AS NUMERIC(14,2)), 0.00) AS mes_'||i;
        END IF;
        i := i - 1;
    END LOOP;

    stSql := stSql ||'
                FROM orcamento.conta_receita
               WHERE conta_receita.cod_estrutural LIKE ''' || substr( cod_estrutural, 3,16)||'%''
                 AND publico.fn_nivel(conta_receita.cod_estrutural) = '||inNivel-1||'
                 AND conta_receita.exercicio = '''||inExercicio||'''
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
       RETURN next reRegistro;
    END LOOP;

RETURN;
END;
$$ LANGUAGE 'plpgsql';
