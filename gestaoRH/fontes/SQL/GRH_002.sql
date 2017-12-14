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
* $Revision: 28350 $
* $Name$
* $Author: gris $
* $Date: 2008-03-05 09:57:44 -0300 (Qua, 05 Mar 2008) $
*
* Versão 002.
*/

/*-----------
VERSÃO 1.51.0
*/

--Versão 1.51.0

--
--
--
-- Inclusão de link para a nova ação de configuração do banrisul.
-- Ticket #11224 Ticket #11225 Ticket #11226
Update administracao.acao set ordem = ordem + 1 WHERE cod_funcionalidade = 354 AND ordem > 5;
INSERT INTO administracao.acao ( cod_acao
, cod_funcionalidade
, nom_arquivo
, parametro
, ordem
, complemento_acao
, nom_acao)
VALUES ( 2177
, 354
, 'FMExportacaoBancoBanrisul.php'
, 'configurar'
, 6
, ''
, 'Exportação Banco Banrisul');

INSERT INTO administracao.acao
(cod_acao
, cod_funcionalidade
, nom_arquivo
, parametro
, ordem
, complemento_acao
, nom_acao)
VALUES (2178
, 353
, 'FLExportarRemessaBanrisul.php'
, 'exportar'
, 4
, ''
, 'Remessa Banco Banrisul');



