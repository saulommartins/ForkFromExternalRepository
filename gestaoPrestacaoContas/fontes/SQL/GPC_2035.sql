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
*
* Script de DDL e DML
*
* Versao 2.03.5
*
* Fabio Bertoldi - 20150106
*
*/

----------------
-- Ticket #22552
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
     VALUES
     ( 3028
     , 451
     , 'FMManterFornecedorSoftware.php'
     , 'manter'
     , 43
     , ''
     , 'Configurar Fornecedor de Software'
     , TRUE
     );

INSERT
  INTO administracao.configuracao
     ( cod_modulo
     , exercicio
     , parametro
     , valor
     )
     VALUES
     ( 55
     , '2015'
     , 'fornecedor_software'
     , ''
     );


----------------
-- Ticket #22364
----------------

UPDATE administracao.acao
   SET complemento_acao = 'Demonstrativo da Disponibilidades de Caixa e dos Restos a Pagar'
 WHERE cod_acao = 2883
     ;

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 6
     , 36
     , 68
     , 'RGF - Anexo 5'
     , 'RGFAnexo5Novo2015.rptdesign'
     );
 

----------------
-- Ticket #22565
----------------

CREATE TABLE tcemg.tipo_lei_origem_decreto(
    cod_tipo_lei    INTEGER         NOT NULL,
    descricao       VARCHAR(100)    NOT NULL,
    CONSTRAINT pk_tipo_lei_origem_decreto PRIMARY KEY (cod_tipo_lei)
);
GRANT ALL ON tcemg.tipo_lei_origem_decreto TO urbem;

INSERT INTO tcemg.tipo_lei_origem_decreto VALUES (1, 'LOA – Lei Orçamentária Anual'                                         );
INSERT INTO tcemg.tipo_lei_origem_decreto VALUES (2, 'LDO – Lei de Diretrizes Orçamentárias'                                );
INSERT INTO tcemg.tipo_lei_origem_decreto VALUES (3, 'LAO – Lei de Alteração Orçamentária'                                  );
INSERT INTO tcemg.tipo_lei_origem_decreto VALUES (4, 'LAOP – Lei de Alteração da Lei Orçamentária (Alteração de Percentual)');


CREATE TABLE tcemg.tipo_lei_alteracao_orcamentaria(
    cod_tipo_lei    INTEGER         NOT NULL,
    descricao       VARCHAR(100)    NOT NULL,
    CONSTRAINT pk_tipo_lei_alteracao_orcamentaria PRIMARY KEY (cod_tipo_lei)
);
GRANT ALL ON tcemg.tipo_lei_alteracao_orcamentaria TO urbem;

INSERT INTO tcemg.tipo_lei_alteracao_orcamentaria VALUES (1, 'Lei autorizativa de Crédito Suplementar'                        );
INSERT INTO tcemg.tipo_lei_alteracao_orcamentaria VALUES (2, 'Lei autorizativa de Crédito Especial'                           );
INSERT INTO tcemg.tipo_lei_alteracao_orcamentaria VALUES (3, 'Lei autorizativa de Remanejamento /Transposição / Transferência');
INSERT INTO tcemg.tipo_lei_alteracao_orcamentaria VALUES (4, 'Lei autorizativa de alteração da fonte de recurso'              );
INSERT INTO tcemg.tipo_lei_alteracao_orcamentaria VALUES (5, 'Lei autorizativa de suplementação de Crédito Especial'          );


CREATE TABLE tcemg.norma_detalhe(
    cod_norma                       INTEGER     NOT NULL,
    tipo_lei_origem_decreto         INTEGER             ,
    tipo_lei_alteracao_orcamentaria INTEGER             ,
    CONSTRAINT pk_norma_detalhe                 PRIMARY KEY             (cod_norma),
    CONSTRAINT fk_norma_detalhe_1               FOREIGN KEY             (cod_norma)
                                                REFERENCES normas.norma (cod_norma),
    CONSTRAINT fk_norma_detalhe_2               FOREIGN KEY                            (tipo_lei_origem_decreto)
                                                REFERENCES tcemg.tipo_lei_origem_decreto          (cod_tipo_lei),
    CONSTRAINT fk_norma_detalhe_3               FOREIGN KEY                    (tipo_lei_alteracao_orcamentaria)
                                                REFERENCES tcemg.tipo_lei_alteracao_orcamentaria  (cod_tipo_lei)
);
GRANT ALL ON tcemg.norma_detalhe TO urbem;


----------------
-- Ticket #22544
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3029
     , 484
     , 'FLRelatorioDespesaTotalPessoal.php'
     , 'consultar'
     , 55
     , ''
     , 'Despesa Total com Pessoal'
     , TRUE
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
VALUES
     ( 6
     , 55
     , 15
     , 'Despesa Total com Pessoal'
     , 'LHTCEMGRelatorioDespesaTotalPessoal.php'
     );
 


----------------
-- Ticket #22562
----------------

ALTER TABLE tcemg.consideracao_arquivo_descricao DROP CONSTRAINT fk_consideracao_arquivo_descricao_1;

UPDATE tcemg.consideracao_arquivo           SET cod_arquivo = 41 WHERE cod_arquivo = 40;
UPDATE tcemg.consideracao_arquivo_descricao SET cod_arquivo = 41 WHERE cod_arquivo = 40;
INSERT INTO tcemg.consideracao_arquivo VALUES (40, 'SUPDEF');

ALTER TABLE tcemg.consideracao_arquivo_descricao ADD  CONSTRAINT fk_consideracao_arquivo_descricao_1
                                                      FOREIGN KEY (cod_arquivo)
                                                      REFERENCES tcemg.consideracao_arquivo(cod_arquivo);


----------------
-- Ticket #22340
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
SELECT 6
     , 36
     , 67
     , 'RGF - Anexo 1 - Demonstrativo da Despesa com Pessoal'
     , 'RGFAnexo1_2015.rptdesign'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.relatorio
              WHERE cod_gestao    = 6
                AND cod_modulo    = 36
                AND cod_relatorio = 67
           )
     ;

