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

    $Revision: 16561 $
    $Name$
    $Author: domluc $
    $Date: 2006-10-09 09:18:10 -0300 (Seg, 09 Out 2006) $

    * Casos de uso: uc-03.05.14
*/
/*
$Log$
Revision 1.2  2006/10/09 12:17:51  domluc
Caso de Uso : uc-03.05.14

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.publicacao_convenio
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoPublicacaoConvenio extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoPublicacaoConvenio()
{
    parent::Persistente();
    $this->setTabela("licitacao.publicacao_convenio");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,num_convenio,numcgm,dt_publicacao');

    $this->AddCampo('exercicio'      ,'char'    ,false  ,'4'  , true, 'TLicitacaoConvenio');
    $this->AddCampo('num_convenio'   ,'integer' ,false  ,''   , true, 'TLicitacaoConvenio');
    $this->AddCampo('numcgm'         ,'integer' ,false  ,''   , true, 'TLicitacaoVeiculosPublicidade');
    $this->AddCampo('dt_publicacao'  ,'date'    ,true   ,''   , true,  false);
    $this->AddCampo('observacao'     ,'varchar' ,false  ,'80' , false, false);
    $this->AddCampo('num_publicacao' ,'integer' ,false  ,''   , false, false);

}

function recuperaVeiculosPublicacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaVeiculosPublicacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaVeiculosPublicacao()
{
    $stSql = " SELECT  publicacao_convenio.num_convenio	                                    \n";
    $stSql .= "     , publicacao_convenio.exercicio 			                    \n";
    $stSql .= "	    , to_char( publicacao_convenio.dt_publicacao, 'dd/mm/yyyy' ) as dt_publicacao   \n";
    $stSql .= "	    , publicacao_convenio.numcgm as num_veiculo	                            \n";
    $stSql .= "	    , publicacao_convenio.num_publicacao                                    \n";
    $stSql .= "	    , sw_cgm.nom_cgm as nom_veiculo 				            \n";
    $stSql .= "	    , publicacao_convenio.observacao 				            \n";
    $stSql .= "       FROM licitacao.publicacao_convenio     		                    \n";
    $stSql .= " INNER JOIN sw_cgm 							    \n";
    $stSql .= "	    ON sw_cgm.numcgm = publicacao_convenio.numcgm 	                    \n";
    $stSql .= "      WHERE num_convenio  = ".$this->getDado('num_convenio')."	            \n";
    $stSql .= "        AND exercicio     = '".$this->getDado('exercicio')."' 		    \n";

    return $stSql;
}
}
