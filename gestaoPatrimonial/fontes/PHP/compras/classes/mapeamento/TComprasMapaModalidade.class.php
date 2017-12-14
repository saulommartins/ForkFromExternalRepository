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
    * Classe de mapeamento da tabela compras.mapa_modalidade
    * Data de Criação: 11/01/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 19295 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-01-12 15:15:18 -0200 (Sex, 12 Jan 2007) $

    * Casos de uso: uc-03.04.05
                    uc-03.05.15
                    uc-03.04.30
*/

/*
$Log$
Revision 1.1  2007/01/12 17:15:18  hboaventura
uc-03.04.05

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.mapa_modalidade
  * Data de Criação: 11/01/2007

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Henrique Boaventura

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasMapaModalidade extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasMapaModalidade()
{
    parent::Persistente();
    $this->setTabela("compras.mapa_modalidade");

    $this->setCampoCod('cod_mapa');
    $this->setComplementoChave('exercicio');

    $this->AddCampo( 'cod_mapa'           ,'integer'  ,true, '' ,true  ,'TComprasMapa'       );
    $this->AddCampo( 'exercicio'          ,'char'     ,true, '4',true  ,'TComprasMapa'       );
    $this->AddCampo( 'cod_modalidade'     ,'integer'  ,true, '' ,false ,'TComprasModalidade' );

}

function recuperaCodModalidade(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaCodModalidade",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaCodModalidade()
{
    $stSql = " SELECT cod_modalidade                               \n";
    $stSql.= "   FROM compras.mapa_modalidade                      \n";
    if ($this->getDado("cod_mapa")) {
        $stSql.= " WHERE mapa_modalidade.cod_mapa = ".$this->getDado("cod_mapa")."  \n";
    }
    if ($this->getDado("exercicio")) {
        $stSql.= " AND mapa_modalidade.exercicio = ".$this->getDado("exercicio")."  \n";
    }

    return $stSql;
}

}
