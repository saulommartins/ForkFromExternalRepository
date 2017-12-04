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
* $Id: $
*
* Versão 1.93.8
*/

----------------
--Ticket #14815
----------------
ALTER TABLE tesouraria.conciliacao_lancamento_contabil ADD COLUMN exercicio_conciliacao CHAR(4);
ALTER TABLE tesouraria.conciliacao_lancamento_contabil DROP CONSTRAINT pk_conciliacao_lancamento_contabil;
ALTER TABLE tesouraria.conciliacao_lancamento_contabil DROP CONSTRAINT fk_conciliacao_lancamento_contabil_1;
ALTER TABLE tesouraria.conciliacao_lancamento_contabil DROP CONSTRAINT fk_conciliacao_lancamento_contabil_2;

    UPDATE tesouraria.conciliacao_lancamento_contabil 
       SET exercicio_conciliacao = '2008' 
     WHERE EXISTS ( SELECT 1 
                      FROM tesouraria.conciliacao
                     WHERE exercicio = '2008'
                       AND conciliacao.cod_plano = conciliacao_lancamento_contabil.cod_plano
                       AND conciliacao.mes = conciliacao_lancamento_contabil.mes);
    UPDATE tesouraria.conciliacao_lancamento_contabil 
       SET exercicio_conciliacao = '2009' 
     WHERE EXISTS ( SELECT 1 
                      FROM tesouraria.conciliacao
                     WHERE exercicio = '2009'
                       AND conciliacao.cod_plano = conciliacao_lancamento_contabil.cod_plano
                       AND conciliacao.mes = conciliacao_lancamento_contabil.mes);
ALTER TABLE tesouraria.conciliacao_lancamento_contabil
        ADD CONSTRAINT pk_conciliacao_lancamento_contabil PRIMARY KEY (cod_plano, exercicio_conciliacao, mes, cod_lote, exercicio, tipo, sequencia, cod_entidade, tipo_valor);
ALTER TABLE tesouraria.conciliacao_lancamento_contabil
        ADD CONSTRAINT fk_conciliacao_lancamento_contabil_1 
        FOREIGN KEY(cod_plano, exercicio_conciliacao, mes) REFERENCES tesouraria.conciliacao(cod_plano, exercicio, mes);
ALTER TABLE tesouraria.conciliacao_lancamento_contabil
        ADD CONSTRAINT fk_conciliacao_lancamento_contabil_2
        FOREIGN KEY  (exercicio, cod_entidade, tipo, cod_lote, sequencia, tipo_valor) REFERENCES contabilidade.valor_lancamento(exercicio, cod_entidade, tipo, cod_lote, sequencia, tipo_valor);
--tesouraria.conciliacao_manual
ALTER TABLE tesouraria.conciliacao_lancamento_manual ADD COLUMN exercicio_conciliacao CHAR(4);
ALTER TABLE tesouraria.conciliacao_lancamento_manual DROP CONSTRAINT pk_conciliacao_lancamento_manual;
ALTER TABLE tesouraria.conciliacao_lancamento_manual DROP CONSTRAINT fk_conciliacao_lancamento_manual_1;

    UPDATE tesouraria.conciliacao_lancamento_manual
       SET exercicio_conciliacao = '2008'
     WHERE EXISTS ( SELECT 1
                      FROM tesouraria.conciliacao
                     WHERE exercicio = '2008'
                       AND conciliacao.cod_plano = conciliacao_lancamento_manual.cod_plano
                       AND conciliacao.mes = conciliacao_lancamento_manual.mes);
    UPDATE tesouraria.conciliacao_lancamento_manual
       SET exercicio_conciliacao = '2009'
     WHERE EXISTS ( SELECT 1
                      FROM tesouraria.conciliacao
                     WHERE exercicio = '2009'
                       AND conciliacao.cod_plano = conciliacao_lancamento_manual.cod_plano
                       AND conciliacao.mes = conciliacao_lancamento_manual.mes);
ALTER TABLE tesouraria.conciliacao_lancamento_manual
        ADD CONSTRAINT pk_conciliacao_lancamento_manual PRIMARY KEY (cod_plano, exercicio, mes, sequencia);
ALTER TABLE tesouraria.conciliacao_lancamento_manual
        ADD CONSTRAINT fk_conciliacao_lancamento_manual_1 
        FOREIGN KEY(cod_plano, exercicio_conciliacao, mes) REFERENCES tesouraria.conciliacao(cod_plano, exercicio, mes);
