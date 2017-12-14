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
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id:$
*
* Versão 1.99.6
*/

----------------
-- Ticket #17120
----------------

ALTER TABLE ima.configuracao_dirf ADD COLUMN pagamento_mes_competencia BOOLEAN not null default TRUE;

CREATE TABLE ima.configuracao_dirf_plano (
    exercicio    CHAR(4)    not null,
    numcgm       INTEGER    not null,
    cod_evento   INTEGER    not null,
    registro_ans NUMERIC(6) not null,
    
    CONSTRAINT pk_configuracao_dirf_plano PRIMARY KEY (exercicio,numcgm,registro_ans),
    CONSTRAINT fk_configuracao_dirf_plano_1 FOREIGN KEY (exercicio) REFERENCES ima.configuracao_dirf(exercicio),
    CONSTRAINT fk_configuracao_dirf_plano_2 FOREIGN KEY (numcgm) REFERENCES sw_cgm_pessoa_juridica(numcgm),
    CONSTRAINT fk_configuracao_dirf_plano_3 FOREIGN KEY (cod_evento) REFERENCES folhapagamento.evento(cod_evento)
);

ALTER TABLE ima.configuracao_dirf ADD COLUMN cod_evento_molestia integer;

ALTER TABLE ima.configuracao_dirf ADD CONSTRAINT fk_configuracao_dirf_4 FOREIGN KEY (cod_evento_molestia) REFERENCES folhapagamento.evento(cod_evento);

CREATE TABLE tmp_cpf_controle_dependentes (
          cpf                   VARCHAR
        , sequencia_evento      INTEGER
    );


CREATE TABLE tmp_valores_decimo (
          cod_contrato          INTEGER
        , valor                 DECIMAL(14,2)
);

GRANT ALL ON ima.configuracao_dirf_plano TO urbem;
GRANT ALL ON tmp_cpf_controle_dependentes TO urbem;
GRANT ALL ON tmp_valores_decimo TO urbem;
