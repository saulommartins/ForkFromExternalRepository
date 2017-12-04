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
* $Revision: 46946 $
* $Name$
* $Author: tonismar $
* $Date: 2012-06-29 13:30:55 -0300 (Fri, 29 Jun 2012) $
*
* Casos de uso: uc-02.00.00
* Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.6  2007/07/30 18:28:33  tonismar
alteração de uc-00.00.00 para uc-02.00.00

Revision 1.5  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION fn_transparenciaExportacaoEmpenho(varchar,varchar,varchar,varchar) RETURNS SETOF record AS $$
DECLARE
    stExercicio     ALIAS FOR $1    ;
    stDataInicial   ALIAS FOR $2    ;
    stDataFinal     ALIAS FOR $3    ;
    stCodEntidade   ALIAS FOR $4    ;
    stSql           VARCHAR := '' ;
    stOut           VARCHAR := '' ;
    raRegistro      RECORD          ;
    arDados         VARCHAR[] := array[0];
BEGIN
stSql = '
    -- Select para soma do itens  --> data vem de empenho.empenho.dt_empenho
------------------------------------------------------------------------------

SELECT  tabela.*
        ,0 as caracteristica
        ,0 as modalidade
        ,''''::text as nro_licitacao
        ,''''::text as nom_modalidades
        ,''N''::text as preco
FROM
    tcers.fn_exportacao_Empenho_Soma_Dos_Itens('''||stExercicio||''','''||stDataInicial||''','''||stDataFinal||''','''||stCodEntidade||''')
as
    tabela
        (
            num_orgao       integer         ,
            num_unidade     integer         ,
            cod_funcao      integer         ,
            cod_subfuncao   integer         ,
            cod_programa    integer         ,
            num_pao         integer         ,
            cod_recurso     integer         ,
            cod_estrutural  varchar         ,
            cod_empenho     integer         ,
            dt_empenho      date            ,
            vl_empenhado    numeric         ,
            sinal           varchar         ,
            cgm             integer         ,
            historico       varchar         ,
            cod_pre_empenho integer         ,
            exercicio       char(4)            ,
            cod_entidade    integer         ,
            ordem           integer         ,
            oid             oid
        )
------------------------------------------------------------------------------
UNION  -- FAZ A UNIAO COM O SEBUNDO BLOCO , ITENS ANULADOS
------------------------------------------------------------------------------
SELECT  tabela.*
        ,0 as caracteristica
        ,0 as modalidade
        ,''''::text as nro_licitacao
        ,''''::text as nom_modalidades
        ,''N''::text as preco
FROM
    tcers.fn_exportacao_Empenho_Itens_Anulados('''||stExercicio||''','''||stDataInicial||''','''||stDataFinal||''','''||stCodEntidade||''')
as
    tabela
        (
            num_orgao       integer         ,
            num_unidade     integer         ,
            cod_funcao      integer         ,
            cod_subfuncao   integer         ,
            cod_programa    integer         ,
            num_pao         integer         ,
            cod_recurso     integer         ,
            cod_estrutural  varchar         ,
            cod_empenho     integer         ,
            dt_empenho      date            ,
            vl_empenhado    numeric         ,
            sinal           varchar         ,
            cgm             integer         ,
            historico       varchar         ,
            cod_pre_empenho integer         ,
            exercicio       char(4)         ,
            cod_entidade    integer         ,
            ordem           integer         ,
            oid             oid
        )

------------------------------------------------------------------------------
UNION -- FAZ UNIAO COM O 3o Bloco , restos a pagar(anos anteriores)
-----------------------------------------------------------------------------
--SELECT (

    SELECT  tabela.*
            ,0 as caracteristica
            ,0 as modalidade
            ,''''::text as nro_licitacao
            ,''''::text as nom_modalidades
            ,''N''::text as preco
    FROM
    tcers.fn_exportacao_Empenho_Restos_Pagar('''||stExercicio||''','''||stCodEntidade||''')
    as
    tabela
        (
            num_orgao       integer         ,
            num_unidade     integer         ,
            cod_funcao      integer         ,
            cod_subfuncao   integer         ,
            cod_programa    integer         ,
            num_pao         integer         ,
            cod_recurso     integer         ,
            cod_estrutural  varchar         ,
            cod_empenho     integer         ,
            dt_empenho      date            ,
            vl_empenhado    numeric         ,
            sinal           varchar         ,
            cgm             integer         ,
            historico       varchar         ,
            cod_pre_empenho integer         ,
            exercicio       char(4)         ,
            cod_entidade    integer         ,
            ordem           integer         ,
            oid             oid
        )
    where tabela.vl_empenhado > 0

--)


