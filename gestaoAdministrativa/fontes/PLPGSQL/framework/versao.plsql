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
* $Revision: 3077 $
* $Name$
* $Author: pablo $
* $Date: 2005-11-29 14:53:37 -0200 (Ter, 29 Nov 2005) $
*
* Caso de uso: uc-01.01.00
*/
-- Gris - 31/03/2005 - Author Gris
   -- Função responsável por retornar a versão do urbem e  o nome da prefeitura.

Drop Function versao();

CREATE OR REPLACE function versao()  RETURNS SETOF VARCHAR(100) as $$
DECLARE
   varVersao VARCHAR(100);
   recVersao RECORD;
   varPref   VARCHAR(100);
BEGIN
   SELECT SUBSTR(BTRIM(configuracao.valor),1,100)
     INTO varPref
     FROM administracao.configuracao
    WHERE parametro = 'nom_prefeitura'
    ORDER BY configuracao.exercicio DESC
    LIMIT 1;

    RETURN NEXT varPref;

   FOR recVersao
    IN SELECT RPAD ('     - Gestao '   || nom_gestao , 40) || ' : Versão '        || versao AS retorno
         FROM administracao.gestao
        ORDER BY nom_gestao
   LOOP
      varPref := recVersao.retorno ;
      RETURN NEXT varPref;
   END LOOP;

   RETURN ;
END;
$$ LANGUAGE 'plpgsql';

