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
* $Id: CalculoEconomico.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Função Abstrata de Calculo Economico                
*

* Casos d uso: uc-05.03.05 
*/

/*
$Log$
Revision 1.16  2007/02/23 17:40:07  dibueno
Bug #8441#

Revision 1.15  2007/02/16 09:55:51  dibueno
Bug #8441#

Revision 1.14  2007/01/17 12:49:11  dibueno
Verificação se a inscricao está baixada antes de executar o calculo

Revision 1.13  2007/01/08 17:09:59  dibueno
Alteraçao para buscar timestamp do faturamento e incluir no calculo economico

Revision 1.12  2006/11/29 09:55:29  fabio
corrigidas para recuperar apenas a ultima formula definida para um credito na tabela arrecadacao.PARAMETRO_CALCULO

Revision 1.11  2006/11/13 11:41:28  fabio
ajustes para a nova estrutura de calculo

Revision 1.10  2006/10/30 12:54:38  dibueno
coluna ordem, pertencendo a tabela arrecadacao.credito_grupo

Revision 1.9  2006/10/26 19:01:30  dibueno
Ajuste para ordenar os calculos de acordo com a tabela parametro_calculo

Revision 1.8  2006/10/24 12:34:57  fabio
correção da TAG de caso de uso

Revision 1.7  2006/10/20 17:57:05  cercato
setando ano_exercicio para recuperar o credito correto.

Revision 1.6  2006/09/26 15:20:40  domluc
Ajustes na captura de exception

Revision 1.5  2006/09/21 09:14:06  domluc
Adicionado insert na tabela de log de calculo

Revision 1.4  2006/09/18 16:12:40  domluc
*** empty log message ***

Revision 1.3  2006/09/18 16:11:19  domluc
Correção no Codigo da Modalidade

Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION CalculoEconomico( ) RETURNS boolean AS $$
DECLARE
    -- variaveis
    inExercicio     integer;    
    inCodGrupo      integer;
    inCodCredito    integer;
    inCodEspecie    integer;
    inCodGenero     integer;
    inCodNatureza   integer;    
    inSandVar       integer;
    stSandVar       numeric;
    boRetorno       boolean;
    stSql                   varchar;       
    reRecord                record;
    reRecord1               record;
    timestampFat            varchar;
    inInscricaoBaixa        integer;

    inCodCalculoCorrente    integer;
    inRegistroCorrente      integer;
    nuValor                 numeric;
    stCredito               varchar;
    stFuncao                varchar;
    boErro                  boolean = false;
    boExecutaSQL            boolean = false;
    stErro                  varchar :='';
    arTemp                  VARCHAR[];
    stTemp                  VARCHAR;
BEGIN 
    inCodGrupo   := recuperarbufferinteiro ( 'inCodGrupo' );
    -- recuperar buffer do exercicio
    inExercicio  := recuperarbufferinteiro ( 'inExercicio' );
    PERFORM removerbuffertexto('sterro');

    if ( inCodGrupo > 0 )  then
        inCodGrupo := recuperarbufferinteiro( 'inCodGrupo' );
        stSql := 'select apc.cod_modulo
                        , apc.cod_biblioteca
                        , apc.cod_funcao
                        , credito_grupo.*
                        , credito.*
                     from arrecadacao.credito_grupo
               inner join
                          (
                          select arrecadacao.parametro_calculo.*
                            from arrecadacao.parametro_calculo
                               , (
                                 select max (ocorrencia_credito) as ocorrencia
                                      , cod_credito
                                      , cod_especie
                                      , cod_genero
                                      , cod_natureza
                                   from arrecadacao.parametro_calculo
                               group by cod_credito
                                      , cod_especie
                                      , cod_genero
                                      , cod_natureza
                                 ) as apc
                           where arrecadacao.parametro_calculo.cod_credito        = apc.cod_credito
                             and arrecadacao.parametro_calculo.cod_especie        = apc.cod_especie
                             and arrecadacao.parametro_calculo.cod_genero         = apc.cod_genero
                             and arrecadacao.parametro_calculo.cod_natureza       = apc.cod_natureza
                             and arrecadacao.parametro_calculo.ocorrencia_credito = apc.ocorrencia
                          ) as apc
                       on apc.cod_credito  = credito_grupo.cod_credito
                      and apc.cod_especie  = credito_grupo.cod_especie
                      and apc.cod_genero   = credito_grupo.cod_genero
                      and apc.cod_natureza = credito_grupo.cod_natureza
               inner join monetario.credito
                       on apc.cod_credito  = credito.cod_credito
                      and apc.cod_especie  = credito.cod_especie
                      and apc.cod_genero   = credito.cod_genero
                      and apc.cod_natureza = credito.cod_natureza
                    where credito_grupo.cod_grupo     = '||inCodGrupo||'
                      and credito_grupo.ano_exercicio = '||quote_literal(inExercicio)||'
                 order by credito_grupo.ordem';
    else
        inCodCredito := recuperarbufferinteiro( 'inCodCredito' );
        inCodEspecie := recuperarbufferinteiro( 'inCodEspecie' );
        inCodGenero  := recuperarbufferinteiro( 'inCodGenero' );
        inCodNatureza:= recuperarbufferinteiro( 'inCodNatureza' );

        stSql := ' select
                        parametro_calculo.cod_modulo
                        , parametro_calculo.cod_biblioteca
                        , parametro_calculo.cod_funcao
                        , credito.*
                    from
                        (
                            select
                                parametro_calculo.*
                            from
                                arrecadacao.parametro_calculo
                                INNER JOIN (
                                    select
                                        max (ocorrencia_credito) as ocorrencia,
                                        cod_credito,
                                        cod_especie,
                                        cod_genero,
                                        cod_natureza
                                    from
                                        arrecadacao.parametro_calculo
                                    group by
                                        cod_credito,
                                        cod_especie,
                                        cod_genero,
                                        cod_natureza
                                ) as apc
                                ON parametro_calculo.cod_credito            = apc.cod_credito
                                and parametro_calculo.cod_especie           = apc.cod_especie
                                and parametro_calculo.cod_genero            = apc.cod_genero
                                and parametro_calculo.cod_natureza          = apc.cod_natureza
                                and parametro_calculo.ocorrencia_credito    = apc.ocorrencia
                        ) as parametro_calculo

                        INNER JOIN monetario.credito
                        on parametro_calculo.cod_credito        = credito.cod_credito
                        and parametro_calculo.cod_especie        = credito.cod_especie
                        and parametro_calculo.cod_genero         = credito.cod_genero
                        and parametro_calculo.cod_natureza       = credito.cod_natureza
                    WHERE
                        parametro_calculo.cod_credito       = '||inCodCredito||'
                        and parametro_calculo.cod_especie   = '||inCodEspecie||'
                        and parametro_calculo.cod_genero    = '||inCodGenero||'
                        and parametro_calculo.cod_natureza  = '||inCodNatureza||'';
    END IF;

    --timestampFat = date('');

    inRegistroCorrente := recuperarbufferinteiro ( 'inRegistro' );

    boExecutaSQL = false;

    FOR reRecord in execute stSql loop
        stCredito := ''||reRecord.cod_credito||'.'||reRecord.cod_especie||'.'||reRecord.cod_genero||'.'||reRecord.cod_natureza;
        stCredito := stCredito||' '||reRecord.descricao_credito;
        stFuncao  := ''||reRecord.cod_modulo||'.'||reRecord.cod_biblioteca||'.'||reRecord.cod_funcao;


        BEGIN

            boExecutaSQL = true;
            inInscricaoBaixa := null;

            IF ( stFuncao is not null ) THEN

                SELECT inscricao_economica
                INTO inInscricaoBaixa
                FROM economico.baixa_cadastro_economico
                WHERE inscricao_economica = inRegistroCorrente
                AND dt_termino is null ;

                IF inInscricaoBaixa is null THEN

                    -- executa calculo
		stSandVar := executagcnumericotributario( stFuncao);


                    nuValor := stSandVar::numeric;


                    -- recuperar proximo codigo de calculo
                    select coalesce(max(cod_calculo),0) + 1 into inCodCalculoCorrente from arrecadacao.calculo;
		stErro := getErro('');

              IF nuValor IS NULL OR stErro != '' THEN
                    arTemp := string_to_array(stErro, '#');
                    stTemp := 'Erro na Função '||arTemp[1]||'('||arTemp[2]||')';
                    IF stTemp IS NULL  THEN                        
                       stTemp := 'Erro na Função '||stErro;
                    END IF; 

                    stErro := stTemp;
                    insert into calculos_mensagem VALUES( inCodCalculoCorrente, stErro);
                    boErro := True;
                END IF;
  
                    -- guardar codigo de calculo
                    INSERT INTO calculos_correntes VALUES ( inCodCalculoCorrente , nuValor );

                    -- calculo
                    INSERT INTO arrecadacao.calculo
                    ( cod_calculo, cod_credito, cod_especie, cod_genero, cod_natureza, exercicio, valor, nro_parcelas, ativo )
                    VALUES
                    ( inCodCalculoCorrente, reRecord.cod_credito, reRecord.cod_especie, reRecord.cod_genero, reRecord.cod_natureza, inExercicio, nuValor, 0 , true);

                    -- log calculo
                    IF ( stErro != '' ) THEN
                        INSERT INTO arrecadacao.log_calculo VALUES ( inCodCalculoCorrente , stFuncao||stErro );
                    ELSE
                        INSERT INTO arrecadacao.log_calculo VALUES ( inCodCalculoCorrente , 'Ok' );
                    END IF;


                    -- cadastro_economico_faturamento
                    PERFORM 1
                       FROM arrecadacao.cadastro_economico_faturamento
                      WHERE inscricao_economica = inRegistroCorrente
                        AND timestamp           = now()::timestamp(3);

                    IF NOT FOUND THEN
                            INSERT INTO arrecadacao.cadastro_economico_faturamento ( inscricao_economica , competencia )
                            VALUES ( inRegistroCorrente , to_char(now()::timestamp,'mm/YYYY') );
                    END IF;


                    select max(timestamp)
                    into
                        timestampFat
                    from arrecadacao.cadastro_economico_faturamento
                    where inscricao_economica = inRegistroCorrente;

                    -- cadastro economico calculo
                    INSERT INTO arrecadacao.cadastro_economico_calculo  ( cod_calculo, timestamp, inscricao_economica )
                    VALUES  ( inCodCalculoCorrente , (timestampFat)::timestamp, inRegistroCorrente );

                    -- calculo erro
		IF boErro =  true then
                -- calculo erro
                	INSERT INTO calculos_erro VALUES ( inRegistroCorrente , stCredito , stFuncao , true , nuValor);
			boErro = true;
		else
			INSERT INTO calculos_erro VALUES ( inRegistroCorrente , stCredito , stFuncao , false , nuValor);
		end if;
                --    INSERT INTO calculos_erro VALUES ( inRegistroCorrente , stCredito , stFuncao , false , nuValor);


                ELSE
                    stErro := stErro||'#Inscrição Baixada';
                    boErro := true;
                END IF;

            ELSE
                stErro := stErro||'#Não há fórmula de cálculo para o crédito';
                boErro := true;
            END IF;


            EXCEPTION
                when others then
                    INSERT INTO calculos_erro VALUES ( inRegistroCorrente , stErro, stFuncao , true , 0.00);
                    INSERT INTO calculos_mensagem VALUES ( inRegistroCorrente, stErro );
		    stErro = getErro(stErro);
	            return false;
            END;

    end loop;



    -- MONTA MENSAGEM DE ERRO QDO NAO ENCONTRA FORMULA DE CALCULO
    IF ( boExecutaSQL = false ) THEN

        SELECT descricao_credito FROM monetario.credito
        INTO stCredito
        WHERE
        cod_credito = inCodCredito and cod_especie = inCodEspecie
        and cod_genero = inCodGenero and cod_natureza = inCodNatureza;
        

        stErro := 'Não há fórmula de cálculo para o crédito '||stCredito;
        boErro := true;

        INSERT INTO calculos_mensagem VALUES ( inRegistroCorrente, stErro );

    END IF;
    

    if ( boErro = false ) then
        -- buscar cgm 
        for reRecord in execute '
            select
                cadastro_economico.*
                , CASE WHEN ( cadastro_economico_autonomo.inscricao_economica is not null ) THEN
                    cadastro_economico_autonomo.numcgm
                  WHEN ( cadastro_economico_empresa_fato.inscricao_economica is not null ) then
                    cadastro_economico_empresa_fato.numcgm
                  WHEN ( cadastro_economico_empresa_direito.inscricao_economica is not null ) then
                    cadastro_economico_empresa_direito.numcgm
                end as numcgm
            from
                economico.cadastro_economico

                left join economico.cadastro_economico_autonomo
                on cadastro_economico_autonomo.inscricao_economica = cadastro_economico.inscricao_economica

                left join economico.cadastro_economico_empresa_fato
                on cadastro_economico_empresa_fato.inscricao_economica = cadastro_economico.inscricao_economica

                left join economico.cadastro_economico_empresa_direito
                on cadastro_economico_empresa_direito.inscricao_economica = cadastro_economico.inscricao_economica

            where
                cadastro_economico.inscricao_economica = '|| inRegistroCorrente||''

        loop
            for reRecord1 in execute ' select * from calculos_correntes' loop
            INSERT INTO arrecadacao.calculo_cgm VALUES ( reRecord1.cod_calculo , reRecord.numcgm );
            end loop;
        end loop;
        return true;
    else
        for reRecord1 in execute ' select * from calculos_correntes' loop
           update arrecadacao.calculo set ativo = false where cod_calculo = reRecord1.cod_calculo;
        end loop;
        return false;
    end if;

--EXCEPTION
--    when others then
--        return false;

END;
$$ LANGUAGE 'plpgsql';
