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
    * Extensão da Classe de Mapeamento TTCEALComprovanteLiquidacao
    *
    * Data de Criação: 02/06/2014
    *
    * @author: Michel Teixeira
    *
    $Id: TTCEALComprovanteLiquidacao.class.php 65764 2016-06-16 17:37:59Z lisiane $
    *
    * @ignore
    *
*/
class TTCEALComprovanteLiquidacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALComprovanteLiquidacao()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaComprovanteLiquidacao.
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
        $stSql = " SELECT
                        (   SELECT PJ.cnpj
                              FROM orcamento.entidade
                        INNER JOIN sw_cgm
                                ON sw_cgm.numcgm = entidade.numcgm
                        INNER JOIN sw_cgm_pessoa_juridica AS PJ
                                ON sw_cgm.numcgm = PJ.numcgm
                             WHERE entidade.exercicio = '".$this->getDado('exercicio')."'
                               AND entidade.cod_entidade = ".$this->getDado('und_gestora')."
                        ) AS Cod_Und_Gestora
                        , LPAD(CE.valor,4,'0') AS Codigo_UA
                        , ".$this->getDado('bimestre')." AS bimestre
                        , '".$this->getDado('exercicio')."' AS exercicio
                        , (NL.exercicio::varchar || TO_CHAR(NL.dt_liquidacao,'mm') || LPAD(NL.cod_nota::text,7,'0'))::varchar AS num_liquidacao
                        , (EE.exercicio::varchar || TO_CHAR(EE.dt_empenho,'mm') || LPAD(EE.cod_empenho::text,7,'0'))::varchar AS num_empenho
                        , NL.cod_entidade
                        , tipo_documento.cod_tipo AS Tipo_Documento
                        , documento.nro_documento AS Num_Documento
                        , TO_CHAR(documento.dt_documento,'dd/mm/yyyy') AS Data_Documento
                        , documento.descricao
                        , documento.autorizacao
                        , documento.modelo
                        , SUM(NLI.vl_total) AS valor
                        , documento.nro_xml_nfe
                                
                     FROM empenho.nota_liquidacao AS NL
                            
               INNER JOIN empenho.empenho AS EE
                       ON EE.exercicio     = NL.exercicio_empenho
                      AND EE.cod_entidade  = NL.cod_entidade
                      AND EE.cod_empenho   = NL.cod_empenho
               
               INNER JOIN empenho.nota_liquidacao_item AS NLI
                       ON NLI.exercicio    = NL.exercicio
                      AND NLI.cod_entidade = NL.cod_entidade
                      AND NLI.cod_nota     = NL.cod_nota
               
               INNER JOIN tceal.documento
                       ON documento.exercicio      = NL.exercicio
                      AND documento.cod_entidade   = NL.cod_entidade
                      AND documento.cod_nota       = NL.cod_nota
                            
               INNER JOIN tceal.tipo_documento
                       ON tipo_documento.cod_tipo  = documento.cod_tipo
                       
                LEFT JOIN administracao.configuracao_entidade AS CE
                       ON CE.exercicio    = '".$this->getDado('exercicio')."'
                      AND CE.cod_entidade = ".$this->getDado('und_gestora')."
                      AND CE.cod_modulo   = 62
                      AND CE.parametro    = 'tceal_configuracao_unidade_autonoma'
               
                    WHERE NL.exercicio = '".Sessao::getExercicio()."'
                      AND NL.cod_entidade IN (".$this->getDado('cod_entidade').")
                      AND NL.dt_liquidacao BETWEEN TO_DATE('".$this->getDado('dtInicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFinal')."','dd/mm/yyyy')
                      AND EE.cod_empenho NOT IN ( SELECT empenho_anulado.cod_empenho
                                                    FROM empenho.empenho_anulado
                                                   WHERE empenho_anulado.exercicio     = EE.exercicio
                                                     AND empenho_anulado.cod_entidade  = EE.cod_entidade
                                                     AND empenho_anulado.cod_empenho   = EE.cod_empenho
                                                )
                      AND NLI.num_item NOT IN ( SELECT NLIA.num_item
                                                  FROM empenho.nota_liquidacao_item_anulado as NLIA
                                                 WHERE NLIA.exercicio         = NLI.exercicio
                                                   AND NLIA.cod_nota         = NLI.cod_nota
                                                   AND NLIA.num_item         = NLI.num_item
                                                   AND NLIA.exercicio_item  = NLI.exercicio_item
                                                   AND NLIA.cod_pre_empenho = NLI.cod_pre_empenho
                                                   AND NLIA.cod_entidade    = NLI.cod_entidade
                                            ) 
                 GROUP BY NL.cod_nota
                        , NL.exercicio
                        , EE.cod_empenho
                        , NL.cod_entidade
                        , EE.exercicio
                        , EE.dt_empenho
                        , Tipo_Documento
                        , Num_Documento
                        , Data_Documento
                        , documento.descricao
                        , documento.autorizacao
                        , documento.modelo
                        , documento.nro_xml_nfe
                        , CE.valor
                 
                 ORDER BY NL.cod_nota
                     , EE.cod_empenho
                ";
        return $stSql;
    }
}
?>
