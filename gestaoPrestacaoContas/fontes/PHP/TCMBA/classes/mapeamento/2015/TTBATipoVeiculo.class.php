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
    * Extensão da Classe de mapeamento
    * Data de Criação: 30/08/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62823 $
    $Name$
    $Author: hboaventura $
    $Date: 2008-08-20 09:07:59 -0300 (Qua, 20 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.2  2007/10/02 18:20:03  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/09/04 00:32:40  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 30/08/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBATipoVeiculo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBATipoVeiculo()
{
    parent::Persistente();
    $this->setTabela('tcmba.tipo_veiculo');

    $this->setCampoCod('cod_tipo_tcm');

    $this->AddCampo('cod_tipo_tcm','integer',true,'',true,false);
    $this->AddCampo('descricao','varchar',false,'200',false,false);
}

function montaRecuperaRelacionamento()
{
    $stSql .= " SELECT  *                                       \n";
    $stSql .= " FROM    tcmba.tipo_veiculo as ttv               \n";
    $stSql .= "         LEFT JOIN frota.tipo_veiculo as ftv     \n";
    $stSql .= "         ON ( ttv.cod_tipo = ftv.cod_tipo )      \n";
    $stSql .= " ORDER BY ttv.cod_tipo_tcm                       \n";

    return $stSql;
}

}
