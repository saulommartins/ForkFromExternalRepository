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
CREATE OR REPLACE FUNCTION  empenhoanulacaoliquidacaomodalidadeslicitacao(character varying, numeric, character varying, integer, character varying, integer, integer) RETURNS INTEGER AS $$
DECLARE
    Exercicio   ALIAS FOR $1;
    Valor       ALIAS FOR $2;
    Complemento ALIAS FOR $3;
    CodLote     ALIAS FOR $4;
    TipoLote    ALIAS FOR $5;
    CodEntidade ALIAS FOR $6;
    CodNota     ALIAS FOR $7;
    
    Modalidade VARCHAR := '';
    Sequencia INTEGER;
BEGIN
    Modalidade := pegaEmpenhoLiquidacaoModalidade(  Exercicio , CodNota , CodEntidade  );
    Modalidade := sem_acentos(Modalidade);

    IF EXERCICIO::integer = 2013 THEN
        IF   Modalidade  =  'Concurso' THEN
            Sequencia := FazerLancamento(  '622920601' , '622920401' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Convite' THEN
            Sequencia := FazerLancamento(  '622920602' , '622920402' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Tomada' THEN
            Sequencia := FazerLancamento(  '622920603' , '622920403' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Concorrencia' THEN
            Sequencia := FazerLancamento(  '622920604' , '622920404' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Dispensa' THEN
            Sequencia := FazerLancamento(  '622920606' , '622920406' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Inexigivel' THEN
            Sequencia := FazerLancamento(  '622920607' , '622920407' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Nao Aplicavel' THEN
            Sequencia := FazerLancamento(  '622920608' , '622920408' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Suprimentos' THEN
            Sequencia := FazerLancamento(  '622920609' , '622920409' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Consulta' THEN
            Sequencia := FazerLancamento(  '622920611' , '622920411' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Pregao Presencial' THEN
            Sequencia := FazerLancamento(  '622920612' , '622920412' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Pregao Eletronico' THEN
            Sequencia := FazerLancamento(  '622920612' , '622920412' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
    END IF;
    
    IF EXERCICIO::INTEGER <= 2012 THEN
        IF   Modalidade  =  'Concurso' THEN
            Sequencia := FazerLancamento(  '292410301000000' , '292410201000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Convite' THEN
            Sequencia := FazerLancamento(  '292410302000000' , '292410202000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Tomada' THEN
            Sequencia := FazerLancamento(  '292410303000000' , '292410203000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Concorrência' THEN
            Sequencia := FazerLancamento(  '292410304000000' , '292410204000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Dispensa' THEN
            Sequencia := FazerLancamento(  '292410306000000' , '292410206000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Inexigivel' THEN
            Sequencia := FazerLancamento(  '292410307000000' , '292410207000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Nao Aplicavel' THEN
            Sequencia := FazerLancamento(  '292410308000000' , '292410208000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Suprimentos' THEN
            Sequencia := FazerLancamento(  '292410309000000' , '292410209000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF    Modalidade  =  'Integracao' THEN
            Sequencia := FazerLancamento(  '292410310000000' , '292410210000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Pregao' THEN
            Sequencia := FazerLancamento(  '292410312000000' , '292410212000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Pregao Presencial' THEN
            Sequencia := FazerLancamento(  '292410312000000' , '292410212000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Pregao Eletronico' THEN
            Sequencia := FazerLancamento(  '292410312000000' , '292410212000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Chamada Publica' THEN
            Sequencia := FazerLancamento(  '292410313000000' , '292410213000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
        IF   Modalidade  =  'Registro de Precos' THEN
            Sequencia := FazerLancamento(  '292410314000000' , '292410214000000' , 905 , Exercicio , Valor , Complemento , CodLote , TipoLote , CodEntidade  );
        END IF;
    END IF;

    RETURN Sequencia;
END;
$$ LANGUAGE 'plpgsql'
