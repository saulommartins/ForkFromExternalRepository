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
    * Classe de mapeamento da tabela pessoal.cbo_cargo
    * Data de Criação: 30/05/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.cbo_cargo
  * Data de Criação: 30/05/2007

  * @author Analista: Dagiane
  * @author Desenvolvedor: André Machado

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalCboCargo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalCboCargo()
{
    parent::Persistente();
    $this->setTabela("pessoal.cbo_cargo");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_cbo,cod_cargo,timestamp');

    $this->AddCampo('cod_cbo'  ,'integer'      ,true  ,'',true,'TPessoalCbo');
    $this->AddCampo('cod_cargo','integer'      ,true  ,'',true,'TPessoalCargo');
    $this->AddCampo('timestamp','timestamp_now',true  ,'',true,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "    SELECT cbo_cargo.*                                                       \n";
    $stSql .= "         , cbo.codigo                                                        \n";
    $stSql .= "         , cbo.descricao                                                     \n";
    $stSql .= "      FROM pessoal.cbo_cargo                        \n";
    $stSql .= "INNER JOIN (   SELECT cod_cargo                                              \n";
    $stSql .= "                    , max(timestamp) as timestamp                            \n";
    $stSql .= "                 FROM pessoal.cbo_cargo             \n";
    $stSql .= "            GROUP BY cod_cargo) as max_cbo_cargo                             \n";
    $stSql .= "        ON max_cbo_cargo.cod_cargo = cbo_cargo.cod_cargo                     \n";
    $stSql .= "       AND max_cbo_cargo.timestamp = cbo_cargo.timestamp                     \n";
    $stSql .= "INNER JOIN pessoal.cbo                              \n";
    $stSql .= "        ON cbo.cod_cbo = cbo_cargo.cod_cbo                                   \n";

    return $stSql;
}
}

?>
