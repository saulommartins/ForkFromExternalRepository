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
* Versao 2.02.1
*
* Fabio Bertoldi - 20130412
*
*/

----------------
-- Ticket #21183
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1 FROM pg_tables WHERE tablename ='tipo_controle_ponto' ;

    IF NOT FOUND THEN

       CREATE TABLE ima.tipo_controle_ponto (
         cod_tipo_controle_ponto   INTEGER         NOT NULL,
         descricao                 VARCHAR(150)    NOT NULL,
         CONSTRAINT pk_tipo_controle_ponto         PRIMARY KEY (cod_tipo_controle_ponto)
       );
       GRANT ALL ON ima.tipo_controle_ponto TO urbem;
       
       INSERT INTO ima.tipo_controle_ponto (cod_tipo_controle_ponto, descricao) VALUES (0, 'Somente para Empresas sem Vínculos (Rais Negativa).'                                                                                          );
       INSERT INTO ima.tipo_controle_ponto (cod_tipo_controle_ponto, descricao) VALUES (1, 'Estabelecimento não adotou sistema de controle de ponto  porque em nenhum mês do ano-base possuía mais de 10 trabalhadores celetistas ativos.');
       INSERT INTO ima.tipo_controle_ponto (cod_tipo_controle_ponto, descricao) VALUES (2, 'Estabelecimento adotou sistema manual.'                                                                                                       );
       INSERT INTO ima.tipo_controle_ponto (cod_tipo_controle_ponto, descricao) VALUES (3, 'Estabelecimento adotou sistema mecânico.'                                                                                                     );
       INSERT INTO ima.tipo_controle_ponto (cod_tipo_controle_ponto, descricao) VALUES (4, 'Estabelecimento adotou Sistema de Registro Eletrônico de Ponto - SREP (Portaria 1.510/2009).'                                                 );
       INSERT INTO ima.tipo_controle_ponto (cod_tipo_controle_ponto, descricao) VALUES (5, 'Estabelecimento adotou sistema não eletrônico alternativo  previsto no art.1º da Portaria 373/2011.'                                          );
       INSERT INTO ima.tipo_controle_ponto (cod_tipo_controle_ponto, descricao) VALUES (6, 'Estabelecimento adotou sistema eletrônico alternativo previsto na Portaria 373/2011.'                                                         );

       ALTER TABLE ima.configuracao_rais ADD COLUMN cod_tipo_controle_ponto INTEGER NOT NULL DEFAULT 1;
       ALTER TABLE ima.configuracao_rais ADD CONSTRAINT fk_configuracao_rais_2 FOREIGN KEY (cod_tipo_controle_ponto)
                                                                        REFERENCES ima.tipo_controle_ponto (cod_tipo_controle_ponto);

    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();





