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
    * Classe de mapeamento da tabela pessoal.cbo
    * Data de Criação: 30/05/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-13 09:55:05 -0300 (Qua, 13 Jun 2007) $

    * Casos de uso: uc-04.04.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.cbo
  * Data de Criação: 30/05/2007

  * @author Analista: Dagiane
  * @author Desenvolvedor: André Machado

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalCbo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalCbo()
{
    parent::Persistente();
    $this->setTabela("pessoal.cbo");

    $this->setCampoCod('cod_cbo');
    $this->setComplementoChave('');

    $this->AddCampo('cod_cbo'   ,'sequence',true  ,''     ,true,false);
    $this->AddCampo('codigo'    ,'integer' ,true  ,''     ,false,false);
    $this->AddCampo('descricao' ,'varchar' ,true  ,'150'  ,false,false);
    $this->AddCampo('dt_inicial','date'    ,true  ,''     ,false,false);
    $this->AddCampo('dt_final'  ,'date'    ,false ,''     ,false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT cbo_cargo.*                                        \n";
    $stSql .= "     , descricao                                          \n";
    $stSql .= "     , codigo                                             \n";
    $stSql .= "  FROM pessoal.cbo_cargo                                  \n";
    $stSql .= "     , (SELECT cod_cargo                                  \n";
    $stSql .= "             , max(timestamp) as timestamp                \n";
    $stSql .= "          FROM pessoal.cbo_cargo                          \n";
    $stSql .= "        GROUP BY cod_cargo) as max_cbo_cargo              \n";
    $stSql .= "     , pessoal.cbo                                        \n";
    $stSql .= " WHERE cbo_cargo.cod_cargo = max_cbo_cargo.cod_cargo      \n";
    $stSql .= "   AND cbo_cargo.timestamp = max_cbo_cargo.timestamp      \n";
    $stSql .= "   AND cbo_cargo.cod_cbo = cbo.cod_cbo                    \n";

    return $stSql;
}

}

?>
