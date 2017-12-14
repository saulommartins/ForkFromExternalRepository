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
 * Nome: Eventos em Parcelas/Empréstimos nas Férias Descrição: Considera eventos em parcelas / Empréstimos nas Férias
 * Data de Criação   : 01/10/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */
CREATE OR REPLACE FUNCTION mediaFeriasValorQuantidadeParcelasEmprestimos() RETURNS Numeric as $$
DECLARE
    inCodContrato               INTEGER;
    inCodPeriodoMovimentacao    INTEGER;
    inCodEvento                 INTEGER;
    inCodRegistro               INTEGER;
    inDiasGozo                  INTEGER:=0;
    inContador                  INTEGER;
    inDiasAbono                 INTEGER;
    inParcela                   INTEGER;
    nuQuantidade                NUMERIC;
    nuRetorno                   NUMERIC:=0;
    stCompetenciaPagamento      VARCHAR;
    stCompetencia               VARCHAR;
    stSql                       VARCHAR;
    stEntidade                  VARCHAR;
    stTimestamp                 TIMESTAMP;
    reRegistro                  RECORD;
    dtInicio                    DATE;
    dtFim                       DATE;
    crCursor                    REFCURSOR;
BEGIN
    inCodContrato := recuperarBufferInteiro('inCodContrato');
    inCodPeriodoMovimentacao := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    --Se (campo pagar_13 = false) então
    IF pegaPagamentoUmTercoFerias(inCodContrato,inCodPeriodoMovimentacao) = 'f' THEN        
        inDiasAbono := recuperarBufferInteiro('inDiasAbono');

        --Se (mês do pagamento das férias = competência) então
        stCompetenciaPagamento := recuperarBufferTexto('stAnoPagamento') ||'-'|| recuperarBufferTexto('stMesPagamento');
        stCompetencia          := substr(recuperarBufferTexto('stDataFinalCompetencia'),1,7);
        IF stCompetenciaPagamento = stCompetencia THEN

            --Se (o contrato possui +15 dias de férias na competência) então
            --Início e fim das férias dentro da competência
            dtInicio := recuperarBufferTexto('stInicio')::DATE;
            dtFim    := recuperarBufferTexto('stFim')::DATE;
            IF  to_char(dtInicio,'yyyy-mm') = stCompetencia 
            AND to_char(dtFim,'yyyy-mm')    = stCompetencia THEN
                inDiasGozo := (dtFim-dtInicio)+1;
            ELSE
                --Início das férias dentro da competência
                IF to_char(dtInicio,'yyyy-mm') = stCompetencia THEN
                    inDiasGozo := (to_date(recuperarBufferTexto('stDataFinalCompetencia'),'yyyy-mm-dd')-dtInicio)+1;
                ELSE
                    --Fim das férias dentro da competência
                    IF to_char(dtFim,'yyyy-mm') = stCompetencia THEN
                        inDiasGozo := to_char(dtFim,'dd')::INTEGER;
                    END IF;
                END IF;
            END IF; 
            IF inDiasAbono IS NOT NULL AND inDiasAbono > 0 THEN
                inDiasGozo := (inDiasGozo*30)/(30-inDiasAbono);
            END IF;

            IF inDiasGozo > 15 THEN

                --Se folha salário estiver aberta
                IF pega0SituacaoDaFolhaSalario() = 'a' THEN
    
                    --Insere evento desc de empréstimo, com a mesma quantidade que está no salário 
                    inCodEvento := recuperarBufferInteiro('inCodEvento');
                    stEntidade := recuperarBufferTexto('stEntidade');

                    stSql := 'SELECT count(1)
                                FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                                JOIN folhapagamento'|| stEntidade ||'.registro_evento
                                  ON registro_evento_periodo.cod_registro = registro_evento.cod_registro
                                JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento
                                  ON registro_evento.cod_registro = ultimo_registro_evento.cod_registro
                               WHERE registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                 AND registro_evento_periodo.cod_contrato = '|| inCodContrato ||'
                                 AND registro_evento.cod_evento = '|| inCodEvento ||'
                                 AND registro_evento.proporcional is true
                                 AND registro_evento.quantidade = 0
                                 AND registro_evento.valor = 0';
                    inContador := selectIntoInteger(stSql);
                    IF inContador = 0 THEN
                        stSql := ' SELECT quantidade
                                    FROM tmp_registro_evento_ferias 
                                    WHERE cod_evento = '|| inCodEvento ||'
                                    AND lido_de = ''fixo_atual'' ';
                        nuRetorno := selectIntoNumeric(stSql);
    
                        --Insere evento desc de empréstimo na aba proporcionais zerada do salário
                        inCodRegistro := selectIntoInteger('SELECT max(cod_registro)+1 as cod_registro
                                                            FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo');
                        stTimestamp = now();
                        stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.registro_evento_periodo
                                (cod_registro,cod_contrato,cod_periodo_movimentacao)
                                VALUES ('|| inCodRegistro ||','|| inCodContrato ||','|| inCodPeriodoMovimentacao ||')';
                        EXECUTE stSql;
                        stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.registro_evento (cod_registro,timestamp,cod_evento,proporcional,valor,quantidade)
                                        VALUES ('|| inCodRegistro ||',TO_TIMESTAMP('|| quote_literal(stTimestamp) ||',''yyyy-mm-dd hh24:mi:ss.us''),'|| inCodEvento ||',true,0,0)';
                        EXECUTE stSql;
                        stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.ultimo_registro_evento (timestamp,cod_registro,cod_evento)
                                        VALUES (TO_TIMESTAMP('|| quote_literal(stTimestamp) ||',''yyyy-mm-dd hh24:mi:ss.us''),'|| inCodRegistro ||','|| inCodEvento ||')';
                        EXECUTE stSql;                    
                    END IF;
                END IF;            
            ELSE
                --Insere evento desc de empréstimo, somando +1 além da parcela do salário
                inCodEvento := recuperarBufferInteiro('inCodEvento');
                stSql := 'SELECT quantidade
                               , parcela
                            FROM tmp_registro_evento_ferias 
                           WHERE cod_evento = '|| inCodEvento ||'
                             AND lido_de = ''fixo_atual'' ';
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO nuQuantidade,inParcela;
                CLOSE crCursor;    
                nuRetorno := nuQuantidade + 1;
                IF nuRetorno > inParcela THEN
                    nuRetorno := 0;
                END IF;
            END IF;
        END IF;      
    END IF;
    RETURN nuRetorno;
END;
$$ language 'plpgsql';
