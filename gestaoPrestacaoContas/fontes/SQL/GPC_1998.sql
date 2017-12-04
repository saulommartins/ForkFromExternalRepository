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
* $Id:$
*
* Versão 1.99.8
*/

----------------
-- Ticket #17041
----------------

INSERT
  INTO administracao.modulo
     ( cod_modulo
     , cod_responsavel
     , nom_modulo
     , nom_diretorio
     , ordem
     , cod_gestao
     )
VALUES
     ( 56
     , 0
     , 'TCE - AM'
     , 'TCEAM/'
     , 37
     , 6
     );

INSERT
  INTO administracao.funcionalidade
     ( cod_funcionalidade
     , cod_modulo
     , nom_funcionalidade
     , nom_diretorio
     , ordem
     )
VALUES
     ( 467
     , 56
     , 'Configuração'
     , 'instancias/configuracao/'
     , 1
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     )
VALUES
     ( 2789
     , 467
     , 'FMManterOrcamento.php'
     , 'incluir'
     , 1
     , 'Configurar Orçamento'
     , 'Definir as Configurações do Orçamento'
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     )
VALUES
     ( 2790
     , 467
     , 'FLVincularPlanoTCE.php'
     , 'incluir'
     , 2
     , ''
     , 'Vincular Plano TCE'
     );


INSERT
  INTO administracao.funcionalidade
     ( cod_funcionalidade
     , cod_modulo
     , nom_funcionalidade
     , nom_diretorio
     , ordem
     )
VALUES
     ( 468
     , 56
     , 'Exportação'
     , 'instancias/exportacao/'
     , 2
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     )
VALUES
     ( 2791
     , 468
     , 'FLExportacao.php'
     , 'orcamento'
     , 1
     , ''
     , 'Orçamento'
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     )
VALUES
     ( 2792
     , 468
     , 'FLExportacao.php'
     , 'informes'
     , 2
     , ''
     , 'Informes Mensais'
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     )
VALUES
     ( 2793
     , 467
     , 'FMManterUnidadeGestora.php'
     , 'incluir'
     , 3
     , 'Definir Unidades Gestoras'
     , 'Definir Código TCE para Unidades'
     );


----------------
-- Ticket #17271
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM pg_tables
      WHERE tablename = 'matriculas'
        AND schemaname = 'tcepb'
          ;
    IF NOT FOUND THEN
        CREATE TABLE tcepb.matriculas (
            numcgm      INTEGER         NOT NULL,
            periodo     VARCHAR(6)      NOT NULL,
            CONSTRAINT pk_matriculas    PRIMARY KEY       (numcgm),
            CONSTRAINT fk_matriculas_1  FOREIGN KEY       (numcgm)
                                        REFERENCES sw_cgm (numcgm)
        );
        GRANT ALL ON tcepb.matriculas TO GROUP urbem;
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #17328
----------------

CREATE TABLE tceam.arquivo_contas (
      cod_conta         INTEGER
    , exercicio         CHAR(4)
    , mes               CHAR(2)
);
GRANT ALL ON tceam.arquivo_contas TO GROUP urbem;

CREATE TABLE tceam.elenco_contas_tce (
    cod_elenco          INTEGER         NOT NULL,
    exercicio           VARCHAR(4)      NOT NULL,
    seq                 INTEGER         NOT NULL,
    cod_conta_tce       VARCHAR(25)     NOT NULL,
    descricao           VARCHAR         NOT NULL,
    nivel               CHAR(1)         NOT NULL,
    CONSTRAINT pk_elenco_contas_tce           PRIMARY KEY (cod_elenco, exercicio)
);
GRANT ALL ON tceam.elenco_contas_tce TO GROUP urbem;