select atualizarBanco('
CREATE TABLE ima.configuracao_convenio_banrisul (
cod_convenio INTEGER NOT NULL,
cod_banco INTEGER NOT NULL,
cod_agencia INTEGER NOT NULL,
cod_conta_corrente INTEGER NOT NULL,
cod_convenio_banco VARCHAR(20) NOT NULL,
CONSTRAINT pk_configuracao_convenio_banrisul_1 PRIMARY KEY(cod_convenio),
CONSTRAINT fk_configuracao_convenio_banrisul_1 FOREIGN KEY(cod_conta_corrente, cod_agencia, cod_banco) REFERENCES monetario.conta_corrente(cod_conta_corrente, cod_agencia, cod_banco)
);

GRANT INSERT, DELETE, UPDATE, SELECT ON ima.configuracao_convenio_banrisul TO GROUP urbem;
');





--
--  Ticket #11238 - Incluir parametro na tabela administracao.configuracao  parametro: num_sequencial_arquivo_banrisul e dt_num_sequencial_arquivo_banrisul
--

   Insert Into administracao.configuracao ( exercicio, cod_modulo, parametro, valor) VALUES ( '2008', 40, 'num_sequencial_arquivo_banrisul'   , '1');
   INSERT INTO administracao.configuracao ( exercicio, cod_modulo, parametro, valor) VALUES ( '2008', 40, 'dt_num_sequencial_arquivo_banrisul', '2008-01-01');


--
--
--
CREATE OR REPLACE FUNCTION public.manutencao() RETURNS VOID AS $$
DECLARE
   recRecno                            RECORD;
   varSchema                           VARCHAR;

   varNum_sequencial_arquivo_banrisul     VARCHAR;
   varDt_num_sequencial_arquivo_banrisul  VARCHAR;
BEGIN

    FOR recRecno IN
        SELECT entidade.cod_entidade, schema_rh.schema_nome
          FROM orcamento.entidade
             , administracao.entidade_rh
             , administracao.schema_rh
         WHERE entidade.exercicio      = entidade_rh.exercicio
           AND entidade.cod_entidade   = entidade_rh.cod_entidade
           AND entidade_rh.schema_cod  = schema_rh.schema_cod
           AND entidade.exercicio =  '2008'
           AND entidade.cod_entidade != (SELECT valor
                                           FROM administracao.configuracao
                                          WHERE parametro = 'cod_entidade_prefeitura'
                                            AND exercicio = '2008'
                                            AND cod_modulo = 8 )
      GROUP BY entidade.cod_entidade, schema_rh.schema_nome
      ORDER BY 2, 1
    LOOP

      varNum_sequencial_arquivo_banrisul      := 'num_sequencial_arquivo_banrisul_'    || BTRIM( TO_CHAR(recRecno.cod_entidade, '99'));
      varDt_num_sequencial_arquivo_banrisul   := 'dt_num_sequencial_arquivo_banrisul_' || BTRIM( TO_CHAR(recRecno.cod_entidade, '99'));

      INSERT INTO  administracao.configuracao ( exercicio, cod_modulo,   valor, parametro )
                                         SELECT '2008'   ,         40,     '1', varNum_sequencial_arquivo_banrisul
                                          WHERE 0 = ( SELECT COALESCE( count(1), 0)
                                                        FROM administracao.configuracao
                                                       WHERE exercicio  = '2008'
                                                         AND cod_modulo =  40
                                                         AND parametro  = varNum_sequencial_arquivo_banrisul )
      ;

      INSERT INTO  administracao.configuracao ( exercicio, cod_modulo,        valor, parametro )
                                         SELECT '2008'   ,         40, '2008-01-01', varDt_num_sequencial_arquivo_banrisul
                                          WHERE 0 = ( SELECT COALESCE( count(1), 0)
                                                        FROM administracao.configuracao
                                                       WHERE exercicio  = '2008'
                                                         AND cod_modulo =  40
                                                         AND parametro  = varDt_num_sequencial_arquivo_banrisul )
      ;


    END LOOP;

    RETURN;
END;
$$ LANGUAGE 'plpgsql'
;

Select public.manutencao();
DROP  FUNCTION  public.manutencao();

--
-- Ticket #11461 - Criado Link Exportação Banco BanPara.
--
   INSERT INTO administracao.acao ( cod_acao, cod_funcionalidade, nom_arquivo, parametro, ordem, complemento_acao, nom_acao) VALUES ( 2213, 353, 'FMExportarRemessaBanPara.php', 'exportar', 4, '', 'Remessa BanPará');

--
--  Ticket #11462 - Criado Link Para Configuração Exportação Banco BanPará
--
   INSERT INTO administracao.acao ( cod_acao, cod_funcionalidade, nom_arquivo, parametro, ordem, complemento_acao, nom_acao) VALUES ( 2186, 354, 'FMExportarRemessaBanPara.php', 'incluir', 13, '', 'Incluir Configuracao BanPará');
   INSERT INTO administracao.acao ( cod_acao, cod_funcionalidade, nom_arquivo, parametro, ordem, complemento_acao, nom_acao) VALUES ( 2187, 354, 'FLExportarRemessaBanPara.php', 'alterar', 14, '', 'Alterar Configuracao BanPará');
   INSERT INTO administracao.acao ( cod_acao, cod_funcionalidade, nom_arquivo, parametro, ordem, complemento_acao, nom_acao) VALUES ( 2188, 354, 'FLExportarRemessaBanPara.php', 'excluir', 15, '', 'Excluir Configuracao BanPará');



--
--  Ticket #11468 - Criação Configuração exportação bancária BanPara.
--
   select atualizarBanco('
      CREATE TABLE ima.banpara_empresa (
        cod_empresa     INTEGER   NOT NULL ,
        codigo          INTEGER   NOT NULL ,
        vigencia        DATE      NOT NULL   ,
      CONSTRAINT pk_empresa_banpara PRIMARY KEY(cod_empresa));
   ');

   select atualizarBanco('
      CREATE TABLE ima.banpara_orgao (
        cod_empresa     INTEGER        NOT NULL ,
        cod_orgao       INTEGER        NOT NULL ,
        codigo          INTEGER        NOT NULL ,
        descricao       VARCHAR(40)    NOT NULL ,
      CONSTRAINT pk_banpara_orgao   PRIMARY KEY(cod_empresa, cod_orgao))
   ');

   select atualizarBanco('
      CREATE TABLE ima.banpara_lotacao (
        cod_empresa     INTEGER        NOT NULL ,
        cod_orgao       INTEGER        NOT NULL ,
        cod_lotacao     INTEGER        NOT NULL ,
      CONSTRAINT pk_banpara_lotacao   PRIMARY KEY(cod_empresa, cod_orgao, cod_lotacao),
      CONSTRAINT fk_banpara_lotacao_1 FOREIGN KEY(cod_empresa, cod_orgao) REFERENCES ima.banpara_orgao(cod_empresa, cod_orgao),
      CONSTRAINT fk_banpara_lotacao_2 FOREIGN KEY(cod_lotacao) REFERENCES organograma.orgao(cod_orgao))
   ');

   select atualizarBanco('
      CREATE TABLE ima.banpara_local (
        cod_empresa     INTEGER        NOT NULL ,
        cod_orgao       INTEGER        NOT NULL ,
        cod_local       INTEGER        NOT NULL ,
      CONSTRAINT pk_banpara_local   PRIMARY KEY(cod_empresa, cod_orgao, cod_local),
      CONSTRAINT fk_banpara_local_1 FOREIGN KEY(cod_empresa, cod_orgao) REFERENCES ima.banpara_orgao(cod_empresa, cod_orgao),
      CONSTRAINT fk_banpara_local_2 FOREIGN KEY(cod_local) REFERENCES organograma.local(cod_local))
   ');


   select atualizarBanco('GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE ima.banpara_empresa     TO GROUP urbem;');
   select atualizarBanco('GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE ima.banpara_orgao       TO GROUP urbem;');
   select atualizarBanco('GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE ima.banpara_lotacao     TO GROUP urbem;');
   select atualizarBanco('GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE ima.banpara_local       TO GROUP urbem;');


--
--  Ticket #11634 - Criação d tabela ima.categoria_sefip.
--
   select atualizarBanco('
      CREATE TABLE ima.categoria_sefip (
        cod_categoria      INTEGER   NOT NULL ,
        cod_modalidade     INTEGER   NOT NULL   ,
      CONSTRAINT pk_categoria_sefip   PRIMARY KEY(cod_categoria, cod_modalidade),
      CONSTRAINT fk_categoria_sefip_1 FOREIGN KEY(cod_categoria) REFERENCES pessoal.categoria(cod_categoria),
      CONSTRAINT fk_categoria_sefip_2 FOREIGN KEY(cod_modalidade)REFERENCES ima.modalidade_recolhimento(cod_modalidade));
   ');

   select atualizarBanco('GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE ima.categoria_sefip       TO GROUP urbem;');



--
--  Ticket #11686 - Alterado o tipo de retorno da função pega0DataContagemTempoContrato para varchar
--
   --
   -- Insere a função.
   --
   CREATE OR REPLACE function public.manutencao_funcao( intCodmodulo       INTEGER
                                                      , intCodBiblioteca   INTEGER
                                                      , varNomeFunc        VARCHAR
                                                      , intCodTiporetorno INTEGER)
   RETURNS integer as $$
   DECLARE
      intCodFuncao INTEGER := 0;
      varAux       VARCHAR;
   BEGIN

      SELECT cod_funcao
        INTO intCodFuncao
        FROM administracao.funcao
       WHERE cod_modulo                = intCodmodulo
         AND cod_biblioteca            = intCodBiblioteca
         AND Lower(Btrim(nom_funcao))  = Lower(Btrim(varNomeFunc))
      ;

      IF FOUND THEN
         DELETE FROM administracao.corpo_funcao_externa  WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.funcao_externa        WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.funcao_referencia     WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.parametro             WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.variavel              WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.funcao                WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
      END IF;

     -- Raise Notice ' Entrou 1 ';

     SELECT (max(cod_funcao)+1)
       INTO intCodFuncao
       FROM administracao.funcao
      WHERE cod_modulo       = intCodmodulo
        AND cod_biblioteca   = intCodBiblioteca
     ;

     --varAux := varNomeFunc || '  -   ' || To_Char( intCodFuncao, '999999') ;
     --RAise Notice '=> % ', varAux;

     IF intCodFuncao IS NULL OR intCodFuncao = 0 THEN
        intCodFuncao := 1;
     END IF;

     INSERT INTO administracao.funcao  ( cod_modulo
                                       , cod_biblioteca
                                       , cod_funcao
                                       , cod_tipo_retorno
                                       , nom_funcao)
                                VALUES ( intCodmodulo
                                       , intCodBiblioteca
                                       , intCodFuncao
                                       , intCodTiporetorno
                                       , varNomeFunc);

      RETURN intCodFuncao;

   END;
   $$ LANGUAGE 'plpgsql';

   --
   -- Inclusão de Váriaveis.
   --
   CREATE OR REPLACE function public.manutencao_variavel( intCodmodulo       INTEGER
                                                        , intCodBiblioteca   INTEGER
                                                        , intCodFuncao       INTEGER
                                                        , varNomVariavel     VARCHAR
                                                        , intTipoVariavel    INTEGER)
   RETURNS integer as $$
   DECLARE
      intCodVariavel INTEGER := 0;
   BEGIN

      If intCodFuncao != 0 THEN
         SELECT COALESCE((max(cod_variavel)+1),1)
           INTO intCodVariavel
           FROM administracao.variavel
          WHERE cod_modulo       = intCodmodulo
            AND cod_biblioteca   = intCodBiblioteca
            AND cod_funcao       = intCodFuncao
         ;

         INSERT INTO administracao.variavel ( cod_modulo
                                            , cod_biblioteca
                                            , cod_funcao
                                            , cod_variavel
                                            , nom_variavel
                                            , cod_tipo )
                                     VALUES ( intCodmodulo
                                            , intCodBiblioteca
                                            , intCodFuncao
                                            , intCodVariavel
                                            , varNomVariavel
                                            , intTipoVariavel
                                            );
      END IF;

      RETURN intCodVariavel;
   END;
   $$ LANGUAGE 'plpgsql';


   --
   -- Inclusão de parametro.
   --
   CREATE OR REPLACE function public.manutencao_parametro( intCodmodulo       INTEGER
                                                         , intCodBiblioteca   INTEGER
                                                         , intCodFuncao       INTEGER
                                                         , intCodVariavel     INTEGER)
   RETURNS VOID as $$
   DECLARE
      intOrdem INTEGER := 0;
   BEGIN
      If intCodFuncao != 0 THEN
         SELECT COALESCE((max(ordem)+1),1)
           INTO intOrdem
           FROM administracao.parametro
          WHERE cod_modulo       = intCodmodulo
            AND cod_biblioteca   = intCodBiblioteca
            AND cod_funcao       = intCodFuncao
         ;

         INSERT INTO administracao.parametro ( cod_modulo
                                             , cod_biblioteca
                                             , cod_funcao
                                             , cod_variavel
                                             , ordem)
                                      VALUES ( intCodmodulo
                                             , intCodBiblioteca
                                             , intCodFuncao
                                             , intCodVariavel
                                             , intOrdem );
      End If;

      RETURN;
   END;
   $$ LANGUAGE 'plpgsql';

   --
   -- Função principal de inclusão no Gerador de Calculo.
   --
   CREATE OR REPLACE function public.manutencao() RETURNS VOID as $$
   DECLARE
      intCodFuncao   INTEGER;
      intCodVariavel INTEGER;
   BEGIN

      -- 1 | INTEIRO
      -- 2 | TEXTO
      -- 3 | BOOLEANO
      -- 4 | NUMERICO
      -- 5 | DATA

      intCodFuncao   := public.manutencao_funcao   (  27, 1, 'pega0DataContagemTempoContrato', 2);
      RETURN;
   END;
   $$ LANGUAGE 'plpgsql';

   --
   -- Execuçao  função.
   --
   Select public.manutencao();
   Drop Function public.manutencao();
   Drop Function public.manutencao_funcao(integer, integer, varchar, integer );
   Drop Function public.manutencao_variavel( integer, integer, integer, varchar, integer );
   Drop Function public.manutencao_parametro( integer, integer, integer, integer );


--
--  Ticket #11678 - Alteração de dado na tabela ima.modalidade_recolhimento
--
select atualizarBanco('
update ima.modalidade_recolhimento set descricao = \'Confirmação Informações anteriores - Rec/Decl ao FGTS e Decl à Previdência\' where cod_modalidade = 5
');

select atualizarBanco('
alter table folhapagamento.evento_calculado_dependente add column desdobramento char(1);
');
