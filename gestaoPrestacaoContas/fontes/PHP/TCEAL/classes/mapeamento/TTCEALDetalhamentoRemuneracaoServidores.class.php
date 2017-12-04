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

    * Extensão da Classe de Mapeamento TTCEALDetalhamentoRemuneracaoServidores
    *
    * Data de Criação: 02/05/2014
    *
    * @author: Carolina Schwaab Marçal
    *
    * $Id: TTCEALDetalhamentoRemuneracaoServidores.class.php 59612 2014-09-02 12:00:51Z gelson $
    *
    * @ignore
    *
*/
class TTCEALDetalhamentoRemuneracaoServidores extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALDetalhamentoRemuneracaoServidores()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
    public function recuperaListarServidores(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
	$obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaListarServidores().$stFiltro.$stOrdem;
	$this->stDebug = $stSql;
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }
       
    public function montaRecuperaListarServidores()
    {
        $stSql = "
		SELECT cod_und_gestora
             , codigo_ua
             , ".$this->getDado('inBimestre')." AS bimestre
             , '".$this->getDado('stExercicio')."' AS exercicio
             , registro
             , tabela.cod_contrato
             , numcgm 
             , cpf
             , horas_mensais
             , horas_semanais::INTEGER AS horas_semanais
             , tabela.cod_periodo_movimentacao                         
             
             , CASE WHEN '13' = any(string_to_array(replace(eventos[7], '°', ''), ' ')) THEN
                      '13'
                     ELSE
                      '".$this->getDado('mes')."'
                END AS mes
             , eventos.valor
             , eventos.quantidade
             , eventos.codigo
             , eventos.cod_evento
             , eventos.descricao
             , eventos.natureza
             , eventos.desdobramento
        FROM(SELECT (SELECT sw_cgm_pessoa_juridica.cnpj
                       FROM sw_cgm_pessoa_juridica
                       JOIN sw_cgm
                         ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                       JOIN orcamento.entidade
                         ON entidade.numcgm = sw_cgm.numcgm
                      WHERE entidade.cod_entidade =  ".$this->getDado('inCodEntidade')."
                        AND entidade.exercicio = '".$this->getDado('stExercicio')."'
                    ) AS cod_und_gestora
                  , LPAD((SELECT configuracao_entidade.valor
                            FROM administracao.configuracao_entidade
                           WHERE configuracao_entidade.cod_modulo = 62
                             AND configuracao_entidade.exercicio = '".$this->getDado('stExercicio')."'
                             AND configuracao_entidade.parametro like 'tceal_configuracao_unidade_autonoma'
                             AND configuracao_entidade.cod_entidade =  ".$this->getDado('inCodEntidade')."
                         ), 4, '0'
                        ) AS codigo_ua
                  , registro
                  , cod_contrato
                  , retorno.numcgm 
                  , sw_cgm_pessoa_fisica.cpf
                  , retorno.horas_mensais
                  , padrao.horas_semanais
                  , ".$this->getDado('cod_periodo_movimentacao')." AS cod_periodo_movimentacao
                  , string_to_array(eventosCalculadosFolhaAnalitica(1,retorno.cod_contrato,".$this->getDado('cod_periodo_movimentacao').",0,'codigo','P','D','')::varchar,',','') AS eventos
         
               FROM folhaAnalitica(1,".$this->getDado('cod_periodo_movimentacao').",0,'','nom_cgm',0,'','','".$this->getDado('stExercicio')."') 
                    AS retorno
         INNER JOIN pessoal".$this->getDado('stEntidade').".servidor
                 ON retorno.numcgm = servidor.numcgm
         
         INNER JOIN sw_cgm_pessoa_fisica
                 ON sw_cgm_pessoa_fisica.numcgm = servidor.numcgm
         
         INNER JOIN pessoal.cargo_padrao
                 ON cargo_padrao.cod_cargo = retorno.cod_contrato
                     
         INNER JOIN folhapagamento.padrao
                 ON padrao.cod_padrao = cargo_padrao.cod_padrao
            ) AS tabela
   INNER JOIN (SELECT evento_calculado.valor
                    , evento_calculado.quantidade
                    , evento.codigo
                    , evento.cod_evento
                    , evento.descricao
                    , evento.natureza
                    , CASE WHEN evento_calculado.desdobramento IS NULL THEN ''
                      ELSE evento_calculado.desdobramento END AS desdobramento
                    , registro_evento_periodo.cod_contrato
                    , registro_evento_periodo.cod_periodo_movimentacao
                 FROM folhapagamento.registro_evento_periodo
                    , folhapagamento.evento_calculado
                    , folhapagamento.evento
                    , folhapagamento.sequencia_calculo_evento
                    , folhapagamento.sequencia_calculo
                WHERE registro_evento_periodo.cod_registro             = evento_calculado.cod_registro
                  AND evento_calculado.cod_evento                      = evento.cod_evento
                  AND evento.cod_evento                                = sequencia_calculo_evento.cod_evento
                  AND sequencia_calculo_evento.cod_sequencia           = sequencia_calculo.cod_sequencia
                  AND registro_evento_periodo.cod_periodo_movimentacao = ".$this->getDado('cod_periodo_movimentacao')."
                  -- AND registro_evento_periodo.cod_contrato             = 46
                  AND evento.natureza = ANY(STRING_TO_ARRAY('P,D', ','))
                ORDER BY codigo
              )  as eventos
           ON eventos.cod_contrato = tabela.cod_contrato
          AND eventos.cod_periodo_movimentacao = tabela.cod_periodo_movimentacao		    
     GROUP BY cod_und_gestora
		   , codigo_ua
		   , registro
		   , tabela.cod_contrato
		   , numcgm
		   , cpf
		   , horas_mensais
           , horas_semanais
		   , tabela.cod_periodo_movimentacao
		   , mes
		   , eventos.valor
           , eventos.quantidade
           , eventos.codigo
           , eventos.cod_evento
           , eventos.descricao
           , eventos.natureza
           , eventos.desdobramento
    ORDER BY registro ";
	return $stSql;
    }
    
    public function listarEventos(&$rsRecordSet,$stFiltro="",$stOrder=" ",$boTransacao="")
    {
        $stSql = " SELECT *
                        , CASE WHEN '13' = any(string_to_array(replace(descricaoe, '°', ''), ' ')) THEN
			       13::integer
                          ELSE
                               ".$this->getDado('mes')."
                          END AS mes
                     FROM eventosCalculadosFolhaAnalitica(1,".$this->getDado('cod_contrato').",".$this->getDado("cod_periodo_movimentacao").",0,'codigo','P','D','') as retorno; " ;

        return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }
    
}
?>
