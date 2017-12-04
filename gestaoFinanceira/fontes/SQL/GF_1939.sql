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
--Ticket #14911
----------------
ALTER TABLE tesouraria.conciliacao_lancamento_manual DROP COLUMN exercicio_conciliacao;
ALTER TABLE tesouraria.conciliacao_lancamento_manual
        ADD CONSTRAINT fk_conciliacao_lancamento_manual_1
        FOREIGN KEY(cod_plano, exercicio, mes) REFERENCES tesouraria.conciliacao(cod_plano, exercicio, mes);

UPDATE tesouraria.conciliacao_lancamento_contabil
       SET exercicio_conciliacao = '2008'  
     WHERE CASE WHEN exercicio = '2008' AND exercicio_conciliacao = '2009'
                THEN FALSE
                ELSE TRUE
           END
       AND exercicio = '2008';
     
    UPDATE tesouraria.conciliacao_lancamento_contabil 
       SET exercicio_conciliacao = '2009' 
     WHERE exercicio = '2009';  
     
     UPDATE tesouraria.conciliacao_lancamento_arrecadacao
       SET exercicio_conciliacao = '2008'
     WHERE exercicio = '2008';
     
    UPDATE tesouraria.conciliacao_lancamento_arrecadacao
       SET exercicio_conciliacao = '2009'
     WHERE exercicio = '2009';
     
   UPDATE tesouraria.conciliacao_lancamento_arrecadacao_estornada
       SET exercicio_conciliacao = '2008'
     WHERE exercicio = '2008';
                       
    UPDATE tesouraria.conciliacao_lancamento_arrecadacao_estornada
       SET exercicio_conciliacao = '2009'
     WHERE exercicio = '2009';

-----------------
--Ticket 14942
-----------------
ALTER TABLE tesouraria.pagamento_tipo_documento ADD num_documento varchar(15);

----------------
-- Atualizando a parâmetro do tribunal de contas de Goiás pois todos os tribunais
-- estão com esse parâmetro true e só o tribunal de Goiás deve estar com valor true.
-- Isto será feito manualmente após essa atualização nas demais prefeituras. 
----------------
update administracao.configuracao set valor = 'false' where parametro = 'seta_tipo_documento_tcmgo';
