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
--FUNÇÃO CRIADA PARA O TRIBUNAL DE CONTAS DO ESTADO DO MATO GROSSO DO SUL

CREATE OR REPLACE FUNCTION realizacaoReceitaFixaTribunal (character varying, numeric, character varying, integer, character varying, integer, integer) RETURNS INTEGER AS $$
DECLARE
    Exercicio ALIAS FOR $1;
    Valor ALIAS FOR $2;
    Complemento ALIAS FOR $3;
    CodLote ALIAS FOR $4;
    TipoLote ALIAS FOR $5;
    CodEntidade ALIAS FOR $6;
    CodHistorico ALIAS FOR $7;

    CodHistoricoInt INTEGER := 907;
    Sequencia INTEGER := 0;
    REREGISTROSCONTAFIXA RECORD;
    SQLCONTAFIXA VARCHAR := '';
BEGIN
    IF   CodHistorico  IS NOT NULL THEN
        CodHistoricoInt := CodHistorico;
    END IF;
    
    SQLCONTAFIXA := '
               SELECT debito.cod_estrutural AS estrutural_debito
                    , credito.cod_estrutural AS estrutural_credito
                    , debito.cod_plano AS plano_debito
                    , credito.cod_plano AS plano_credito
                    , debito.exercicio
                 FROM (
                        SELECT plano_conta.cod_estrutural
                             , plano_analitica.cod_plano
                             , plano_conta.exercicio
                             , plano_conta.escrituracao
                          FROM contabilidade.plano_conta
                    INNER JOIN contabilidade.plano_analitica
                            ON plano_conta.cod_conta = plano_analitica.cod_conta
                           AND plano_conta.exercicio = plano_analitica.exercicio
                         WHERE REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE ''621100000%''
                      ) AS debito
           INNER JOIN (
                        SELECT plano_conta.cod_estrutural
                             , plano_analitica.cod_plano
                             , plano_conta.exercicio
                             , plano_conta.escrituracao
                          FROM contabilidade.plano_conta
                    INNER JOIN contabilidade.plano_analitica
                            ON plano_conta.cod_conta = plano_analitica.cod_conta
                           AND plano_conta.exercicio = plano_analitica.exercicio
                         WHERE REPLACE(plano_conta.cod_estrutural, ''.'', '''') LIKE ''621200000%''
                      ) AS credito
                    ON debito.exercicio = credito.exercicio
                 WHERE debito.exercicio = '|| quote_literal(Exercicio) ||'
       ';

    FOR REREGISTROSCONTAFIXA IN EXECUTE SQLCONTAFIXA
    LOOP
        SEQUENCIA := FAZERLANCAMENTO(  REREGISTROSCONTAFIXA.estrutural_debito , REREGISTROSCONTAFIXA.estrutural_credito , CodHistoricoInt , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade , REREGISTROSCONTAFIXA.plano_debito, REREGISTROSCONTAFIXA.plano_credito );
    END LOOP;

    RETURN Sequencia;
END;
$$ language 'plpgsql';
