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
    * Data de Criação   : 10/03/2015
    * 
    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Michel Teixeira
    * 
    * @package URBEM
    * @subpackage
    * 
    * @ignore
    * 
    $Id: TTCEMGOperacoesCreditoARO.class.php 62269 2015-04-15 18:28:39Z franver $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGOperacoesCreditoARO extends Persistente
{
/**
* Método Construtor
* @access Private
*/
    public function TTCEMGOperacoesCreditoARO()
    {
        parent::Persistente();
        $this->setTabela("tcemg.operacao_credito_aro");

        $this->setCampoCod('exercicio');
        $this->setComplementoChave('cod_entidade');

        $this->AddCampo( 'cod_entidade'     , 'integer' , true, ''      , true  , true  );
        $this->AddCampo( 'exercicio'        , 'varchar' , true, '4'     , true  , true  );
        $this->AddCampo( 'dt_contratacao'   , 'date'    , true, ''      , false , false );
        $this->AddCampo( 'vl_contratado'    , 'numeric' , true, '14,2'  , false , false );
        $this->AddCampo( 'dt_principal'     , 'date'    , true, ''      , false , false );
        $this->AddCampo( 'dt_juros'         , 'date'    , true, ''      , false , false );
        $this->AddCampo( 'dt_encargos'      , 'date'    , true, ''      , false , false );
        $this->AddCampo( 'vl_liquidacao'    , 'numeric' , true, '14,2'  , false , false );
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
        $stSql = "SELECT 12 AS mes
                       , cod_entidade
                       , exercicio
                       , TO_CHAR(dt_contratacao     ,'ddmmyyyy') AS dt_contratacao
                       , REPLACE(vl_contratado::TEXT,'.',    '') AS vl_contratado
                       , TO_CHAR(dt_principal       ,'ddmmyyyy') AS dt_principal
                       , TO_CHAR(dt_juros           ,'ddmmyyyy') AS dt_juros
                       , TO_CHAR(dt_encargos        ,'ddmmyyyy') AS dt_encargos
                       , REPLACE(vl_liquidacao::TEXT,'.',    '') AS vl_liquidacao
                    FROM tcemg.operacao_credito_aro
                   WHERE cod_entidade IN (".$this->getDado('cod_entidade').")
                     AND exercicio = '".$this->getDado('exercicio')."'";

        return $stSql;
    }
}
