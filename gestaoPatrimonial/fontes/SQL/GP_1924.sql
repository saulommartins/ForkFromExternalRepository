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
* $Id:  $
*
* Versão 1.92.4
*/

-------------------------------------------------
-- ADICIONANDO TIPO colunasListaClassificacoesMae
-------------------------------------------------

DROP FUNCTION almoxarifado.fn_lista_classificacoes_mae(integer, varchar);

CREATE TYPE colunasListaClassificacoesMae AS (
      cod_estrutural    VARCHAR
    , descricao         VARCHAR
    , cod_nivel         VARCHAR
    , descricao_nivel   VARCHAR
    , mascara           VARCHAR
    , nivel             INTEGER
);


------------------------------------------------------------
-- CORRIGINDO COLUNA quantidade DE compras.mapa_item_dotacao
------------------------------------------------------------

ALTER TABLE compras.mapa_item_dotacao ALTER COLUMN quantidade TYPE NUMERIC(14,4);


------------------------------------------------------------------
-- CORRIGINDO FKs EM compras.mapa_item E compras.mapa_item_dotacao
------------------------------------------------------------------

ALTER TABLE compras.mapa_item           DROP CONSTRAINT fk_mapa_item_2;
ALTER TABLE compras.mapa_item           ADD  CONSTRAINT fk_mapa_item_2 FOREIGN KEY (exercicio_solicitacao, cod_entidade, cod_solicitacao, cod_centro, cod_item)
                                                                       REFERENCES compras.solicitacao_item(exercicio, cod_entidade, cod_solicitacao, cod_centro, cod_item);
ALTER TABLE compras.mapa_item_dotacao   DROP CONSTRAINT fk_mapa_item_dotacao_2;
ALTER TABLE compras.mapa_item_dotacao   ADD  CONSTRAINT fk_mapa_item_dotacao_2 FOREIGN KEY (exercicio_solicitacao, cod_entidade, cod_solicitacao, cod_centro, cod_item, cod_conta, cod_despesa)
                                                                               REFERENCES compras.solicitacao_item_dotacao(exercicio, cod_entidade, cod_solicitacao, cod_centro, cod_item, cod_conta, cod_despesa);


---------------------------------------------
-- ALTERANDO PK EM compras.mapa_item_anulacao
---------------------------------------------

ALTER TABLE compras.mapa_item_anulacao DROP CONSTRAINT pk_mapa_item_anulacao;
ALTER TABLE compras.mapa_item_anulacao ADD  CONSTRAINT pk_mapa_item_anulacao PRIMARY KEY (exercicio, cod_mapa, exercicio_solicitacao, cod_entidade, cod_solicitacao, cod_centro, cod_item, timestamp, lote, cod_conta, cod_despesa);


