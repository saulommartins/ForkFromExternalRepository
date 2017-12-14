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
*
* Script de DDL e DML
*
* Versao 2.00.9
*
* Fabio Bertoldi - 20120718
*
*/

-------------------------
-- INCLUSAO DE MUNICIPIOS
-------------------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM sw_municipio
      WHERE nom_municipio = 'AceguÃ¡'
        AND cod_uf        = 23
          ;
    IF NOT FOUND THEN
        INSERT INTO sw_municipio VALUES ((SELECT MAX(cod_municipio)+1 FROM sw_municipio WHERE cod_uf = 23), 23, 'AceguÃ¡');
    END IF;

    PERFORM 1
       FROM sw_municipio
      WHERE nom_municipio = 'Pedras Altas'
        AND cod_uf        = 23
          ;
    IF NOT FOUND THEN
        INSERT INTO sw_municipio VALUES ((SELECT MAX(cod_municipio)+1 FROM sw_municipio WHERE cod_uf = 23), 23, 'Pedras Altas');
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #19400
----------------

ALTER TABLE administracao.modulo         ADD COLUMN ativo BOOLEAN NOT NULL DEFAULT TRUE;
ALTER TABLE administracao.funcionalidade ADD COLUMN ativo BOOLEAN NOT NULL DEFAULT TRUE;
ALTER TABLE administracao.acao           ADD COLUMN ativo BOOLEAN NOT NULL DEFAULT TRUE;

UPDATE administracao.modulo         SET ativo = FALSE WHERE cod_modulo         = 11;
UPDATE administracao.funcionalidade SET ativo = FALSE WHERE cod_funcionalidade = 212;
UPDATE administracao.funcionalidade SET ativo = FALSE WHERE cod_funcionalidade = 310;
UPDATE administracao.acao           SET ativo = FALSE WHERE cod_acao           = 1412;
UPDATE administracao.acao           SET ativo = FALSE WHERE cod_acao           = 1414;
UPDATE administracao.acao           SET ativo = FALSE WHERE cod_acao           = 1415;


----------------
-- Ticket #
----------------

CREATE OR REPLACE FUNCTION empenholiquidacaomodalidadeslicitacao(character varying, numeric, character varying, integer, character varying, integer, integer) RETURNS INTEGER AS $$
DECLARE
    Exercicio   ALIAS FOR $1;
    Valor       ALIAS FOR $2;
    Complemento ALIAS FOR $3;
    CodLote     ALIAS FOR $4;
    TipoLote    ALIAS FOR $5;
    CodEntidade ALIAS FOR $6;
    CodNota     ALIAS FOR $7;

    Modalidade  VARCHAR := '';
    Sequencia   INTEGER;
BEGIN
Modalidade := pegaEmpenhoLiquidacaoModalidade(  Exercicio , CodNota , CodEntidade  ); 
IF   Modalidade  =  'Concurso' THEN
    Sequencia := FazerLancamento(  '292410201000000' , '292410301000000' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  'Convite' THEN
    Sequencia := FazerLancamento(  '292410202000000' , '292410302000000' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  'Tomada' THEN
    Sequencia := FazerLancamento(  '292410203000000' , '292410303000000' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  'Concorrência' THEN
    Sequencia := FazerLancamento(  '292410204000000' , '292410304000000' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  'Dispensa' THEN
    Sequencia := FazerLancamento(  '292410206000000' , '292410306000000' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  'Inexigível' THEN
    Sequencia := FazerLancamento(  '292410207000000' , '292410307000000' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  'Não Aplicável' THEN
    Sequencia := FazerLancamento(  '292410208000000' , '292410308000000' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  'Suprimentos' THEN
    Sequencia := FazerLancamento(  '292410209000000' , '292410309000000' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF     Modalidade  =  'Integração' THEN
    Sequencia := FazerLancamento(  '292410210000000' , '292410310000000' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  'Pregão' THEN
    Sequencia := FazerLancamento(  '292410212000000' , '292410312000000' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  'Pregão Presencial' THEN
    Sequencia := FazerLancamento(  '292410212000000' , '292410312000000' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  'Pregão Eletrônico' THEN
    Sequencia := FazerLancamento(  '292410212000000' , '292410312000000' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  'Chamada Pública' THEN
    Sequencia := FazerLancamento(  '292410213000000' , '292410313000000' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
IF   Modalidade  =  'Registro de Preços' THEN
    Sequencia := FazerLancamento(  '292410214000000' , '292410314000000' , 902 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  ); 
END IF;
RETURN Sequencia;
END;
$$ LANGUAGE 'plpgsql';

