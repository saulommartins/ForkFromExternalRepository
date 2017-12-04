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
--
-- script de funcao PLSQL
-- 
-- URBEM Solucões de Gestão Pública Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23095 $
-- $Name$
-- $Autor: MArcia $
-- Date: 2005/12/15 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: Obtem a data de nascimento do servidor a partir da informacao do codigo do servidor..
--
--
--


CREATE OR REPLACE FUNCTION pega0DataNascimentoServidor(integer) RETURNS varchar as $$
DECLARE
    inCodServidor           ALIAS FOR $1;
    stDataNascimento        VARCHAR;
    stSql                   VARCHAR;
    inNumCGM                INTEGER;
    stEntidade              VARCHAR;
BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    stSql := 'SELECT numcgm
                FROM pessoal'|| stEntidade ||'.servidor
               WHERE cod_servidor = '|| inCodServidor;
    inNumCGM := selectIntoInteger(stSql);

    stSql := 'SELECT TO_CHAR(dt_nascimento,''yyyy-mm-dd'')
                FROM sw_cgm_pessoa_fisica
               WHERE numcgm = '|| inNumCGM;

    stDataNascimento := selectIntoVarchar(stSql);
    RETURN stDataNascimento;
END;
$$ LANGUAGE 'plpgsql';

