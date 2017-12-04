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
 * @author Analista: Dagiane Vieira
 * @author Desenvolvedor: Michel Teixeira
 *
 * $Id: TTCEMGProjecaoAtuarial.class.php 61821 2015-03-06 17:01:48Z michel $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGProjecaoAtuarial extends Persistente
{
/**
* Método Construtor
* @access Private
*/
    public function TTCEMGProjecaoAtuarial()
    {
        parent::Persistente();
        $this->setTabela("tcemg.projecao_atuarial");

        $this->setCampoCod('exercicio');
        $this->setComplementoChave('cod_entidade,exercicio_entidade');

        $this->AddCampo( 'cod_entidade'         , 'integer' , true  , ''     , true  , true  );
        $this->AddCampo( 'exercicio'            , 'varchar' , true  , '4'    , true  , false );
        $this->AddCampo( 'exercicio_entidade'   , 'varchar' , true  , '4' 	 , true  , true	 );
        $this->AddCampo( 'vl_patronal'          , 'numeric' , true  , '14,2' , false , false );
        $this->AddCampo( 'vl_receita'           , 'numeric' , true  , '14,2' , false , false );
        $this->AddCampo( 'vl_despesa'           , 'numeric' , true  , '14,2' , false , false );
        $this->AddCampo( 'vl_rpps'              , 'numeric' , true  , '14,2' , false , false );
    }

    public function recuperaPorEntidade(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaPorEntidade();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

        return $obErro;
    }

    public function montaRecuperaPorEntidade()
    {
        $stSql = "SELECT *
                    FROM tcemg.projecao_atuarial
                   WHERE cod_entidade = ".$this->getDado('cod_entidade')."
                     AND exercicio_entidade = '".$this->getDado('exercicio_entidade')."'";

        return $stSql;
    }

    public function limpaProjecao($boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;

        $stSql  = " DELETE                                                                  \n";
        $stSql .= "   FROM tcemg.projecao_atuarial                                          \n";
        $stSql .= "  WHERE exercicio_entidade = '".$this->getDado('exercicio_entidade')."'  \n";
        $stSql .= "    AND cod_entidade = ".$this->getDado('cod_entidade')."                \n";
        $stSql .= "    AND exercicio = '".$this->getDado('exercicio')."'                    \n";

        $this->setDebug( $stSql );
        $obErro = $obConexao->executaDML( $stSql, $boTransacao );

        return $obErro;
    }
    
    public function recuperaPorExercicioEntidade(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaPorExercicioEntidade();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

        return $obErro;
    }

    public function montaRecuperaPorExercicioEntidade()
    {
        $stSql = "SELECT exercicio
                       , cod_entidade
                       , exercicio_entidade
                       , COALESCE(REPLACE(vl_patronal::text ,'.',''),'000') AS vl_patronal
                       , COALESCE(REPLACE(vl_receita::text  ,'.',''),'000') AS vl_receita
                       , COALESCE(REPLACE(vl_despesa::text  ,'.',''),'000') AS vl_despesa
                       , COALESCE(REPLACE(vl_rpps::text     ,'.',''),'000') AS vl_rpps
                    FROM tcemg.projecao_atuarial
                   WHERE exercicio_entidade = '".$this->getDado('exercicio_entidade')."'
                     AND cod_entidade = ".$this->getDado('cod_entidade');

        return $stSql;
    }
}
