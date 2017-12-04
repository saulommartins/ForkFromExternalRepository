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
    * Data de Criação: 25/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    $Id: TLicitacaoPublicacaoRescisaoConvenio.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.05.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TLicitacaoPublicacaoRescisaoConvenio extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TLicitacaoPublicacaoRescisaoConvenio()
    {
        parent::Persistente();
        $this->setTabela("licitacao.publicacao_rescisao_convenio");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio_convenio, num_convenio, cgm_imprensa, dt_publicacao');

        $this->AddCampo( 'exercicio_convenio'    , 'char'    , true, '4'     , true  , true  );
        $this->AddCampo( 'num_convenio'          , 'integer' , true, ''      , true  , true  );
        $this->AddCampo( 'cgm_imprensa'          , 'integer' , true, ''      , false , true  );
        $this->AddCampo( 'dt_publicacao'         , 'date'    , true, ''      , false , false );
        $this->AddCampo( 'observacao'            , 'char'    , true, '100'   , false , false );
        $this->AddCampo( 'num_publicacao'        ,'integer'  ,false,''       , false, false  );
    }

    public function recuperaVeiculosPublicacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaVeiculosPublicacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaVeiculosPublicacao()
    {
        $stSql = " SELECT     publicacao_rescisao_convenio.num_convenio                                              \n";
        $stSql .= "         , publicacao_rescisao_convenio.exercicio_convenio as exercicio              \n";
        $stSql .= "	    , to_char( publicacao_rescisao_convenio.dt_publicacao, 'dd/mm/yyyy' ) as dt_publicacao   \n";
        $stSql .= "	    , publicacao_rescisao_convenio.cgm_imprensa as num_veiculo                  \n";
        $stSql .= "	    , publicacao_rescisao_convenio.num_publicacao                               \n";
        $stSql .= "	    , sw_cgm.nom_cgm as nom_veiculo 				                \n";
        $stSql .= "	    , publicacao_rescisao_convenio.observacao 	        	                \n";
        $stSql .= "       FROM licitacao.publicacao_rescisao_convenio                                   \n";
        $stSql .= " INNER JOIN sw_cgm 							                \n";
        $stSql .= "	    ON sw_cgm.numcgm       = publicacao_rescisao_convenio.cgm_imprensa          \n";
        $stSql .= "      WHERE num_convenio        = ".$this->getDado('num_convenio')."	                \n";
        $stSql .= "        AND exercicio_convenio  = '".$this->getDado('exercicio_convenio')."' 		\n";

        return $stSql;
    }
}
