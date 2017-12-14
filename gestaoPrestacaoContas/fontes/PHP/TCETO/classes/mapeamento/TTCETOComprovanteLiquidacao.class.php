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
    * Extensão da Classe de Mapeamento TTCETOComprovanteLiquidacao
    *
    * Data de Criação: 27/05/2014
    *
    * @author: Franver Sarmento de Moraes
    *
    * $Id: TTCETOComprovanteLiquidacao.class.php 60870 2014-11-19 18:56:35Z evandro $
    *
    * @ignore
    *
*/
class TTCETOComprovanteLiquidacao extends Persistente
{
    /**
    * Método Construtor
    * @access Public
    */
    public function TTCETOComprovanteLiquidacao()
    {
        parent::Persistente();
    }
    /**
     * Método para trazer todos os registros de Projeto Atividade, para o TCETO
     * @access Public
     * @param  Object  $rsRecordSet Objeto RecordSet
     * @param  String  $stCondicao  String de condição do SQL (WHERE)
     * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
     * @param  Boolean $boTransacao
     * @return Object  Objeto Erro
    */
    public function recuperaComprovanteLiquidacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaComprovanteLiquidacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaComprovanteLiquidacao()
    {
        $stSql = "
  SELECT *
    FROM (
          SELECT (SELECT PJ.cnpj
                    FROM orcamento.entidade
                    JOIN sw_cgm
                      ON sw_cgm.numcgm = entidade.numcgm
                    JOIN sw_cgm_pessoa_juridica AS PJ
                      ON sw_cgm.numcgm = PJ.numcgm
                   WHERE entidade.exercicio = '".$this->getDado('exercicio')."'
                     AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."
                 ) AS id_unidade_gestora
               , ".$this->getDado('bimestre')." AS bimestre
               , nota_liquidacao.exercicio AS exercicio
               , nota_liquidacao.exercicio_empenho || LPAD(nota_liquidacao.cod_empenho::varchar,9,'0') AS numero_empenho
               , nota_liquidacao.exercicio_empenho || LPAD(nota_liquidacao.cod_nota::varchar,9,'0') AS numero_liquidacao
               , nota_liquidacao_documento.cod_tipo AS tipo_documento
               , nota_liquidacao_documento.nro_documento AS numero_documento
               , '+' AS sinal
               , empenho.fn_consultar_valor_liquidado_nota(nota_liquidacao.exercicio, nota_liquidacao.cod_empenho, nota_liquidacao.cod_entidade, nota_liquidacao.cod_nota) AS valor
               , nota_liquidacao_documento.dt_documento AS data_documento
               , nota_liquidacao_documento.descricao AS descricao
               , nota_liquidacao_documento.autorizacao AS autorizacao_nota_fiscal
               , nota_liquidacao_documento.modelo AS modelo_nota_fiscal
            FROM empenho.nota_liquidacao
      INNER JOIN empenho.empenho
              ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
             AND empenho.cod_entidade = nota_liquidacao.cod_entidade
             AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
       LEFT JOIN empenho.empenho_anulado
              ON empenho_anulado.exercicio    = empenho.exercicio
             AND empenho_anulado.cod_empenho  = empenho.cod_empenho
             AND empenho_anulado.cod_entidade = empenho.cod_entidade
       LEFT JOIN tceto.nota_liquidacao_documento
              ON nota_liquidacao_documento.exercicio    = nota_liquidacao.exercicio
             AND nota_liquidacao_documento.cod_entidade = nota_liquidacao.cod_entidade
             AND nota_liquidacao_documento.cod_nota     = nota_liquidacao.cod_nota
           WHERE nota_liquidacao.exercicio = '".$this->getDado('exercicio')."'
             AND nota_liquidacao.cod_entidade = ".$this->getDado('cod_entidade')."
             AND nota_liquidacao.dt_liquidacao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                   AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
             AND empenho_anulado.cod_empenho IS NULL
        GROUP BY nota_liquidacao.exercicio
               , nota_liquidacao.cod_empenho
               , nota_liquidacao.cod_entidade
               , nota_liquidacao.cod_nota
               , nota_liquidacao_documento.cod_tipo
               , nota_liquidacao_documento.nro_documento
               , nota_liquidacao_documento.dt_documento
               , nota_liquidacao_documento.descricao
               , nota_liquidacao_documento.autorizacao
               , nota_liquidacao_documento.modelo
     UNION
          SELECT (SELECT PJ.cnpj
                    FROM orcamento.entidade
                    JOIN sw_cgm
                      ON sw_cgm.numcgm = entidade.numcgm
                    JOIN sw_cgm_pessoa_juridica AS PJ
                      ON sw_cgm.numcgm = PJ.numcgm
                   WHERE entidade.exercicio = '".$this->getDado('exercicio')."'
                     AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."
                 ) AS id_unidade_gestora
               , ".$this->getDado('bimestre')." AS bimestre
               , nota_liquidacao.exercicio AS exercicio
               , nota_liquidacao.exercicio_empenho || LPAD(nota_liquidacao.cod_empenho::varchar,9,'0') AS numero_empenho
               , nota_liquidacao.exercicio_empenho || LPAD(nota_liquidacao.cod_nota::varchar,9,'0') AS numero_liquidacao
               , nota_liquidacao_documento.cod_tipo AS tipo_documento
               , nota_liquidacao_documento.nro_documento AS numero_documento
               , '-' AS sinal
               , SUM(nota_liquidacao_item_anulado.vl_anulado) AS valor
               , nota_liquidacao_documento.dt_documento AS data_documento
               , nota_liquidacao_documento.descricao AS descricao
               , nota_liquidacao_documento.autorizacao AS autorizacao_nota_fiscal
               , nota_liquidacao_documento.modelo AS modelo_nota_fiscal
            FROM empenho.nota_liquidacao
      INNER JOIN empenho.empenho
              ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
             AND empenho.cod_entidade = nota_liquidacao.cod_entidade
             AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
       LEFT JOIN empenho.empenho_anulado
              ON empenho_anulado.exercicio    = empenho.exercicio
             AND empenho_anulado.cod_empenho  = empenho.cod_empenho
             AND empenho_anulado.cod_entidade = empenho.cod_entidade
      INNER JOIN empenho.nota_liquidacao_item
              ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
             AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
             AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota
      INNER JOIN empenho.nota_liquidacao_item_anulado
              ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
             AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
             AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
             AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
             AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
             AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
       LEFT JOIN tceto.nota_liquidacao_documento
              ON nota_liquidacao_documento.exercicio    = nota_liquidacao.exercicio
             AND nota_liquidacao_documento.cod_entidade = nota_liquidacao.cod_entidade
             AND nota_liquidacao_documento.cod_nota     = nota_liquidacao.cod_nota
           WHERE nota_liquidacao.exercicio = '".$this->getDado('exercicio')."'
             AND nota_liquidacao.cod_entidade = ".$this->getDado('cod_entidade')."
             AND TO_DATE(TO_CHAR(nota_liquidacao_item_anulado.timestamp, 'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                                                         AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
             AND empenho_anulado.cod_empenho IS NULL
        GROUP BY nota_liquidacao.exercicio
               , nota_liquidacao.cod_empenho
               , nota_liquidacao.cod_entidade
               , nota_liquidacao.cod_nota
               , nota_liquidacao_documento.cod_tipo
               , nota_liquidacao_documento.nro_documento
               , nota_liquidacao_item_anulado.exercicio
               , nota_liquidacao_item_anulado.cod_pre_empenho
               , nota_liquidacao_item_anulado.cod_entidade
               , nota_liquidacao_item_anulado.cod_nota
               , nota_liquidacao_documento.cod_tipo
               , nota_liquidacao_documento.nro_documento
               , nota_liquidacao_documento.dt_documento
               , nota_liquidacao_documento.descricao
               , nota_liquidacao_documento.autorizacao
               , nota_liquidacao_documento.modelo
         ) AS comprovante_liquidacao
   WHERE comprovante_liquidacao.valor > 0.00
ORDER BY comprovante_liquidacao.numero_liquidacao ASC
       , comprovante_liquidacao.sinal DESC        
        
        ";
               
        return $stSql;
    }

}
?>