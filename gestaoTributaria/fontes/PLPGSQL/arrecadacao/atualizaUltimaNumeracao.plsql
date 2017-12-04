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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: atualizaUltimaNumeracao.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos d uso: uc-05.03.11
*/

/*
$Log$
Revision 1.5  2006/09/15 10:20:10  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

--
-- Função de inclusão de ultimo andamento.
--
CREATE OR REPLACE FUNCTION arrecadacao.fn_atualiza_ultima_numeracao()   RETURNS TRIGGER AS '
DECLARE
    stTeste VARCHAR;
BEGIN    
    SELECT numeracao INTO stTeste FROM arrecadacao.carne_limite  WHERE cod_convenio = new.cod_convenio;

    if Found then
    	UPDATE arrecadacao.carne_limite SET numeracao = new.numeracao WHERE cod_convenio = new.cod_convenio;
    else
       INSERT INTO arrecadacao.carne_limite VALUES (new.cod_convenio,new.numeracao);
    end if;

	If Not Found Then
		RAISE EXCEPTION ''Operação % não pode ser completada na tabela: %'', TG_OP,TG_RELNAME;
      	End If;

   Return new;

END;
' LANGUAGE plpgsql;
