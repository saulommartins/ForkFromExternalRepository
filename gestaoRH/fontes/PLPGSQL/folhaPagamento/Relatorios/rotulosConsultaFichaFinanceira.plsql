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
--    * Plpgsql
--    * Data de Criação: 07/11/2007
--
--
--    * @author Diego Lemos de Souza
--
--    * Casos de uso: uc-04.00.00
--
--    $Id: rotulosConsultaFichaFinanceira.sql 29220 2008-04-15 18:46:02Z souzadl $
--*/
CREATE OR REPLACE FUNCTION recuperaRotuloAcumuladosComMatricula(INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    inCodPrevidencia            ALIAS FOR $3;
    inNumCGM                    ALIAS FOR $4;
    stEntidade                  ALIAS FOR $5;
    stRotulo                    VARCHAR;
    stSelect                    VARCHAR;
    inRegistro                  INTEGER;
    reRegistro                  RECORD;
BEGIN
    stSelect := 'SELECT registro FROM pessoal'|| stEntidade ||'.contrato WHERE cod_contrato = '|| inCodContrato;
    inRegistro := selectIntoInteger(stSelect);

    stSelect := '
SELECT *
  FROM (
SELECT folhas.folha
  FROM (SELECT evento_complementar_calculado.cod_evento
             , registro_evento_complementar.cod_contrato
             , registro_evento_complementar.cod_periodo_movimentacao
             , ''(C''|| registro_evento_complementar.cod_complementar ||'')'' as folha
          FROM folhapagamento'|| stEntidade ||'.evento_complementar_calculado
             , folhapagamento'|| stEntidade ||'.registro_evento_complementar
             , pessoal'|| stEntidade ||'.servidor_contrato_servidor
             , pessoal'|| stEntidade ||'.servidor               
         WHERE evento_complementar_calculado.cod_registro = registro_evento_complementar.cod_registro
           AND evento_complementar_calculado.cod_evento = registro_evento_complementar.cod_evento
           AND evento_complementar_calculado.cod_configuracao = registro_evento_complementar.cod_configuracao
           AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp
           AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
           AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
           AND servidor.numcgm = '|| inNumCGM ||'             
           AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
           AND EXISTS (SELECT *
                         FROM folhapagamento'|| stEntidade ||'.complementar
                            , folhapagamento'|| stEntidade ||'.complementar_situacao
                            , (SELECT cod_periodo_movimentacao
                            , cod_complementar
                            ,  max(timestamp) as timestamp
                         FROM folhapagamento'|| stEntidade ||'.complementar_situacao
                     GROUP BY cod_periodo_movimentacao
                            , cod_complementar) as max_complementar_situacao
                        WHERE complementar.cod_complementar = complementar_situacao.cod_complementar
                          AND complementar_situacao.cod_complementar = max_complementar_situacao.cod_complementar
                          AND complementar_situacao.cod_periodo_movimentacao = max_complementar_situacao.cod_periodo_movimentacao
                          AND complementar_situacao.timestamp = max_complementar_situacao.timestamp
                          AND complementar_situacao.situacao = ''f''
                          AND complementar_situacao.cod_complementar = registro_evento_complementar.cod_complementar
                          AND complementar_situacao.cod_periodo_movimentacao = registro_evento_complementar.cod_periodo_movimentacao)             
      GROUP BY evento_complementar_calculado.cod_evento
             , registro_evento_complementar.cod_contrato
             , registro_evento_complementar.cod_periodo_movimentacao                          
             , registro_evento_complementar.cod_complementar
         UNION          
        SELECT evento_ferias_calculado.cod_evento
             , registro_evento_ferias.cod_contrato
             , registro_evento_ferias.cod_periodo_movimentacao
             , ''(F)'' as folha
          FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado
             , folhapagamento'|| stEntidade ||'.registro_evento_ferias
             , pessoal'|| stEntidade ||'.servidor_contrato_servidor
             , pessoal'|| stEntidade ||'.servidor             
         WHERE evento_ferias_calculado.cod_registro = registro_evento_ferias.cod_registro
           AND evento_ferias_calculado.cod_evento = registro_evento_ferias.cod_evento
           AND evento_ferias_calculado.desdobramento = registro_evento_ferias.desdobramento
           AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp           
           AND evento_ferias_calculado.desdobramento != ''D''
           AND registro_evento_ferias.cod_contrato = servidor_contrato_servidor.cod_contrato
           AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
           AND servidor.numcgm = '|| inNumCGM ||'             
           AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'           
      GROUP BY evento_ferias_calculado.cod_evento
             , registro_evento_ferias.cod_contrato
             , registro_evento_ferias.cod_periodo_movimentacao                                   
         UNION
        SELECT evento_calculado.cod_evento
             , registro_evento_periodo.cod_contrato
             , registro_evento_periodo.cod_periodo_movimentacao
             , ''(S)'' as folha
          FROM folhapagamento'|| stEntidade ||'.evento_calculado
             , folhapagamento'|| stEntidade ||'.registro_evento_periodo
             , pessoal'|| stEntidade ||'.servidor_contrato_servidor
             , pessoal'|| stEntidade ||'.servidor             
         WHERE evento_calculado.cod_registro = registro_evento_periodo.cod_registro
           AND (desdobramento IS NULL OR desdobramento = ''F'' OR desdobramento = ''A'')
           AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
           AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
           AND servidor.numcgm = '|| inNumCGM ||'             
           AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'           
      GROUP BY evento_calculado.cod_evento
             , registro_evento_periodo.cod_contrato
             , registro_evento_periodo.cod_periodo_movimentacao  ) AS folhas
     , (   SELECT contrato.*
                , CASE WHEN servidor.numcgm IS NOT NULL THEN servidor.numcgm
                  ELSE pensionista.numcgm END AS numcgm
             FROM pessoal'|| stEntidade ||'.contrato
        LEFT JOIN (SELECT servidor_contrato_servidor.cod_contrato
                        , servidor.numcgm
                     FROM pessoal'|| stEntidade ||'.servidor_contrato_servidor
                        , pessoal'|| stEntidade ||'.servidor
                    WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor) AS servidor
               ON contrato.cod_contrato = servidor.cod_contrato      
        LEFT JOIN (SELECT cod_contrato
                        , numcgm
                     FROM pessoal'|| stEntidade ||'.contrato_pensionista
                        , pessoal'|| stEntidade ||'.pensionista
                    WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                      AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) AS pensionista
               ON contrato.cod_contrato = pensionista.cod_contrato  ) as contratos  
     , folhapagamento'|| stEntidade ||'.evento                                          
 WHERE folhas.cod_contrato = contratos.cod_contrato
   AND folhas.cod_evento = evento.cod_evento
   AND EXISTS (SELECT previdencia_evento.cod_evento                                                                                    
                          FROM folhapagamento'|| stEntidade ||'.previdencia_evento                                                                                
                             , (  SELECT cod_previdencia                                                                                        
                                       , max(timestamp) as timestamp                                                                            
                                    FROM folhapagamento'|| stEntidade ||'.previdencia_evento                                                                      
                                GROUP BY cod_previdencia) as max_previdencia_evento                                                             
                             , folhapagamento'|| stEntidade ||'.tipo_evento_previdencia                                                                           
                         WHERE previdencia_evento.cod_tipo = tipo_evento_previdencia.cod_tipo                                                   
                           AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                      
                           AND previdencia_evento.timestamp  = max_previdencia_evento.timestamp                                                 
                           AND previdencia_evento.cod_previdencia = '|| inCodPrevidencia ||'                                   
                           AND tipo_evento_previdencia.cod_tipo IN (1,2)  
                           AND previdencia_evento.cod_evento = folhas.cod_evento )
   AND natureza = ''B''
UNION
SELECT folhas.folha
  FROM (SELECT evento_complementar_calculado.cod_evento
             , registro_evento_complementar.cod_contrato
             , registro_evento_complementar.cod_periodo_movimentacao
             , ''(C''|| registro_evento_complementar.cod_complementar||'')'' as folha
          FROM folhapagamento'|| stEntidade ||'.evento_complementar_calculado
             , folhapagamento'|| stEntidade ||'.registro_evento_complementar
             , pessoal'|| stEntidade ||'.servidor_contrato_servidor
             , pessoal'|| stEntidade ||'.servidor             
         WHERE evento_complementar_calculado.cod_registro = registro_evento_complementar.cod_registro
           AND evento_complementar_calculado.cod_evento = registro_evento_complementar.cod_evento
           AND evento_complementar_calculado.cod_configuracao = registro_evento_complementar.cod_configuracao
           AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp
           AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
           AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
           AND servidor.numcgm = '|| inNumCGM ||'             
           AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'           
           AND EXISTS (SELECT *
                         FROM folhapagamento'|| stEntidade ||'.complementar
                            , folhapagamento'|| stEntidade ||'.complementar_situacao
                            , (SELECT cod_periodo_movimentacao
                            , cod_complementar
                            ,  max(timestamp) as timestamp
                         FROM folhapagamento'|| stEntidade ||'.complementar_situacao
                     GROUP BY cod_periodo_movimentacao
                            , cod_complementar) as max_complementar_situacao
                        WHERE complementar.cod_complementar = complementar_situacao.cod_complementar
                          AND complementar_situacao.cod_complementar = max_complementar_situacao.cod_complementar
                          AND complementar_situacao.cod_periodo_movimentacao = max_complementar_situacao.cod_periodo_movimentacao
                          AND complementar_situacao.timestamp = max_complementar_situacao.timestamp
                          AND complementar_situacao.situacao = ''f''
                          AND complementar_situacao.cod_complementar = registro_evento_complementar.cod_complementar
                          AND complementar_situacao.cod_periodo_movimentacao = registro_evento_complementar.cod_periodo_movimentacao)             
      GROUP BY evento_complementar_calculado.cod_evento
             , registro_evento_complementar.cod_contrato
             , registro_evento_complementar.cod_periodo_movimentacao                          
             , registro_evento_complementar.cod_complementar                          
         UNION          
        SELECT evento_ferias_calculado.cod_evento
             , registro_evento_ferias.cod_contrato
             , registro_evento_ferias.cod_periodo_movimentacao
             , ''(F)'' as folha
          FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado
             , folhapagamento'|| stEntidade ||'.registro_evento_ferias
             , pessoal'|| stEntidade ||'.servidor_contrato_servidor
             , pessoal'|| stEntidade ||'.servidor             
         WHERE evento_ferias_calculado.cod_registro = registro_evento_ferias.cod_registro
           AND evento_ferias_calculado.cod_evento = registro_evento_ferias.cod_evento
           AND evento_ferias_calculado.desdobramento = registro_evento_ferias.desdobramento
           AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp           
           AND evento_ferias_calculado.desdobramento != ''D''
           AND registro_evento_ferias.cod_contrato = servidor_contrato_servidor.cod_contrato
           AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
           AND servidor.numcgm = '|| inNumCGM ||'             
           AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'           
      GROUP BY evento_ferias_calculado.cod_evento
             , registro_evento_ferias.cod_contrato
             , registro_evento_ferias.cod_periodo_movimentacao              
         UNION
        SELECT evento_calculado.cod_evento
             , registro_evento_periodo.cod_contrato
             , registro_evento_periodo.cod_periodo_movimentacao
             , ''(S)'' as folha
          FROM folhapagamento'|| stEntidade ||'.evento_calculado
             , folhapagamento'|| stEntidade ||'.registro_evento_periodo
             , pessoal'|| stEntidade ||'.servidor_contrato_servidor
             , pessoal'|| stEntidade ||'.servidor             
         WHERE evento_calculado.cod_registro = registro_evento_periodo.cod_registro
           AND (desdobramento IS NULL OR desdobramento = ''F'' OR desdobramento = ''A'')
           AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
           AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
           AND servidor.numcgm = '|| inNumCGM ||'             
           AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'           
      GROUP BY evento_calculado.cod_evento
             , registro_evento_periodo.cod_contrato
             , registro_evento_periodo.cod_periodo_movimentacao  ) AS folhas
     , (   SELECT contrato.*
             FROM pessoal'|| stEntidade ||'.contrato
        LEFT JOIN (SELECT servidor_contrato_servidor.cod_contrato
                        , servidor.numcgm
                     FROM pessoal'|| stEntidade ||'.servidor_contrato_servidor
                        , pessoal'|| stEntidade ||'.servidor
                    WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor) AS servidor
               ON contrato.cod_contrato = servidor.cod_contrato      
        LEFT JOIN (SELECT cod_contrato
                        , numcgm
                     FROM pessoal'|| stEntidade ||'.contrato_pensionista
                        , pessoal'|| stEntidade ||'.pensionista
                    WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                      AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) AS pensionista
               ON contrato.cod_contrato = pensionista.cod_contrato                                    
                     ) as contratos     
     , folhapagamento'|| stEntidade ||'.evento
 WHERE folhas.cod_contrato = contratos.cod_contrato
   AND folhas.cod_evento = evento.cod_evento
   AND EXISTS (SELECT tabela_irrf_evento.cod_evento                                                                                    
                              FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento                                                                                
                                 , (  SELECT cod_tabela                                                                                             
                                           , max(timestamp) as timestamp                                                                            
                                        FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento                                                                      
                                    GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                  
                                 , folhapagamento'|| stEntidade ||'.tipo_evento_irrf                                                                                  
                             WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                          
                               AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                
                               AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                 
                               AND tipo_evento_irrf.cod_tipo IN (7,4,5,6,3) 
                               AND folhas.cod_evento = tabela_irrf_evento.cod_evento)
   AND natureza = ''B''
) AS folhas
GROUP BY folhas.folha';
    stRotulo := 'Matrícula(s): ';
    FOR reRegistro IN EXECUTE stSelect LOOP
        stRotulo := stRotulo || inRegistro || reRegistro.folha ||'/';
    END LOOP;
    RETURN substr(stRotulo,1,length(stRotulo)-1);
END;
$$ LANGUAGE 'plpgsql';

--SELECT recuperaRotuloAcumuladosComMatricula(193,488,1,720,'') as rotulo;


CREATE OR REPLACE FUNCTION recuperaRotuloAcumuladosAteMatricula(INTEGER,INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    inCodPrevidencia            ALIAS FOR $3;
    inCodConfiguracao           ALIAS FOR $4;
    inNumCGM                    ALIAS FOR $5;
    stEntidade                  ALIAS FOR $6;
    stRotulo                    VARCHAR;
    stSelect                    VARCHAR;
    inRegistro                  INTEGER;
    reRegistro                  RECORD;
BEGIN
    stSelect := 'SELECT registro FROM pessoal'|| stEntidade ||'.contrato WHERE cod_contrato = '|| inCodContrato;
    inRegistro := selectIntoInteger(stSelect);

    stSelect := '
SELECT *
  FROM (
SELECT folhas.folha
  FROM (SELECT evento_complementar_calculado.cod_evento
             , registro_evento_complementar.cod_contrato
             , registro_evento_complementar.cod_periodo_movimentacao
             , ''(C''|| registro_evento_complementar.cod_complementar||'')'' as folha
          FROM folhapagamento'|| stEntidade ||'.evento_complementar_calculado
             , folhapagamento'|| stEntidade ||'.registro_evento_complementar
             , pessoal'|| stEntidade ||'.servidor_contrato_servidor
             , pessoal'|| stEntidade ||'.servidor                
         WHERE evento_complementar_calculado.cod_registro = registro_evento_complementar.cod_registro
           AND evento_complementar_calculado.cod_evento = registro_evento_complementar.cod_evento
           AND evento_complementar_calculado.cod_configuracao = registro_evento_complementar.cod_configuracao
           AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp
           AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
           AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
           AND servidor.numcgm = '|| inNumCGM ||'             
           AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'           
           ';           
IF inCodConfiguracao = '0' THEN
    stSelect := stSelect  ||'  AND registro_evento_complementar.cod_contrato != '|| inCodContrato;
END IF;
    stSelect := stSelect  ||'           
           AND EXISTS (SELECT *
                         FROM folhapagamento'|| stEntidade ||'.complementar
                            , folhapagamento'|| stEntidade ||'.complementar_situacao
                            , (SELECT cod_periodo_movimentacao
                            , cod_complementar
                            ,  max(timestamp) as timestamp
                         FROM folhapagamento'|| stEntidade ||'.complementar_situacao
                     GROUP BY cod_periodo_movimentacao
                            , cod_complementar) as max_complementar_situacao
                        WHERE complementar.cod_complementar = complementar_situacao.cod_complementar
                          AND complementar_situacao.cod_complementar = max_complementar_situacao.cod_complementar
                          AND complementar_situacao.cod_periodo_movimentacao = max_complementar_situacao.cod_periodo_movimentacao
                          AND complementar_situacao.timestamp = max_complementar_situacao.timestamp
                          AND complementar_situacao.situacao = ''f''
                          AND complementar_situacao.cod_complementar = registro_evento_complementar.cod_complementar
                          AND complementar_situacao.cod_periodo_movimentacao = registro_evento_complementar.cod_periodo_movimentacao)             
      GROUP BY evento_complementar_calculado.cod_evento
             , registro_evento_complementar.cod_contrato
             , registro_evento_complementar.cod_periodo_movimentacao                          
             , registro_evento_complementar.cod_complementar
         UNION          
        SELECT evento_ferias_calculado.cod_evento
             , registro_evento_ferias.cod_contrato
             , registro_evento_ferias.cod_periodo_movimentacao
             , ''(F)'' as folha
          FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado
             , folhapagamento'|| stEntidade ||'.registro_evento_ferias
             , pessoal'|| stEntidade ||'.servidor_contrato_servidor
             , pessoal'|| stEntidade ||'.servidor                
         WHERE evento_ferias_calculado.cod_registro = registro_evento_ferias.cod_registro
           AND evento_ferias_calculado.cod_evento = registro_evento_ferias.cod_evento
           AND evento_ferias_calculado.desdobramento = registro_evento_ferias.desdobramento
           AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp           
           AND evento_ferias_calculado.desdobramento != ''D''
           AND registro_evento_ferias.cod_contrato = servidor_contrato_servidor.cod_contrato
           AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
           AND servidor.numcgm = '|| inNumCGM ||'             
           AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'           
           ';
IF inCodConfiguracao = '2' THEN
    stSelect := stSelect  ||'  AND registro_evento_ferias.cod_contrato != '|| inCodContrato;
END IF;
    stSelect := stSelect  ||'           
      GROUP BY evento_ferias_calculado.cod_evento
             , registro_evento_ferias.cod_contrato
             , registro_evento_ferias.cod_periodo_movimentacao                                   
         UNION
        SELECT evento_calculado.cod_evento
             , registro_evento_periodo.cod_contrato
             , registro_evento_periodo.cod_periodo_movimentacao
             , ''(S)'' as folha
          FROM folhapagamento'|| stEntidade ||'.evento_calculado
             , folhapagamento'|| stEntidade ||'.registro_evento_periodo
             , pessoal'|| stEntidade ||'.servidor_contrato_servidor
             , pessoal'|| stEntidade ||'.servidor                
         WHERE evento_calculado.cod_registro = registro_evento_periodo.cod_registro
           AND (desdobramento IS NULL OR desdobramento = ''F'' OR desdobramento = ''A'')
           AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
           AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
           AND servidor.numcgm = '|| inNumCGM ||'             
           AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'           
           ';
IF inCodConfiguracao = '1' THEN
    stSelect := stSelect  ||'  AND registro_evento_periodo.cod_contrato != '|| inCodContrato;
END IF;           
    stSelect := stSelect  ||'   
      GROUP BY evento_calculado.cod_evento
             , registro_evento_periodo.cod_contrato
             , registro_evento_periodo.cod_periodo_movimentacao  ) AS folhas
     , (   SELECT contrato.*
                , CASE WHEN servidor.numcgm IS NOT NULL THEN servidor.numcgm
                  ELSE pensionista.numcgm END AS numcgm
             FROM pessoal'|| stEntidade ||'.contrato
        LEFT JOIN (SELECT servidor_contrato_servidor.cod_contrato
                        , servidor.numcgm
                     FROM pessoal'|| stEntidade ||'.servidor_contrato_servidor
                        , pessoal'|| stEntidade ||'.servidor
                    WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor) AS servidor
               ON contrato.cod_contrato = servidor.cod_contrato      
        LEFT JOIN (SELECT cod_contrato
                        , numcgm
                     FROM pessoal'|| stEntidade ||'.contrato_pensionista
                        , pessoal'|| stEntidade ||'.pensionista
                    WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                      AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) AS pensionista
               ON contrato.cod_contrato = pensionista.cod_contrato  ) as contratos  
     , folhapagamento'|| stEntidade ||'.evento                                          
 WHERE folhas.cod_contrato = contratos.cod_contrato
   AND folhas.cod_evento = evento.cod_evento
   AND EXISTS (SELECT previdencia_evento.cod_evento                                                                                    
                          FROM folhapagamento'|| stEntidade ||'.previdencia_evento                                                                                
                             , (  SELECT cod_previdencia                                                                                        
                                       , max(timestamp) as timestamp                                                                            
                                    FROM folhapagamento'|| stEntidade ||'.previdencia_evento                                                                      
                                GROUP BY cod_previdencia) as max_previdencia_evento                                                             
                             , folhapagamento'|| stEntidade ||'.tipo_evento_previdencia                                                                           
                         WHERE previdencia_evento.cod_tipo = tipo_evento_previdencia.cod_tipo                                                   
                           AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                      
                           AND previdencia_evento.timestamp  = max_previdencia_evento.timestamp                                                 
                           AND previdencia_evento.cod_previdencia = '|| inCodPrevidencia ||'                                   
                           AND tipo_evento_previdencia.cod_tipo IN (1,2)  
                           AND previdencia_evento.cod_evento = folhas.cod_evento )
   AND natureza = ''D'' 
UNION
SELECT folhas.folha
  FROM (SELECT evento_complementar_calculado.cod_evento
             , registro_evento_complementar.cod_contrato
             , registro_evento_complementar.cod_periodo_movimentacao
             , ''(C''|| registro_evento_complementar.cod_complementar||'')'' as folha
          FROM folhapagamento'|| stEntidade ||'.evento_complementar_calculado
             , folhapagamento'|| stEntidade ||'.registro_evento_complementar
             , pessoal'|| stEntidade ||'.servidor_contrato_servidor
             , pessoal'|| stEntidade ||'.servidor                
         WHERE evento_complementar_calculado.cod_registro = registro_evento_complementar.cod_registro
           AND evento_complementar_calculado.cod_evento = registro_evento_complementar.cod_evento
           AND evento_complementar_calculado.cod_configuracao = registro_evento_complementar.cod_configuracao
           AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp
           AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
           AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
           AND servidor.numcgm = '|| inNumCGM ||'             
           AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'           
           AND EXISTS (SELECT *
                         FROM folhapagamento'|| stEntidade ||'.complementar
                            , folhapagamento'|| stEntidade ||'.complementar_situacao
                            , (SELECT cod_periodo_movimentacao
                            , cod_complementar
                            ,  max(timestamp) as timestamp
                         FROM folhapagamento'|| stEntidade ||'.complementar_situacao
                     GROUP BY cod_periodo_movimentacao
                            , cod_complementar) as max_complementar_situacao
                        WHERE complementar.cod_complementar = complementar_situacao.cod_complementar
                          AND complementar_situacao.cod_complementar = max_complementar_situacao.cod_complementar
                          AND complementar_situacao.cod_periodo_movimentacao = max_complementar_situacao.cod_periodo_movimentacao
                          AND complementar_situacao.timestamp = max_complementar_situacao.timestamp
                          AND complementar_situacao.situacao = ''f''
                          AND complementar_situacao.cod_complementar = registro_evento_complementar.cod_complementar
                          AND complementar_situacao.cod_periodo_movimentacao = registro_evento_complementar.cod_periodo_movimentacao)';
IF inCodConfiguracao = '0' THEN
    stSelect := stSelect  ||'  AND registro_evento_complementar.cod_contrato != '|| inCodContrato;
END IF;
    stSelect := stSelect  ||'                                     
      GROUP BY evento_complementar_calculado.cod_evento
             , registro_evento_complementar.cod_contrato
             , registro_evento_complementar.cod_periodo_movimentacao                          
             , registro_evento_complementar.cod_complementar                          
         UNION          
        SELECT evento_ferias_calculado.cod_evento
             , registro_evento_ferias.cod_contrato
             , registro_evento_ferias.cod_periodo_movimentacao
             , ''(F)'' as folha
          FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado
             , folhapagamento'|| stEntidade ||'.registro_evento_ferias
             , pessoal'|| stEntidade ||'.servidor_contrato_servidor
             , pessoal'|| stEntidade ||'.servidor                
         WHERE evento_ferias_calculado.cod_registro = registro_evento_ferias.cod_registro
           AND evento_ferias_calculado.cod_evento = registro_evento_ferias.cod_evento
           AND evento_ferias_calculado.desdobramento = registro_evento_ferias.desdobramento
           AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp           
           AND evento_ferias_calculado.desdobramento != ''D''
           AND registro_evento_ferias.cod_contrato = servidor_contrato_servidor.cod_contrato
           AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
           AND servidor.numcgm = '|| inNumCGM ||'             
           AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'           
           ';
IF inCodConfiguracao = '2' THEN
    stSelect := stSelect  ||'  AND registro_evento_ferias.cod_contrato != '|| inCodContrato;
END IF;
    stSelect := stSelect  ||'           
      GROUP BY evento_ferias_calculado.cod_evento
             , registro_evento_ferias.cod_contrato
             , registro_evento_ferias.cod_periodo_movimentacao              
         UNION
        SELECT evento_calculado.cod_evento
             , registro_evento_periodo.cod_contrato
             , registro_evento_periodo.cod_periodo_movimentacao
             , ''(S)'' as folha
          FROM folhapagamento'|| stEntidade ||'.evento_calculado
             , folhapagamento'|| stEntidade ||'.registro_evento_periodo
             , pessoal'|| stEntidade ||'.servidor_contrato_servidor
             , pessoal'|| stEntidade ||'.servidor                
         WHERE evento_calculado.cod_registro = registro_evento_periodo.cod_registro
           AND (desdobramento IS NULL OR desdobramento = ''F'' OR desdobramento = ''A'')
           AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
           AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
           AND servidor.numcgm = '|| inNumCGM ||'             
           AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'           
           ';
IF inCodConfiguracao = '1' THEN
    stSelect := stSelect  ||'  AND registro_evento_periodo.cod_contrato != '|| inCodContrato;
END IF;
    stSelect := stSelect  ||'  
      GROUP BY evento_calculado.cod_evento
             , registro_evento_periodo.cod_contrato
             , registro_evento_periodo.cod_periodo_movimentacao  ) AS folhas
     , (   SELECT contrato.*
                , CASE WHEN servidor.numcgm IS NOT NULL THEN servidor.numcgm
                  ELSE pensionista.numcgm END AS numcgm
             FROM pessoal'|| stEntidade ||'.contrato
        LEFT JOIN (SELECT servidor_contrato_servidor.cod_contrato
                        , servidor.numcgm
                     FROM pessoal'|| stEntidade ||'.servidor_contrato_servidor
                        , pessoal'|| stEntidade ||'.servidor
                    WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor) AS servidor
               ON contrato.cod_contrato = servidor.cod_contrato      
        LEFT JOIN (SELECT cod_contrato
                        , numcgm
                     FROM pessoal'|| stEntidade ||'.contrato_pensionista
                        , pessoal'|| stEntidade ||'.pensionista
                    WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                      AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) AS pensionista
               ON contrato.cod_contrato = pensionista.cod_contrato                                    
                     ) as contratos     
     , folhapagamento'|| stEntidade ||'.evento
 WHERE folhas.cod_contrato = contratos.cod_contrato
   AND folhas.cod_evento = evento.cod_evento
   AND EXISTS (SELECT tabela_irrf_evento.cod_evento                                                                                    
                              FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento                                                                                
                                 , (  SELECT cod_tabela                                                                                             
                                           , max(timestamp) as timestamp                                                                            
                                        FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento                                                                      
                                    GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                  
                                 , folhapagamento'|| stEntidade ||'.tipo_evento_irrf                                                                                  
                             WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                          
                               AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                
                               AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                 
                               AND tipo_evento_irrf.cod_tipo IN (7,4,5,6,3) 
                               AND folhas.cod_evento = tabela_irrf_evento.cod_evento)
   AND natureza = ''D''
) AS folhas
GROUP BY folhas.folha';
    stRotulo := 'Matrícula(s): ';
    FOR reRegistro IN EXECUTE stSelect LOOP
        stRotulo := stRotulo || inRegistro || reRegistro.folha ||'/';
    END LOOP;
    RETURN substr(stRotulo,1,length(stRotulo)-1);
END;
$$ LANGUAGE 'plpgsql';

--SELECT recuperaRotuloAcumuladosAteMatricula(193,488,1,1,720,'') as rotulo;