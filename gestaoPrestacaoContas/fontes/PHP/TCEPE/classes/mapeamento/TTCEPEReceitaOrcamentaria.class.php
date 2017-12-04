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

class TTCEPEReceitaOrcamentaria extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPEReceitaOrcamentaria()
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
    function recuperaReceitaOrcamentaria(&$rsRecordSet,$stFiltro = "",$stOrder = "",$boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaReceitaOrcamentaria",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaReceitaOrcamentaria()
    {
        $stSql = "
          SELECT tipo_registro
                , SUM(vl_mensal) AS vl_mensal
                , receita_ug
                , fonte_recursos
             FROM (
                   SELECT 0 AS reservado_tce
                        , SUBSTR(REPLACE(retorno.cod_estrutural,'.',''),1,11) AS receita_ug
                        , retorno.tipo_registro
                        , CASE WHEN retorno.tipo_registro = 1 
                               THEN (retorno.arrecadado_periodo * -1) 
                               ELSE retorno.arrecadado_periodo 
                          END AS vl_mensal
                        , retorno.recurso AS fonte_recursos
                        , (SELECT publico.fn_nivel(retorno.cod_estrutural)) as nivel
                    FROM tcepe.receita_orcamentaria( '".$this->getDado('exercicio')."' 
                                                    , '".$this->getDado('data_inicial')."'
                                                    , '".$this->getDado('data_final')."'
                                                    , '".$this->getDado('cod_entidade')."'
                                                   ) AS retorno (                      
                                                      cod_estrutural      VARCHAR
                                                    , receita             INTEGER
                                                    , recurso             INTEGER
                                                    , descricao           VARCHAR
                                                    , arrecadado_periodo  NUMERIC
                                                    , tipo_registro       INTEGER
                                                   )
                ) AS consulta
                
                WHERE nivel = 5
                
                GROUP BY tipo_registro
                       , receita_ug
                       , fonte_recursos
                ORDER BY receita_ug ";
        return $stSql;
    }
}
?>