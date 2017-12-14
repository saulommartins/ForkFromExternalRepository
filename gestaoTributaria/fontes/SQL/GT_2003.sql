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
* Versão 2.00.3
*/

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
BEGIN
    PERFORM 1
       FROM pg_tables
      WHERE tablename = 'tmp_cpf_controle_dependentes'
          ;
    IF NOT FOUND THEN
        CREATE TABLE tmp_cpf_controle_dependentes (
              cpf                   VARCHAR
            , sequencia_evento      INTEGER
        );
        GRANT ALL ON tmp_cpf_controle_dependentes TO urbem;
    END IF;

    PERFORM 1
       FROM pg_tables
      WHERE tablename = 'tmp_valores_decimo'
          ;
    IF NOT FOUND THEN
        CREATE TABLE tmp_valores_decimo (
                  cod_contrato          INTEGER
                , valor                 DECIMAL(14,2)
        );
        GRANT ALL ON tmp_valores_decimo TO urbem;
    END IF;
END;
$$ LANGUAGE 'plpgsql';
SELECT        manutencao();
DROP FUNCTION manutencao();