CREATE TABLE tceam.vinculo_elenco_plano_contas (
    cod_plano               INTEGER                 NOT NULL,
    exercicio_plano         VARCHAR(4)              NOT NULL,
    cod_elenco              INTEGER                 NOT NULL,
    exercicio_elenco        VARCHAR(4)              NOT NULL,
    CONSTRAINT pk_vinculo_elenco_plano_contas       PRIMARY KEY (cod_plano, exercicio_plano),
    CONSTRAINT fk_vinculo_elenco_plano_contas_1     FOREIGN KEY (cod_plano, exercicio_plano)
                                                        REFERENCES contabilidade.plano_analitica (cod_plano, exercicio),
    CONSTRAINT fk_vinculo_elenco_plano_contas_2     FOREIGN KEY (cod_elenco, exercicio_elenco)
                                                        REFERENCES tceam.elenco_contas_tce (cod_elenco, exercicio)
);
GRANT ALL ON tceam.vinculo_elenco_plano_contas TO GROUP urbem;

INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 1  , '1'                    , 'SISTEMA ORÇAMENTÁRIO'                                    , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 2  , '2'                    , 'SISTEMA FINANCEIRO'                                      , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 3  , '21'                   , 'ATIVO FINANCEIRO'                                        , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 4  , '211'                  , 'DISPONÍVEL'                                              , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 5  , '211.01'               , 'CAIXA'                                                   , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 6  , '211.02'               , 'BANCOS CONTA MOVIMENTO'                                  , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 7  , '211.02.01'            , 'BANCOS OFICIAIS'                                         , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 8  , '211.02.02'            , 'BANCOS NÃO OFICIAIS'                                     , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 9  , '211.03'               , 'EXATORES'                                                , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 10 , '211.04'               , 'OUTRAS DISPONIBILIDADES'                                 , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 11 , '212'                  , 'VINCULADO EM CONTA CORRENTE BANCÁRIA'                    , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 12 , '212.01'               , 'BANCOS OFICIAIS'                                         , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 13 , '212.02'               , 'BANCOS NÃO OFICIAIS'                                     , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 14 , '213'                  , 'REALIZÁVEL'                                              , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 15 , '213.01'               , 'APLICAÇOES FINANCEIRAS'                                  , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 16 , '213.01.01'            , 'BANCOS OFICIAIS'                                         , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 17 , '213.01.02'            , 'BANCOS NÃO OFICIAIS'                                     , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 18 , '213.02'               , 'PAGAMENTOS ANTECIPADOS'                                  , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 19 , '213.03'               , 'RESPONSABILIDADES FINANCEIRAS'                           , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 20 , '213.04'               , 'SUPRIMENTOS'                                             , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 21 , '213.05'               , 'OUTRAS OPERAÇÕES REALIZÁVEIS'                            , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 22 , '214'                  , 'CONVERSÃO MONETÁRIA DO SISTEMA FINANCEIRO'               , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 23 , '22'                   , 'PASSIVO FINANCEIRO'                                      , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 24 , '221'                  , 'EXIGÍVEL'                                                , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 25 , '221.01'               , 'RESTOS A PAGAR'                                          , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 26 , '221.02'               , 'RESTITUIÇÕES A PAGAR'                                    , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 27 , '221.03'               , 'CREDORES DIVERSOS'                                       , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 28 , '221.04'               , 'DÉBITOS DE TESOURARIA'                                   , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 29 , '221.05'               , 'DEPÓSITOS DE DIVERSAS ORIGENS'                           , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 30 , '221.05.01'            , 'INSS'                                                    , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 31 , '221.05.02'            , 'IPESC'                                                   , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 32 , '221.05.03'            , 'SEGUROS'                                                 , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 33 , '221.05.04'            , 'ASSOCIAÇÕES'                                             , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 34 , '221.05.05'            , 'IRRF'                                                    , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 35 , '221.05.06'            , 'DDO-DIVERSOS'                                            , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 312, '221.05.07'            , 'FUNDO DE PREVIDÊNCIA PRÓPRIO'                            , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 36 , '221.06'               , 'DEPÓSITOS ESPECIAIS'                                     , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 37 , '221.07'               , 'SERVIÇOS DA DÍVIDA A PAGAR'                              , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 38 , '221.08'               , 'DESPESAS EMPENHADAS A PAGAR'                             , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 39 , '221.09'               , 'OUTROS EXIGÍVEIS'                                        , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 40 , '23'                   , 'CONTAS OPERACIONAIS DO EXERCÍCIO'                        , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 41 , '231'                  , 'RECEITA ORÇAMENTÁRIA'                                    , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 42 , '231.01'               , 'RECEITAS CORRENTES'                                      , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 43 , '231.01.01'            , 'RECEITA TRIBUTÁRIA'                                      , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 44 , '231.01.01.01'         , 'IMPOSTOS'                                                , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 45 , '231.01.01.01.01'      , 'IMPOSTO S/PROPRIEDADE PREDIAL E TERRITORIAL URBANA'      , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 46 , '231.01.01.01.02'      , 'IMPOSTO S/TRANSMISSÃO DE BENS IMÓVEIS'                   , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 47 , '231.01.01.01.03'      , 'IMPOSTO S/SERVIÇOS DE QUALQUER NATUREZA'                 , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 49 , '231.01.01.01.05'      , 'OUTROS IMPOSTOS'                                         , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 313, '231.01.01.01.06'      , 'IRRF-IMPOSTO S/A RENDA E PROV. DE QUALQUER NAT.'         , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 50 , '231.01.01.02'         , 'TAXAS'                                                   , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 51 , '231.01.01.02.01'      , 'TAXA PELO EXERCÍCIO DO PODER DE POLÍCIA'                 , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 52 , '231.01.01.02.02'      , 'TAXA PELA PRESTAÇÃO DE SERVIÇOS'                         , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 53 , '231.01.01.02.03'      , 'OUTRAS TAXAS'                                            , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 54 , '231.01.01.03'         , 'CONTRIBUIÇÕES DE MELHORIA'                               , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 55 , '231.01.02'            , 'RECEITA DE CONTRIBUIÇÕES'                                , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 56 , '231.01.02.01'         , 'CONTRIBUIÇÕES SOCIAIS'                                   , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 57 , '231.01.02.02'         , 'CONTRIBUIÇÕES ECONÔMICAS'                                , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 58 , '231.01.03'            , 'RECEITA PATRIMONIAL'                                     , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 59 , '231.01.03.01'         , 'RECEITAS IMOBILIÁRIAS'                                   , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 60 , '231.01.03.02'         , 'RECEITAS DE VALORES MOBILIÁRIOS'                         , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 61 , '231.01.03.03'         , 'OUTRAS RECEITAS PATRIMONIAIS'                            , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 62 , '231.01.04'            , 'RECEITA AGROPECUÁRIA'                                    , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 63 , '231.01.05'            , 'RECEITA INDUSTRIAL'                                      , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 64 , '231.01.06'            , 'RECEITA DE SERVIÇOS'                                     , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 65 , '231.01.07'            , 'TRANSFERÊNCIAS CORRENTES'                                , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 66 , '231.01.07.01'         , 'TRANSFERÊNCIAS INTRAGOVERNAMENTAIS'                      , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 67 , '231.01.07.02'         , 'TRANSFERÊNCIAS INTERGOVERNAMENTAIS'                      , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 68 , '231.01.07.02.01'      , 'TRANSFERÊNCIAS DA UNIÃO'                                 , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 283, '231.01.07.02.01.01'   , 'PARTICIPAÇÃO NA RECEITA DA UNIÃO'                        , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 69 , '231.01.07.02.01.01.01', 'COTA-PARTE FUNDO PARTICIPAÇÃO MUNICÍPIOS'                , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 72 , '231.01.07.02.01.01.04', 'TRANSF.IMPOSTO S/PROPRIEDADE TERRITORIAL RURAL'          , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 73 , '231.01.07.02.01.01.05', 'COTA-PARTE IPI ESTADOS S/EXPORTAÇÃO'                     , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 284, '231.01.07.02.01.01.06', 'TRANSFERÊNCIAS DE RECURSOS DO FUNDEF'                    , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 75 , '231.01.07.02.01.01.07', 'COTA-PARTE DA CONTRIB. DO SALÁRIO-EDUCAÇÃO'              , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 74 , '231.01.07.02.01.01.08', 'COTA-PARTE VALOR PETRÓLEO BRUTO'                         , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 76 , '231.01.07.02.01.01.09', 'OUTRAS TRANSFERÊNCIAS DE IMPOSTOS DA UNIÃO'              , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 314, '231.01.07.02.01.01.10', 'AJUSTE DO FPM (LC 91/97)'                                , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 315, '231.01.07.02.01.01.11', 'TRANSFERÊNCIA DE RECURSOS-SIST. ÚNICO DE SAÚDE-SUS'      , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 322, '231.01.07.02.01.01.12', 'DEDUÇÃO DE RECEITA - FPM'                                 , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 323, '231.01.07.02.01.01.13', 'DEDUÇÃO DE RECEITA - IPI'                                 , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 324, '231.01.07.02.01.01.14', 'DEDUÇÃO DE RECEITA - L.C. N. 87/96 (DES. ICMS)'           , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 77 , '231.01.07.02.01.02'   , 'OUTRAS TRANSFERÊNCIAS DA UNIÃO'                          , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 316, '231.01.07.02.01.02.01', 'TRANSFERÊNCIA FINANCEIRA -  L.C.N.87/96 (DES. ICMS)'      , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 325, '231.01.07.02.01.02.99', 'DEMAIS TRANSFERÊNCIAS DA UNIÃO'                          , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 78 , '231.01.07.02.02'      , 'TRANFERÊNCIAS DOS ESTADOS'                               , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 286, '231.01.07.02.02.01'   , 'PARTICIPAÇÃO NA RECEITA DOS ESTADOS'                     , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 79 , '231.01.07.02.02.01.02', 'COTA-PARTE ICMS'                                         , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 80 , '231.01.07.02.02.01.03', 'COTA-PARTE IPVA'                                         , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 81 , '231.01.07.02.02.01.04', 'COTA-PARTE CAUSA MORTIS'                                 , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 82 , '231.01.07.02.02.01.05', 'TRANSFERÊNCIA DE OUTROS IMPOSTOS DO ESTADO'              , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 317, '231.01.07.02.02.01.06', 'DEDUÇÃO DE RECEITA -  ICMS'                               , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 83 , '231.01.07.02.02.02'   , 'OUTRAS TRANSFERÊNCIAS DOS ESTADOS'                       , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 84 , '231.01.07.02.03'      , 'TRANSFERÊNCIAS DOS MUNICÍPIOS'                           , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 85 , '231.01.07.02.04'      , 'TRANFERÊNCIAS DE CONVÊNIOS DIVERSOS'                     , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 319, '231.01.07.02.05'      , 'TRANFERÊNCIAS MULTIGOVERNAMENTAIS'                       , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 320, '231.01.07.02.05.01'   , 'TRANFERÊNCIAS DE RECURSOS DO FUNDEF'                     , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 321, '231.01.07.02.05.02'   , 'TRANFERÊNCIAS DE RECURSOS-COMPLEMENTAÇÃO DO FUNDEF'      , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 86 , '231.01.07.03'         , 'TRANSFERÊNCIAS DE INSTITUIÇÕES PRIVADAS'                 , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 87 , '231.01.07.04'         , 'TRANFERÊNCIAS DO EXTERIOR'                               , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 88 , '231.01.07.05'         , 'TRANSFERÊNCIAS DE PESSOAS'                               , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 89 , '231.01.08'            , 'OUTRAS RECEITAS CORRENTES'                               , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 90 , '231.01.08.01'         , 'MULTAS E JUROS DE MORA'                                  , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 91 , '231.01.08.02'         , 'INDENIZAÇÕES E RESTITUIÇÕES'                             , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 92 , '231.01.08.03'         , 'RECEITA DA DÍVIDA ATIVA'                                 , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 290, '231.01.08.03.01'      , 'RECEITA DA DÍVIDA ATIVA-TRIBUTÁRIA DE IMPOSTOS'          , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 291, '231.01.08.03.02'      , 'RECEITA DA DÍVIDA ATIVA-NÃO ORIGINÁRIA DE IMPOSTOS'      , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 93 , '231.01.08.04'         , 'RECEITAS DIVERSAS'                                       , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 308, '231.01.08.04.01'      , 'ANULAÇÃO DE RESTOS A PAGAR'                              , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 309, '231.01.08.04.02'      , 'OUTRAS RECEITAS DIVERSAS'                                , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 94 , '231.02'               , 'RECEITAS DE CAPITAL'                                     , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 95 , '231.02.01'            , 'OPERAÇÕES DE CRÉDITO'                                    , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 96 , '231.02.02'            , 'ALIENAÇÃO DE BENS'                                       , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 97 , '231.02.02.01'         , 'ALIENAÇÃO DE BENS MÓVEIS'                                , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 98 , '231.02.02.02'         , 'ALIENAÇÃO DE BENS IMÓVEIS'                               , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 99 , '231.02.03'            , 'AMORTIZAÇÃO DE EMPRÉSTIMOS'                              , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 100, '231.02.04'            , 'TRANSFERÊNCIA DE CAPITAL'                                , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 101, '231.02.04.01'         , 'TRANSFERÊNCIAS INTRAGOVERNAMENTAIS'                      , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 102, '231.02.04.02'         , 'TRANSFERÊNCIAS INTERGOVERNAMENTAIS'                      , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 103, '231.02.04.02.01'      , 'TRANSFERÊNCIAS DA UNIÃO'                                 , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 287, '231.02.04.02.01.01'   , 'PARTICIPAÇÃO NA RECEITA DA UNIÃO'                        , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 104, '231.02.04.02.01.01.01', 'COTA-PARTE FUNDO DE PARTICIPAÇÃO DOS MUNICÍPIOS'         , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 105, '231.02.04.02.01.01.02', 'COTA-PARTE DO FUNDO ESPECIAL'                            , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 107, '231.02.04.02.01.01.04', 'TRANSF.DO IMPOSTO S/PROPRIEDADE TERRITORIAL RURAL'       , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 108, '231.02.04.02.01.01.05', 'COTA-PARTE IPI- ESTADOS EXP. PROD. IND.'                 , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 109, '231.02.04.02.01.01.06', 'COTA-PARTE DA CONTRIBUIÇÃO SALÁRIO-EDUCAÇÃO'             , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 110, '231.02.04.02.01.01.07', 'OUTRAS TRANSFERÊNCIAS DE IMPOSTOS DA UNIÃO'              , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 111, '231.02.04.02.01.02'   , 'OUTRAS TRANSFERÊNCIAS DA UNIÃO'                          , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 112, '231.02.04.02.02'      , 'TRANSFERÊNCIAS DOS ESTADOS'                              , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 288, '231.02.04.02.02.01'   , 'PARTICIPAÇÃO NA RECEITA DOS ESTADOS'                     , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 113, '231.02.04.02.02.01.01', 'COTA-PARTE ICMS'                                         , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 114, '231.02.04.02.02.01.02', 'TRANSFERÊNCIAS DE OUTROS IMPOSTOS DO ESTADOS'            , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 115, '231.02.04.02.02.02'   , 'OUTRAS TRANSFERÊNCIAS DO ESTADO'                         , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 116, '231.02.04.02.03'      , 'TRANSFERÊNCIAS DOS MUNICÍPIOS'                           , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 117, '231.02.04.02.04'      , 'TRANSFERÊNCIAS DE CONVÊNIOS DIVERSOS'                    , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 118, '231.02.04.03'         , 'TRANSFERÊNCIAS DE INSTITUIÇÕES PRIVADAS'                 , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 119, '231.02.04.04'         , 'TRANSFERÊNCIAS DO EXTERIOR'                              , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 120, '231.02.04.05'         , 'TRANSFERÊNCIAS DE PESSOAS'                               , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 121, '231.02.05'            , 'OUTRAS RECEITAS DE CAPITAL'                              , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 122, '231.03'               , 'CONTAS DE RECEITA ORÇAMENTÁRIA A REGULARIZAR'            , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 123, '232'                  , 'DESPESA ORÇAMENTÁRIA'                                    , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 175, '24'                   , 'CONTAS DE INTERFERÊNCIA'                                 , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 176, '241'                  , 'TRANSFERÊNCIAS FINANCEIRAS'                              , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 177, '241.01'               , 'RESULTADO FINANCEIRO DO EXERCÍCIO'                       , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 326, '241.02'               , 'TRANSFERÊNCIAS FINANCEIRAS CONCEDIDAS'                   , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 178, '242'                  , 'CONVERSÃO MONETÁRIA'                                     , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 179, '243'                  , 'RECEITAS A CLASSIFICAR'                                  , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 180, '3'                    , 'SISTEMA PATRIMONIAL'                                     , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 181, '31'                   , 'ATIVO PERMANENTE'                                        , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 182, '311'                  , 'BENS MÓVEIS'                                             , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 183, '312'                  , 'BENS IMÓVEIS'                                            , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 184, '312.01'               , 'BENS IMÓVEIS DIVERSOS'                                   , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 185, '312.02'               , 'OBRAS EM ANDAMENTO'                                      , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 186, '313'                  , 'BENS DE NATUREZA INDUSTRIAL'                             , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 187, '314'                  , 'CRÉDITOS'                                                , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 188, '314.01'               , 'DÍVIDA ATIVA'                                            , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 189, '314.02'               , 'DEVEDORES DIVERSOS'                                      , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 190, '315'                  , 'VALORES'                                                 , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 191, '316'                  , 'DIVERSOS'                                                , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 192, '316.01'               , 'ALMOXARIFADO'                                            , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 193, '316.02'               , 'OUTROS BENS E DIREITOS'                                  , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 194, '32'                   , 'PASSIVO PERMANENTE'                                      , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 195, '321'                  , 'DÍVIDA FUNDADA'                                          , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 196, '321.01'               , 'DÍVIDA FUNDADA INTERNA'                                  , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 197, '321.02'               , 'DÍVIDA FUNDADA EXTERNA'                                  , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 198, '322'                  , 'DIVERSOS'                                                , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 318, '323'                  , 'DÉBITOS CONSOLIDADOS'                                    , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 199, '33'                   , 'SALDO PATRIMONIAL'                                       , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 200, '331'                  , 'ATIVO REAL LÍQUIDO'                                      , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 201, '332'                  , 'PASSIVO REAL A DESCOBERTO'                               , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 202, '34'                   , 'SISTEMA DE RESULTADO PATRIMONIAL'                        , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 203, '341'                  , 'VARIAÇÕES ATIVAS-RESULTANTE EXECUÇÃO ORCAMENTÁRIA'       , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 204, '341.01'               , 'RECEITA ORÇAMENTÁRIA REALIZADA'                          , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 205, '341.02'               , 'VA. REO-MUTAÇÕES PATRIMONIAIS'                           , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 206, '341.02.01'            , 'AQUISIÇÃO DE BENS MÓVEIS'                                , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 207, '341.02.02'            , 'CONSTRUÇÃO E AQUISIÇÃO DE BENS IMÓVEIS'                  , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 208, '341.02.03'            , 'CONSTRUÇÃO E AQUISIÇÃO DE BENS NATUR INDUSTRIAL'         , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 209, '341.02.04'            , 'FORMAÇÃO DE CRÉDITOS DIVERSOS'                           , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 210, '341.02.05'            , 'AQUISIÇÃO DE TÍTULOS E VALORES'                          , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 211, '341.02.06'            , 'AQUISIÇÃO DE BENS DIVERSOS'                              , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 212, '341.02.07'            , 'EMPRÉSTIMOS CONCEDIDOS'                                  , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 213, '341.02.08'            , 'AMORTIZAÇÃO DA DÍVIDA FUNDADA'                           , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 214, '341.02.09'            , 'AMORTIZAÇÃO DE ARRENDAMENTO MERCANTIL'                   , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 215, '341.02.10'            , 'AMORTIZAÇÃO DE DÉBITOS CONSOLIDADOS'                     , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 216, '341.02.11'            , 'APROPRIAÇÃO DE DESPESAS'                                 , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 217, '341.02.12'            , 'INCORPORAÇÃO DE ALMOXARIFADO'                            , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 218, '341.02.13'            , 'OUTRAS VA - REO -MUTAÇÕES PATRIMONIAIS'                  , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 219, '342'                  , 'VARIAÇÕES ATIVAS INDEPENDENTES EXEC. ORÇAMENTÁRIA'       , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 220, '342.01'               , 'VA -IEO - VARIAÇÕES PATRIMONIAIS'                        , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 221, '342.01.01'            , 'INCORPORAÇÃO DE BENS E VALORES'                          , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 222, '342.01.02'            , 'RECEBIMENTO DE BENS EM DOAÇÃO'                           , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 223, '342.01.03'            , 'REAVALIAÇÃO DE BENS E VALORES'                           , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 224, '342.01.04'            , 'INCORPORAÇÃO DE ALMOXARIFADO'                            , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 225, '342.01.05'            , 'INSCRIÇÃO DA DÍVIDA ATIVA'                               , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 226, '342.01.06'            , 'INSCRIÇÃO DE OUTROS CRÉDITOS'                            , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 227, '342.01.07'            , 'CANCELAMENTO DE DÍVIDAS PASSIVAS'                        , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 228, '342.01.08'            , 'BAIXA PASSIVO PRESCRITOS OU INDEVIDOS'                   , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 229, '342.01.09'            , 'TRANSFERÊNCIAS DE OBRAS EM ANDAMENTO P/CUSTO FINAL'      , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 230, '342.01.10'            , 'OUTRAS VA - IEO- VARIAÇÕES PATRIMONIAIS'                  , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 231, '343'                  , 'VARIAÇÕES PASSIVAS RESULTANTES EXEC. ORÇAMENTÁRIA'       , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 232, '343.01'               , 'DESPESAS ORÇAMENTÁRIAS REALIZADAS'                       , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 233, '343.02'               , 'VP - REO- MUTAÇÕES PATRIMONIAIS'                          , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 234, '343.02.01'            , 'ALIENAÇÃO DE BENS MÓVEIS'                                , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 235, '343.02.02'            , 'ALIENAÇÃO DE BENS IMÓVEIS'                               , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 236, '343.02.03'            , 'ALIENAÇÃO DE BENS DE NATUREZA INDUSTRIAL'                , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 237, '343.02.04'            , 'RECEBIMENTO DE OUTROS CRÉDITOS'                          , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 238, '343.02.05'            , 'ALIENAÇÃO DE TÍTULOS E VALORES'                          , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 239, '343.02.06'            , 'ALIENAÇÃO DE BENS DIVERSOS'                              , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 240, '343.02.07'            , 'RECEBIMENTO DE EMPRÉSTIMOS CONCEDIDOS'                   , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 241, '343.02.08'            , 'EMPRÉSTIMOS TOMADOS'                                     , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 242, '343.02.09'            , 'COBRANCA DA DÍVIDA ATIVA'                                , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 243, '343.02.10'            , 'OUTRAS VP- REO - MUTAÇÕES PATRIMONIAIS'                   , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 244, '344'                  , 'VARIAÇÕES PASSIVAS INDEPENDENTE DE EXEC. ORCAM.'         , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 245, '344.01'               , 'VP -  IEO -  VARIAÇÕES PATRIMONIAIS'                       , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 246, '344.01.01'            , 'BAIXA DE BENS INSERVÍVEIS'                               , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 247, '344.01.02'            , 'BAIXA DE BENS POR PERMUTA'                               , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 248, '344.01.03'            , 'BAIXA DE BENS POR DOAÇÃO'                                , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 249, '344.01.04'            , 'BAIXA DE BENS INCINERADOS'                               , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 250, '344.01.05'            , 'BAIXA DE BENS POR FURTO OU ROUBO'                        , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 251, '344.01.06'            , 'BAIXA DE ALMOXARIFADO'                                   , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 252, '344.01.07'            , 'DEPRECIAÇÃO DE BENS'                                     , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 253, '344.01.08'            , 'CANCELAMENTO DA DÍVIDA ATIVA'                            , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 254, '344.01.09'            , 'CANCELAMENTO DE CRÉDITOS'                                , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 255, '344.01.10'            , 'ENCAMPAÇÃO DE DÍVIDAS PASSIVAS'                          , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 256, '344.01.11'            , 'CORREÇÃO DE DÍVIDAS PASSIVAS'                            , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 257, '344.01.12'            , 'BAIXA DE TÍTULOS E VALORES'                              , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 258, '344.01.13'            , 'TRANSFERÊNCIA DE OBRAS EM ANDAMENTO P/CUSTO FINAL'       , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 259, '344.01.14'            , 'DEVOLUÇÃO DE BENS'                                       , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 260, '344.01.15'            , 'TRANSFERÊNCIA DE BENS EM PROCESSO DE COMPRA'             , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 261, '344.01.16'            , 'INSCRIÇÃO DE DÍVIDA'                                     , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 262, '344.01.17'            , 'OUTRAS VP -  IEO -  VARIAÇÕES PATRIMONIAIS'                , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 263, '345'                  , 'CONTAS DE INTERFERÊNCIA'                                 , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 264, '345.01'               , 'TRANSFERÊNCIAS PATRIMONIAIS DO EXERCÍCIO'                , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 265, '345.02'               , 'RESULTADO PATRIMONIAL DO EXERCÍCIO'                      , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 266, '345.03'               , 'CONVERSÃO MONETÁRIA'                                     , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 267, '4'                    , 'SISTEMA DE COMPENSÃO'                                    , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 268, '41'                   , 'ATIVO COMPENSADO'                                        , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 269, '411'                  , 'VALORES EM PODER DE TERCEIROS'                           , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 270, '412'                  , 'VALORES DE TERCEIROS'                                    , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 271, '413'                  , 'VALORES NOMINAIS EMITIDOS'                               , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 272, '414'                  , 'DIVERSOS'                                                , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 273, '415'                  , 'CONTRAPARTIDAS'                                          , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 274, '42'                   , 'PASSIVO COMPENSADO'                                      , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 275, '421'                  , 'CONTRAPARTIDAS'                                          , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 276, '421.01'               , 'CONTRAPARTIDAS SINTÉTICAS'                               , 'N');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 277, '421.01.01'            , 'CONTRAPARTIDA DE VALORES EM PODER TERCEIROS'             , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 278, '421.01.02'            , 'CONTRAPARTIDA DE VALORES DE TERCEIROS'                   , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 279, '421.01.03'            , 'CONTRAPARTIDA DE VALORES NOMINAIS EMITIDOS'              , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 280, '421.01.04'            , 'CONTRAPARTIDA DE RESPONSABILIDADE DIVERSAS'              , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 281, '422'                  , 'RESPONSABILIDADES DA UNIDADE'                            , 'S');
INSERT INTO tceam.elenco_contas_tce VALUES ((SELECT COALESCE(MAX(cod_elenco)+1, 1) FROM tceam.elenco_contas_tce), '2011', 282, '423'                  , 'CONVERSÃO MONETÁRIA'                                     , 'S');


----------------
-- Ticket #17329
----------------

CREATE TABLE tceam.configuracao_arquivo_licitacao (
    cod_mapa                        INTEGER         NOT NULL,
    exercicio                       VARCHAR(4)      NOT NULL,
    diario_oficial                  INTEGER             NULL,
    arquivo_texto                   VARCHAR(50)         NULL,
    dt_publicacao_homologacao       DATE                NULL,
    CONSTRAINT pk_configuracao_arquivo_licitacao    PRIMARY KEY (cod_mapa, exercicio),
    CONSTRAINT fk_configuracao_arquivo_licitacao_1  FOREIGN KEY (cod_mapa, exercicio)
                                   REFERENCES compras.mapa (cod_mapa, exercicio)
);
GRANT ALL ON tceam.configuracao_arquivo_licitacao TO GROUP urbem;


INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     )
VALUES
     ( 2794
     , 467
     , 'FLConfigurarArquivoLicitacao.php'
     , 'incluir'
     , 3
     , ''
     , 'Configurar Arquivo Licitação'
     );


----------------
-- Ticket #
----------------

ALTER TABLE tceam.tipo_documento_diaria ADD COLUMN quantidade NUMERIC(5);

