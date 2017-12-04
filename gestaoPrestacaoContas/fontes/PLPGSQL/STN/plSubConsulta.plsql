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
CREATE OR REPLACE FUNCTION stn.sub_consulta_rcl (varchar, varchar, integer) RETURNS SETOF RECORD AS '
DECLARE
    dtData          ALIAS FOR $1;
    cod_estrutural ALIAS FOR $2;
    inNivel         ALIAS FOR $3;

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
    stSql             VARCHAR :='''';

BEGIN

    inAno :=  substr(dtData, 7, 4 ) ;
    inMes :=  substr(dtData, 4, 2 ) ; 
    
    inExercicio := inAno;
    
    i := 1;
    while i <= 12 loop
        if ( inMes < 10 ) then
            stMes := ''0'' || inMes;
        else
            stMes := inMes;
        end if;
    
        arDatas[i] :=  ''01/'' || stMes || ''/''|| inAno;
    
        i := i +1;
        inMes := inMes -1;
        if ( inMes = 0 ) then
            inAno := inAno -1;
            inMes := 12;
        end if;
    end loop;
    
    stDataIni :=  ''01'' || substr(dtData,3,8) ;
    stDataFim := dtData;
    
    stSql := '' select cast ( plano_conta.cod_conta                      as varchar ) as cod_conta
                     ,cast ( 

                        coalesce(  stn.tituloRCL( publico.fn_mascarareduzida(plano_conta.cod_estrutural))
                                 , plano_conta.nom_conta )              as varchar ) as nom_conta

                     ,cast ( plano_conta.cod_estrutural                 as varchar ) as cod_estrutural '';
    
    i := 12;
    while i >= 1 loop
            stDataIni := arDatas[i];
            inDia := stn.calculaNrDiasAnoMes(  substr(stDataIni,7,4)::integer, substr(stDataIni,4,2)::integer );
            stDataFim := inDia ||  substr(stDataIni,3,8);

            --if( substr(stDataIni, 7, 4) < 2008 ) then
            stSql = stSql || ''
                     ,coalesce(  cast ( orcamento.fn_somatorio_balancete_receita( 
                                                 publico.fn_mascarareduzida(  substr( plano_conta.cod_estrutural, 3,16))
                                                ,''''''||stDataIni||'''''',
                                                 ''''''||stDataFim||'''''') * -1  as numeric(14,2) ), 0 )  as mes_''||i ;
            --else
            --stSql = stSql || ''
            --         ,coalesce(  cast ( orcamento.fn_somatorio_balancete_receita(
            --                                     publico.fn_mascarareduzida( plano_conta.cod_estrutural )
            --                                    ,''''''||stDataIni||'''''',
            --                                     ''''''||stDataFim||'''''') * -1  as numeric(14,2) ), 0 )  as mes_''||i ;
            --end if;
            i := i - 1;
    end loop;
    
    stSql := stSql ||''  from contabilidade.plano_conta
               where
                    plano_conta.cod_estrutural like '''''' ||cod_estrutural||''%'''' 
                and publico.fn_nivel(plano_conta.cod_estrutural) = '''''' || inNivel || '''''' 
                and plano_conta.exercicio = ''''''|| inExercicio ||''''''
               
                                                        '';
    FOR reRegistro IN EXECUTE stSql
    LOOP
       RETURN next reRegistro;        
    END LOOP;
   
    
RETURN;

END;
'language 'plpgsql';
