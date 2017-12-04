<?php
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
?>
<?php
/**
    * 
    * Data de Criação   : 16/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Michel Teixeira
    $Id: TTCEPELiquidacaoRestosEstorno.class.php 60545 2014-10-28 11:48:19Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPERemuneracaoAgentesEletivos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPERemuneracaoAgentesEletivos()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql = " SELECT retorno.mes_competencia,
                          retorno.cpf,
                          retorno.tipo_remuneracao,
                          retorno.norma,
                          retorno.ano_norma,
                          retorno.num_norma,
                          retorno.nom_agente,
                          retorno.cargo,
                          retorno.historico,
                          COALESCE(SUM(retorno.remuneracao_permitida),0.00) AS remuneracao_permitida,
                          COALESCE(SUM(retorno.remuneracao_percebida),0.00) AS remuneracao_percebida
                          
                    FROM (
                            SELECT contratos.mes_competencia,
                                   contratos.cpf,
                                   contratos.tipo_remuneracao,
                                   contratos.norma,
                                   contratos.ano_norma,
                                   contratos.num_norma,
                                   contratos.nom_agente,
                                   contratos.cargo,
                                   contratos.historico,
                                   COALESCE(SUM(eventos.valor),0.00) AS remuneracao_permitida,
                                   COALESCE(SUM(eventos.valor),0.00) AS remuneracao_percebida
                                   
                            FROM (
                                    SELECT COALESCE(SUM(evento_calculado.valor),0.00) AS valor
                                         , registro_evento_periodo.cod_contrato
                                         , registro_evento_periodo.cod_periodo_movimentacao
                                         
                                      FROM folhapagamento".$this->getDado('cod_entidade').".registro_evento_periodo
                                          , folhapagamento".$this->getDado('cod_entidade').".evento_calculado
                                          , folhapagamento".$this->getDado('cod_entidade').".evento
                                          , folhapagamento".$this->getDado('cod_entidade').".sequencia_calculo_evento
                                          , folhapagamento".$this->getDado('cod_entidade').".sequencia_calculo
                                          
                                     WHERE registro_evento_periodo.cod_registro             = evento_calculado.cod_registro
                                       AND evento_calculado.cod_evento                      = evento.cod_evento
                                       AND evento.cod_evento                                = sequencia_calculo_evento.cod_evento
                                       AND sequencia_calculo_evento.cod_sequencia           = sequencia_calculo.cod_sequencia
                                       AND evento.natureza = 'P'
                                       
                                  GROUP BY cod_contrato, registro_evento_periodo.cod_periodo_movimentacao
                                           
                                    UNION
                                    
                                     SELECT COALESCE(SUM(evento_ferias_calculado.valor),0.00) AS valor
                                          , registro_evento_ferias.cod_contrato
                                          , registro_evento_ferias.cod_periodo_movimentacao
                                         
                                     FROM folhapagamento".$this->getDado('cod_entidade').".registro_evento_ferias
                                         , folhapagamento".$this->getDado('cod_entidade').".evento_ferias_calculado
                                         , folhapagamento".$this->getDado('cod_entidade').".evento
                                         , folhapagamento".$this->getDado('cod_entidade').".sequencia_calculo_evento
                                         , folhapagamento".$this->getDado('cod_entidade').".sequencia_calculo
                                         
                                     WHERE registro_evento_ferias.cod_registro             = evento_ferias_calculado.cod_registro
                                       AND registro_evento_ferias.desdobramento            = evento_ferias_calculado.desdobramento
                                       AND registro_evento_ferias.timestamp                = evento_ferias_calculado.timestamp_registro
                                       AND registro_evento_ferias.cod_evento               = evento_ferias_calculado.cod_evento
                                       AND evento_ferias_calculado.cod_evento              = evento.cod_evento
                                       AND evento.cod_evento                               = sequencia_calculo_evento.cod_evento
                                       AND sequencia_calculo_evento.cod_sequencia          = sequencia_calculo.cod_sequencia
                                       AND evento.natureza = 'P'
                                       
                                  GROUP BY cod_contrato, registro_evento_ferias.cod_periodo_movimentacao
                                              
                                     UNION
                                     
                                    SELECT COALESCE(SUM(evento_decimo_calculado.valor),0.00) AS valor
                                         , registro_evento_decimo.cod_contrato
                                         , registro_evento_decimo.cod_periodo_movimentacao
                                        
                                    FROM folhapagamento".$this->getDado('cod_entidade').".registro_evento_decimo
                                        , folhapagamento".$this->getDado('cod_entidade').".evento_decimo_calculado
                                        , folhapagamento".$this->getDado('cod_entidade').".evento
                                        , folhapagamento".$this->getDado('cod_entidade').".sequencia_calculo_evento
                                        , folhapagamento".$this->getDado('cod_entidade').".sequencia_calculo
                                        
                                    WHERE registro_evento_decimo.cod_registro             = evento_decimo_calculado.cod_registro
                                      AND registro_evento_decimo.cod_evento               = evento_decimo_calculado.cod_evento
                                      AND registro_evento_decimo.desdobramento            = evento_decimo_calculado.desdobramento
                                      AND registro_evento_decimo.timestamp                = evento_decimo_calculado.timestamp_registro
                                      AND evento_decimo_calculado.cod_evento              = evento.cod_evento
                                      AND evento.cod_evento                               = sequencia_calculo_evento.cod_evento
                                      AND sequencia_calculo_evento.cod_sequencia          = sequencia_calculo.cod_sequencia
                                      AND evento.natureza = 'P'
                                      
                                 GROUP BY cod_contrato, registro_evento_decimo.cod_periodo_movimentacao
                                          
                                    UNION
                                    
                                   SELECT COALESCE(SUM(evento_rescisao_calculado.valor),0.00) AS valor
                                        , registro_evento_rescisao.cod_contrato
                                        , registro_evento_rescisao.cod_periodo_movimentacao
                                        
                                     FROM folhapagamento".$this->getDado('cod_entidade').".registro_evento_rescisao
                                        , folhapagamento".$this->getDado('cod_entidade').".evento_rescisao_calculado
                                        , folhapagamento".$this->getDado('cod_entidade').".evento
                                        , folhapagamento".$this->getDado('cod_entidade').".sequencia_calculo_evento
                                        , folhapagamento".$this->getDado('cod_entidade').".sequencia_calculo
                                        
                                    WHERE registro_evento_rescisao.cod_registro             = evento_rescisao_calculado.cod_registro
                                      AND registro_evento_rescisao.cod_evento               = evento_rescisao_calculado.cod_evento
                                      AND registro_evento_rescisao.desdobramento            = evento_rescisao_calculado.desdobramento
                                      AND registro_evento_rescisao.timestamp                = evento_rescisao_calculado.timestamp_registro
                                      AND evento_rescisao_calculado.cod_evento              = evento.cod_evento
                                      AND evento.cod_evento                                 = sequencia_calculo_evento.cod_evento
                                      AND sequencia_calculo_evento.cod_sequencia            = sequencia_calculo.cod_sequencia
                                      AND evento.natureza = 'P'
                                      
                                    GROUP BY cod_contrato, registro_evento_rescisao.cod_periodo_movimentacao
                                    
                                    UNION
                                    
                                   SELECT COALESCE(SUM(evento_complementar_calculado.valor),0.00) AS valor
                                        , registro_evento_complementar.cod_contrato
                                        , registro_evento_complementar.cod_periodo_movimentacao
                                        
                                     FROM folhapagamento".$this->getDado('cod_entidade').".registro_evento_complementar
                                        , folhapagamento".$this->getDado('cod_entidade').".evento_complementar_calculado
                                        , folhapagamento".$this->getDado('cod_entidade').".evento
                                        , folhapagamento".$this->getDado('cod_entidade').".sequencia_calculo_evento
                                        , folhapagamento".$this->getDado('cod_entidade').".sequencia_calculo
                                        
                                    WHERE registro_evento_complementar.cod_registro             = evento_complementar_calculado.cod_registro
                                      AND registro_evento_complementar.cod_evento               = evento_complementar_calculado.cod_evento
                                      AND registro_evento_complementar.cod_configuracao         = evento_complementar_calculado.cod_configuracao
                                      AND registro_evento_complementar.timestamp                = evento_complementar_calculado.timestamp_registro
                                      AND evento_complementar_calculado.cod_evento              = evento.cod_evento
                                      AND evento.cod_evento                                     = sequencia_calculo_evento.cod_evento
                                      AND sequencia_calculo_evento.cod_sequencia                = sequencia_calculo.cod_sequencia
                                      AND registro_evento_complementar.cod_complementar         = 0
                                      AND evento.natureza = 'P'
                                      
                                    GROUP BY cod_contrato, registro_evento_complementar.cod_periodo_movimentacao
                                    
                                ) AS eventos
                                
                            JOIN (
                                    SELECT contrato.cod_contrato,
                                           periodo_movimentacao.cod_periodo_movimentacao,
                                           TO_CHAR(periodo_movimentacao.dt_inicial,'mm') AS mes_competencia,
                                           sw_cgm_pessoa_fisica.cpf,
                                           agente_eletivo.cod_tipo_remuneracao AS tipo_remuneracao,
                                           agente_eletivo.cod_norma as norma,
                                           norma.exercicio AS ano_norma,
                                           norma.num_norma,
                                           sw_cgm.nom_cgm AS nom_agente,
                                           cargo.descricao AS cargo,
                                           ''::VARCHAR AS historico
                                           
                                    FROM pessoal".$this->getDado('cod_entidade').".contrato
                                    
                                    JOIN pessoal".$this->getDado('cod_entidade').".contrato_servidor
                                      ON contrato_servidor.cod_contrato = contrato.cod_contrato
                                    
                                    JOIN pessoal".$this->getDado('cod_entidade').".cargo
                                      ON cargo.cod_cargo = contrato_servidor.cod_cargo
                                    
                                    JOIN pessoal".$this->getDado('cod_entidade').".servidor_contrato_servidor
                                      ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
                                    
                                    JOIN pessoal".$this->getDado('cod_entidade').".servidor
                                      ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
                                      
                                    JOIN sw_cgm
                                      ON sw_cgm.numcgm = servidor.numcgm
                                    
                                    JOIN sw_cgm_pessoa_fisica
                                      ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                                    
                                    JOIN folhapagamento".$this->getDado('cod_entidade').".contrato_servidor_periodo
                                      ON contrato_servidor_periodo.cod_contrato = contrato.cod_contrato
                                    
                                    JOIN folhapagamento".$this->getDado('cod_entidade').".periodo_movimentacao
                                      ON periodo_movimentacao.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao
                                    
                                    JOIN tcepe.agente_eletivo
                                      ON agente_eletivo.cod_cargo = contrato_servidor.cod_cargo

                                    JOIN normas.norma
                                      ON norma.cod_norma = agente_eletivo.cod_norma
                                      
                                    WHERE periodo_movimentacao.dt_inicial >= TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                    AND periodo_movimentacao.dt_final <= TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                                ) AS contratos
                              ON contratos.cod_contrato = eventos.cod_contrato
                             AND contratos.cod_periodo_movimentacao = eventos.cod_periodo_movimentacao
                             
                        GROUP BY contratos.cod_contrato,
                                 contratos.cod_periodo_movimentacao,
                                 contratos.mes_competencia,
                                 contratos.cpf,
                                 contratos.tipo_remuneracao,
                                 contratos.norma,
                                 contratos.ano_norma,
                                 contratos.num_norma,
                                 contratos.nom_agente,
                                 contratos.cargo,
                                 contratos.historico
                                 
                        ORDER BY contratos.mes_competencia
                        ) AS retorno
                        
                GROUP BY retorno.mes_competencia,retorno.cpf,retorno.tipo_remuneracao,retorno.norma,retorno.ano_norma,retorno.num_norma,retorno.nom_agente,retorno.cargo,retorno.historico
                
                ORDER BY retorno.mes_competencia
        ";
        return $stSql;
    }
}

?>