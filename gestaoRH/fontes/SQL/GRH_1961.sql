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
* $Id: GRH_1961.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.96.1
*/

----------------
-- Ticket #14556
----------------

UPDATE administracao.acao 
   SET nom_acao = replace(nom_acao, 'Remessa ', '') 
 WHERE cod_funcionalidade = 353 
   AND cod_acao IN (1995, 1703, 1863, 2178, 2213, 2222);
   
   
----------------
-- Ticket #14517
----------------

UPDATE administracao.tabelas_rh
   SET nome_tabela = 'beneficio_cadastro' 
 WHERE schema_cod = 5 
   AND nome_tabela = 'beneficio';


UPDATE administracao.tabelas_rh
   SET sequencia = 2 
 WHERE schema_cod = 5 
   AND nome_tabela = 'tipo_concessao_vale_transporte';

UPDATE administracao.tabelas_rh
   SET sequencia = 2 
 WHERE schema_cod = 2 
   AND nome_tabela = 'tipo_evento_beneficio';

UPDATE administracao.tabelas_rh
   SET nome_tabela = 'calendario_cadastro' 
 WHERE schema_cod = 6 
   AND nome_tabela = 'calendario';


   
----------------
-- Ticket #14664
----------------

create or replace function manutencao() returns void as $$
declare
    stSql varchar;
    stEntidade varchar:='';
    reRegistro record;
    reEntidades record;
    inCodValor integer;
    inCountRegistros integer;
    inCodEntidadePrincipal integer;
begin
    stSql := 'SELECT count(1)
                FROM administracao.tabelas_rh
               WHERE schema_cod = 7
                 AND nome_tabela = \'natureza_estabelecimento\'';
    inCountRegistros := selectIntoInteger(stSql);
    
    if inCountRegistros = 0 then
        stSql := 'INSERT INTO administracao.tabelas_rh (schema_cod, nome_tabela, sequencia) VALUES (7, \'natureza_estabelecimento\', 1)';
        execute stSql;
    end if;
    
    stSql := 'SELECT valor
                FROM administracao.configuracao
               WHERE parametro = \'cod_entidade_prefeitura\'
                 AND exercicio = \'2009\'';
    inCodEntidadePrincipal := selectIntoInteger(stSql);    

    stSql := 'SELECT entidade.cod_entidade
                  FROM orcamento.entidade
                 WHERE exercicio = \'2009\'
                   AND cod_entidade IN (SELECT cod_entidade
                                          FROM administracao.entidade_rh
                                         WHERE exercicio = \'2009\'
                                        GROUP BY cod_entidade)';

    for reEntidades in execute stSql loop
        if reEntidades.cod_entidade != inCodEntidadePrincipal then
            stEntidade := '_'||reEntidades.cod_entidade;
            
            -- verifica o numero de registros na tabela natureza_estabelecimento da entidade secundaria - se vazia insere registros
            stSql := 'SELECT count(1)
                        FROM ima'||stEntidade||'.natureza_estabelecimento';
            inCountRegistros := selectIntoInteger(stSql);
            
            -- se tabele vazia copia registros da entidade principal
            if inCountRegistros = 0 then
                stSql := 'INSERT INTO ima'||stEntidade||'.natureza_estabelecimento 
                               SELECT cod_natureza, exercicio_vigencia, descricao 
                                 FROM ima.natureza_estabelecimento';
                execute stSql;
            end if;            
            
        end if;
        
    end loop;
end
$$ language 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

