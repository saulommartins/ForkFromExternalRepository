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
    * Data de Criação   : 01/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Evandro Melos
    $Id: TTCEPEProgramas.class.php 60149 2014-10-02 12:35:22Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPETransferenciaRecebida extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPETransferenciaRecebida()
    {
        parent::Persistente();
    }


    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaArquivoTCEPEProgramas.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    
    function recuperaTransferenciaRecebida(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaTransferenciaRecebida",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }


    public function montaRecuperaTransferenciaRecebida()
    {
        $stSql = " SELECT 0 AS reservado_tce
                          , (retorno.vl_lancamento * -1) AS vl_mensal
                          , (SELECT valor FROM administracao.configuracao_entidade WHERE cod_modulo = 63 AND parametro = 'tcepe_codigo_unidade_gestora'
                                AND cod_entidade = tipo_transferencia_recebida.cod_entidade
                            ) AS unidade_gestora_beneficiada
                          , (SELECT valor FROM administracao.configuracao_entidade WHERE cod_modulo = 63 AND parametro = 'tcepe_codigo_unidade_gestora'
                                AND cod_entidade = tipo_transferencia_recebida.cod_entidade_transferidora
                            ) AS unidade_gestora_transferidora
                          , tipo_transferencia.cod_tipo AS transferencia
                          , recurso.cod_recurso AS fonte_recursos
                    FROM contabilidade.fn_relatorio_razao (
                                                             '".Sessao::getExercicio()."'
                                                             , ''
                                                             , '4.5.0.0.0.00.00.00.00.00'
                                                             , '4.5.9.9.9.99.99.99.99.99'
                                                             , '".$this->getDado('data_inicial')."'
                                                             , '".$this->getDado('data_final')."'
                                                             , '".$this->getDado('cod_entidade')."'
                                                             , '".$this->getDado('data_ant_inicial')."'
                                                             , '".$this->getDado('data_ant_final')."'
                                                             , 'N'
                                                             , 'N'
                                                        ) AS retorno ( 
                                                                         cod_lote          integer                                
                                                                        ,sequencia         integer                                
                                                                        ,cod_historico     integer                                
                                                                        ,nom_historico     varchar                                
                                                                        ,complemento       varchar                                
                                                                        ,observacao        text                                   
                                                                        ,exercicio         char(4)                                
                                                                        ,cod_entidade      integer                                
                                                                        ,tipo              char(1)                                
                                                                        ,vl_lancamento     numeric                                
                                                                        ,tipo_valor        char(1)                                
                                                                        ,dt_lote           varchar                                
                                                                        ,dt_lote_formatado date                                
                                                                        ,cod_plano         integer                                
                                                                        ,cod_estrutural    varchar                                
                                                                        ,nom_conta         varchar                                
                                                                        ,contra_partida    numeric                                
                                                                        ,saldo_anterior    numeric                                
                                                                        ,num_lancamentos   integer      
                                                                    )
                                                                    
                    JOIN tesouraria.transferencia
                      ON transferencia.cod_lote     = retorno.cod_lote
                     AND transferencia.tipo         = retorno.tipo
                     AND transferencia.cod_entidade = retorno.cod_entidade
                     AND transferencia.exercicio    = retorno.exercicio
                     
                    JOIN contabilidade.plano_analitica
  		      ON plano_analitica.cod_plano = retorno.cod_plano
  		     AND plano_analitica.exercicio = retorno.exercicio
                     
	       LEFT JOIN contabilidade.plano_recurso
  		      ON plano_recurso.cod_plano = plano_analitica.cod_plano
		     AND plano_recurso.exercicio = plano_analitica.exercicio
                     
	       LEFT JOIN orcamento.recurso
		      ON recurso.cod_recurso = plano_recurso.cod_recurso
		     AND recurso.exercicio = plano_recurso.exercicio
                     
	       LEFT JOIN tcepe.codigo_fonte_recurso
		      ON codigo_fonte_recurso.cod_recurso = recurso.cod_recurso
   		     AND codigo_fonte_recurso.exercicio = recurso.exercicio
                     
		    JOIN tcepe.tipo_transferencia_recebida
                      ON tipo_transferencia_recebida.cod_lote      = transferencia.cod_lote
                     AND tipo_transferencia_recebida.cod_entidade  = transferencia.cod_entidade
                     AND tipo_transferencia_recebida.exercicio     = transferencia.exercicio
                     AND tipo_transferencia_recebida.tipo          = transferencia.tipo
                     
		    JOIN tcepe.tipo_transferencia
                      ON tipo_transferencia.cod_tipo = tipo_transferencia_recebida.cod_tipo_tcepe
                     
		   WHERE retorno.tipo <> 'I'
                ";
                
        return $stSql;
    }

}
?>