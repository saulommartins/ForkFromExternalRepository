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
/**
   * Funç PLSQL
   * Data de Criaç: 12/04/2007


   * @author Analista: Dagiane
   * @author Desenvolvedor: Diego Lemos de Souza

   * @package URBEM
   * @subpackage

   $Revision: 23177 $
   $Name$
   $Author: souzadl $
   $Date: 2007-06-12 11:45:22 -0300 (Ter, 12 Jun 2007) $

   * Casos de uso: uc-04.05.18
*/

CREATE OR REPLACE FUNCTION processarSomatorioDeducoesRescisao(BOOLEAN,VARCHAR) RETURNS NUMERIC as $$
DECLARE
    boComPensao                 ALIAS FOR $1;
    stDesdobramento             ALIAS FOR $2;
    nuDeducoes                  NUMERIC := 0.00;
    nuValorDeducaoDependente    NUMERIC := 0.00;
    nuValorTemp                 NUMERIC := 0.00;
    inNumCGM                    INTEGER;
    inCodPeriodoMovimentacao    INTEGER;
    inCodPrevidencia            INTEGER;
    inCodTipo                   INTEGER;
    dtVigencia                  VARCHAR := '';
    stSql                       VARCHAR := '';
    stSituacaoFolhaComplementar VARCHAR := '';
    stSituacaoFolhaSalario      VARCHAR := '';
    stEntidade                  VARCHAR;
    stSqlAux                    VARCHAR := '';
