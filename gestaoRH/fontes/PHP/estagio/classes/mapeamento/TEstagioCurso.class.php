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
    * Classe de mapeamento da tabela estagio.curso
    * Data de Criação: 04/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.07.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  estagio.curso
  * Data de Criação: 04/10/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEstagioCurso extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEstagioCurso()
{
    parent::Persistente();
    $this->setTabela("estagio.curso");

    $this->setCampoCod('cod_curso');
    $this->setComplementoChave('');

    $this->AddCampo('cod_curso'            ,'sequence',true  ,''    ,true,false);
    $this->AddCampo('cod_grau'             ,'integer' ,true  ,''    ,false,'TEstagioGrau');
    $this->AddCampo('cod_area_conhecimento','integer' ,true  ,''    ,false,'TEstagioAreaConhecimento');
    $this->AddCampo('nom_curso'            ,'varchar' ,true  ,'80'  ,false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT curso.*                              \n";
    $stSql .= "     , grau.descricao                       \n";
    $stSql .= "  FROM estagio.curso                        \n";
    $stSql .= "     , estagio.grau                         \n";
    $stSql .= " WHERE curso.cod_grau = grau.cod_grau       \n";

    return $stSql;
}

}
?>
