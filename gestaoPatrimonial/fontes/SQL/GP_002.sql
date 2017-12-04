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
* $Revision: 28350 $
* $Name$
* $Author: gris $
* $Date: 2008-03-05 09:57:44 -0300 (Qua, 05 Mar 2008) $
*
* Versão 002.
*/

UPDATE administracao.acao SET ordem =  8  WHERE cod_acao = 1573;
UPDATE administracao.acao SET ordem =  9  WHERE cod_acao = 1574;
UPDATE administracao.acao SET ordem = 10  WHERE cod_acao = 1575;
UPDATE administracao.acao SET ordem = 11  WHERE cod_acao = 1589;
UPDATE administracao.acao SET ordem = 12  WHERE cod_acao = 1590;
UPDATE administracao.acao SET ordem = 13  WHERE cod_acao = 2185;
UPDATE administracao.acao SET ordem = 14  WHERE cod_acao = 1595;
UPDATE administracao.acao SET ordem = 15  WHERE cod_acao = 1596;


UPDATE administracao.funcionalidade set ordem = 1  WHERE cod_funcionalidade = 269;
UPDATE administracao.funcionalidade set ordem = 2  WHERE cod_funcionalidade = 263;
UPDATE administracao.funcionalidade set ordem = 3  WHERE cod_funcionalidade = 268;
UPDATE administracao.funcionalidade set ordem = 4  WHERE cod_funcionalidade = 278;
UPDATE administracao.funcionalidade set ordem = 6  WHERE cod_funcionalidade = 277;
UPDATE administracao.funcionalidade set ordem = 9  WHERE cod_funcionalidade = 288;
UPDATE administracao.funcionalidade set ordem = 5  WHERE cod_funcionalidade = 289;
UPDATE administracao.funcionalidade set ordem = 8  WHERE cod_funcionalidade = 290;
UPDATE administracao.funcionalidade set ordem = 10 WHERE cod_funcionalidade = 291;
UPDATE administracao.funcionalidade set ordem = 7  WHERE cod_funcionalidade = 311;

-- Solicitado pelo Gelson - Jabber
DROP TABLE licitacao.convenio_despesa;

-- Solicitado pelo Gelson - email
/* Atualização do caminho para GP/Patrimonio/Relatorio */
UPDATE administracao.funcionalidade
 SET nom_diretorio = 'instancias/relatorio/'
WHERE cod_modulo = 6
 AND cod_funcionalidade = 28;

/* Atualização do nome dos arquivos na tabela de administracao.acao */
UPDATE administracao.acao SET nom_arquivo = 'FLRelatorioPatrimonial.php' WHERE cod_acao = 106 AND cod_funcionalidade = 28;
UPDATE administracao.acao SET nom_arquivo = 'FLFichaPatrimonial.php' WHERE cod_acao = 1111 AND cod_funcionalidade = 28;
UPDATE administracao.acao SET nom_arquivo = 'financeiraNatureza.php' WHERE cod_acao = 108 AND cod_funcionalidade = 28;
UPDATE administracao.acao SET nom_arquivo = 'cargaPatrimonial.php' WHERE cod_acao = 110 AND cod_funcionalidade = 28;
UPDATE administracao.acao SET nom_arquivo = 'apoliceSeguros.php' WHERE cod_acao = 111 AND cod_funcionalidade = 28;
UPDATE administracao.acao SET nom_arquivo = 'apoliceSegurosVencer.php' WHERE cod_acao = 112 AND cod_funcionalidade = 28;
UPDATE administracao.acao SET nom_arquivo = 'relatorioManutencao.php' WHERE cod_acao = 113 AND cod_funcionalidade = 28;
UPDATE administracao.acao SET nom_arquivo = 'relatorioClassificacao.php' WHERE cod_acao = 125 AND cod_funcionalidade = 28;
UPDATE administracao.acao SET nom_arquivo = 'FLRelatorioBaixa.php' WHERE cod_acao = 819 AND cod_funcionalidade = 28;
UPDATE administracao.acao SET nom_arquivo = 'relatorioCustomizavel.php' WHERE cod_acao = 824 AND cod_funcionalidade = 28;
UPDATE administracao.acao SET nom_arquivo = 'relatorioBemEntidade.php' WHERE cod_acao = 1110 AND cod_funcionalidade = 28;


