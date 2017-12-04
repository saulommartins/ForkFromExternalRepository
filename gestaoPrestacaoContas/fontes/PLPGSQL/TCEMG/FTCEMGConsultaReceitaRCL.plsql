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
CREATE OR REPLACE FUNCTION tcemg.sub_consulta_receita_rcl_novo (varchar, varchar, integer, integer) RETURNS SETOF RECORD AS $$
DECLARE
    dtData         ALIAS FOR $1;
    cod_estrutural ALIAS FOR $2;
    inNivel        ALIAS FOR $3;
    inTipoDados    ALIAS FOR $4;

    stDataIni varchar;
    stDataFim varchar;

    stMes varchar;

    inAno       integer;
    inMes       integer;
    inDia       integer;    
    inExercicio integer;
    i           integer;
        
    arDatas varchar[];

    reRegistro        RECORD;
    stSql             VARCHAR :='';

BEGIN

    inAno :=  substr(dtData, 7, 4 ) ;
    inMes :=  substr(dtData, 4, 2 ) ; 
    
    inExercicio := inAno;
    
    i := 1;
    while i <= 12 loop
        if ( inMes < 10 ) then
            stMes := '0' || inMes;
        else
            stMes := inMes;
        end if;
    
        arDatas[i] :=  '01/' || stMes || '/'|| inAno;
    
        i := i +1;
        inMes := inMes -1;
        if ( inMes = 0 ) then
            inAno := inAno -1;
            inMes := 12;
        end if;
    end loop;
    
    stDataIni :=  '01' || substr(dtData,3,8) ;
    stDataFim := dtData;
    
    stSql := '
       SELECT CAST ( conta_receita.cod_conta AS VARCHAR ) AS cod_conta
            , CAST ( COALESCE(  stn.tituloRCL( publico.fn_mascarareduzida(conta_receita.cod_estrutural)), conta_receita.descricao ) AS VARCHAR ) AS nom_conta
            , CAST ( conta_receita.cod_estrutural AS VARCHAR ) AS cod_estrutural ';
    
    i := 12;
    while i >= 1 loop
            stDataIni := arDatas[i];
            inDia := stn.calculaNrDiasAnoMes(  substr(stDataIni,7,4)::integer, substr(stDataIni,4,2)::integer );
            stDataFim := inDia ||  substr(stDataIni,3,8);
            
            IF inNivel = 2 AND cod_estrutural = '4.1' AND inTipoDados = 1 THEN
            stSql = stSql || '
            , CASE WHEN COALESCE( CAST( orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida( conta_receita.cod_estrutural ), '''||stDataIni||''', '''||stDataFim||''') * -1  AS NUMERIC(14,2)), 0) = 0 AND '||substr(stDataIni,7,4)::integer||' < 2014
                   THEN (SELECT COALESCE(SUM(valor), 0) FROM stn.receita_corrente_liquida WHERE mes = '||substr(stDataIni,4,2)::integer||' AND ano = '''||substr(stDataIni,7,4)::integer||''')
                   ELSE COALESCE( CAST( orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida( conta_receita.cod_estrutural ), '''||stDataIni||''', '''||stDataFim||''') * -1  AS NUMERIC(14,2)), 0)
               END AS mes_'||i;
            ELSE
            stSql = stSql || '
            , COALESCE( CAST( orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida( conta_receita.cod_estrutural ), '''||stDataIni||''', '''||stDataFim||''') * -1  AS NUMERIC(14,2)), 0) AS mes_'||i ;
            END IF;
            i := i - 1;
    end loop;
    
    stSql := stSql ||'
         FROM orcamento.conta_receita
        WHERE conta_receita.cod_estrutural LIKE ''' ||substr( cod_estrutural, 3,16)||'%'' 
          AND publico.fn_nivel(conta_receita.cod_estrutural) = ''' || inNivel-1 || ''' 
          AND conta_receita.exercicio = '''|| inExercicio ||'''
            
            ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
       RETURN next reRegistro;        
    END LOOP;
   
    
RETURN;

END;
$$ language 'plpgsql';
