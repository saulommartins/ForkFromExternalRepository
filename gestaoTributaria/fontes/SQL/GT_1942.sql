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
* $Id: GT_1942.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.94.2
*/


----------------
-- Ticket #13763
----------------

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
VALUES ( '2008'
     , 33
     , 'secretaria'
     , ''
     );

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
VALUES ( '2008'
     , 33
     , 'coordenador'
     , ''
     );

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
VALUES ( '2008'
     , 33
     , 'chefe_departamento'
     , ''
     );

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
VALUES ( '2008'
     , 33
     , 'prazo_regularizacao_debitos'
     , ''
     );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2415
          , 307
          , 'FMManterConfiguracaoDocumento.php'
          , 'documento'
          , 4
          , ''
          , 'Configurar Documentos'
          );


----------------
-- Ticket #13969
----------------

CREATE OR REPLACE FUNCTION manutencao () RETURNS VOID AS $$
DECLARE
    stAux      VARCHAR;
BEGIN

    SELECT valor
      INTO stAux
      FROM administracao.configuracao
     WHERE parametro  = 'cnpj'
       AND exercicio  = '2008'
       AND cod_modulo = 2;

    IF FOUND THEN

        INSERT INTO administracao.arquivos_documento        VALUES ( (SELECT MAX(cod_arquivo) + 1    from administracao.arquivos_documento),'notificacaoCobrancaUirauna.odt', '7afe3f8ca861bb7ae2c1d3f1b3409d05', true );
        INSERT INTO administracao.modelo_documento          VALUES ( (SELECT MAX(cod_documento) + 1  from administracao.modelo_documento), 'Notificação Cobrança', 'notificacaoAcordo.agt', 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1648, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1649, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1637, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1638, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1634, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1635, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (1636, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #13800
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2418
          , 366
          , 'FLExtratoDivida.php'
          , 'incluir'
          , 4
          , ''
          , 'Extrato de Dívida Ativa'
          );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 5
         , 33
         , 4
         , 'Extrato de Dívida Ativa'
         , 'extratoDividaAtiva.rptdesign'
         );


----------------
-- Ticket #14024
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stAux   VARCHAR;
BEGIN
    SELECT valor
      INTO stAux
      FROM administracao.configuracao
     WHERE parametro = 'cnpj'
       AND exercicio = '2008'
       AND valor     = '13805528000180';

    IF FOUND THEN
        UPDATE arrecadacao.modelo_carne
           SET nom_modelo = 'Carne I.P.T.U.'
         WHERE cod_modelo = 8;

        UPDATE arrecadacao.modelo_carne
           SET nom_modelo = 'Carne I.P.T.U. 2007'
         WHERE cod_modelo = 5;

    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();