BEGIN
    inCodPeriodoMovimentacao    := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    inCodPrevidencia            := recuperarBufferInteiro('inCodPrevidenciaOficial');
    inNumCGM                    := recuperarBufferInteiro('inNumCGM');
    dtVigencia                  := recuperarBufferTexto('dtVigenciaPrevidencia');
    stSituacaoFolhaComplementar := pega0SituacaoDaFolhaComplementar();
    stSituacaoFolhaSalario      := pega0SituacaoDaFolhaSalario();
    stEntidade                  := recuperarBufferTexto('stEntidade');

    inCodTipo := 4;
    IF boComPensao IS TRUE THEN
        inCodTipo := 5;
    END IF;
    
    -- Recupera o valor da deduç da folha rescisãde acordo com o desdobramento
    nuDeducoes := selectIntoNumeric('SELECT sum(evento_rescisao_calculado.valor) as valor
                                       FROM folhapagamento'||stEntidade||'.tabela_irrf_evento
                                          , (  SELECT cod_tabela
                                                    , max(timestamp) as timestamp
                                                 FROM folhapagamento'||stEntidade||'.tabela_irrf_evento
                                             GROUP BY cod_tabela) as max_tabela_irrf_evento
                                          , folhapagamento'||stEntidade||'.registro_evento_rescisao
                                          , folhapagamento'||stEntidade||'.ultimo_registro_evento_rescisao
                                          , folhapagamento'||stEntidade||'.evento_rescisao_calculado
                                          , pessoal'||stEntidade||'.servidor_contrato_servidor
                                          , pessoal'||stEntidade||'.servidor
                                      WHERE tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela
                                        AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp
                                        AND tabela_irrf_evento.cod_evento = ultimo_registro_evento_rescisao.cod_evento
                                        AND ultimo_registro_evento_rescisao.cod_registro     = evento_rescisao_calculado.cod_registro
                                        AND ultimo_registro_evento_rescisao.cod_evento       = evento_rescisao_calculado.cod_evento
                                        AND ultimo_registro_evento_rescisao.desdobramento    = evento_rescisao_calculado.desdobramento
                                        AND ultimo_registro_evento_rescisao.timestamp        = evento_rescisao_calculado.timestamp_registro
                                        AND ultimo_registro_evento_rescisao.cod_registro     = registro_evento_rescisao.cod_registro
                                        AND ultimo_registro_evento_rescisao.cod_evento       = registro_evento_rescisao.cod_evento
                                        AND ultimo_registro_evento_rescisao.desdobramento    = registro_evento_rescisao.desdobramento
                                        AND ultimo_registro_evento_rescisao.timestamp        = registro_evento_rescisao.timestamp
                                        AND registro_evento_rescisao.cod_contrato = servidor_contrato_servidor.cod_contrato
                                        AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                        AND servidor.numcgm = '||inNumCGM||'
                                        AND tabela_irrf_evento.cod_tipo = '||inCodTipo||'
                                        AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                        AND registro_evento_rescisao.desdobramento = '''||stDesdobramento||''' ');

    -- Inicio da busca das deduçs da folha complementar
    -- De acordo com o desdobramente, busca a deduç da complementar
    IF stSituacaoFolhaComplementar = 'f' THEN
        stSql := 'SELECT sum(evento_complementar_calculado.valor) as valor
                    FROM folhapagamento'||stEntidade||'.tabela_irrf_evento 
              INNER JOIN (  SELECT cod_tabela
                                 , max(timestamp) as timestamp
                              FROM folhapagamento'||stEntidade||'.tabela_irrf_evento 
                          GROUP BY cod_tabela) as max_tabela_irrf_evento
                      ON tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela
                     AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp
              INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento_complementar 
                      ON tabela_irrf_evento.cod_evento = ultimo_registro_evento_complementar.cod_evento
              INNER JOIN folhapagamento'||stEntidade||'.evento_complementar_calculado
                      ON ultimo_registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                     AND ultimo_registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                     AND ultimo_registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                     AND ultimo_registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro
              INNER JOIN folhapagamento'||stEntidade||'.registro_evento_complementar
                      ON ultimo_registro_evento_complementar.cod_registro = registro_evento_complementar.cod_registro
                     AND ultimo_registro_evento_complementar.cod_evento = registro_evento_complementar.cod_evento
                     AND ultimo_registro_evento_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao
                     AND ultimo_registro_evento_complementar.timestamp = registro_evento_complementar.timestamp    
              INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                      ON registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
              INNER JOIN pessoal'||stEntidade||'.servidor
                      ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                   WHERE servidor.numcgm = '||inNumCGM||'
                     AND tabela_irrf_evento.cod_tipo = '||inCodTipo||'
                     AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'';
    
        -- Desdobramento: A Aviso Previo / S Saldo Salario
        stSqlAux := stSql;
        IF trim(stDesdobramento) IN ('A','S') THEN 
            -- Dedudaç da Complementar, Configuraç saláo
            stSql := stSql || ' AND evento_complementar_calculado.cod_configuracao = 1';
        END IF;
    
        -- Desdobramento: V Ferias vencidas / P Ferias Proporcional
        IF trim(stDesdobramento) IN ('V','P') THEN 
            -- Dedudaç da Complementar, Configuraç Féas
            stSql := stSql || ' AND evento_complementar_calculado.cod_configuracao = 2';
        END IF;
    
        -- Desdobramento: 13 Salario
        IF trim(stDesdobramento) = 'D' THEN 
            -- Dedudaç da Complementar, Configuraç Démo
            stSql := stSql || ' AND evento_complementar_calculado.cod_configuracao = 3';
        END IF;
   
        nuDeducoes := nuDeducoes + COALESCE(selectIntoNumeric(stSql),0);

        IF trim(stDesdobramento) IN ('V','P') AND (nuDeducoes = 0.00 OR nuDeducoes IS NULL) THEN
           stSqlAux := stSqlAux || ' AND evento_complementar_calculado.cod_configuracao = 1';
           nuDeducoes := nuDeducoes + COALESCE(selectIntoNumeric(stSqlAux),0);      
        END IF;

    END IF;
	
   IF stSituacaoFolhaSalario = 'f' AND trim(stDesdobramento) IN ('A','S') THEN
        stSql := 'SELECT sum(evento_calculado.valor) as valor
                    FROM folhapagamento'||stEntidade||'.tabela_irrf_evento 
              INNER JOIN (  SELECT cod_tabela
                                 , max(timestamp) as timestamp
                              FROM folhapagamento'||stEntidade||'.tabela_irrf_evento 
                          GROUP BY cod_tabela) as max_tabela_irrf_evento
                      ON tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela
                     AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp
              INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento 
                      ON tabela_irrf_evento.cod_evento = ultimo_registro_evento.cod_evento
              INNER JOIN folhapagamento'||stEntidade||'.evento_calculado
                      ON ultimo_registro_evento.cod_registro = evento_calculado.cod_registro
                     AND ultimo_registro_evento.cod_evento = evento_calculado.cod_evento
                     AND ultimo_registro_evento.timestamp = evento_calculado.timestamp_registro
              INNER JOIN folhapagamento'||stEntidade||'.registro_evento
                      ON ultimo_registro_evento.cod_registro = registro_evento.cod_registro
                     AND ultimo_registro_evento.cod_evento = registro_evento.cod_evento
                     AND ultimo_registro_evento.timestamp = registro_evento.timestamp    
              INNER JOIN folhapagamento'||stEntidade||'.registro_evento_periodo
                      ON ultimo_registro_evento.cod_registro = registro_evento_periodo.cod_registro					 
              INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                      ON registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
              INNER JOIN pessoal'||stEntidade||'.servidor
                      ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                   WHERE servidor.numcgm = '||inNumCGM||'
                     AND tabela_irrf_evento.cod_tipo = '||inCodTipo||'
                     AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                     AND evento_calculado.desdobramento IS NULL';	 
        -- Deducao da salario, desdobramento em branco  = salario
	    nuDeducoes := nuDeducoes + COALESCE(selectIntoNumeric(stSql),0);
    END IF;

    IF nuDeducoes IS NULL THEN
        nuDeducoes := 0.00;
    END IF;

    RETURN nuDeducoes;
END;
$$ LANGUAGE 'plpgsql';
