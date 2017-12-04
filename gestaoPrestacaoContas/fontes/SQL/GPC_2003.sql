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
* Versão 2.00.3
*/

------------------------------------
-- UPDATE NA COLUNA complemento_acao
------------------------------------

--RREO

UPDATE administracao.acao SET complemento_acao = 'BALANÇO ORÇAMENTÁRIO' WHERE cod_funcionalidade = 314 AND cod_acao = 1501;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO DA EXECUÇÃO DAS DESPESAS POR FUNÇÃO/SUBFUNÇÃO' WHERE cod_funcionalidade = 314 AND cod_acao = 1502;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO DA EXECUÇÃO DAS DESPESAS POR FUNÇÃO/SUBFUNÇÃO' WHERE cod_funcionalidade = 314 AND cod_acao = 1502;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO DOS RESTOS A PAGAR POR PODER E ÓRGÃO' WHERE cod_funcionalidade = 314 AND cod_acao = 2225;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO DAS RECEITAS E DESPESAS PREVIDENCIÁRIAS DO RPPS' WHERE cod_funcionalidade = 314 AND cod_acao = 2219;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO DO RESULTADO NOMINAL' WHERE cod_funcionalidade = 314 AND cod_acao = 2190;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO DO RESULTADO PRIMÁRIO' WHERE cod_funcionalidade = 314 AND cod_acao = 2214;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO DAS RECEITAS E DESPESAS COM MDE' WHERE cod_funcionalidade = 314 AND cod_acao = 2195;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO DAS RECEITAS DE OPERAÇÕES DE CRÉDITO E DESPESAS DE CAPITAL' WHERE cod_funcionalidade = 314 AND cod_acao = 2230;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO DA PROJEÇÃO ATUARIAL DO RPPS' WHERE cod_funcionalidade = 314 AND cod_acao = 2422;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO DA RECEITA DE ALIENAÇÃO DE ATIVOS E APLICAÇÃO DOS RECURSOS' WHERE cod_funcionalidade = 314 AND cod_acao = 2189;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO DA RECEITA DE IMPOSTOS LÍQUIDA E DAS DESPESAS PRÓPRIAS COM ASPS' WHERE cod_funcionalidade = 314 AND cod_acao = 2220;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO SIMPLIFICADO DO RELATÓRIO RESUMIDO DA EXECUÇÃO ORÇAMENTÁRIA' WHERE cod_funcionalidade = 314 AND cod_acao = 2264;

--RGF

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO DA DESPESA COM PESSOAL ' WHERE cod_funcionalidade = 315 AND cod_acao = 1504;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO DA DÍVIDA CONSOLIDADA LÍQUIDA' WHERE cod_funcionalidade = 315 AND cod_acao = 1505;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO DAS GARANTIAS E CONTRAGARANTIAS DE VALORES' WHERE cod_funcionalidade = 315 AND cod_acao = 1506;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO DAS OPERAÇÕES DE CRÉDITO' WHERE cod_funcionalidade = 315 AND cod_acao = 1507;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO DA DISPONIBILIDADE DE CAIXA' WHERE cod_funcionalidade = 315 AND cod_acao = 2170;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO DOS RESTOS A PAGAR' WHERE cod_funcionalidade = 315 AND cod_acao = 2259;

UPDATE administracao.acao SET complemento_acao = 'DEMONSTRATIVO SIMPLIFICADO DO RELATÓRIO DE GESTÃO FISCAL' WHERE cod_funcionalidade = 315 AND cod_acao = 2265;


