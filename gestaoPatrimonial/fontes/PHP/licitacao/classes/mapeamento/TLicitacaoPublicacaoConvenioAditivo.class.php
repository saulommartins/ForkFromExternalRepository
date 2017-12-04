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
    * Classe de mapeamento da tabela licitacao.publicacao_convenio
    * Data de Criação: 15/09/2006

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.05.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TLicitacaoPublicacaoConvenioAditivo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoPublicacaoConvenioAditivo()
{
    parent::Persistente();
    $this->setTabela("licitacao.convenio_aditivos_publicacao");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,exercicio_convenio,num_convenio,num_aditivo,numcgm,dt_publicacao');

    $this->AddCampo('exercicio'          ,'char'    ,false  ,'4'  , true,  false);
    $this->AddCampo('exercicio_convenio' ,'char'    ,false  ,'4'  , true,  false);
    $this->AddCampo('num_convenio'       ,'integer' ,false  ,''   , true,  false);
    $this->AddCampo('num_aditivo'        ,'integer' ,false  ,''   , true,  false);
    $this->AddCampo('numcgm'             ,'integer' ,false  ,''   , true,  false);
    $this->AddCampo('dt_publicacao'      ,'date'    ,true   ,''   , true,  false);
    $this->AddCampo('observacao'         ,'varchar' ,false  ,'80' , false, false);
    $this->AddCampo('num_publicacao'     ,'integer' ,false  ,''   , false, false);

}

function recuperaVeiculosPublicacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaVeiculosPublicacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaVeiculosPublicacao()
{
    $stSql = " SELECT  convenio_aditivos_publicacao.num_convenio                \n";
    $stSql .= "     , convenio_aditivos_publicacao.exercicio 	                \n";
    $stSql .= "	    , to_char( convenio_aditivos_publicacao.dt_publicacao, 'dd/mm/yyyy' ) as dt_publicacao \n";
    $stSql .= "	    , convenio_aditivos_publicacao.numcgm as num_veiculo        \n";
    $stSql .= "	    , convenio_aditivos_publicacao.num_publicacao               \n";
    $stSql .= "	    , sw_cgm.nom_cgm as nom_veiculo 				            \n";
    $stSql .= "	    , convenio_aditivos_publicacao.observacao 	        	    \n";
    $stSql .= "       FROM licitacao.convenio_aditivos_publicacao               \n";
    $stSql .= " INNER JOIN sw_cgm 							                    \n";
    $stSql .= "	    ON sw_cgm.numcgm = convenio_aditivos_publicacao.numcgm      \n";
    $stSql .= "      WHERE num_convenio = ".$this->getDado('num_convenio')."	\n";
    $stSql .= "        AND num_aditivo  = ".$this->getDado('num_aditivo')." 	\n";
    $stSql .= "        AND exercicio    = '".$this->getDado('exercicio')."' 	\n";

    return $stSql;
}
}
