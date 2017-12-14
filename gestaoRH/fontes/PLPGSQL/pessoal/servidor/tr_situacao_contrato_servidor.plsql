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
/* tr_situacao_contrato_servidor
*
* Data de Criacao : 25/09/2013

* @author Analista : Dagiane
* @author Desenvolvedor : Fabio e Schitz

* @package URBEM
* @subpackage

$Id:$
*/

CREATE OR REPLACE FUNCTION tr_situacao_contrato_servidor() RETURNS TRIGGER AS $$
DECLARE
    stSchema        VARCHAR := '';
    stEntidade      VARCHAR := '';
    inCodPeriodo    INTEGER;
    stSQL           VARCHAR;
BEGIN

    SELECT nspname
      INTO stSchema
      FROM pg_namespace
      JOIN pg_class
        ON pg_class.relnamespace = pg_namespace.oid
     WHERE pg_class.oid = TG_RELID
         ;

    IF substr(stSchema, length(stSchema)-1, 1) = '_' THEN
        stEntidade := substr(stSchema, length(stSchema)-1, 2);
    END IF;

    IF TG_OP = 'INSERT' THEN
        inCodPeriodo := selectintointeger('
                                              SELECT cod_periodo_movimentacao
                                                FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                            ORDER BY cod_periodo_movimentacao DESc
                                               LIMIT 1
                                                   ;
                                          ');

        stSQL := '
                   INSERT
                     INTO '|| stSchema ||'.contrato_servidor_situacao
                        ( cod_contrato
                        , situacao
                        , cod_periodo_movimentacao
                        , situacao_literal
                        )
                   VALUES
                        ( '|| NEW.cod_contrato ||'
                        , '|| quote_literal('A') ||'
                        , '|| inCodPeriodo ||'
                        , '|| quote_literal('Ativo') ||'
                        );
                 ';

        EXECUTE stSQL;
        RETURN NEW;
    
    ELSEIF TG_OP = 'DELETE' THEN
        stSQL := '
                   DELETE FROM '|| stSchema ||'.contrato_servidor_situacao
                    WHERE cod_contrato = '|| OLD.cod_contrato ||'
                        ;
                 ';
    	EXECUTE stSQL;
    	RETURN OLD;
    
    ELSE
        RETURN NEW;
    END IF;

END;
$$ LANGUAGE 'plpgsql';
