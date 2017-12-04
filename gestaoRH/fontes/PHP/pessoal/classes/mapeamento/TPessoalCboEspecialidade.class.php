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
    * Classe de mapeamento da tabela pessoal.cbo_especialidade
    * Data de Criação: 12/06/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-15 15:00:34 -0300 (Sex, 15 Jun 2007) $

    * Casos de uso: uc-04.04.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.cbo_especialidade
  * Data de Criação: 12/06/2007

  * @author Analista: Dagiane
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalCboEspecialidade extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalCboEspecialidade()
{
    parent::Persistente();
    $this->setTabela("pessoal.cbo_especialidade");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_cbo,cod_especialidade,timestamp');

    $this->AddCampo('cod_cbo'  ,'integer'      ,true  ,'',true,'TPessoalCbo');
    $this->AddCampo('cod_especialidade','integer'      ,true  ,'',true,'TPessoalEspecialidade');
    $this->AddCampo('timestamp','timestamp_now',true  ,'',true,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "    SELECT cbo_especialidade.*                                                             \n";
    $stSql .= "         , cbo.codigo                                                                      \n";
    $stSql .= "         , cbo.descricao                                                                   \n";
    $stSql .= "      FROM pessoal.cbo_especialidade                              \n";
    $stSql .= "INNER JOIN (   SELECT cod_especialidade                                                    \n";
    $stSql .= "                    , max(timestamp) as timestamp                                          \n";
    $stSql .= "                 FROM pessoal.cbo_especialidade                   \n";
    $stSql .= "            GROUP BY cod_especialidade) as max_cbo_especialidade                           \n";
    $stSql .= "        ON max_cbo_especialidade.cod_especialidade = cbo_especialidade.cod_especialidade   \n";
    $stSql .= "       AND max_cbo_especialidade.timestamp = cbo_especialidade.timestamp                   \n";
    $stSql .= "INNER JOIN pessoal.cbo                                            \n";
    $stSql .= "        ON cbo.cod_cbo = cbo_especialidade.cod_cbo                                         \n";

    return $stSql;
}
}

?>
