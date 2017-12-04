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
--/**
--    * Função PLSQL
--    * Data de Criação: 08/01/2009
--
--
--    * @author Analista: Dagiane Vieira
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23402 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-20 16:57:16 -0300 (Qua, 20 Jun 2007) $
--
--    * Casos de uso: uc-04.05.19
--*/

CREATE OR REPLACE FUNCTION processarValorDeducaoDependente() RETURNS NUMERIC AS $$
DECLARE
    nuValorDeducaoDependente    NUMERIC := 0.00;
    nuValorTemp                 NUMERIC := 0.00;
    stSql                       VARCHAR;
    stEntidade                  VARCHAR;
    stTipoFolha                 VARCHAR;
    stDesdobramento             VARCHAR;
    inNumCgm                    INTEGER;
    inCodPeriodoMovimentacao    INTEGER;
    inCodContrato               INTEGER;
    inCodTipo                   INTEGER;
    inCodTipoFerias             INTEGER;
    inCodTipoDecimo             INTEGER;
    inCodEvento                 INTEGER;
    inCodComplementar           INTEGER;
    inCodConfiguracao           INTEGER;
    boRetorno                   BOOLEAN;
BEGIN
    --Verificação da necessidade de incluir a dedução de dependente na soma da base de dedução
    --Foi criada a tabela deducao_dependente onde é armazenada a informação que diz se já
    --foi utilizada a dedução em alguma matrícula em alguma folha. Caso exista um registro
    --que corresponda ao cgm e periodo de movimentação em calculo quer disser que já
    --foi utilizada a dedução de dependente e por tanto não deve ser inserida novamente na soma de base.

    --OBS.:
    --Para funcionamento correto dessa PL, foi inserido no registro de evento uma verificação
    --que identifica se o contrato possui registros de eventos, caso não possua, é excluído
    --o dado da tabela folhapagamento'|| stEntidade ||'.deducao_dependente que identifica a utilização de valor
    --de dedução de dependente.

    --Também foi incluído o mesmo processo na PL copiarRegistroEventoSalarioParaRegistroEventoRescisao

    stTipoFolha := recuperarBufferTexto('stTipoFolha');
    stEntidade := recuperarBufferTexto('stEntidade');
    inNumCgm := recuperarBufferInteiro('inNumCgm');
    inCodPeriodoMovimentacao := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    inCodContrato := recuperarBufferInteiro('inCodContrato');

    --Código que verifica em qual folha está sendo feito o cálculo
    inCodTipoFerias = 1;
    inCodTipoDecimo = 4;
    IF stTipoFolha = 'F' THEN
        inCodTipo = inCodTipoFerias;
    END IF;
    IF stTipoFolha = 'S' THEN
        inCodTipo = 2;
    END IF;
    IF stTipoFolha = 'C' THEN
        inCodTipo = 3;
        inCodComplementar := recuperarBufferInteiro('inCodComplementar');
        inCodConfiguracao := recuperarBufferInteiro('inCodConfiguracao');
    END IF;
    IF stTipoFolha = 'D' THEN
        inCodTipo = 4;
    END IF;
    IF stTipoFolha = 'R' THEN
        inCodTipo = 5;
        stDesdobramento := recuperarBufferTexto('stDesdobramentoDedDependente');
    END IF;


    IF stTipoFolha = 'C' THEN
        stSql := '    SELECT TRUE AS retorno
                        FROM folhapagamento'|| stEntidade ||'.deducao_dependente
                  INNER JOIN folhapagamento'|| stEntidade ||'.deducao_dependente_complementar
                          ON deducao_dependente_complementar.numcgm = deducao_dependente.numcgm
                         AND deducao_dependente_complementar.cod_periodo_movimentacao = deducao_dependente.cod_periodo_movimentacao
                         AND deducao_dependente_complementar.cod_complementar = '|| inCodComplementar ||'
                       WHERE deducao_dependente.numcgm = '|| inNumCgm ||'
                         AND deducao_dependente.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                         AND deducao_dependente.cod_contrato = '|| inCodContrato ||'
                         AND deducao_dependente.cod_tipo = '|| inCodTipo;
    ELSE
        stSql := 'SELECT TRUE AS retorno
                    FROM folhapagamento'|| stEntidade ||'.deducao_dependente
                   WHERE deducao_dependente.numcgm = '|| inNumCgm ||'
                     AND deducao_dependente.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                     AND deducao_dependente.cod_contrato = '|| inCodContrato ||'
                     AND deducao_dependente.cod_tipo = '|| inCodTipo;
    END IF;
    boRetorno := selectIntoBoolean(stSql);        
    IF boRetorno IS TRUE THEN

        IF stTipoFolha = 'C' THEN
            stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.deducao_dependente_complementar 
                   WHERE numcgm = '|| inNumCgm ||'
                     AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                     AND cod_tipo = '|| inCodTipo;

            EXECUTE stSql;
        END IF;

        stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.deducao_dependente
                   WHERE numcgm = '|| inNumCgm ||'
                     AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                     AND cod_contrato = '|| inCodContrato ||'
                     AND cod_tipo = '|| inCodTipo;
        EXECUTE stSql;
    END IF;

    IF stTipoFolha = 'F' THEN
        stSql := 'SELECT TRUE AS retorno
                    FROM folhapagamento'|| stEntidade ||'.deducao_dependente
                   WHERE numcgm = '|| inNumCgm ||'
                     AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                     AND cod_tipo = '|| inCodTipoFerias;
    ELSE
        IF stTipoFolha = 'D' THEN
            stSql := 'SELECT TRUE AS retorno
                        FROM folhapagamento'|| stEntidade ||'.deducao_dependente
                       WHERE numcgm = '|| inNumCgm ||'
                         AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                         AND cod_tipo = '|| inCodTipoDecimo;            
        ELSE
            stSql := 'SELECT TRUE AS retorno
                        FROM folhapagamento'|| stEntidade ||'.deducao_dependente
                       WHERE numcgm = '|| inNumCgm ||'
                         AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                         AND cod_tipo NOT IN ('|| inCodTipoFerias ||','|| inCodTipoDecimo ||')';
        END IF;
    END IF;
    boRetorno := selectIntoBoolean(stSql);
    
    IF boRetorno IS NOT TRUE THEN
        stSql := 'SELECT tabela_irrf_evento.cod_evento
                    FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                       , (  SELECT cod_tabela
                                 , max(timestamp) as timestamp
                              FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                          GROUP BY cod_tabela) as max_tabela_irrf_evento
                   WHERE tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela
                     AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp
                     AND tabela_irrf_evento.cod_tipo = 1';
        inCodEvento := selectIntoInteger(stSql);

        --Folha Férias
        IF stTipoFolha = 'F' THEN
            stSql := 'SELECT evento_ferias_calculado.valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                           , folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                       WHERE registro_evento_ferias.cod_registro  = evento_ferias_calculado.cod_registro
                         AND registro_evento_ferias.cod_evento    = evento_ferias_calculado.cod_evento
                         AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                         AND registro_evento_ferias.timestamp     = evento_ferias_calculado.timestamp_registro
                         AND registro_evento_ferias.cod_evento = '|| inCodEvento ||'
                         AND registro_evento_ferias.cod_contrato = '|| inCodContrato ||'
                         AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao;
        END IF;
        --Folha Salário
        IF stTipoFolha = 'S' THEN
            stSql := 'SELECT evento_calculado.valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                           , folhapagamento'|| stEntidade ||'.evento_calculado
                       WHERE registro_evento_periodo.cod_registro  = evento_calculado.cod_registro
                         AND evento_calculado.cod_evento = '|| inCodEvento ||'
                         AND registro_evento_periodo.cod_contrato = '|| inCodContrato ||'
                         AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao;
        END IF;
        --Folha Complementar
        IF stTipoFolha = 'C' THEN
            stSql := 'SELECT evento_complementar_calculado.valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                           , folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                       WHERE registro_evento_complementar.cod_registro  = evento_complementar_calculado.cod_registro
                         AND registro_evento_complementar.cod_evento    = evento_complementar_calculado.cod_evento
                         AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                         AND registro_evento_complementar.timestamp     = evento_complementar_calculado.timestamp_registro
                         AND registro_evento_complementar.cod_evento = '|| inCodEvento ||'
                         AND registro_evento_complementar.cod_complementar = '|| inCodComplementar ||'
                         AND registro_evento_complementar.cod_configuracao = '|| inCodConfiguracao ||'
                         AND registro_evento_complementar.cod_contrato = '|| inCodContrato ||'
                         AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao;
        END IF;
        --Folha Rescisao
        IF stTipoFolha = 'R' THEN
            stSql := 'SELECT evento_rescisao_calculado.valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                           , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                       WHERE registro_evento_rescisao.cod_registro  = evento_rescisao_calculado.cod_registro
                         AND registro_evento_rescisao.cod_evento    = evento_rescisao_calculado.cod_evento
                         AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                         AND registro_evento_rescisao.timestamp     = evento_rescisao_calculado.timestamp_registro
                         AND registro_evento_rescisao.cod_evento = '|| inCodEvento ||'
                         AND registro_evento_rescisao.desdobramento = '|| quote_literal(stDesdobramento) ||'
                         AND registro_evento_rescisao.cod_contrato = '|| inCodContrato ||'
                         AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao;
        END IF;
        --Folha Décimo
        IF stTipoFolha = 'D' THEN
            stSql := 'SELECT evento_decimo_calculado.valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                           , folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                       WHERE registro_evento_decimo.cod_registro  = evento_decimo_calculado.cod_registro
                         AND registro_evento_decimo.cod_evento    = evento_decimo_calculado.cod_evento
                         AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                         AND registro_evento_decimo.timestamp     = evento_decimo_calculado.timestamp_registro
                         AND registro_evento_decimo.cod_evento = '|| inCodEvento ||'
                         AND registro_evento_decimo.cod_contrato = '|| inCodContrato ||'
                         AND registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao;
        END IF;
        nuValorTemp := selectIntoNumeric(stSql);
        IF nuValorTemp is not null THEN
            nuValorDeducaoDependente := criarBufferNumerico('nuValorDeducaoDependente'|| COALESCE(inCodConfiguracao::varchar, ''),nuValorTemp);

            stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.deducao_dependente 
                        (numcgm,cod_periodo_movimentacao,cod_contrato,cod_tipo) VALUES
                        ('|| inNumCgm ||','|| inCodPeriodoMovimentacao ||','|| inCodContrato ||','|| inCodTipo ||')';
            EXECUTE stSql;
            IF stTipoFolha = 'C' THEN
                stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.deducao_dependente_complementar 
                            (numcgm,cod_periodo_movimentacao,cod_tipo,cod_complementar) VALUES
                            ('|| inNumCgm ||','|| inCodPeriodoMovimentacao ||','|| inCodTipo ||','|| inCodComplementar ||')';
                EXECUTE stSql;
            END IF;
        END IF;        
    END IF;
    RETURN nuValorDeducaoDependente; 
END;
$$ LANGUAGE 'plpgsql';
