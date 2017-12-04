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
  * Página de
  * Data de criação : 25/10/2005

  * @copyright CCA Consultoria de Gestão Pública S/S Ltda.
  * @link http://www.ccanet.com.br CCA Consultoria de Gestão Pública S/S Ltda.

  * @author Analista:
  * @author Programador: Fernando Zank Correa Evangelista

  $Id: TPatrimonioBemPlanoAnalitica.class.php 43154 2009-11-20 11:16:13Z vitorhugo $

  Caso de uso: uc-03.01.09
  Caso de uso: uc-03.01.21

  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TPatrimonioBemPlanoAnalitica extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPatrimonioBemPlanoAnalitica()
    {
        parent::Persistente();
        $this->setTabela('patrimonio.bem_plano_analitica');
        $this->setCampoCod('cod_bem');
        $this->setComplementoChave('timestamp');
        $this->AddCampo('cod_bem','integer',true,'',true,false);
        $this->AddCampo('timestamp','timestamp',false,'',false,false);
        $this->AddCampo('exercicio','integer',true,'',true,false);
        $this->AddCampo('cod_plano','integer',true,'',true,false);
    }

   public function recuperaBemPlanoAnalitica(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
   {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaBemPlanoAnalitica().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaBemPlanoAnalitica()
    {
        $stSql  = "       SELECT                                            \n";
        $stSql .= "       	bem_plano_analitica.cod_bem                 \n";
        $stSql .= "       	,bem_plano_analitica.timestamp              \n";
        $stSql .= "       	,bem_plano_analitica.cod_plano              \n";
        $stSql .= "       	,bem_plano_analitica.exercicio              \n";
        $stSql .= "       FROM  patrimonio.bem_plano_analitica              \n";
        $stSql .= "            WHERE ";

        if ($this->getDado('cod_bem')) {
            $stSql.= " bem_plano_analitica.cod_bem = ".$this->getDado('cod_bem')."   AND ";
        }

        if ($this->getDado('timestamp')) {
            $stSql.= " bem_plano_analitica.timestamp = '".$this->getDado('timestamp')."'   AND ";
        }

        if ($this->getDado('cod_plano')) {
            $stSql.= " bem_plano_analitica.cod_plano = ".$this->getDado('cod_plano')."   AND ";
        }

        if ($this->getDado('exercicio')) {
            $stSql.= " bem_plano_analitica.exercicio = '".$this->getDado('exercicio')."'   AND ";
        }

        return substr($stSql,0,-6);
    }

    public function recuperaMaxTimestampBemPlanoAnalitica(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaMaxTimestampBemPlanoAnalitica",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaMaxTimestampBemPlanoAnalitica()
    {
        $stSql = "
                  SELECT  MAX(patrimonio.bem_plano_analitica.timestamp::timestamp) AS timestamp
                       ,  (SELECT timestamp FROM patrimonio.bem_plano_analitica ORDER BY timestamp DESC LIMIT 1) AS ultimo_timestamp
                       ,  patrimonio.bem_plano_analitica.cod_bem
                       ,  bem_plano_analitica.cod_plano
                       ,  bem_plano_analitica.exercicio
                       ,  nom_conta

                    FROM  patrimonio.bem_plano_analitica

              INNER JOIN  contabilidade.plano_analitica
                      ON  plano_analitica.cod_plano = bem_plano_analitica.cod_plano
                     AND  plano_analitica.exercicio = bem_plano_analitica.exercicio

              INNER JOIN  contabilidade.plano_conta
                      ON  plano_conta.cod_conta = plano_analitica.cod_conta
                     AND  plano_conta.exercicio = plano_analitica.exercicio

                    WHERE 1=1 ";

        if ($this->getDado('cod_bem')) {
            $stSql.= " AND bem_plano_analitica.cod_bem = ".$this->getDado('cod_bem');
        }

        if ($this->getDado('cod_plano')) {
            $stSql.= "  AND bem_plano_analitica.cod_plano = ".$this->getDado('cod_plano');
        }

        if ($this->getDado('exercicio')) {
            $stSql.= "  AND bem_plano_analitica.exercicio = '".$this->getDado('exercicio')."'";
        }

        $stSql .= "
               GROUP BY  patrimonio.bem_plano_analitica.cod_bem
                      ,  bem_plano_analitica.cod_plano
                      ,  bem_plano_analitica.exercicio
                      ,  nom_conta

               ORDER BY  timestamp DESC ";

        return $stSql;
    }
}

?>