--tesouraria.conciliacao_lancamento_arrecadacao
ALTER TABLE tesouraria.conciliacao_lancamento_arrecadacao ADD COLUMN exercicio_conciliacao CHAR(4);
ALTER TABLE tesouraria.conciliacao_lancamento_arrecadacao DROP CONSTRAINT fk_conciliacao_lancamento_arrecadacao_1;
ALTER TABLE tesouraria.conciliacao_lancamento_arrecadacao DROP CONSTRAINT fk_conciliacao_lancamento_arrecadacao_2;

    UPDATE tesouraria.conciliacao_lancamento_arrecadacao
       SET exercicio_conciliacao = '2008'
     WHERE EXISTS ( SELECT 1
                      FROM tesouraria.conciliacao
                     WHERE exercicio = '2008'
                       AND conciliacao.cod_plano = conciliacao_lancamento_arrecadacao.cod_plano
                       AND conciliacao.mes = conciliacao_lancamento_arrecadacao.mes);
    UPDATE tesouraria.conciliacao_lancamento_arrecadacao
       SET exercicio_conciliacao = '2009'
     WHERE EXISTS ( SELECT 1
                      FROM tesouraria.conciliacao
                     WHERE exercicio = '2009'
                       AND conciliacao.cod_plano = conciliacao_lancamento_arrecadacao.cod_plano
                       AND conciliacao.mes = conciliacao_lancamento_arrecadacao.mes);
ALTER TABLE tesouraria.conciliacao_lancamento_arrecadacao
        ADD CONSTRAINT pk_conciliacao_lancamento_arrecadacao PRIMARY KEY (cod_plano, exercicio_conciliacao, mes, cod_arrecadacao, exercicio, timestamp_arrecadacao,tipo);
ALTER TABLE tesouraria.conciliacao_lancamento_arrecadacao
        ADD CONSTRAINT fk_conciliacao_lancamento_arrecadacao_1 
        FOREIGN KEY(cod_plano, exercicio_conciliacao, mes) REFERENCES tesouraria.conciliacao(cod_plano, exercicio, mes);
ALTER TABLE tesouraria.conciliacao_lancamento_arrecadacao
        ADD CONSTRAINT fk_conciliacao_lancamento_arrecadacao_2  
         FOREIGN KEY (cod_arrecadacao, exercicio, timestamp_arrecadacao) REFERENCES tesouraria.arrecadacao(cod_arrecadacao, exercicio, timestamp_arrecadacao);
--tesouraria.conciliacao_lancamento_arrecadacao
ALTER TABLE tesouraria.conciliacao_lancamento_arrecadacao_estornada ADD COLUMN exercicio_conciliacao CHAR(4);
ALTER TABLE tesouraria.conciliacao_lancamento_arrecadacao_estornada DROP CONSTRAINT pk_conciliacao_lancamento_arrecadacao_estornada;
ALTER TABLE tesouraria.conciliacao_lancamento_arrecadacao_estornada DROP CONSTRAINT fk_conciliacao_lancamento_arrecadacao_estornada_1;
ALTER TABLE tesouraria.conciliacao_lancamento_arrecadacao_estornada DROP CONSTRAINT fk_conciliacao_lancamento_arrecadacao_estornada_2;

    UPDATE tesouraria.conciliacao_lancamento_arrecadacao_estornada
       SET exercicio_conciliacao = '2008'
     WHERE EXISTS ( SELECT 1
                      FROM tesouraria.conciliacao
                     WHERE exercicio = '2008'
                       AND conciliacao.cod_plano = conciliacao_lancamento_arrecadacao_estornada.cod_plano
                       AND conciliacao.mes = conciliacao_lancamento_arrecadacao_estornada.mes);
    UPDATE tesouraria.conciliacao_lancamento_arrecadacao_estornada
       SET exercicio_conciliacao = '2009'
     WHERE EXISTS ( SELECT 1
                      FROM tesouraria.conciliacao
                     WHERE exercicio = '2009'
                       AND conciliacao.cod_plano = conciliacao_lancamento_arrecadacao_estornada.cod_plano
                       AND conciliacao.mes = conciliacao_lancamento_arrecadacao_estornada.mes);
ALTER TABLE tesouraria.conciliacao_lancamento_arrecadacao_estornada
        ADD CONSTRAINT pk_conciliacao_lancamento_arrecadacao_estornada PRIMARY KEY (cod_plano, exercicio_conciliacao, mes, cod_arrecadacao, exercicio, timestamp_arrecadacao,tipo);
ALTER TABLE tesouraria.conciliacao_lancamento_arrecadacao_estornada
        ADD CONSTRAINT fk_conciliacao_lancamento_arrecadacao_estornada_1 
        FOREIGN KEY(cod_plano, exercicio_conciliacao, mes) REFERENCES tesouraria.conciliacao(cod_plano, exercicio, mes);
ALTER TABLE tesouraria.conciliacao_lancamento_arrecadacao_estornada
        ADD CONSTRAINT fk_conciliacao_lancamento_arrecadacao_estornada_2  
         FOREIGN KEY (cod_arrecadacao, exercicio, timestamp_arrecadacao,timestamp_estornada) REFERENCES tesouraria.arrecadacao_estornada(cod_arrecadacao, exercicio, timestamp_arrecadacao,timestamp_estornada);
