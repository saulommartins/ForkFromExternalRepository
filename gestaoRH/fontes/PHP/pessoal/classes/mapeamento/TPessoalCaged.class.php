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
    * Classe de mapeamento da tabela pessoal.caged
    * Data de Criação: 23/04/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.04.10

    $Id: TPessoalCaged.class.php 30566 2008-06-27 13:50:23Z domluc $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal._caged
  * Data de Criação: 23/04/2008

  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalCaged extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalCaged()
{
    parent::Persistente();
    $this->setTabela("pessoal.caged");

    $this->setCampoCod('cod_caged');
    $this->setComplementoChave('');

    $this->AddCampo('cod_caged','integer',true  ,''    ,true,false);
    $this->AddCampo('num_caged','integer',true  ,''    ,false,false);
    $this->AddCampo('descricao','varchar',true  ,'60'  ,false,false);
    $this->AddCampo('tipo','varchar'     ,true  ,'1'   ,false,false);
}
}
?>
