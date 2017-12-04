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
* Versão 1.99.6
*/

----------------
-- Ticket #17103
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
     )
values
     ( 2787
     , 288
     , 'FLManterRequisicao.php'
     , 'homologar'
     , 8
     , ''
     , 'Homologar Requisição'
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao )
VALUES
     ( 2788
     , 288
     , 'FLManterRequisicao.php'
     , 'anular_homolog'
     , 9
     , ''
     , 'Anular Homologação de Requisição'
     );

CREATE TABLE almoxarifado.requisicao_homologada (
    exercicio           CHARACTER(4)        NOT NULL,
    cod_requisicao      INTEGER             NOT NULL,
    cod_almoxarifado    INTEGER             NOT NULL,
    timestamp           TIMESTAMP           NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    cgm_homologador     INTEGER             NOT NULL,
    homologada          BOOLEAN             NOT NULL,
    CONSTRAINT pk_requisicao_homologada     PRIMARY KEY                         (exercicio, cod_requisicao, cod_almoxarifado,timestamp),
    CONSTRAINT fk_requisicao_homologada_1   FOREIGN KEY                         (exercicio, cod_requisicao, cod_almoxarifado)
                                            REFERENCES almoxarifado.requisicao  (exercicio, cod_requisicao, cod_almoxarifado),
    CONSTRAINT fk_requisicao_homologada_2   FOREIGN KEY                         (cgm_homologador)
                                            REFERENCES administracao.usuario    (numcgm)
);
GRANT ALL ON almoxarifado.requisicao_homologada TO GROUP urbem;

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
VALUES
     ( '2011'
     , 29
     , 'homologacao_automatica_requisicao'
     , 'true'
     );

-- Insert para deixar as requisições do exercicio homologadas.
INSERT
  INTO almoxarifado.requisicao_homologada
SELECT exercicio
     , cod_requisicao
     , cod_almoxarifado
     , now()::timestamp(3) with time zone
     , 0
     , TRUE
  FROM almoxarifado.requisicao
 WHERE exercicio='2011'
     ;