';
        -- Encerra conteudo do sql

    FOR raRegistro IN EXECUTE stSql
    LOOP

        arDados := tcers.fn_exportacao_dados_empenho(raRegistro.cod_empenho,raRegistro.exercicio,raRegistro.cod_entidade);
        raRegistro.num_orgao        := to_number(arDados[1], '9999999999');
        raRegistro.num_unidade      := to_number(arDados[2], '9999999999');
        raRegistro.cod_funcao       := to_number(arDados[3], '9999999999');
        raRegistro.cod_subfuncao    := to_number(arDados[4], '9999999999');
        raRegistro.cod_programa     := to_number(arDados[5], '9999999999');
        raRegistro.num_pao          := to_number(arDados[6], '9999999999');
        raRegistro.cod_estrutural   := arDados[7];
        raRegistro.cod_recurso      := to_number(arDados[8], '9999999999');
        raRegistro.caracteristica   :=  (
                                        select  case 
                                                     when valor::integer = 1 then 000 
                                                     when valor::integer = 2 then 501 
                                                     when valor::integer = 3 then 502 
                                                     when valor::integer = 4 then 901 
                                                     when valor::integer = 5 then 902 
                                                     when valor::integer = 6 then 903 
                                                     when valor::integer = 7 then 904 
                                                     when valor::integer = 8 then 905 
                                                     when valor::integer = 9 then 906 
                                                end as caracteristica
                                        from    empenho.atributo_empenho_valor
                                        where   exercicio       = raRegistro.exercicio
                                        and     cod_pre_empenho = raRegistro.cod_pre_empenho
                                        and     cod_modulo      = 10
                                        and     cod_cadastro    = 1
                                        and     cod_atributo    = 2001
                                        and     timestamp       = (SELECT MAX(timestamp) 
                                                                     FROM empenho.atributo_empenho_valor
                                                                    WHERE exercicio           = raRegistro.exercicio
                                                                      and     cod_pre_empenho = raRegistro.cod_pre_empenho
                                                                      and     cod_modulo      = 10
                                                                      and     cod_cadastro    = 1
                                                                      and     cod_atributo    = 2001)
                                        );

        IF raRegistro.caracteristica IS NULL THEN
            raRegistro.caracteristica   := 000;
        END IF;
       
        raRegistro.modalidade      :=  (
                                        select  valor as modalidade
                                        from    empenho.atributo_empenho_valor
                                        where   exercicio       = raRegistro.exercicio
                                        and     cod_pre_empenho = raRegistro.cod_pre_empenho
                                        and     cod_modulo      = 10
                                        and     cod_cadastro    = 1
                                        and     cod_atributo    = 101
                                        and     timestamp       = (SELECT MAX(timestamp)
                                                                     FROM empenho.atributo_empenho_valor
                                                                    WHERE exercicio           = raRegistro.exercicio
                                                                      and     cod_pre_empenho = raRegistro.cod_pre_empenho
                                                                      and     cod_modulo      = 10
                                                                      and     cod_cadastro    = 1
                                                                      and     cod_atributo    = 101)
                                        );
                                        
        raRegistro.nom_modalidades  := (
                                        SELECT  valor_padrao as nom_modalidades
                                        FROM    administracao.atributo_valor_padrao
                                        WHERE   atributo_valor_padrao.cod_valor = raRegistro.modalidade
                                          AND   atributo_valor_padrao.cod_modulo = 10
                                          AND   atributo_valor_padrao.cod_cadastro = 1
                                          AND   atributo_valor_padrao.cod_atributo = 101
                                        );
                                        
        raRegistro.nro_licitacao   :=  (
                                        select  coalesce(valor,null)::text as nro_licitacao
                                        from    empenho.atributo_empenho_valor
                                        where   exercicio       = raRegistro.exercicio
                                        and     cod_pre_empenho = raRegistro.cod_pre_empenho
                                        and     cod_modulo      = 10
                                        and     cod_cadastro    = 1
                                        and     cod_atributo    = 110
                                        and     timestamp       = (SELECT MAX(timestamp)
                                                                     FROM empenho.atributo_empenho_valor
                                                                    WHERE exercicio           = raRegistro.exercicio
                                                                      and     cod_pre_empenho = raRegistro.cod_pre_empenho
                                                                      and     cod_modulo      = 10
                                                                      and     cod_cadastro    = 1
                                                                      and     cod_atributo    = 110)
                                        );
        
        RETURN NEXT raRegistro;
    END LOOP;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';

