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
* Classe de mapeamento da função VenalTerreno()
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3631 $
$Name$
$Author: tonismar $
$Date: 2005-12-08 13:24:36 -0200 (Qui, 08 Dez 2005) $

Casos de uso: uc-01.03.95, uc-05.03.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';

/**
  * Data de Criação: 20/10/2005

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Lucas Teixeira Stephanou

  * @package URBEM
  * @subpackage Mapeamento
*/
class FVenalTerreno extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FVenalTerreno()
{
    parent::Persistente();
/*    $this->setTabela('arrecadacao.grupo_credito');
    $this->setCampoCod('cod_grupo');
    $this->setComplementoChave('cod_grupo');*/
    $this->AddCampo('valor','numeric'  ,false       ,''     ,false   ,false );
}

function executaFuncao(&$rsRecordset, $stParametros, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;

    $stSql  = $this->montaExecutaFuncao($stParametros);
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordset, $stSql, $boTransacao );

return $obErro;
}

function montaExecutaFuncao($stParametros)
{
    $stSql  = " SELECT  VENALTERRENO(".$stParametros.") as valor \r\n";

return $stSql;
}

}
?>
