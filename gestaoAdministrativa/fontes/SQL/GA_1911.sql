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
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: GA_1911.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.91.1
*/


-------------------------------------------------
-- SOLICITADO POR DIEGO LEMOS DE SOUZA - 20081010
-------------------------------------------------

create or replace function manutencao() returns void as $$
declare
    stSql varchar;
    stEntidade varchar:='';
    reRegistro record;
    reEntidades record;
    inCodValor integer;
    inCodEntidadePrincipal integer;
begin
    stSql := 'SELECT valor
                FROM administracao.configuracao
               WHERE parametro = \'cod_entidade_prefeitura\'
                 AND exercicio = \'2008\'';
    inCodEntidadePrincipal := selectIntoInteger(stSql);

    stSql := 'SELECT entidade.cod_entidade
                  FROM orcamento.entidade
                 WHERE exercicio = \'2008\'
                   AND cod_entidade IN (SELECT cod_entidade
                                          FROM administracao.entidade_rh
                                         WHERE exercicio = \'2008\'
                                        GROUP BY cod_entidade)';

    for reEntidades in execute stSql loop
        if reEntidades.cod_entidade != inCodEntidadePrincipal then
            stEntidade := '_'||reEntidades.cod_entidade;
        else
            stEntidade := '';
        end if;

        stSql := 'select atributo_contrato_servidor_valor.*
                    from pessoal'||stEntidade||'.atributo_contrato_servidor_valor
                where cod_atributo = 11
                    and trim(valor) not in (select cod_valor 
                                            from administracao.atributo_valor_padrao 
                                            where cod_atributo = 11 
                                                and cod_cadastro = 5 
                                                and cod_modulo = 22)
                    and trim(valor) <> \'\'';
        for reRegistro in execute stSql loop
            stSql := 'select cod_valor
                        from administracao.atributo_valor_padrao
                    where valor_padrao = \''||trim(reRegistro.valor)||'\'
                        and cod_atributo = '||reRegistro.cod_atributo||' 
                        and cod_cadastro = '||reRegistro.cod_cadastro||' 
                        and cod_modulo = '||reRegistro.cod_modulo;
            inCodValor := selectIntoInteger(stSql);
            if inCodValor is not null then
    --             raise notice '%',reRegistro.valor;
    --             raise notice 'Codigo: %',inCodValor;
                stSql := 'update pessoal'||stEntidade||'.atributo_contrato_servidor_valor
                            set valor = '||inCodValor||'
                        where cod_atributo = '||reRegistro.cod_atributo||' 
                            and cod_cadastro = '||reRegistro.cod_cadastro||' 
                            and cod_modulo = '||reRegistro.cod_modulo||'
                            and cod_cadastro = '||reRegistro.cod_cadastro||' 
                            and timestamp = \''||trim(reRegistro.timestamp)||'\'
                            and valor = \''||trim(reRegistro.valor)||'\'';
    --             raise notice '%',stSql;
                execute stSql;
            end if;
        end loop;
    end loop;
end
$$ language 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();
